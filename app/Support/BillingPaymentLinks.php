<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

final class BillingPaymentLinks
{
    /**
     * Convert plain text (possibly multiple lines / URLs) to HTML with clickable http(s) links.
     */
    public static function toHtml(?string $text): string
    {
        if ($text === null || trim($text) === '') {
            return '';
        }

        $escaped = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $linked = preg_replace_callback(
            '#(?<![\w/])(https?://[^\s<>"\']+|www\.[^\s<>"\']+)#i',
            function (array $m): string {
                $display = $m[1];
                $href = preg_match('#^https?://#i', $display) ? $display : ('https://' . $display);
                $scheme = strtolower((string) parse_url($href, PHP_URL_SCHEME));
                if (! in_array($scheme, ['http', 'https'], true)) {
                    return htmlspecialchars($display, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                }
                if (! filter_var($href, FILTER_VALIDATE_URL)) {
                    return htmlspecialchars($display, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                }

                $safeHref = htmlspecialchars($href, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

                return '<a href="'.$safeHref.'" target="_blank" rel="noopener noreferrer" class="text-[#9E2469] underline hover:text-[#B52D75] break-all">'.$display.'</a>';
            },
            $escaped
        );

        return '<div class="billing-payment-links whitespace-normal">'.nl2br($linked, false).'</div>';
    }

    /**
     * Normalize and validate raw billing URL lines for storage in program_registrations.payment_links.
     *
     * @param  array<int, string|null>  $rawLines
     * @throws ValidationException
     */
    public static function paymentLinksColumnValue(array $rawLines): ?string
    {
        $normalized = [];
        foreach ($rawLines as $raw) {
            $u = trim((string) $raw);
            if ($u === '') {
                continue;
            }
            $href = preg_match('#^https?://#i', $u) ? $u : 'https://'.$u;
            if (! filter_var($href, FILTER_VALIDATE_URL)) {
                throw ValidationException::withMessages([
                    'billing_urls' => 'Invalid URL: '.$u,
                ]);
            }
            $scheme = strtolower((string) parse_url($href, PHP_URL_SCHEME));
            if (! in_array($scheme, ['http', 'https'], true)) {
                throw ValidationException::withMessages([
                    'billing_urls' => 'Only http and https links are allowed: '.$u,
                ]);
            }
            $normalized[] = $href;
        }

        $normalized = array_values(array_unique($normalized));
        $joined = implode("\n", $normalized);

        return $joined !== '' ? $joined : null;
    }
}
