<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Live SMTP verification (opt-in).
 *
 * Default PHPUnit config uses MAIL_MAILER=array; this test overrides config to smtp
 * when MAIL_SMTP_TEST is enabled so your real .env MAIL_* values are exercised.
 *
 * Usage:
 *   Set in .env: MAIL_SMTP_TEST=true
 *   Optional: MAIL_SMTP_TEST_RECIPIENT=you@example.com (defaults to MAIL_FROM_ADDRESS)
 *   Run: php artisan test --group=smtp
 */
#[Group('smtp')]
class SmtpMailTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! $this->smtpTestEnabled()) {
            return;
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp', array_merge(
            config('mail.mailers.smtp', []),
            [
                'transport' => 'smtp',
                'host' => env('MAIL_HOST', '127.0.0.1'),
                'port' => (int) env('MAIL_PORT', 587),
                'encryption' => env('MAIL_ENCRYPTION', 'tls'),
                'username' => env('MAIL_USERNAME'),
                'password' => env('MAIL_PASSWORD'),
                'timeout' => null,
                'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
                'verify_peer' => filter_var(env('MAIL_VERIFY_PEER', true), FILTER_VALIDATE_BOOLEAN),
            ]
        ));
        Config::set('mail.from.address', env('MAIL_FROM_ADDRESS', 'hello@example.com'));
        Config::set('mail.from.name', env('MAIL_FROM_NAME', 'Example'));
    }

    public function test_live_smtp_can_send_raw_message(): void
    {
        if (! $this->smtpTestEnabled()) {
            $this->markTestSkipped(
                'Enable with MAIL_SMTP_TEST=true in .env and valid MAIL_HOST / MAIL_* SMTP settings. Run: php artisan test --group=smtp'
            );
        }

        $recipient = env('MAIL_SMTP_TEST_RECIPIENT') ?: env('MAIL_FROM_ADDRESS');
        $this->assertIsString($recipient);
        $this->assertNotSame('', trim($recipient), 'Set MAIL_SMTP_TEST_RECIPIENT or MAIL_FROM_ADDRESS for the test recipient.');

        Mail::raw(
            'SMTP connectivity test from PHPUnit ('.config('app.name').'). If you received this, outbound SMTP works.',
            function ($message) use ($recipient): void {
                $message->to($recipient)->subject('SMTP PHPUnit test — '.config('app.name'));
            }
        );

        $this->assertTrue(true);
    }

    private function smtpTestEnabled(): bool
    {
        return filter_var(env('MAIL_SMTP_TEST', false), FILTER_VALIDATE_BOOLEAN);
    }
}
