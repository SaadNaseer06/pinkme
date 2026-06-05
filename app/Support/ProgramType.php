<?php

namespace App\Support;

class ProgramType
{
    public const FINANCIAL_ASSISTANCE = 'financial_assistance';

    public const MOMENTS_THAT_MATTER = 'moments_that_matter';

    public const LABELS = [
        self::FINANCIAL_ASSISTANCE => 'Financial Assistance',
        self::MOMENTS_THAT_MATTER => 'Moments That Matter',
    ];

    public static function options(): array
    {
        return self::LABELS;
    }

    public static function label(?string $type): string
    {
        return self::LABELS[$type ?? ''] ?? 'Program';
    }

    public static function isMomentsThatMatter(?string $type): bool
    {
        return $type === self::MOMENTS_THAT_MATTER;
    }
}
