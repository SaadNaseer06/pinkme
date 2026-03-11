<?php

namespace App\Mail;

use App\Models\ProgramRegistration;
use App\Models\RegistrationInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BudgetAllocatedToAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public ProgramRegistration $registration;
    public RegistrationInvoice $invoice;

    /** @var string|null Raw PDF content for attachment */
    public ?string $pdfContent;

    public function __construct(ProgramRegistration $registration, RegistrationInvoice $invoice, ?string $pdfContent = null)
    {
        $this->registration = $registration;
        $this->invoice = $invoice;
        $this->pdfContent = $pdfContent;
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

        if ($this->pdfContent) {
            $mailable->attachData($this->pdfContent, 'Invoice-' . $this->invoice->invoice_number . '.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return $mailable;
    }
}
