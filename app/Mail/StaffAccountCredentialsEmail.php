<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Email sent when an admin creates a case manager or finance user with an initial password.
 */
class StaffAccountCredentialsEmail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public function __construct(
        User $user,
        public string $plainPassword,
        /** e.g. "Case Manager" or "Finance" */
        public string $roleLabel,
    ) {
        $this->user = $user->loadMissing('profile', 'role');
    }

    public function build()
    {
        $recipientName = optional($this->user->profile)->full_name
            ?? $this->user->email
            ?? 'there';

        $appName = config('app.name', 'Pink Me');
        // Always staff portal — do not use register/patient login here.
        $staffLoginUrl = route('login.staff');
        if ($this->user->role?->name === 'finance') {
            $staffLoginUrl .= str_contains($staffLoginUrl, '?') ? '&finance=1' : '?finance=1';
        }

        return $this
            ->subject('Your ' . $appName . ' ' . $this->roleLabel . ' account')
            ->view('emails.staff_account_credentials')
            ->with([
                'recipientName' => $recipientName,
                'email' => $this->user->email,
                'password' => $this->plainPassword,
                'roleLabel' => $this->roleLabel,
                'loginUrl' => $staffLoginUrl,
                'appName' => $appName,
            ]);
    }
}
