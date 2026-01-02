<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Webinar;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WebinarRegistrationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public Webinar $webinar;
    public User $user;

    public function __construct(Webinar $webinar, User $user)
    {
        $this->webinar = $webinar;
        $this->user = $user;
    }

    public function build()
    {
        $scheduledAt = $this->webinar->scheduled_at
            ? $this->webinar->scheduled_at
                ->timezone(config('app.timezone'))
                ->format('F j, Y g:i A')
            : null;

        $recipientName = optional($this->user->profile)->full_name
            ?? ($this->user->name ?? $this->user->email);

        return $this
            ->subject('Webinar Registration Confirmed: ' . ($this->webinar->title ?? 'Webinar'))
            ->view('emails.webinars.registration')
            ->with([
                'webinar' => $this->webinar,
                'recipientName' => $recipientName,
                'scheduledAt' => $scheduledAt,
                'joinUrl' => $this->webinar->join_url,
            ]);
    }
}
