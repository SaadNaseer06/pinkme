<?php

namespace App\Mail;

use App\Models\ProgramRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProgramRegistrationStatus extends Mailable
{
    use Queueable, SerializesModels;

    public ProgramRegistration $registration;
    public string $statusLabel;
    public ?string $note;

    public function __construct(ProgramRegistration $registration, string $statusLabel, ?string $note = null)
    {
        $this->registration = $registration;
        $this->statusLabel = $statusLabel;
        $this->note = $note;
    }

    public function build()
    {
        $recipientName = optional($this->registration->user?->profile)->full_name
            ?? $this->registration->full_name
            ?? $this->registration->user?->email
            ?? $this->registration->email
            ?? 'there';

        $programTitle = $this->registration->program?->title ?? 'a program';

        return $this
            ->subject('Program Registration ' . $this->statusLabel . ': ' . $programTitle)
            ->view('emails.programs.registration_status')
            ->with([
                'recipientName' => $recipientName,
                'programTitle' => $programTitle,
                'statusLabel' => $this->statusLabel,
                'note' => $this->note,
                'detailUrl' => route('patient.programRegistrations.show', $this->registration),
            ]);
    }
}
