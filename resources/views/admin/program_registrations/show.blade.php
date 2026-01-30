@php
    use App\Models\ProgramRegistration;
    $registration->loadMissing(['program', 'user', 'reviewer']);
    $status = strtolower($registration->status);
    $badgeClasses = match ($status) {
        ProgramRegistration::STATUS_APPROVED => 'bg-[#C5E8D1] text-[#20B354] border border-[#A5D0B7]',
        ProgramRegistration::STATUS_REJECTED => 'bg-[#FAD4D4] text-[#B32020] border border-[#E6A5A5]',
        default => 'bg-[#FDE8F3] text-[#DB69A2] border border-[#F4BBD5]',
    };

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

@extends('admin.layouts.admin')

@section('title', 'Program Registration Request')

@push('head')
    <style>
        .action-spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.5);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: action-spin 0.7s linear infinite;
            margin-left: 8px;
        }

        .is-loading .action-spinner {
            display: inline-block;
        }

        .is-loading .action-text {
            opacity: 0.8;
        }

        @keyframes action-spin {
            to {
                transform: rotate(360deg);
            }
        }

        .long-text-scroll {
            max-height: 28rem;
            overflow-y: auto;
            overflow-x: hidden;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
    </style>
@endpush

@section('content')
    <main class="flex-1 min-w-0">
        <div class="max-w-full mx-auto min-w-0">
            <div class="mt-6 bg-[#F6EDF5] rounded-xl p-6 md:p-8 space-y-8 shadow-sm min-w-0">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-[#DCCFD8] pb-5 min-w-0">
                    <div class="min-w-0 flex-1">
                        <h2 class="text-3xl font-semibold text-[#213430] app-main break-words">Registration Details</h2>
                        <p class="text-base text-[#6C5F67] app-text mt-2 break-words">
                            Submitted on {{ $registration->created_at?->format('d M Y, h:i A') ?? 'N/A' }}
                        </p>
                    </div>
                    <span class="flex-shrink-0 px-5 py-2 rounded-full text-base font-semibold app-text {{ $badgeClasses }}">
                        Status: {{ ucfirst($status) }}
                    </span>
                </div>

                <div class="bg-white rounded-lg p-5 md:p-6 border border-[#E6D8E1] min-w-0">
                    <h3 class="text-xl font-semibold text-[#213430] app-main">Assign Case Manager</h3>
                    <p class="text-sm text-[#6C5F67] app-text mt-1 break-words">
                        Assign a case manager to handle this registration.
                    </p>
                    <form method="POST" action="{{ route('admin.program_registrations.assign', $registration) }}" class="mt-4 flex flex-col md:flex-row md:items-center gap-3">
                        @csrf
                        <select name="case_manager_id"
                            class="w-full md:w-80 rounded-md px-3 py-2 text-sm text-[#213430] bg-white border border-[#91848C] focus:outline-none">
                            <option value="">Unassigned</option>
                            @foreach ($caseManagers as $manager)
                                <option value="{{ $manager->id }}" @selected($registration->assigned_case_manager_id === $manager->id)>
                                    {{ $manager->profile->full_name ?? $manager->email }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit"
                            class="px-4 py-2 bg-[#DB69A2] text-white rounded-md text-sm font-medium hover:bg-[#c95791] transition app-text">
                            Save Assignment
                        </button>
                    </form>
                    <div class="mt-3 text-sm text-[#6C5F67] app-text">
                        Current: {{ $registration->assignedCaseManager?->profile?->full_name ?? $registration->assignedCaseManager?->email ?? 'Unassigned' }}
                        @if ($registration->assigned_at)
                            <span class="text-[#91848C]">• assigned {{ $registration->assigned_at->format('d M Y, h:i A') }}</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 min-w-0">
                <div class="bg-white rounded-lg p-5 md:p-6 space-y-3 border border-[#E6D8E1] min-w-0 overflow-hidden">
                    <h3 class="text-xl font-semibold text-[#213430] app-main">Applicant</h3>
                    <div class="text-base text-[#213430] app-text leading-relaxed break-words min-w-0">
                        <p class="break-words"><span class="font-medium">Name:</span> {{ $registration->full_name }}</p>
                        <p class="break-words"><span class="font-medium">Email:</span> {{ $registration->email ?? 'N/A' }}</p>
                        <p class="break-words"><span class="font-medium">Phone:</span> {{ $registration->phone ?? 'N/A' }}</p>
                        <p class="break-words"><span class="font-medium">Date of Birth:</span> {{ $registration->dob?->format('d M Y') ?? 'N/A' }}</p>
                        <p class="break-words"><span class="font-medium">Referral Type:</span> {{ $registration->referral_type === 'facility' ? 'Healthcare facility referral' : 'Self referral' }}</p>
                        <p class="break-words"><span class="font-medium">Treatment Facility:</span> {{ $registration->treatment_facility_name ?? 'N/A' }}</p>
                        <p class="break-words"><span class="font-medium">Address:</span> {{ $registration->street_address ?? 'N/A' }}</p>
                        <p class="break-words"><span class="font-medium">City / State:</span> {{ $registration->city ?? 'N/A' }} {{ $registration->state ? ', ' . $registration->state : '' }}</p>
                        <p class="break-words"><span class="font-medium">Postal Code:</span> {{ $registration->postal_code ?? 'N/A' }}</p>
                        <p class="break-words"><span class="font-medium">Username:</span> {{ $registration->username }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-5 md:p-6 space-y-3 border border-[#E6D8E1] min-w-0 overflow-hidden">
                    <h3 class="text-xl font-semibold text-[#213430] app-main">Program</h3>
                    <div class="text-base text-[#213430] app-text leading-relaxed break-words min-w-0">
                        <p class="break-words"><span class="font-medium">Program Title:</span> {{ $registration->program->title ?? 'N/A' }}</p>
                        <p class="break-words"><span class="font-medium">Assistance Type:</span> {{ $registration->assistance_type ?? 'N/A' }}</p>
                    </div>
                    <div class="mt-3 min-w-0">
                        <h4 class="font-medium text-[#213430] text-base mb-1 app-text">Programs Applied</h4>
                        <p class="text-base text-[#213430] app-text leading-relaxed break-words">
                            {{ collect($registration->programs_applied ?? [])->map(fn ($p) => $programLabels[$p] ?? $p)->filter()->implode(', ') ?: 'N/A' }}
                        </p>
                        <p class="text-base text-[#213430] app-text"><span class="font-medium">Quarter:</span>
                            {{ $quarterLabels[$registration->quarter_applied] ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-5 md:p-6 space-y-4 border border-[#E6D8E1] min-w-0">
                <h3 class="text-xl font-semibold text-[#213430] app-main">Application Details</h3>
                <p class="text-base text-[#213430] app-text break-words"><span class="font-medium">Active Treatment:</span> {{ $registration->active_treatment ? 'Yes' : 'No' }}</p>
                <p class="text-base text-[#213430] app-text break-words"><span class="font-medium">Family History:</span> {{ $registration->family_history ?? 'N/A' }}</p>
                <p class="text-base text-[#213430] app-text break-words"><span class="font-medium">Received Assistance Before:</span> {{ $registration->assistance_history ?? 'N/A' }}</p>
                <p class="text-base text-[#213430] app-text break-words"><span class="font-medium">Heard About Us:</span> {{ $registration->heard_about ?? 'N/A' }}</p>
                <p class="text-base text-[#213430] app-text break-words"><span class="font-medium">Proof of Income Status:</span>
                    {{ collect($registration->proof_of_income_status ?? [])->map(fn ($p) => $incomeLabels[$p] ?? $p)->filter()->implode(', ') ?: 'N/A' }}</p>
                <div>
                    <h4 class="font-medium text-[#213430] text-base mb-1 app-text">Story</h4>
                    <div class="long-text-scroll rounded border border-[#E6D8E1] bg-[#FDF7FB] px-3 py-2">
                        <p class="text-base text-[#6C5F67] app-text whitespace-pre-line leading-relaxed break-words">{{ $registration->story ?? 'No story provided.' }}</p>
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
                            <div class="flex items-center gap-3">
                                <a href="{{ $registration->treatment_letter['url'] }}" target="_blank" class="inline-flex items-center text-[#B3477D] font-semibold hover:underline">Preview</a>
                                <a href="{{ $registration->treatment_letter['url'] }}" download class="inline-flex items-center text-[#213430] font-semibold hover:underline">Download</a>
                            </div>
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
                                        <div class="flex items-center gap-3">
                                            <a href="{{ $bill['url'] }}" target="_blank" class="inline-flex items-center text-[#B3477D] font-semibold hover:underline">Preview</a>
                                            <a href="{{ $bill['url'] }}" download class="inline-flex items-center text-[#213430] font-semibold hover:underline">Download</a>
                                        </div>
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
                                        <div class="flex items-center gap-3">
                                            <a href="{{ $income['url'] }}" target="_blank" class="inline-flex items-center text-[#B3477D] font-semibold hover:underline">Preview</a>
                                            <a href="{{ $income['url'] }}" download class="inline-flex items-center text-[#213430] font-semibold hover:underline">Download</a>
                                        </div>
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
                                        <div class="flex items-center gap-3">
                                            <a href="{{ $document['url'] }}" target="_blank"
                                                class="inline-flex items-center text-[#B3477D] font-semibold hover:underline">Preview</a>
                                            <a href="{{ $document['url'] }}" download
                                                class="inline-flex items-center text-[#213430] font-semibold hover:underline">Download</a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            @if ($registration->status === ProgramRegistration::STATUS_PENDING)
                <div class="bg-white rounded-lg p-5 md:p-6 space-y-6 border border-[#E6D8E1]">
                    <h3 class="text-xl font-semibold text-[#213430] app-main">Admin Review</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <form method="POST" action="{{ route('admin.program_registrations.approve', $registration) }}" data-action-loader class="bg-[#F8F2F6] rounded-lg p-4 space-y-3 border border-[#E6D8E1]">
                            @csrf
                            <h4 class="font-semibold text-[#213430] app-main">Approve Registration</h4>
                            <p class="text-base text-[#6C5F67] app-text">Optional: add a short note for the applicant.</p>
                            <textarea name="note" rows="3" class="w-full px-3 py-2 rounded-md border border-[#DCCFD8] bg-white text-base focus:outline-none focus:ring-2 focus:ring-[#DB69A2]" placeholder="Optional note"></textarea>
                            <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-3 bg-[#20B354] text-white rounded-md text-base font-semibold hover:bg-[#1A9444] transition">
                                <span class="action-text">Approve Request</span>
                                <span class="action-spinner" aria-hidden="true"></span>
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.program_registrations.reject', $registration) }}" data-action-loader class="bg-[#F8F2F6] rounded-lg p-4 space-y-3 border border-[#E6D8E1]">
                            @csrf
                            <h4 class="font-semibold text-[#213430] app-main">Reject Registration</h4>
                            <p class="text-base text-[#6C5F67] app-text">Provide a short reason to keep for the record.</p>
                            <textarea name="note" rows="3" required class="w-full px-3 py-2 rounded-md border border-[#DCCFD8] bg-white text-base focus:outline-none focus:ring-2 focus:ring-[#DB69A2]" placeholder="Reason for rejection"></textarea>
                            <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-3 bg-[#B32020] text-white rounded-md text-base font-semibold hover:bg-[#8F1A1A] transition">
                                <span class="action-text">Reject Request</span>
                                <span class="action-spinner" aria-hidden="true"></span>
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
                                <div class="long-text-scroll mt-1 rounded border border-[#E6D8E1] bg-[#FDF7FB] px-3 py-2">
                                    <p class="text-[#6C5F67] whitespace-pre-line app-text break-words">{{ $registration->review_note }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="flex justify-between items-center pt-5 border-t border-[#DCCFD8]">
                    <a href="{{ route('admin.registrations.index', ['status' => $status === 'pending' ? 'pending' : 'all']) }}"
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

@push('scripts')
    <script>
        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) return;
            if (!form.matches('[data-action-loader]')) return;

            const button = form.querySelector('button[type="submit"]');
            if (!button) return;

            button.classList.add('is-loading');
            button.setAttribute('disabled', 'disabled');
            button.setAttribute('aria-busy', 'true');
        }, true);
    </script>
@endpush
