<?php

namespace App\Support;

use Illuminate\Support\Str;

class EventHighlightFormatter
{
    /**
     * Convert the raw event highlights content into sanitized HTML
     * with consistent list styling.
     */
    public static function format(?string $content): ?string
    {
        if (! $content || trim(strip_tags($content)) === '') {
            return null;
        }

        $allowedTags = '<p><br><strong><em><u><ol><ul><li><a><span><div><blockquote>';
        $sanitized = strip_tags($content, $allowedTags);

        $formatted = Str::contains($sanitized, ['<ul', '<ol', '<li'])
            ? $sanitized
            : self::convertTextToList($content, $sanitized);

        return self::applyListStyling($formatted);
    }

    /**
     * Build a list from raw text where applicable.
     */
    protected static function convertTextToList(string $rawContent, string $fallback): string
    {
        $lines = preg_split('/\r\n|\n|\r/', $rawContent);

        $items = collect($lines)
            ->map(function ($line) {
                $clean = trim((string) $line);
                $clean = preg_replace('/^[\s\p{Pd}\*\x{2022}\x{2023}\-\+]+/u', '', $clean) ?? '';
                return trim($clean);
            })
            ->filter()
            ->values();

        if ($items->isEmpty()) {
            return $fallback;
        }

        $listItems = $items->map(fn ($item) => '<li>' . e($item) . '</li>')->implode('');

        return '<ul>' . $listItems . '</ul>';
    }

    /**
     * Ensure unordered and ordered lists carry consistent utility classes.
     */
    protected static function applyListStyling(string $html): string
    {
        $applyClasses = static function (string $html, string $tag, string $classes) {
            return preg_replace_callback(
                sprintf('/<%s\b([^>]*)>/i', $tag),
                function ($matches) use ($tag, $classes) {
                    $attributes = $matches[1] ?? '';

                    if (preg_match('/\bclass=["\']([^"\']*)["\']/', $attributes, $classMatch)) {
                        $existingClasses = $classMatch[1];
                        $newClasses = trim($existingClasses . ' ' . $classes);
                        $attributes = str_replace(
                            $classMatch[0],
                            'class="' . $newClasses . '"',
                            $attributes
                        );
                    } else {
                        $attributes .= ' class="' . $classes . '"';
                    }

                    return '<' . $tag . $attributes . '>';
                },
                $html
            );
        };

        $html = $applyClasses($html, 'ul', 'list-disc pl-6 space-y-2');
        $html = $applyClasses($html, 'ol', 'list-decimal pl-6 space-y-2');

        return preg_replace_callback(
            '/<li\b([^>]*)>/i',
            static function ($matches) {
                $attributes = $matches[1] ?? '';

                if (preg_match('/\bclass=["\']([^"\']*)["\']/', $attributes, $classMatch)) {
                    $existingClasses = $classMatch[1];
                    $newClasses = trim($existingClasses . ' mb-2');
                    $attributes = str_replace(
                        $classMatch[0],
                        'class="' . $newClasses . '"',
                        $attributes
                    );
                } else {
                    $attributes .= ' class="mb-2"';
                }

                return '<li' . $attributes . '>';
            },
            $html
        ) ?? $html;
    }
}
