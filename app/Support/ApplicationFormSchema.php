<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

final class ApplicationFormSchema
{
    /** Columns on program_registrations that dynamic fields may map into. */
    public const MAPPABLE_COLUMNS = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'dob',
        'gender',
        'street_address',
        'apartment_suite',
        'city',
        'state',
        'postal_code',
        'medical_condition',
        'story',
        'justification',
        'treatment_facility_name',
    ];

    /**
     * @param  list<array<string, mixed>>  $rawFields
     * @return list<array<string, mixed>>
     */
    public static function normalize(array $rawFields): array
    {
        $allowedTypes = array_keys(ApplicationFormFieldTypes::options());
        $seenNames = [];

        return collect($rawFields)
            ->values()
            ->map(function ($field, $index) use ($allowedTypes, &$seenNames) {
                if (! is_array($field)) {
                    return null;
                }

                $type = $field['type'] ?? ApplicationFormFieldTypes::SHORT_TEXT;
                if (! in_array($type, $allowedTypes, true)) {
                    $type = ApplicationFormFieldTypes::SHORT_TEXT;
                }

                $label = trim((string) ($field['label'] ?? ''));
                $name = self::slugifyName($field['name'] ?? $label);
                if ($name === '') {
                    $name = 'field_'.($index + 1);
                }

                $base = $name;
                $suffix = 2;
                while (in_array($name, $seenNames, true)) {
                    $name = $base.'_'.$suffix;
                    $suffix++;
                }
                $seenNames[] = $name;

                $mapsTo = $field['maps_to_column'] ?? null;
                if ($mapsTo && ! in_array($mapsTo, self::MAPPABLE_COLUMNS, true)) {
                    $mapsTo = null;
                }

                $conditional = null;
                if (! empty($field['conditional_field']) && ($field['conditional_value'] ?? '') !== '') {
                    $conditional = [
                        'field' => self::slugifyName($field['conditional_field']),
                        'value' => trim((string) $field['conditional_value']),
                    ];
                }

                return [
                    'id' => $field['id'] ?? 'af_'.Str::random(8),
                    'name' => $name,
                    'label' => $label !== '' ? $label : self::defaultLabel($name),
                    'type' => $type,
                    'section' => trim((string) ($field['section'] ?? '')),
                    'required' => (bool) ($field['required'] ?? false),
                    'help_text' => trim((string) ($field['help_text'] ?? '')),
                    'options' => self::normalizeOptions($field['options'] ?? []),
                    'maps_to_column' => $mapsTo,
                    'conditional' => $conditional,
                    'sort_order' => (int) ($field['sort_order'] ?? $index),
                ];
            })
            ->filter()
            ->sortBy('sort_order')
            ->values()
            ->all();
    }

    /**
     * @param  list<array<string, mixed>>  $schema
     * @return array<string, mixed>
     */
    public static function buildValidationRules(array $schema, Request $request): array
    {
        $rules = [
            'program_id' => ['required', 'exists:programs,id'],
            'app_field' => ['nullable', 'array'],
        ];

        $values = $request->input('app_field', []);
        if (! is_array($values)) {
            $values = [];
        }

        foreach ($schema as $field) {
            $type = $field['type'] ?? '';
            if (ApplicationFormFieldTypes::isDisplayOnly($type)) {
                continue;
            }

            $name = $field['name'] ?? '';
            if ($name === '') {
                continue;
            }

            if (! self::isFieldApplicable($field, $values)) {
                continue;
            }

            $key = 'app_field.'.$name;
            $fieldRules = [];

            if (! empty($field['required'])) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            switch ($type) {
                case ApplicationFormFieldTypes::EMAIL:
                    $fieldRules[] = 'email';
                    $fieldRules[] = 'max:255';
                    break;
                case ApplicationFormFieldTypes::PHONE:
                case ApplicationFormFieldTypes::SHORT_TEXT:
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:500';
                    break;
                case ApplicationFormFieldTypes::LONG_TEXT:
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:5000';
                    break;
                case ApplicationFormFieldTypes::NUMBER:
                    $fieldRules[] = 'numeric';
                    break;
                case ApplicationFormFieldTypes::DATE:
                    $fieldRules[] = 'date';
                    break;
                case ApplicationFormFieldTypes::SELECT:
                case ApplicationFormFieldTypes::RADIO:
                    $options = collect($field['options'] ?? [])->pluck('value')->filter()->all();
                    if ($options !== []) {
                        $fieldRules[] = Rule::in($options);
                    }
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:255';
                    break;
                case ApplicationFormFieldTypes::CHECKBOX:
                    $fieldRules[] = 'in:1,0,true,false,on,yes';
                    break;
                case ApplicationFormFieldTypes::CHECKBOX_GROUP:
                    $key = 'app_field.'.$name;
                    $rules[$key] = array_merge(
                        ! empty($field['required']) ? ['required', 'array', 'min:1'] : ['nullable', 'array'],
                        []
                    );
                    $optionValues = collect($field['options'] ?? [])->pluck('value')->filter()->all();
                    if ($optionValues !== []) {
                        $rules[$key.'.*'] = ['string', Rule::in($optionValues)];
                    }
                    continue 2;
                case ApplicationFormFieldTypes::FILE:
                    $rules[$key] = array_merge(
                        ! empty($field['required']) ? ['required'] : ['nullable'],
                        ['file', 'mimes:pdf,jpg,jpeg,png', 'max:25600']
                    );
                    continue 2;
                case ApplicationFormFieldTypes::SIGNATURE:
                    $fieldRules[] = 'string';
                    break;
                default:
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:1000';
                    break;
            }

            $rules[$key] = $fieldRules;
        }

        return $rules;
    }

    /**
     * @param  list<array<string, mixed>>  $schema
     * @return array<string, mixed>
     */
    public static function extractMappedAttributes(array $schema, array $responses): array
    {
        $attributes = [];

        foreach ($schema as $field) {
            $mapsTo = $field['maps_to_column'] ?? null;
            $name = $field['name'] ?? '';
            if (! $mapsTo || $name === '' || ! array_key_exists($name, $responses)) {
                continue;
            }

            $value = $responses[$name];
            if (is_array($value)) {
                $value = implode(', ', array_filter($value));
            }

            $attributes[$mapsTo] = $value;
        }

        return $attributes;
    }

    /**
     * @param  list<array<string, mixed>>  $schema
     */
    public static function isFieldApplicable(array $field, array $values): bool
    {
        $conditional = $field['conditional'] ?? null;
        if (! is_array($conditional) || empty($conditional['field'])) {
            return true;
        }

        $parentValue = $values[$conditional['field']] ?? null;
        if (is_array($parentValue)) {
            $parentValue = implode(',', $parentValue);
        }

        return strtolower(trim((string) $parentValue)) === strtolower(trim((string) ($conditional['value'] ?? '')));
    }

    /**
     * @param  mixed  $raw
     * @return list<array{value: string, label: string}>
     */
    public static function normalizeOptions($raw): array
    {
        if (is_string($raw)) {
            $raw = preg_split('/\r\n|\r|\n/', $raw) ?: [];
        }

        if (! is_array($raw)) {
            return [];
        }

        $options = [];
        foreach ($raw as $item) {
            if (is_array($item)) {
                $value = trim((string) ($item['value'] ?? $item['label'] ?? ''));
                $label = trim((string) ($item['label'] ?? $value));
            } else {
                $value = trim((string) $item);
                $label = $value;
            }

            if ($value === '') {
                continue;
            }

            $options[] = ['value' => $value, 'label' => $label !== '' ? $label : $value];
        }

        return $options;
    }

    public static function slugifyName(?string $value): string
    {
        $slug = Str::slug((string) $value, '_');

        return preg_replace('/[^a-z0-9_]/', '', strtolower($slug)) ?? '';
    }

    private static function defaultLabel(string $name): string
    {
        return Str::title(str_replace('_', ' ', $name));
    }
}
