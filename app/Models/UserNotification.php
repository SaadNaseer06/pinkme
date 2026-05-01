<?php

namespace App\Models;

use App\Events\UserNotificationCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotification extends Model
{
    use HasFactory;

    /** @var array<int, string|null> */
    private static array $programBannerCache = [];

    public const PRIORITY_NORMAL = 'normal';

    public const PRIORITY_IMPORTANT = 'important';

    /**
     * Dispatch domain events for model lifecycle hooks.
     *
     * @var array<string, class-string<\Illuminate\Foundation\Events\Dispatchable>>
     */
    protected $dispatchesEvents = [
        'created' => UserNotificationCreated::class,
    ];

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'priority',
        'link_url',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    protected $attributes = [
        'priority' => self::PRIORITY_NORMAL,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeImportant($query)
    {
        return $query->where('priority', self::PRIORITY_IMPORTANT);
    }

    public function isImportant(): bool
    {
        return $this->priority === self::PRIORITY_IMPORTANT;
    }

    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => now()])->save();
        }
    }

    /**
     * Format payload for frontend consumers (dropdown, realtime, etc.).
     */
    public function toFrontendPayload(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'link_url' => $this->link_url,
            'image_url' => $this->resolveImageUrl(),
            'priority' => $this->priority,
            'read_at' => optional($this->read_at)->toISOString(),
            'created_at' => optional($this->created_at)->toISOString(),
            'created_at_formatted' => optional($this->created_at)->format('d M Y, h:i A'),
        ];
    }

    private function resolveImageUrl(): ?string
    {
        if (! $this->link_url) {
            return null;
        }

        $path = (string) parse_url($this->link_url, PHP_URL_PATH);
        if (! preg_match('#/patient/programs/(\d+)$#', $path, $matches)) {
            return null;
        }

        $programId = (int) ($matches[1] ?? 0);
        if ($programId <= 0) {
            return null;
        }

        if (! array_key_exists($programId, self::$programBannerCache)) {
            self::$programBannerCache[$programId] = Program::query()->whereKey($programId)->value('banner');
        }

        $banner = self::$programBannerCache[$programId];
        if (! $banner) {
            return null;
        }

        return storage_url((string) $banner);
    }
}
