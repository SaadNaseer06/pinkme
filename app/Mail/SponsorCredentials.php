<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SponsorCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public User $sponsor;
    public string $plainPassword;
    public string $loginUrl;

    public function __construct(User $sponsor, string $plainPassword, string $loginUrl)
    {
        $this->sponsor = $sponsor;
        $this->plainPassword = $plainPassword;
        $this->loginUrl = $loginUrl;
    }

    public function build()
    {
        $recipientName = optional($this->sponsor->profile)->full_name
            ?: $this->sponsor->email
            ?: 'there';

        return $this
            ->subject('Your Sponsor Account Credentials')
            ->view('emails.sponsors.credentials')
            ->with([
                'recipientName' => $recipientName,
                'email' => $this->sponsor->email,
                'password' => $this->plainPassword,
                'loginUrl' => $this->loginUrl,
            ]);
    }
}
