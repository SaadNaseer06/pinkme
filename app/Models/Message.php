<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
                'url' => asset('storage/' . ltrim($this->attachment_path, '/')),
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
}
