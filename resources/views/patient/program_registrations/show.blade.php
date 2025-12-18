@php
    use App\Models\ProgramRegistration;
    $registration->loadMissing([
        'program',
        'program.sponsorships.sponsor.profile',
        'program.sponsorships.sponsor.sponsorDetail',
    ]);

    $status = strtolower($registration->status ?? ProgramRegistration::STATUS_PENDING);
    $badgeClass = match ($status) {
        ProgramRegistration::STATUS_APPROVED => 'bg-[#C5E8D1] text-[#20B354] border border-[#A5D0B7]',
        ProgramRegistration::STATUS_REJECTED => 'bg-[#FAD4D4] text-[#B32020] border border-[#E6A5A5]',
        default => 'bg-[#FDE8F3] text-[#DB69A2] border border-[#F4BBD5]',
    };

    $program = $registration->program;
    $latestSponsorship = optional($program)->sponsorships->sortByDesc('date')->sortByDesc('id')->first();
    $sponsorModel = optional($latestSponsorship)->sponsor;
    $sponsorProfile = optional($sponsorModel)->profile;
    $sponsorDetail = optional($sponsorModel)->sponsorDetail;
    $sponsor = $sponsorModel;

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
        'q1' => 'Quarter One (January thru March)',
        'q2' => 'Quarter Two (April thru June)',
        'q3' => 'Quarter Three (July thru September)',
        'q4' => 'Quarter Four (October thru December)',
    ];
@endphp

@extends('patient.layouts.app')

