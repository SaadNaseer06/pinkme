<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'content',
        'attachment_path',
        'attachment_name',
        'attachment_size',
        'attachment_mime',
        'is_read',
        'sent_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function scopeBetweenUsers(Builder $query, int $firstUserId, int $secondUserId): Builder
    {
        return $query->where(function (Builder $inner) use ($firstUserId, $secondUserId): void {
            $inner->where('sender_id', $firstUserId)
                ->where('receiver_id', $secondUserId);
        })->orWhere(function (Builder $inner) use ($firstUserId, $secondUserId): void {
            $inner->where('sender_id', $secondUserId)
                ->where('receiver_id', $firstUserId);
        });
    }

    public static function markThreadAsRead(int $authUserId, int $contactUserId): int
    {
        return static::betweenUsers($authUserId, $contactUserId)
            ->where('receiver_id', $authUserId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'updated_at' => now(),
            ]);
    }

    public function toFrontendPayload(): array
    {
        return [
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'content' => $this->content,
            'attachment' => $this->attachment_path ? [
                'url' => storage_url($this->attachment_path),
                'name' => $this->attachment_name,
                'size' => $this->attachment_size,
                'mime' => $this->attachment_mime,
                'is_image' => str_starts_with((string) $this->attachment_mime, 'image/'),
            ] : null,
            'is_read' => (bool) $this->is_read,
            'sent_at' => optional($this->sent_at)->toIso8601String(),
            'sent_at_display' => optional($this->sent_at)->format('d M Y, h:i A'),
            'sender' => [
                'id' => $this->sender_id,
                'name' => optional($this->sender?->profile)->full_name ?? $this->sender?->email,
                'avatar_url' => $this->sender?->avatar_url,
            ],
            'receiver' => [
                'id' => $this->receiver_id,
                'name' => optional($this->receiver?->profile)->full_name ?? $this->receiver?->email,
                'avatar_url' => $this->receiver?->avatar_url,
            ],
        ];
    }

    /**
     * Build latest-message + unread-count metadata for a contact list with minimal queries.
     *
     * @param  array<int, int>  $contactIds
     * @return array{
     *   latest_by_contact: array<int, self>,
     *   unread_by_contact: array<int, int>
     * }
     */
    public static function contactSummariesForUser(int $authUserId, array $contactIds): array
    {
        $contactIds = collect($contactIds)
            ->filter(fn ($id) => (int) $id > 0)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($contactIds->isEmpty()) {
            return [
                'latest_by_contact' => [],
                'unread_by_contact' => [],
            ];
        }

        $unreadByContact = static::query()
            ->selectRaw('sender_id, COUNT(*) as unread_count')
            ->where('receiver_id', $authUserId)
            ->whereIn('sender_id', $contactIds)
            ->where('is_read', false)
            ->groupBy('sender_id')
            ->pluck('unread_count', 'sender_id')
            ->map(fn ($count) => (int) $count)
            ->all();

        /** @var Collection<int, self> $ordered */
        $ordered = static::query()
            ->where(function (Builder $query) use ($authUserId, $contactIds): void {
                $query->where(function (Builder $q) use ($authUserId, $contactIds): void {
                    $q->where('sender_id', $authUserId)
                        ->whereIn('receiver_id', $contactIds);
                })->orWhere(function (Builder $q) use ($authUserId, $contactIds): void {
                    $q->where('receiver_id', $authUserId)
                        ->whereIn('sender_id', $contactIds);
                });
            })
            ->orderByDesc('sent_at')
            ->orderByDesc('id')
            ->get();

        $latestByContact = [];
        foreach ($ordered as $message) {
            $contactId = (int) ($message->sender_id === $authUserId ? $message->receiver_id : $message->sender_id);
            if (! isset($latestByContact[$contactId])) {
                $latestByContact[$contactId] = $message;
            }
            if (count($latestByContact) >= $contactIds->count()) {
                break;
            }
        }

        return [
            'latest_by_contact' => $latestByContact,
            'unread_by_contact' => $unreadByContact,
        ];
    }
}
