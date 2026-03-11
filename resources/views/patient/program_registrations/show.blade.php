@php
    use App\Models\ProgramRegistration;
    $registration->loadMissing([
        'program',
    ]);

    $status = strtolower($registration->status ?? ProgramRegistration::STATUS_PENDING);
    $badgeClass = match ($status) {
        ProgramRegistration::STATUS_APPROVED => 'bg-[#C5E8D1] text-[#20B354] border border-[#A5D0B7]',
        ProgramRegistration::STATUS_REJECTED => 'bg-[#FAD4D4] text-[#B32020] border border-[#E6A5A5]',
        default => 'bg-[#FDE8F3] text-[#9E2469] border border-[#F4BBD5]',
    };

    $program = $registration->program;

    $programLabels = [
        'breast_cancer_treatment' => 'Breast Cancer Treatment Assistance Program',
        'mastectomy_wellness' => 'Pink Mastectomy and Wellness Assistance Program',
        'pinkme_food_hunger' => 'PINK “ME” Food & Hunger Grant',
    ];

    $incomeLabels = [
        'employed' => 'Employed',
        'self_employed' => 'Self Employed',
        'disabled' => 'Disabled',
        'retired' => 'Retired',
        'student' => 'Student',
    ];

    $authorizationLabels = [
        'full_name' => 'Use my full name',
        'story_anonymous' => 'Share part of my story anonymously',
        'story_full' => 'Share my story with my name',
        'photos' => 'Use photos / media of me',
        'contact_details' => 'Contact me for follow-ups related to my story',
    ];

    $quarterLabels = [
        'option1' => 'Option 1: May through June',
        'option2' => 'Option 2: November through December',
    ];
@endphp

@extends('patient.layouts.app')

@section('title', 'Program Registration')

