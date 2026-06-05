<?php

namespace App\Mail;

use App\Models\ProgramRegistration;
use App\Models\RegistrationInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentProofUploadedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  'patient'|'admin'|'case_manager'  $audience
     */
    public function __construct(
        public ProgramRegistration $registration,
        public RegistrationInvoice $invoice,
        public string $audience = 'patient'
    ) {
        $this->registration->loadMissing('program');
    }

    public function build()
    {
        $patientName = $this->registration->full_name ?: 'Applicant';
        $amount = number_format((float) $this->invoice->amount, 2);
        $programTitle = $this->registration->program?->title ?? 'Program';

        $lines = match ($this->audience) {
            'patient' => [
                'heading' => 'Payment confirmation received',
                'lead' => 'We have received proof of payment for your application for '.$programTitle.'.',
            ],
            'admin' => [
                'heading' => 'Payment proof on file',
                'lead' => 'Finance uploaded payment proof for '.$patientName.' ('.$programTitle.').',
            ],
            default => [
                'heading' => 'Payment proof uploaded — case update',
                'lead' => 'Finance has uploaded payment proof for '.$patientName.' — '.$programTitle.'. You can close the loop with the patient as needed.',
            ],
        };

        return $this
            ->subject($lines['heading'].' — '.$this->invoice->invoice_number)
            ->view('emails.finance.payment_proof_uploaded')
            ->with([
                'lines' => $lines,
                'patientName' => $patientName,
                'invoiceNumber' => $this->invoice->invoice_number,
                'amount' => $amount,
                'programTitle' => $programTitle,
                'paymentMethod' => $this->invoice->payment_method,
                'audience' => $this->audience,
            ]);
    }
}
