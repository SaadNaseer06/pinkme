<?php

namespace App\Support;

class Brand
{
    public static function name(): string
    {
        $plain = self::namePlain();

        return $plain."\u{00AE}";
    }

    public static function namePlain(): string
    {
        $candidates = [
            (string) config('app.brand_name', ''),
            (string) config('app.name', ''),
            'PINK "ME"',
        ];

        foreach ($candidates as $candidate) {
            $cleaned = self::stripCorruptMarks($candidate);
            if ($cleaned !== '') {
                return $cleaned;
            }
        }

        return 'PINK "ME"';
    }

    public static function staffAccessNotice(): string
    {
        return (string) config('app.staff_access_notice', 'BOARD MEMBER ACCESS ONLY');
    }

    /**
     * Remove CJK / corrupted trademark substitutes and normalize to plain brand text.
     */
    private static function stripCorruptMarks(string $value): string
    {
        // Drop CJK Unified Ideographs and related blocks (e.g. 登録, 速) and existing ®.
        $cleaned = preg_replace('/[\x{00AE}\x{3000}-\x{9FFF}\x{F900}-\x{FAFF}]/u', '', $value) ?? $value;
        $cleaned = str_replace(['(R)', '(r)', 'Ⓡ', '™'], '', $cleaned);
        $cleaned = trim(preg_replace('/\s+/u', ' ', $cleaned) ?? $cleaned);

        return $cleaned;
    }
}
