<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestMailCommand extends Command
{
    protected $signature = 'mail:test {email? : Email address to send test to}';

    protected $description = 'Send a test email to verify SMTP configuration';

    public function handle(): int
    {
        $email = $this->argument('email') ?: config('mail.from.address');

        $this->info('Testing mail configuration...');
        $smtp = config('mail.mailers.smtp', []);
        $this->table(
            ['Setting', 'Value'],
            [
                ['MAIL_MAILER', config('mail.default')],
                ['MAIL_HOST', $smtp['host'] ?? '-'],
                ['MAIL_PORT', $smtp['port'] ?? '-'],
                ['MAIL_ENCRYPTION', $smtp['encryption'] ?? 'null'],
                ['MAIL_USERNAME', !empty($smtp['username']) ? '***' : '(empty)'],
                ['MAIL_FROM', config('mail.from.address')],
                ['QUEUE_CONNECTION', config('queue.default')],
            ]
        );

        if (config('mail.default') === 'log') {
            $this->warn('MAIL_MAILER is "log" - emails are written to storage/logs, not sent. Set MAIL_MAILER=smtp in .env');
            return 1;
        }

        if (config('queue.default') !== 'sync') {
            $this->warn('Emails use queue. Run "php artisan queue:work" on the server for queued emails to be sent.');
        }

        try {
            Mail::raw('This is a test email from ' . config('app.name') . '. If you received this, SMTP is working.', function ($message) use ($email) {
                $message->to($email)
                    ->subject('SMTP Test - ' . config('app.name'));
            });

            $this->info("Test email sent to {$email}. Check your inbox.");
            return 0;
        } catch (\Throwable $e) {
            $this->error('Mail failed: ' . $e->getMessage());
            Log::error('Mail test failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return 1;
        }
    }
}
