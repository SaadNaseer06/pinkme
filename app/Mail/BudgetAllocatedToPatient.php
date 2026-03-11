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
        $recipientName = optional($this->registration->user?->profile)->full_name
            ?? $this->registration->full_name
            ?? $this->registration->user?->email
            ?? $this->registration->email
            ?? 'there';

        $programTitle = $this->registration->program?->title ?? 'a program';

        $mailable = $this
            ->subject('Budget Allocated: ' . $programTitle . ' – Invoice #' . $this->invoice->invoice_number)
            ->view('emails.finance.budget_allocated_to_patient')
            ->with([
                'recipientName' => $recipientName,
                'programTitle' => $programTitle,
                'invoice' => $this->invoice,
                'registration' => $this->registration,
            ]);

        if ($this->pdfContent) {
            $mailable->attachData($this->pdfContent, 'Invoice-' . $this->invoice->invoice_number . '.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return $mailable;
    }
}
