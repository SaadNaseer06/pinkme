@php
    use App\Models\ProgramRegistration;
    $registration->loadMissing(['program', 'user', 'reviewer', 'assignedCaseManager']);
    $status = strtolower($registration->status);
    $badgeClasses = match ($status) {
        ProgramRegistration::STATUS_APPROVED => 'bg-[#C5E8D1] text-[#20B354] border border-[#A5D0B7]',
        ProgramRegistration::STATUS_REJECTED => 'bg-[#FAD4D4] text-[#B32020] border border-[#E6A5A5]',
        default => 'bg-[#FDE8F3] text-[#DB69A2] border border-[#F4BBD5]',
    };

    $programLabels = [
        'breast_cancer_treatment' => 'Breast Cancer Treatment Assistance Program',
        'mastectomy_wellness' => 'Pink Mastectomy and Wellness Assistance Program',
        'pinkme_food_hunger' => 'PINK ME Food & Hunger Grant',
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

@extends('case_manager.layouts.app')

@section('title', 'Program Registration Request')

@section('content')
    <main class="flex-1">
        <div class="max-w-full mx-auto">
            <div class="mt-6 bg-[#F6EDF5] rounded-xl p-6 md:p-8 space-y-8 shadow-sm">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-[#DCCFD8] pb-5">
                    <div>
                        <h2 class="text-3xl font-semibold text-[#213430] app-main">Registration Details</h2>
                        <p class="text-base text-[#6C5F67] app-text mt-2">
                            Submitted on {{ $registration->created_at?->format('d M Y, h:i A') ?? 'N/A' }}
                        </p>
                    </div>
                    <span class="px-5 py-2 rounded-full text-base font-semibold app-text {{ $badgeClasses }}">
                        Status: {{ ucfirst($status) }}
                    </span>
                </div>

                <div class="bg-white rounded-lg p-5 md:p-6 border border-[#E6D8E1]">
                    <h3 class="text-xl font-semibold text-[#213430] app-main">Assigned Case Manager</h3>
                    <p class="text-base text-[#213430] app-text mt-2">
                        {{ $registration->assignedCaseManager?->profile?->full_name ?? $registration->assignedCaseManager?->email ?? 'Unassigned' }}
                        @if ($registration->assigned_at)
                            <span class="text-[#91848C]">• assigned {{ $registration->assigned_at->format('d M Y, h:i A') }}</span>
                        @endif
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg p-5 md:p-6 space-y-3 border border-[#E6D8E1]">
                        <h3 class="text-xl font-semibold text-[#213430] app-main">Applicant</h3>
                        <div class="text-base text-[#213430] app-text leading-relaxed">
                            <p><span class="font-medium">Name:</span> {{ $registration->full_name }}</p>
                            <p><span class="font-medium">Email:</span> {{ $registration->email ?? 'N/A' }}</p>
                            <p><span class="font-medium">Phone:</span> {{ $registration->phone ?? 'N/A' }}</p>
                            <p><span class="font-medium">Date of Birth:</span> {{ $registration->dob?->format('d M Y') ?? 'N/A' }}</p>
                            <p><span class="font-medium">Referral Type:</span> {{ $registration->referral_type === 'facility' ? 'Healthcare facility referral' : 'Self referral' }}</p>
                            <p><span class="font-medium">Treatment Facility:</span> {{ $registration->treatment_facility_name ?? 'N/A' }}</p>
                            <p><span class="font-medium">Address:</span> {{ $registration->street_address ?? 'N/A' }}</p>
                            <p><span class="font-medium">City / State:</span> {{ $registration->city ?? 'N/A' }} {{ $registration->state ? ', ' . $registration->state : '' }}</p>
                            <p><span class="font-medium">Postal Code:</span> {{ $registration->postal_code ?? 'N/A' }}</p>
                            <p><span class="font-medium">Username:</span> {{ $registration->username }}</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-5 md:p-6 space-y-3 border border-[#E6D8E1]">
                        <h3 class="text-xl font-semibold text-[#213430] app-main">Program</h3>
                        <div class="text-base text-[#213430] app-text leading-relaxed">
                            <p><span class="font-medium">Program Title:</span> {{ $registration->program->title ?? 'N/A' }}</p>
                            <p><span class="font-medium">Medical Condition:</span> {{ $registration->medical_condition ?? 'N/A' }}</p>
                            <p><span class="font-medium">Assistance Type:</span> {{ $registration->assistance_type ?? 'N/A' }}</p>
                        </div>
                        <div class="mt-3">
                            <h4 class="font-medium text-[#213430] text-base mb-1 app-text">Programs Applied</h4>
                            <p class="text-base text-[#213430] app-text leading-relaxed">
                                {{ collect($registration->programs_applied ?? [])->map(fn ($p) => $programLabels[$p] ?? $p)->filter()->implode(', ') ?: 'N/A' }}
                            </p>
                            <p class="text-base text-[#213430] app-text"><span class="font-medium">Quarter:</span>
                                {{ $quarterLabels[$registration->quarter_applied] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-5 md:p-6 space-y-4 border border-[#E6D8E1]">
                    <h3 class="text-xl font-semibold text-[#213430] app-main">Application Details</h3>
                    <p class="text-base text-[#213430] app-text"><span class="font-medium">Active Treatment:</span> {{ $registration->active_treatment ? 'Yes' : 'No' }}</p>
                    <p class="text-base text-[#213430] app-text"><span class="font-medium">Family History:</span> {{ $registration->family_history ?? 'N/A' }}</p>
                    <p class="text-base text-[#213430] app-text"><span class="font-medium">Received Assistance Before:</span> {{ $registration->assistance_history ?? 'N/A' }}</p>
                    <p class="text-base text-[#213430] app-text"><span class="font-medium">Heard About Us:</span> {{ $registration->heard_about ?? 'N/A' }}</p>
                    <p class="text-base text-[#213430] app-text"><span class="font-medium">Proof of Income Status:</span>
                        {{ collect($registration->proof_of_income_status ?? [])->map(fn ($p) => $incomeLabels[$p] ?? $p)->filter()->implode(', ') ?: 'N/A' }}</p>
                    <div>
                        <h4 class="font-medium text-[#213430] text-base mb-1 app-text">Story</h4>
                        <div class="max-h-96 overflow-y-auto overflow-x-hidden rounded border border-[#E6D8E1] bg-[#FDF7FB] px-3 py-2 break-words">
                            <p class="text-base text-[#6C5F67] app-text whitespace-pre-line leading-relaxed">{{ $registration->story ?? 'No story provided.' }}</p>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-medium text-[#213430] text-base mb-1 app-text">Authorization</h4>
                        <p class="text-base text-[#213430] app-text">{{ $registration->authorization_allow ? 'Consent granted' : 'Consent not granted' }}</p>
                        @if ($registration->authorization_allow && !empty($registration->authorization_permissions))
                            <ul class="list-disc ml-5 text-base text-[#6C5F67] app-text leading-relaxed">
                                @foreach ($registration->authorization_permissions as $perm)
                                    <li>{{ $authorizationLabels[$perm] ?? ucfirst(str_replace('_', ' ', $perm)) }}</li>
                                @endforeach
                            </ul>
                        @endif
                        <p class="text-base text-[#213430] app-text mt-2"><span class="font-medium">Billing Details:</span> {{ $registration->billing_details ?? 'N/A' }}</p>
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
                </div>

                <div class="bg-white rounded-lg p-5 md:p-6 border border-[#E6D8E1]">
                    <h3 class="text-xl font-semibold text-[#213430] app-main mb-4">Supporting Documents</h3>
                    <div class="space-y-4 text-base app-text">
                        <div class="flex items-center justify-between gap-3 bg-[#FAF7FA] rounded-md px-4 py-3">
                            <span class="text-[#213430] font-medium">Treatment Verification Letter</span>
                            @if ($registration->treatment_letter)
                                <a href="{{ $registration->treatment_letter['url'] }}" target="_blank" class="inline-flex items-center text-[#B3477D] font-semibold hover:underline">Preview</a>
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
                                            <a href="{{ $bill['url'] }}" target="_blank" class="inline-flex items-center text-[#B3477D] font-semibold hover:underline">Preview</a>
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
                                            <a href="{{ $income['url'] }}" target="_blank" class="inline-flex items-center text-[#B3477D] font-semibold hover:underline">Preview</a>
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
                                                class="inline-flex items-center text-[#B3477D] font-semibold hover:underline">Preview</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($registration->status === ProgramRegistration::STATUS_PENDING)
                    <div class="bg-white rounded-lg p-5 md:p-6 space-y-6 border border-[#E6D8E1]">
                        <h3 class="text-xl font-semibold text-[#213430] app-main">Case Manager Review</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <form method="POST" action="{{ route('case_manager.program_registrations.approve', $registration) }}" class="bg-[#F8F2F6] rounded-lg p-4 space-y-3 border border-[#E6D8E1]">
                                @csrf
                                <h4 class="font-semibold text-[#213430] app-main">Approve Registration</h4>
                                <p class="text-base text-[#6C5F67] app-text">Optional: add a short note for the applicant.</p>
                                <textarea name="note" rows="3" class="w-full px-3 py-2 rounded-md border border-[#DCCFD8] bg-white text-base focus:outline-none focus:ring-2 focus:ring-[#DB69A2]" placeholder="Optional note"></textarea>
                                <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-4 py-3 bg-[#20B354] text-white rounded-md text-base font-semibold hover:bg-[#1A9444] transition">
                                    Approve Request
                                </button>
                            </form>

                            <form method="POST" action="{{ route('case_manager.program_registrations.reject', $registration) }}" class="bg-[#F8F2F6] rounded-lg p-4 space-y-3 border border-[#E6D8E1]">
                                @csrf
                                <h4 class="font-semibold text-[#213430] app-main">Reject Registration</h4>
                                <p class="text-base text-[#6C5F67] app-text">Provide a short reason to keep for the record.</p>
                                <textarea name="note" rows="3" required class="w-full px-3 py-2 rounded-md border border-[#DCCFD8] bg-white text-base focus:outline-none focus:ring-2 focus:ring-[#DB69A2]" placeholder="Reason for rejection"></textarea>
                                <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-4 py-3 bg-[#B32020] text-white rounded-md text-base font-semibold hover:bg-[#8F1A1A] transition">
                                    Reject Request
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-lg p-5 md:p-6 space-y-3 text-base text-[#213430] app-text border border-[#E6D8E1]">
                        <h3 class="text-xl font-semibold text-[#213430] app-main">Review Summary</h3>
                        <p><span class="font-medium">Reviewed By:</span> {{ $registration->reviewer?->profile->full_name ?? $registration->reviewer?->email ?? 'N/A' }}</p>
                        <p><span class="font-medium">Reviewed At:</span> {{ $registration->reviewed_at?->format('d M Y, h:i A') ?? 'N/A' }}</p>
                        @if ($registration->review_note)
                            <div>
                                <span class="font-medium">Review Note:</span>
                                <p class="text-[#6C5F67] whitespace-pre-line app-text">{{ $registration->review_note }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="flex justify-between items-center pt-5 border-t border-[#DCCFD8]">
                    <a href="{{ route('case_manager.program_registrations.index', ['status' => $status === 'pending' ? 'pending' : 'all']) }}"
                        class="inline-flex items-center gap-2 text-base text-[#6C5F67] hover:text-[#213430] app-text">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to list
                    </a>
                </div>
            </div>
        </div>
    </main>
@endsection
