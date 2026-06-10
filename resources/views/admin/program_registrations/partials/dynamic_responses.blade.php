@php
    $schema = $registration->program?->application_form_schema ?? [];
    $responses = $registration->application_responses ?? [];
    $currentSection = null;
@endphp

@if (!empty($schema) && !empty($responses))
    <div class="registration-detail-section bg-white rounded-lg p-5 md:p-6 space-y-4 border border-[#E6D8E1] w-full min-w-0">
        <h3 class="text-xl font-semibold text-[#213430] app-main">Application Responses</h3>

        @foreach ($schema as $field)
            @php
                $type = $field['type'] ?? '';
                $name = $field['name'] ?? '';
                $value = $responses[$name] ?? null;
                $section = $field['section'] ?? '';
            @endphp

            @if ($type === 'section_header')
                <h4 class="text-lg font-semibold text-[#213430] pt-2 border-t border-[#E6D8E1]">{{ $field['label'] ?? 'Section' }}</h4>
                @continue
            @endif

            @if ($type === 'guidelines')
                <p class="text-sm text-[#6C5B68] whitespace-pre-line rounded border border-[#E6D8E1] bg-[#FDF7FB] px-3 py-2">{{ $field['label'] ?? '' }}</p>
                @continue
            @endif

            @if ($section && $section !== $currentSection)
                @php $currentSection = $section; @endphp
                <h4 class="text-base font-semibold text-[#213430] mt-2">{{ $currentSection }}</h4>
            @endif

            @if ($value === null || $value === '' || (is_array($value) && count($value) === 0))
                @continue
            @endif

            <div class="text-base text-[#213430] app-text break-words">
                <span class="font-medium">{{ $field['label'] ?? $name }}:</span>

                @if ($type === 'signature' && is_string($value))
                    <div class="mt-2">
                        <img src="{{ storage_url($value) }}" alt="Signature" class="h-24 max-w-full object-contain border border-[#E6D8E1] rounded bg-white p-1">
                    </div>
                @elseif ($type === 'file' && is_string($value))
                    <a href="{{ storage_url($value) }}" target="_blank" class="text-[#9E2469] hover:underline ml-1">View uploaded file</a>
                @elseif (is_array($value))
                    <span class="ml-1">{{ implode(', ', $value) }}</span>
                @else
                    <span class="ml-1 whitespace-pre-line">{{ $value }}</span>
                @endif
            </div>
        @endforeach
    </div>
@endif
