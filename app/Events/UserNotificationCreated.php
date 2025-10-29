<?php

namespace App\Events;

use App\Models\UserNotification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserNotificationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The notification instance being broadcast.
     */
    public UserNotification $notification;

    /**
     * Create a new event instance.
     */
    public function __construct(UserNotification $notification)
    {
        // Ensure we have the latest timestamps and strip heavy relations.
        $this->notification = $notification->fresh(['user']) ?? $notification;
        $this->notification->unsetRelation('user');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel("users.{$this->notification->user_id}.notifications")];
    }

    /**
     * Data that should be broadcast with the event.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $unreadCount = UserNotification::query()
            ->where('user_id', $this->notification->user_id)
            ->whereNull('read_at')
            ->count();

        return [
            'notification' => $this->notification->toFrontendPayload(),
            'unread_count' => $unreadCount,
        ];
    }

    public function broadcastAs(): string
    {
        return 'user.notification.created';
    }
}
