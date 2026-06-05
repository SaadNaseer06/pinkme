<?php

namespace App\Mail;

use App\Models\ProgramRegistration;
use App\Models\RegistrationInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class BudgetAllocatedToPatient extends Mailable
{
    use Queueable, SerializesModels;

    public ProgramRegistration $registration;
    public RegistrationInvoice $invoice;

    /** @var string|null Path relative to storage disk */
    public ?string $pdfPath;

    /** @var string Disk name used when the PDF was stored */
    public string $pdfDisk;

    public function __construct(
        ProgramRegistration $registration,
        RegistrationInvoice $invoice,
        ?string $pdfPath = null,
        ?string $pdfDisk = null,
    ) {
        $this->registration = $registration;
        $this->invoice = $invoice;
        $this->pdfPath = $pdfPath;
        $this->pdfDisk = $pdfDisk ?? (string) config('filesystems.default', 'local');
    }

    public function build()
    {
        $firstName = $this->registration->first_name
            ?? (optional($this->registration->user?->profile)->first_name)
            ?? explode(' ', trim((string) (optional($this->registration->user?->profile)->full_name ?? $this->registration->full_name)))[0]
            ?? 'there';

        $brandName = \App\Support\Brand::name();

        /* Patients receive confirmation only — no invoice or payment receipt attachments (proof stays with staff). */
        return $this
            ->subject('Your bill has been paid - '.$brandName.' financial assistance program.')
            ->view('emails.finance.budget_allocated_to_patient')
            ->with([
                'firstName' => $firstName,
                'brandName' => $brandName,
                'programTitle' => $this->registration->program?->title ?? 'financial assistance program',
                'invoice' => $this->invoice,
                'registration' => $this->registration,
                'portalUrl' => route('patient.dashboard'),
            ]);
    }
}
