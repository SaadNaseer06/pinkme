<?php

namespace App\Support;

final class ApplicationFormFieldTypes
{
    public const SECTION_HEADER = 'section_header';

    public const GUIDELINES = 'guidelines';

    public const SHORT_TEXT = 'short_text';

    public const LONG_TEXT = 'long_text';

    public const NUMBER = 'number';

    public const EMAIL = 'email';

    public const PHONE = 'phone';

    public const DATE = 'date';

    public const SELECT = 'select';

    public const RADIO = 'radio';

    public const CHECKBOX = 'checkbox';

    public const CHECKBOX_GROUP = 'checkbox_group';

    public const FILE = 'file';

    public const SIGNATURE = 'signature';

    public const LABELS = [
        self::SECTION_HEADER => 'Section header',
        self::GUIDELINES => 'Guidelines / instructions',
        self::SHORT_TEXT => 'Short text',
        self::LONG_TEXT => 'Long text',
        self::NUMBER => 'Number',
        self::EMAIL => 'Email',
        self::PHONE => 'Phone',
        self::DATE => 'Date',
        self::SELECT => 'Dropdown',
        self::RADIO => 'Radio buttons',
        self::CHECKBOX => 'Single checkbox',
        self::CHECKBOX_GROUP => 'Checkbox group',
        self::FILE => 'File upload',
        self::SIGNATURE => 'Signature',
    ];

    /** Types that collect patient input (not display-only). */
    public const INPUT_TYPES = [
        self::SHORT_TEXT,
        self::LONG_TEXT,
        self::NUMBER,
        self::EMAIL,
        self::PHONE,
        self::DATE,
        self::SELECT,
        self::RADIO,
        self::CHECKBOX,
        self::CHECKBOX_GROUP,
        self::FILE,
        self::SIGNATURE,
    ];

    public static function options(): array
    {
        return self::LABELS;
    }

    public static function isInputType(?string $type): bool
    {
        return in_array($type, self::INPUT_TYPES, true);
    }

    public static function isDisplayOnly(?string $type): bool
    {
        return in_array($type, [self::SECTION_HEADER, self::GUIDELINES], true);
    }
}
