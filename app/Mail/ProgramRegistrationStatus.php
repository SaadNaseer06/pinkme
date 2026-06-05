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

        $firstName = $this->registration->first_name
            ?? (is_string($recipientName) ? (explode(' ', trim($recipientName), 2)[0] ?? 'there') : 'there');

        $programTitle = $this->registration->program?->title ?? 'a program';
        $brandName = \App\Support\Brand::name();
        $detailUrl = route('patient.programRegistrations.show', $this->registration);

        if (strcasecmp(trim($this->statusLabel), 'Received') === 0) {
            if ($this->registration->isMomentsThatMatterApplication()) {
                return $this
                    ->subject('Moments That Matter Package Request Received')
                    ->view('emails.programs.moments_that_matter_registration_received')
                    ->with([
                        'firstName' => $firstName,
                        'brandName' => $brandName,
                        'detailUrl' => $detailUrl,
                    ]);
            }

            return $this
                ->subject('Financial Assistance Program Application Received')
                ->view('emails.programs.registration_received')
                ->with([
                    'firstName' => $firstName,
                    'brandName' => $brandName,
                    'detailUrl' => $detailUrl,
                ]);
        }

        return $this
            ->subject('Program Registration '.$this->statusLabel.': '.$programTitle)
            ->view('emails.programs.registration_status')
            ->with([
                'recipientName' => $recipientName,
                'programTitle' => $programTitle,
                'statusLabel' => $this->statusLabel,
                'note' => $this->note,
                'detailUrl' => $detailUrl,
            ]);
    }
}
