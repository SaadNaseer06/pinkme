<?php

namespace App\Support;

class ProgramType
{
    public const FINANCIAL_ASSISTANCE = 'financial_assistance';

    public const MOMENTS_THAT_MATTER = 'moments_that_matter';

    public const MAMMOGRAM_IMAGING = 'mammogram_imaging';

    public const FOOD_ASSISTANCE = 'food_assistance';

    public const LABELS = [
        self::FINANCIAL_ASSISTANCE => 'Financial Assistance',
        self::MOMENTS_THAT_MATTER => 'Moments That Matter',
        self::MAMMOGRAM_IMAGING => 'Mammogram & Imaging Support',
        self::FOOD_ASSISTANCE => 'Food Assistance',
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

    public static function usesDynamicApplicationForm(?string $type): bool
    {
        return in_array($type, [self::MAMMOGRAM_IMAGING, self::FOOD_ASSISTANCE], true);
    }
}
