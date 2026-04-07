<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChatMessageReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $sender,
        public User $recipient,
        public string $snippet,
        public string $chatUrl
    ) {}

    public function build()
    {
        $senderName = optional($this->sender->profile)->full_name ?? $this->sender->email ?? 'Someone';
        $recipientName = optional($this->recipient->profile)->full_name ?? $this->recipient->email ?? 'there';

        return $this
            ->subject('New message from ' . $senderName)
            ->view('emails.new_chat_message')
            ->with([
                'senderName' => $senderName,
                'recipientName' => $recipientName,
                'content' => $this->snippet,
                'chatUrl' => $this->chatUrl,
            ]);
    }
}
