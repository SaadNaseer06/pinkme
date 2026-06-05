<?php

namespace App\Support;

class MomentsThatMatterOptions
{
    public const PACKAGES = [
        'ring_the_bell' => 'Ring the Bell',
        'strength_in_the_storm' => 'Strength in the Storm',
        'forever_in_bloom' => 'Forever in Bloom',
    ];

    public const PACKAGE_DESCRIPTIONS = [
        'ring_the_bell' => 'For fighters who have completed chemotherapy or radiation treatment.',
        'strength_in_the_storm' => 'For individuals currently going through treatment and needing encouragement and support.',
        'forever_in_bloom' => 'For honoring and remembering a loved one who has passed due to breast cancer.',
    ];

    public const APPLYING_FOR = [
        'self' => 'Yourself',
        'family_member' => 'A Family Member',
        'friend_loved_one' => 'A Friend / Loved One',
    ];

    public const TREATMENT_STATUS = [
        'in_treatment' => 'Currently in Treatment',
        'recently_completed' => 'Recently Completed Treatment',
        'survivor' => 'Survivor',
        'honoring_loved_one' => 'Honoring a Loved One',
        'other' => 'Other',
    ];

    public const STORY_PERMISSION = [
        'full' => 'Yes, full permission granted',
        'anonymous' => 'Yes, but anonymously',
        'private' => 'No, please keep my information private',
    ];

    public const REQUIRED_ACKNOWLEDGMENTS = [
        'no_guarantee',
        'limited_availability',
        'accurate_information',
        'usa_shipping_only',
        'one_per_year',
    ];

    public const ACKNOWLEDGMENT_LABELS = [
        'no_guarantee' => 'I understand that submission does not guarantee package approval.',
        'limited_availability' => 'I understand that packages are limited and distributed based on availability and review.',
        'accurate_information' => 'I confirm the information provided is accurate to the best of my knowledge.',
        'usa_shipping_only' => 'I understand that PINK “ME”® currently ships within the United States only.',
        'one_per_year' => 'I understand that only one package per individual may be provided annually.',
    ];
}
