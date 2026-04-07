<?php

namespace App\Mail;

use App\Models\ProgramRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProgramRegistrationAdminNotice extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $emailSubject,
        public ProgramRegistration $registration,
        public string $bodyLine,
        public ?string $detailUrl = null
    ) {}

    public function build()
    {
        $programTitle = $this->registration->program?->title ?? 'Program';
        $applicant = $this->registration->full_name ?? 'Applicant';
        $url = $this->detailUrl ?? route('admin.program_registrations.show', $this->registration);
        $linkLabel = match (true) {
            str_contains($url, 'case_manager') => 'Open in case manager portal',
            str_contains($url, 'patient') => 'View your application',
            default => 'View application in admin',
        };

        return $this
            ->subject($this->emailSubject)
            ->view('emails.programs.admin_notice')
            ->with([
                'bodyLine' => $this->bodyLine,
                'programTitle' => $programTitle,
                'applicant' => $applicant,
                'detailUrl' => $url,
                'linkLabel' => $linkLabel,
            ]);
    }
}
