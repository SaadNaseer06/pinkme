<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The message instance being broadcast.
     */
    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load(['sender.profile', 'receiver.profile']);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("users.{$this->message->sender_id}.messages"),
            new PrivateChannel("users.{$this->message->receiver_id}.messages"),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->message->toFrontendPayload(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'chat.message';
    }
}
