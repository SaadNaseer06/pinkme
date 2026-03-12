<?php

namespace App\Mail;

use App\Models\ProgramRegistration;
use App\Models\RegistrationInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class BudgetAllocatedToPatient extends Mailable
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

        if ($this->pdfPath && Storage::exists($this->pdfPath)) {
            $mailable->attachData(Storage::get($this->pdfPath), 'Invoice-' . $this->invoice->invoice_number . '.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return $mailable;
    }
}
