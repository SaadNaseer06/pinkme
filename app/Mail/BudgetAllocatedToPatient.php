<?php

namespace App\Mail;

use App\Models\ProgramRegistration;
use App\Models\RegistrationInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

        if ($this->pdfPath && Storage::disk($this->pdfDisk)->exists($this->pdfPath)) {
            $mailable->attachFromStorageDisk(
                $this->pdfDisk,
                $this->pdfPath,
                'Invoice-' . $this->invoice->invoice_number . '.pdf',
                ['mime' => 'application/pdf'],
            );
        } elseif ($this->pdfPath) {
            Log::warning('Budget invoice PDF missing for patient email attachment', [
                'path' => $this->pdfPath,
                'disk' => $this->pdfDisk,
                'invoice_id' => $this->invoice->id ?? null,
            ]);
        }

        return $mailable;
    }
}
