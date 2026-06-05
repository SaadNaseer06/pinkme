@php
    use App\Support\MomentsThatMatterOptions;
    $isMtm = $registration->program?->isMomentsThatMatter()
        || filled($registration->mtm_package);
@endphp

@if ($isMtm)
    <div class="registration-detail-section bg-white rounded-lg p-5 md:p-6 space-y-4 border border-[#E6D8E1] w-full min-w-0">
        <h3 class="text-xl font-semibold text-[#213430] app-main">Moments That Matter — application details</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-base min-w-0">
            <div class="min-w-0">
                <p class="text-sm text-[#91848C] app-text">Package selected</p>
                <p class="font-medium text-[#213430] app-text break-words">{{ MomentsThatMatterOptions::PACKAGES[$registration->mtm_package] ?? $registration->mtm_package ?? '—' }}</p>
            </div>
            <div class="min-w-0">
                <p class="text-sm text-[#91848C] app-text">Applying for</p>
                <p class="font-medium text-[#213430] app-text break-words">{{ MomentsThatMatterOptions::APPLYING_FOR[$registration->applying_for] ?? $registration->applying_for ?? '—' }}</p>
            </div>
            @if ($registration->patient_loved_one_name)
                <div class="md:col-span-2 min-w-0">
                    <p class="text-sm text-[#91848C] app-text">Patient / loved one name</p>
                    <p class="font-medium text-[#213430] app-text break-words">{{ $registration->patient_loved_one_name }}</p>
                </div>
            @endif
            <div class="min-w-0">
                <p class="text-sm text-[#91848C] app-text">Treatment status</p>
                <p class="font-medium text-[#213430] app-text break-words">
                    @if ($registration->mtm_treatment_status === 'other')
                        {{ $registration->mtm_treatment_status_other ?: 'Other' }}
                    @else
                        {{ MomentsThatMatterOptions::TREATMENT_STATUS[$registration->mtm_treatment_status] ?? $registration->mtm_treatment_status ?? '—' }}
                    @endif
                </p>
            </div>
            <div class="min-w-0">
                <p class="text-sm text-[#91848C] app-text">Story permission</p>
                <p class="font-medium text-[#213430] app-text break-words">{{ MomentsThatMatterOptions::STORY_PERMISSION[$registration->mtm_story_permission] ?? $registration->mtm_story_permission ?? '—' }}</p>
            </div>
            @if ($registration->mtm_diagnosis_type)
                <div class="min-w-0">
                    <p class="text-sm text-[#91848C] app-text">Diagnosis type</p>
                    <p class="font-medium text-[#213430] app-text break-words">{{ $registration->mtm_diagnosis_type }}</p>
                </div>
            @endif
            @if ($registration->mtm_diagnosis_date)
                <div class="min-w-0">
                    <p class="text-sm text-[#91848C] app-text">Date of diagnosis</p>
                    <p class="font-medium text-[#213430] app-text">{{ $registration->mtm_diagnosis_date->format('M j, Y') }}</p>
                </div>
            @endif
        </div>

        <div class="min-w-0">
            <p class="text-sm text-[#91848C] app-text mb-1">Shipping address</p>
            <p class="text-base text-[#213430] app-text break-words leading-relaxed">
                {{ $registration->street_address }}@if($registration->apartment_suite), {{ $registration->apartment_suite }}@endif<br>
                {{ $registration->city }}, {{ $registration->state }} {{ $registration->postal_code }}<br>
                {{ $registration->shipping_usa ? 'United States' : 'Non-U.S. (not eligible)' }}
            </p>
        </div>

        @if ($registration->story)
            <div class="min-w-0">
                <p class="text-sm text-[#91848C] app-text mb-1">Your story</p>
                <div class="long-text-scroll rounded border border-[#E6D8E1] bg-[#FDF7FB] px-3 py-2">
                    <p class="text-base text-[#6C5F67] app-text whitespace-pre-line leading-relaxed break-words">{{ $registration->story }}</p>
                </div>
            </div>
        @endif

        <div class="min-w-0">
            <p class="text-base text-[#213430] app-text"><span class="font-medium">Signature:</span></p>
            @if ($registration->signature)
                <div class="mt-2">
                    <img src="{{ storage_url($registration->signature) }}" alt="Signature" class="h-24 max-w-full object-contain">
                </div>
            @else
                <p class="text-[#6C5F67] app-text">N/A</p>
            @endif
            @if ($registration->signature_date)
                <p class="text-base text-[#213430] app-text mt-2"><span class="font-medium">Signature date:</span> {{ $registration->signature_date->format('M j, Y') }}</p>
            @endif
        </div>
    </div>
@endif
