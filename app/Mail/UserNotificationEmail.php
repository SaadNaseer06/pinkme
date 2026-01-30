<?php

namespace App\Mail;

use App\Models\UserNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserNotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public UserNotification $userNotification;

    public function __construct(UserNotification $userNotification)
    {
        $this->userNotification = $userNotification->loadMissing('user');
    }

    public function build()
    {
        $user = $this->userNotification->user;
        $recipientName = optional($user?->profile)->full_name ?? $user?->email ?? 'there';
        $linkUrl = $this->userNotification->link_url
            ? (str_starts_with($this->userNotification->link_url, 'http') ? $this->userNotification->link_url : url($this->userNotification->link_url))
            : null;

        return $this
            ->subject($this->userNotification->title ?: 'New notification')
            ->view('emails.user_notification')
            ->with([
                'recipientName' => $recipientName,
                'title' => $this->userNotification->title ?: 'Notification',
                'message' => $this->userNotification->message,
                'linkUrl' => $linkUrl,
            ]);
    }
}
