<?php

namespace App\Mail;

use App\Models\ProgramRegistration;
use App\Models\RegistrationInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class BudgetAllocatedToAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public ProgramRegistration $registration;
    public RegistrationInvoice $invoice;

    /** @var string|null Storage path to PDF for attachment (used when queued) */
    public ?string $pdfPath;

    public function __construct(ProgramRegistration $registration, RegistrationInvoice $invoice, ?string $pdfPath = null)
    {
        $this->registration = $registration;
        $this->invoice = $invoice;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        $applicantName = $this->registration->full_name ?? 'N/A';
        $programTitle = $this->registration->program?->title ?? 'Program';

        $mailable = $this
            ->subject('Finance: Budget Allocated – ' . $applicantName . ' – ' . $programTitle)
            ->view('emails.finance.budget_allocated_to_admin')
            ->with([
                'applicantName' => $applicantName,
                'programTitle' => $programTitle,
                'invoice' => $this->invoice,
                'registration' => $this->registration,
                'detailUrl' => route('admin.program_registrations.show', $this->registration),
            ]);

        if ($this->pdfPath && Storage::exists($this->pdfPath)) {
            $mailable->attachData(Storage::get($this->pdfPath), 'Invoice-' . $this->invoice->invoice_number . '.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return $mailable;
    }
}
