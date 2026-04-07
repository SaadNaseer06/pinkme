<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PatientWelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user->loadMissing('profile');
    }

    public function build()
    {
        $recipientName = optional($this->user->profile)->full_name
            ?? $this->user->email
            ?? 'there';

        $appName = config('app.name', 'Pink Me');

        return $this
            ->subject('Welcome to ' . $appName)
            ->view('emails.patient_welcome')
            ->with([
                'recipientName' => $recipientName,
                'appName' => $appName,
                'loginUrl' => route('register', ['tab' => 'login']),
            ]);
    }
}
