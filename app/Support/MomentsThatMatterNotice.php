<?php

namespace App\Support;

class MomentsThatMatterNotice
{
    public const TITLE = 'Moments That Matter Package request received';

    /**
     * @return list<string>
     */
    public static function paragraphs(): array
    {
        return [
            'Thank you for submitting your request for our Moments That Matter Package.',
            'We will carefully review your information as we prepare your special package with love and intention. We hope this small token brings comfort, encouragement, and reminds you that you are thought of and supported.',
            'With care,',
            Brand::name(),
        ];
    }

    public static function body(): string
    {
        return implode("\n\n", self::paragraphs());
    }

    public static function notificationMessage(): string
    {
        return 'Thank you for submitting your request for our Moments That Matter Package. We will carefully review your information as we prepare your special package with love and intention.';
    }
}