@section('title', 'Program Registration')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-[#F3E8EF] rounded-2xl shadow-sm p-6 space-y-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-[#213430] app-main">Program Registration</h1>
                    <p class="text-sm text-[#91848C] app-text">
                        Submitted on {{ optional($registration->created_at)->format('d M Y, h:i A') ?? 'N/A' }}
                    </p>
                </div>
                <span class="px-4 py-1 rounded-full text-sm font-semibold {{ $badgeClass }} app-text">
                    {{ ucfirst($status) }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white/70 rounded-xl p-4 space-y-2">
                    <h2 class="text-lg font-semibold text-[#213430] app-main">Program Details</h2>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Title:</span>
                        {{ optional($program)->title ?? 'N/A' }}</p>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Description:</span>
                        {{ optional($program)->description ?? 'N/A' }}</p>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Event Date:</span>
                        {{ optional(optional($program)->event_date)->format('d M Y') ?? 'N/A' }}</p>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Event Time:</span>
                        {{ optional($program)->event_time ?? 'N/A' }}</p>
                </div>

                <div class="bg-white/70 rounded-xl p-4 space-y-2">
                    <h2 class="text-lg font-semibold text-[#213430] app-main">Applicant</h2>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Name:</span>
                        {{ $registration->full_name }}</p>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Email:</span>
                        {{ $registration->email ?? 'N/A' }}</p>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Phone:</span>
                        {{ $registration->phone ?? 'N/A' }}</p>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Date of Birth:</span>
                        {{ optional($registration->dob)->format('d M Y') ?? 'N/A' }}</p>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Referral Type:</span>
                        {{ $registration->referral_type === 'facility' ? 'Healthcare facility referral' : 'Self referral' }}</p>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Treatment Facility:</span>
                        {{ $registration->treatment_facility_name ?? 'N/A' }}</p>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Address:</span>
                        {{ $registration->street_address ?? 'N/A' }}</p>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">City / State:</span>
                        {{ $registration->city ?? 'N/A' }} {{ $registration->state ? ', ' . $registration->state : '' }}</p>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Postal Code:</span>
                        {{ $registration->postal_code ?? 'N/A' }}</p>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Username:</span> {{ $registration->username }}</p>
                </div>
            </div>

            <div class="bg-white/70 rounded-xl p-4 space-y-3">
                <h2 class="text-lg font-semibold text-[#213430] app-main">Application Details</h2>
                <p class="text-sm text-[#213430] app-text"><span class="font-medium">Quarter:</span>
                    {{ $quarterLabels[$registration->quarter_applied] ?? 'N/A' }}</p>
                <p class="text-sm text-[#213430] app-text"><span class="font-medium">Programs Applied:</span>
                    {{ collect($registration->programs_applied ?? [])->map(fn ($p) => $programLabels[$p] ?? $p)->filter()->implode(', ') ?: 'N/A' }}
                </p>
                <p class="text-sm text-[#213430] app-text"><span class="font-medium">Active Treatment:</span>
                    {{ $registration->active_treatment ? 'Yes' : 'No' }}</p>
                <p class="text-sm text-[#213430] app-text"><span class="font-medium">Pregnant:</span>
                    {{ $registration->pregnant ? 'Yes' : 'No' }}</p>
                <p class="text-sm text-[#213430] app-text"><span class="font-medium">Family History:</span>
                    {{ $registration->family_history ?? 'N/A' }}</p>
                <p class="text-sm text-[#213430] app-text"><span class="font-medium">Received Assistance Before:</span>
                    {{ $registration->assistance_history ?? 'N/A' }}</p>
                <p class="text-sm text-[#213430] app-text"><span class="font-medium">How did you hear about us?:</span>
                    {{ $registration->heard_about ?? 'N/A' }}</p>
                <p class="text-sm text-[#213430] app-text"><span class="font-medium">Proof of Income Status:</span>
                    {{ collect($registration->proof_of_income_status ?? [])->map(fn ($p) => $incomeLabels[$p] ?? $p)->filter()->implode(', ') ?: 'N/A' }}
                </p>
            </div>

            <div class="bg-white/70 rounded-xl p-4 space-y-3">
                <h2 class="text-lg font-semibold text-[#213430] app-main">Your Story & Authorization</h2>
                <div>
                    <span class="text-sm font-medium text-[#213430] app-main">Story</span>
                    <p class="text-sm text-[#91848C] whitespace-pre-line app-text mt-1">
                        {{ $registration->story ?? 'No story provided.' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-[#213430] app-main">Authorization</span>
                    <p class="text-sm text-[#213430] app-text mt-1">
                        {{ $registration->authorization_allow ? 'Consent granted' : 'Consent not granted' }}
                    </p>
                    @if ($registration->authorization_allow && !empty($registration->authorization_permissions))
                        <ul class="list-disc ml-5 text-sm text-[#91848C] app-text">
                            @foreach ($registration->authorization_permissions as $perm)
                                <li>{{ $authorizationLabels[$perm] ?? ucfirst(str_replace('_', ' ', $perm)) }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <p class="text-sm text-[#213430] app-text"><span class="font-medium">Billing Details:</span>
                    {{ $registration->billing_details ?? 'N/A' }}</p>
                <div>
                    <span class="text-sm font-medium text-[#213430] app-main">Signature</span>
                    @if ($registration->signature)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . ltrim($registration->signature, '/')) }}" alt="Signature" class="h-20 object-contain">
                        </div>
                    @else
                        <p class="text-sm text-[#91848C] app-text">N/A</p>
                    @endif
                </div>
            </div>

            <div class="bg-white/70 rounded-xl p-4 space-y-3">
                <h2 class="text-lg font-semibold text-[#213430] app-main">Uploaded Documents</h2>
                <div class="space-y-3 text-sm app-text">
                    <div class="flex items-center justify-between gap-3 bg-white/70 rounded-md px-3 py-2">
                        <span class="text-[#213430] font-medium">Treatment Verification Letter</span>
                        @if ($registration->treatment_letter)
                            <a href="{{ $registration->treatment_letter['url'] }}" target="_blank" class="text-[#DB69A2] hover:underline">Download</a>
                        @else
                            <span class="text-[#91848C]">Not provided</span>
                        @endif
                    </div>

                    <div class="bg-white/70 rounded-md px-3 py-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[#213430] font-medium">Bill Statements</span>
                            @if (!empty($registration->bill_statements))
                                <span class="text-xs text-[#91848C]">{{ count($registration->bill_statements) }} file(s)</span>
                            @endif
                        </div>
                        @if (!empty($registration->bill_statements))
                            <ul class="mt-2 space-y-1">
                                @foreach ($registration->bill_statements as $bill)
                                    <li class="flex items-center justify-between gap-2">
                                        <span class="text-[#213430] truncate">{{ $bill['filename'] }}</span>
                                        <a href="{{ $bill['url'] }}" target="_blank" class="text-[#DB69A2] hover:underline">Download</a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-[#91848C] text-sm">No bill statements uploaded.</p>
                        @endif
                    </div>

                    <div class="bg-white/70 rounded-md px-3 py-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[#213430] font-medium">Proof of Income Documents</span>
                            @if (!empty($registration->income_documents))
                                <span class="text-xs text-[#91848C]">{{ count($registration->income_documents) }} file(s)</span>
                            @endif
                        </div>
                        @if (!empty($registration->income_documents))
                            <ul class="mt-2 space-y-1">
                                @foreach ($registration->income_documents as $income)
                                    <li class="flex items-center justify-between gap-2">
                                        <span class="text-[#213430] truncate">{{ $income['filename'] }}</span>
                                        <a href="{{ $income['url'] }}" target="_blank" class="text-[#DB69A2] hover:underline">Download</a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-[#91848C] text-sm">No income documents uploaded.</p>
                        @endif
                    </div>

                    @if (!empty($registration->documents))
                        <div class="bg-white/70 rounded-md px-3 py-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[#213430] font-medium">Additional Documents</span>
                                <span class="text-xs text-[#91848C]">{{ count($registration->documents) }} file(s)</span>
                            </div>
                            <ul class="mt-2 space-y-1">
                                @foreach ($registration->documents as $document)
                                    <li class="flex items-center justify-between gap-2">
                                        <span class="text-[#213430] truncate">{{ $document['filename'] }}</span>
                                        <a href="{{ $document['url'] }}" target="_blank" class="text-[#DB69A2] hover:underline">Download</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white/70 rounded-xl p-4 space-y-3">
                <h2 class="text-lg font-semibold text-[#213430] app-main">Sponsor</h2>
                <div class="flex items-center gap-3">
                    <img src="{{ $sponsor->user ? $sponsor->user->avatar_url : asset('public/images/profile.png') }}"
                        alt="Sponsor logo" class="w-12 h-12 rounded-full object-cover">
                    <div class="text-sm text-[#213430] app-text">
                        <p><span class="font-medium">Name:</span>
                            {{ optional($sponsorDetail)->company_name ?? ($sponsorProfile->full_name ?? 'N/A') }}</p>
                        <p><span class="font-medium">Phone:</span>
                            {{ optional($sponsorDetail)->company_phone ?? ($sponsorProfile->phone ?? 'N/A') }}</p>
                        <p><span class="font-medium">Email:</span>
                            {{ optional($sponsorDetail)->company_email ?? (optional($program)->contact_email ?? 'N/A') }}</p>
                    </div>
                </div>
            </div>

            @if ($registration->review_note)
                <div class="bg-white/70 border border-[#DCCFD8] rounded-xl p-4">
                    <h2 class="text-lg font-semibold text-[#213430] app-main">Admin Note</h2>
                    <p class="text-sm text-[#91848C] whitespace-pre-line app-text">{{ $registration->review_note }}</p>
                </div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('patient.programsAndAids') }}"
                    class="px-5 py-3 border border-[#DCCFD8] text-[#91848C] rounded-md hover:bg-[#F3E8EF] transition app-text">
                    Back to Programs
                </a>
            </div>
        </div>
    </div>
@endsection
