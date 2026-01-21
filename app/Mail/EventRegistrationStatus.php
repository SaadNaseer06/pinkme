<?php

namespace App\Mail;

use App\Models\EventSponsorship;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventRegistrationStatus extends Mailable
{
    use Queueable, SerializesModels;

    public EventSponsorship $registration;
    public string $statusLabel;

    public function __construct(EventSponsorship $registration, string $statusLabel)
    {
        $this->registration = $registration;
        $this->statusLabel = $statusLabel;
    }

    public function build()
    {
        $recipientName = optional($this->registration->sponsor?->profile)->full_name
            ?? $this->registration->sponsor?->name
            ?? $this->registration->sponsor?->email
            ?? 'there';

        $eventTitle = $this->registration->event?->title ?? 'an event';
        $eventDate = $this->registration->event?->date
            ? $this->registration->event->date->timezone(config('app.timezone'))->format('F j, Y')
            : null;

        return $this
            ->subject('Event Registration ' . $this->statusLabel . ': ' . $eventTitle)
            ->view('emails.events.registration_status')
            ->with([
                'recipientName' => $recipientName,
                'eventTitle' => $eventTitle,
                'eventDate' => $eventDate,
                'statusLabel' => $this->statusLabel,
                'amount' => $this->registration->amount,
            ]);
    }
}
