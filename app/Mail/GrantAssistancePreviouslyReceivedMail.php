<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Sent when the applicant indicates they have received financial assistance from the organization before (one-time grant policy).
 */
class GrantAssistancePreviouslyReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $firstName)
    {
    }

    public function build()
    {
        $name = trim($this->firstName) !== '' ? trim($this->firstName) : 'there';

        return $this
            ->subject('Thank you for your submission - Grants are provided on one-time basis')
            ->view('emails.patient.grant_one_time_only_rejection')
            ->with([
                'firstName' => $name,
            ]);
    }
}
