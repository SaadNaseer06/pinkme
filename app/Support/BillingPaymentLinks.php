<?php

namespace App\Support;

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
}
