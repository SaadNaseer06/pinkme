<?php

namespace App\Mail;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewChatMessageEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message->loadMissing(['sender.profile', 'receiver.profile']);
    }

    public function build()
    {
        $receiver = $this->message->receiver;
        $sender = $this->message->sender;
        $recipientName = optional($receiver?->profile)->full_name ?? $receiver?->email ?? 'there';
        $senderName = optional($sender?->profile)->full_name ?? $sender?->email ?? 'Someone';
        $content = $this->message->content;
        if (strlen($content) > 500) {
            $content = substr($content, 0, 497) . '...';
        }
        $chatUrl = url('/');
        $role = optional($receiver->role)->name;
        try {
            if ($role === 'patient') {
                $chatUrl = route('patient.patientChats');
            } elseif ($role === 'sponsor') {
                $chatUrl = route('sponsor.dashboard');
            } elseif ($role === 'casemanager') {
                $chatUrl = route('case_manager.patientChats');
            } elseif ($role === 'admin') {
                $chatUrl = route('admin.dashboard');
            }
        } catch (\Throwable $e) {
            // keep default url
        }

        return $this
            ->subject("You've got a new message from " . $senderName)
            ->view('emails.new_chat_message')
            ->with([
                'recipientName' => $recipientName,
                'senderName' => $senderName,
                'content' => $content,
                'chatUrl' => $chatUrl,
            ]);
    }
}
