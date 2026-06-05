<?php

namespace App\Support;

class Brand
{
    public static function name(): string
    {
        return (string) config('app.brand_name', 'PINK "ME"®');
    }

    public static function namePlain(): string
    {
        return (string) config('app.name', 'PINK "ME"');
    }

    public static function staffAccessNotice(): string
    {
        return (string) config('app.staff_access_notice', 'BOARD MEMBER ACCESS ONLY');
    }
}
