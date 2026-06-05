<?php

namespace App\Support;

use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class TransactionalMail
{
    /**
     * Send transactional mail immediately by default so delivery does not depend on a queue worker.
     *
     * @param  string|array<int, string>  $recipients
     */
    public static function send(string|array $recipients, Mailable $mailable): void
    {
        if (! filled($recipients)) {
            return;
        }

        try {
            $pending = Mail::to($recipients);
            if (config('mail.send_transactional_sync', true)) {
                $pending->send($mailable);
            } else {
                $pending->queue($mailable);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
