<?php

namespace App\Mail;

use App\Models\ProgramRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReadyForFinance extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ProgramRegistration $registration,
        public string $applicationReference,
        public ?float $approvedAmount
    ) {
        $this->registration->loadMissing('program', 'assignedCaseManager.profile');
    }

    public function build()
    {
        $patientName = $this->registration->full_name ?: 'Applicant';
        $programTitle = $this->registration->program?->title ?? 'Program';
        $caseManagerName = $this->registration->assignedCaseManager?->profile?->full_name ?? 'Case manager';

        return $this
            ->subject('Payment Ready for Processing — '.$patientName)
            ->view('emails.finance.payment_ready_for_processing')
            ->with([
                'patientName' => $patientName,
                'applicationReference' => $this->applicationReference,
                'approvedAmount' => $this->approvedAmount,
                'programTitle' => $programTitle,
                'caseManagerName' => $caseManagerName,
                'dashboardUrl' => route('finance.registrations.show', $this->registration),
            ]);
    }
}