@section('content')
    <main class="flex-1">
        <div class="max-w-full mx-auto">
            <div class="mt-6 bg-[#F6EDF5] rounded-xl p-6 md:p-8 space-y-8 shadow-sm">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-[#DCCFD8] pb-5">
                    <div>
                        <h1 class="text-3xl font-semibold text-[#213430] app-main">Program Registration</h1>
                        <p class="text-base text-[#6C5F67] app-text mt-2">
                            Submitted on {{ optional($registration->created_at)->format('d M Y, h:i A') ?? 'N/A' }}
                        </p>
                    </div>
                    <span class="px-5 py-2 rounded-full text-base font-semibold {{ $badgeClass }} app-text">
                        {{ ucfirst($status) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg p-5 md:p-6 space-y-3 border border-[#E6D8E1]">
                        <h2 class="text-xl font-semibold text-[#213430] app-main">Program Details</h2>
                        <div class="text-base text-[#213430] app-text leading-relaxed">
                            <p><span class="font-medium">Title:</span> {{ optional($program)->title ?? 'N/A' }}</p>
                            <div class="mt-1">
                                <span class="font-medium">Description:</span>
                                <div class="mt-1 max-h-80 overflow-y-auto overflow-x-hidden rounded border border-[#E6D8E1] bg-[#FDF7FB] px-3 py-2 break-words">
                                    <p class="text-base text-[#213430] app-text">{{ optional($program)->description ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <p><span class="font-medium">Event Date:</span>
                                {{ optional(optional($program)->event_date)->format('d M Y') ?? 'N/A' }}</p>
                            <p><span class="font-medium">Event Time:</span> {{ optional($program)->event_time ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-5 md:p-6 space-y-3 border border-[#E6D8E1]">
                        <h2 class="text-xl font-semibold text-[#213430] app-main">Applicant</h2>
                        <div class="text-base text-[#213430] app-text leading-relaxed">
                            <p><span class="font-medium">Name:</span> {{ $registration->full_name }}</p>
                            <p><span class="font-medium">Email:</span> {{ $registration->email ?? 'N/A' }}</p>
                            <p><span class="font-medium">Phone:</span> {{ $registration->phone ?? 'N/A' }}</p>
                            <p><span class="font-medium">Date of Birth:</span> {{ optional($registration->dob)->format('d M Y') ?? 'N/A' }}</p>
                            <p><span class="font-medium">Referral Type:</span>
                                {{ $registration->referral_type === 'facility' ? 'Healthcare facility referral' : 'Self referral' }}</p>
                            <p><span class="font-medium">Treatment Facility:</span> {{ $registration->treatment_facility_name ?? 'N/A' }}</p>
                            <p><span class="font-medium">Address:</span> {{ $registration->street_address ?? 'N/A' }}</p>
                            <p><span class="font-medium">City / State:</span>
                                {{ $registration->city ?? 'N/A' }} {{ $registration->state ? ', ' . $registration->state : '' }}</p>
                            <p><span class="font-medium">Postal Code:</span> {{ $registration->postal_code ?? 'N/A' }}</p>
                            <p><span class="font-medium">Username:</span> {{ $registration->username }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-5 md:p-6 space-y-4 border border-[#E6D8E1]">
                    <h2 class="text-xl font-semibold text-[#213430] app-main">Application Details</h2>
                    <p class="text-base text-[#213430] app-text"><span class="font-medium">Quarter:</span>
                        {{ $quarterLabels[$registration->quarter_applied] ?? 'N/A' }}</p>
                    <p class="text-base text-[#213430] app-text"><span class="font-medium">Programs Applied:</span>
                        {{ collect($registration->programs_applied ?? [])->map(fn ($p) => $programLabels[$p] ?? $p)->filter()->implode(', ') ?: 'N/A' }}
                    </p>
                    <p class="text-base text-[#213430] app-text"><span class="font-medium">Active Treatment:</span>
                        {{ $registration->active_treatment ? 'Yes' : 'No' }}</p>
                    <p class="text-base text-[#213430] app-text"><span class="font-medium">Family History:</span>
                        {{ $registration->family_history ?? 'N/A' }}</p>
                    <p class="text-base text-[#213430] app-text"><span class="font-medium">Received Assistance Before:</span>
                        {{ $registration->assistance_history ?? 'N/A' }}</p>
                    <p class="text-base text-[#213430] app-text"><span class="font-medium">How did you hear about us?:</span>
                        {{ $registration->heard_about ?? 'N/A' }}</p>
                    <p class="text-base text-[#213430] app-text"><span class="font-medium">Proof of Income Status:</span>
                        {{ collect($registration->proof_of_income_status ?? [])->map(fn ($p) => $incomeLabels[$p] ?? $p)->filter()->implode(', ') ?: 'N/A' }}
                    </p>
                </div>

                <div class="bg-white rounded-lg p-5 md:p-6 space-y-4 border border-[#E6D8E1]">
                    <h2 class="text-xl font-semibold text-[#213430] app-main">Your Story & Authorization</h2>
                    <div>
                        <h3 class="font-medium text-[#213430] text-base mb-1 app-text">Story</h3>
                        <div class="max-h-96 overflow-y-auto overflow-x-hidden rounded border border-[#E6D8E1] bg-[#FDF7FB] px-3 py-2 break-words">
                            <p class="text-base text-[#6C5F67] app-text whitespace-pre-line leading-relaxed">
                                {{ $registration->story ?? 'No story provided.' }}</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-medium text-[#213430] text-base mb-1 app-text">Authorization</h3>
                        <p class="text-base text-[#213430] app-text">
                            {{ $registration->authorization_allow ? 'Consent granted' : 'Consent not granted' }}
                        </p>
                        @if ($registration->authorization_allow && !empty($registration->authorization_permissions))
                            <ul class="list-disc ml-5 text-base text-[#6C5F67] app-text leading-relaxed">
                                @foreach ($registration->authorization_permissions as $perm)
                                    <li>{{ $authorizationLabels[$perm] ?? ucfirst(str_replace('_', ' ', $perm)) }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <p class="text-base text-[#213430] app-text"><span class="font-medium">Billing Details:</span>
                        {{ $registration->billing_details ?? 'N/A' }}</p>
                    <div class="text-base text-[#213430] app-text">
                        <span class="font-medium">Signature:</span>
                        @if ($registration->signature)
                            <div class="mt-2">
                                <img src="{{ asset('storage/app/public/' . ltrim($registration->signature, '/')) }}" alt="Signature" class="h-24 object-contain">
                            </div>
                        @else
                            <p class="text-[#6C5F67]">N/A</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-lg p-5 md:p-6 border border-[#E6D8E1]">
                    <h2 class="text-xl font-semibold text-[#213430] app-main mb-4">Uploaded Documents</h2>
                    <div class="space-y-4 text-base app-text">
                        <div class="flex items-center justify-between gap-3 bg-[#FAF7FA] rounded-md px-4 py-3">
                            <span class="text-[#213430] font-medium">Treatment Verification Letter</span>
                            @if ($registration->treatment_letter)
                                <a href="{{ $registration->treatment_letter['url'] }}" target="_blank" class="text-[#B3477D] font-semibold hover:underline">Preview</a>
                            @else
                                <span class="text-[#6C5F67]">Not provided</span>
                            @endif
                        </div>

                        <div class="bg-[#FAF7FA] rounded-md px-4 py-3">
                            <div class="flex items-center justify-between">
                                <span class="text-[#213430] font-medium">Bill Statements</span>
                                @if (!empty($registration->bill_statements))
                                    <span class="text-sm text-[#6C5F67]">{{ count($registration->bill_statements) }} file(s)</span>
                                @endif
                            </div>
                            @if (!empty($registration->bill_statements))
                                <ul class="mt-2 space-y-1">
                                    @foreach ($registration->bill_statements as $bill)
                                        <li class="flex items-center justify-between gap-2">
                                            <span class="text-[#213430] truncate">{{ $bill['filename'] }}</span>
                                            <a href="{{ $bill['url'] }}" target="_blank" class="text-[#B3477D] font-semibold hover:underline">Preview</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-[#6C5F67] text-base">No bill statements uploaded.</p>
                            @endif
                        </div>

                        <div class="bg-[#FAF7FA] rounded-md px-4 py-3">
                            <div class="flex items-center justify-between">
                                <span class="text-[#213430] font-medium">Proof of Income Documents</span>
                                @if (!empty($registration->income_documents))
                                    <span class="text-sm text-[#6C5F67]">{{ count($registration->income_documents) }} file(s)</span>
                                @endif
                            </div>
                            @if (!empty($registration->income_documents))
                                <ul class="mt-2 space-y-1">
                                    @foreach ($registration->income_documents as $income)
                                        <li class="flex items-center justify-between gap-2">
                                            <span class="text-[#213430] truncate">{{ $income['filename'] }}</span>
                                            <a href="{{ $income['url'] }}" target="_blank" class="text-[#B3477D] font-semibold hover:underline">Preview</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-[#6C5F67] text-base">No income documents uploaded.</p>
                            @endif
                        </div>

                        @if (!empty($registration->documents))
                            <div class="bg-[#FAF7FA] rounded-md px-4 py-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-[#213430] font-medium">Additional Documents</span>
                                    <span class="text-sm text-[#6C5F67]">{{ count($registration->documents) }} file(s)</span>
                                </div>
                                <ul class="mt-2 space-y-1">
                                    @foreach ($registration->documents as $document)
                                        <li class="flex items-center justify-between gap-2">
                                            <span class="text-[#213430] truncate">{{ $document['filename'] }}</span>
                                            <a href="{{ $document['url'] }}" target="_blank"
                                                class="text-[#B3477D] font-semibold hover:underline">Preview</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($registration->review_note)
                    <div class="bg-white rounded-lg p-5 md:p-6 space-y-3 text-base text-[#213430] app-text border border-[#E6D8E1]">
                        <h2 class="text-xl font-semibold text-[#213430] app-main">Admin Note</h2>
                        <p class="text-[#6C5F67] whitespace-pre-line app-text">{{ $registration->review_note }}</p>
                    </div>
                @endif

                <div class="flex justify-end pt-2">
                    <a href="{{ route('patient.programsAndAids') }}"
                        class="px-5 py-3 border border-[#DCCFD8] text-[#6C5F67] rounded-md hover:bg-[#F3E8EF] transition app-text">
                        Back to Programs
                    </a>
                </div>
            </div>
        </div>
    </main>
@endsection
