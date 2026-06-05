@php
    $registration->loadMissing(['program', 'user', 'reviewer', 'shipper.profile', 'financeUser.profile', 'registrationInvoices']);
    $isMtm = $registration->isMomentsThatMatterApplication();
    $status = strtolower((string) $registration->status);
    $badgeClasses = match ($status) {
        \App\Models\ProgramRegistration::STATUS_APPROVED => 'bg-[#C5E8D1] text-[#20B354] border border-[#A5D0B7]',
        \App\Models\ProgramRegistration::STATUS_SHIPPED => 'bg-[#D4E8FA] text-[#1A6BB3] border border-[#A5C8E6]',
        \App\Models\ProgramRegistration::STATUS_REJECTED => 'bg-[#FAD4D4] text-[#B32020] border border-[#E6A5A5]',
        \App\Models\ProgramRegistration::STATUS_PENDING_FINANCE => 'bg-amber-100 text-amber-900 border border-amber-200',
        default => 'bg-[#FDE8F3] text-[#9E2469] border border-[#F4BBD5]',
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

        /* Full-width admin detail: global body * { max-width:100% } shrinks MTM pages with less content */
        .admin-program-registration-show {
            width: 100%;
            max-width: none;
        }

        .admin-program-registration-show .registration-detail-shell,
        .admin-program-registration-show .registration-detail-section {
            width: 100%;
            max-width: none;
        }

        .admin-program-registration-show.mtm-registration-detail .registration-detail-shell {
            width: 100% !important;
            max-width: none !important;
        }
    </style>
@endpush

@section('content')
    <div class="admin-program-registration-show w-full {{ $isMtm ? 'mtm-registration-detail' : '' }}">
        <div class="max-w-8xl mx-auto w-full px-5 mt-6">
            <div class="mb-4">
                <a href="{{ route('admin.registrations.index', [
                    'program_type' => $isMtm ? \App\Support\ProgramType::MOMENTS_THAT_MATTER : ($registration->program?->program_type ?? 'all'),
                    'program_status' => $status === \App\Models\ProgramRegistration::STATUS_PENDING ? 'pending' : 'all',
                ]) }}"
                    class="inline-flex items-center gap-2 text-base text-[#6C5F67] hover:text-[#213430] app-text">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to list
                </a>
            </div>
            <div class="registration-detail-shell bg-[#F6EDF5] rounded-xl p-6 md:p-8 space-y-8 shadow-sm w-full min-w-0">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-[#DCCFD8] pb-5 min-w-0">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            @if ($isMtm)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#FDE8F3] text-[#9E2469] border border-[#F4BBD5]">Moments That Matter</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white text-[#6C5F67] border border-[#E6D8E1]">Financial Assistance</span>
                            @endif
                        </div>
                        <h2 class="text-3xl font-semibold text-[#213430] app-main break-words">Registration Details</h2>
                        <p class="text-base text-[#6C5F67] app-text mt-2 break-words">
                            Submitted on {{ $registration->created_at?->timezone(config('app.timezone'))->format('d M Y, h:i A') ?? 'N/A' }}
                        </p>
                    </div>
                    <span class="flex-shrink-0 px-5 py-2 rounded-full text-base font-semibold app-text {{ $badgeClasses }}">
                        Status: {{ $registration->status_label }}
                    </span>
                </div>

                @if (! $isMtm && $registration->registrationInvoices->isEmpty() && $status === 'pending')
                <div class="bg-white rounded-lg p-5 md:p-6 border border-[#E6D8E1] min-w-0">
                    <h3 class="text-xl font-semibold text-[#213430] app-main">Case manager (optional override)</h3>
                    <p class="text-sm text-[#6C5F67] app-text mt-1 break-words">
                        Applications normally go to the case manager shared inbox without assignment. Use this only to pre-assign or reassign when needed.
                    </p>
                    <div id="assignCaseManagerForm"
                        data-assign-path="{{ route('admin.program_registrations.assign', $registration, false) }}"
                        class="mt-4 flex flex-col md:flex-row md:items-center gap-3">
                        @csrf
                        <select name="case_manager_id" id="assignCaseManagerSelect"
                            class="w-full md:w-80 rounded-md px-3 py-2 text-sm text-[#213430] bg-white border border-[#91848C] focus:outline-none">
                            <option value="">Unassigned</option>
                            @foreach ($caseManagers as $manager)
                                <option value="{{ $manager->id }}" @selected($registration->assigned_case_manager_id === $manager->id)>
                                    {{ $manager->profile->full_name ?? $manager->email }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" id="assignCaseManagerSubmit"
                            class="px-4 py-2 bg-[#9E2469] text-white rounded-md text-sm font-medium hover:bg-[#B52D75] transition app-text">
                            Save Assignment
                        </button>
                    </div>
                    <div id="assignCaseManagerStatus" class="mt-3 text-sm text-[#6C5F67] app-text">
                        Current: <span id="assignCaseManagerCurrentName">{{ $registration->assignedCaseManager?->profile?->full_name ?? $registration->assignedCaseManager?->email ?? 'Unassigned' }}</span>
                        @if ($registration->assigned_at)
                            <span id="assignCaseManagerAssignedAt" class="text-[#91848C]">• assigned {{ $registration->assigned_at->timezone(config('app.timezone'))->format('d M Y, h:i A') }}</span>
                        @else
                            <span id="assignCaseManagerAssignedAt" class="text-[#91848C] hidden"></span>
                        @endif
                    </div>
                </div>
                @elseif (! $isMtm && $registration->registrationInvoices->isEmpty())
                <div class="bg-white rounded-lg p-5 md:p-6 border border-[#E6D8E1] min-w-0">
                    <h3 class="text-xl font-semibold text-[#213430] app-main">Assigned Case Manager</h3>
                    <p class="mt-3 text-sm text-[#6C5F67] app-text">
                        {{ $registration->assignedCaseManager?->profile?->full_name ?? $registration->assignedCaseManager?->email ?? 'Unassigned' }}
                        @if ($registration->assigned_at)
                            <span class="text-[#91848C]">• assigned {{ $registration->assigned_at->timezone(config('app.timezone'))->format('d M Y, h:i A') }}</span>
                        @endif
                    </p>
                </div>
                @elseif (! $isMtm)
                <div class="bg-white rounded-lg p-5 md:p-6 border border-[#E6D8E1] min-w-0">
                    <h3 class="text-xl font-semibold text-[#213430] app-main">Assigned Case Manager</h3>
                    <p class="mt-3 text-sm text-[#6C5F67] app-text">
                        {{ $registration->assignedCaseManager?->profile?->full_name ?? $registration->assignedCaseManager?->email ?? 'Unassigned' }}
                        @if ($registration->assigned_at)
                            <span class="text-[#91848C]">• assigned {{ $registration->assigned_at->timezone(config('app.timezone'))->format('d M Y, h:i A') }}</span>
                        @endif
                    </p>
                </div>
                @endif

                @if ($isMtm)
                <div class="registration-detail-section bg-[#FDF7FB] rounded-lg p-4 border border-[#E6D8E1] text-sm text-[#6C5F67] app-text w-full min-w-0">
                    <strong class="text-[#213430]">Moments That Matter</strong> — fulfilled by admin only. Mark as shipped when the care package has been sent (not assigned to patient coordinators or finance).
                </div>
                @endif

                {{-- Finance / Budget Allocation --}}
                @unless ($isMtm)
                <div id="finance" class="bg-white rounded-lg p-5 md:p-6 border border-[#E6D8E1] min-w-0">
                    <h3 class="text-xl font-semibold text-[#213430] app-main">Finance &amp; bills paid</h3>
                    <p class="text-sm text-[#6C5F67] app-text mt-1">Finance status and patient bills paid (invoices).</p>
                    <div class="mt-4 space-y-3">
                        @if ($registration->registrationInvoices->isNotEmpty())
                            <div class="flex items-center gap-2 text-green-700">
                                <i class="fas fa-check-circle"></i>
                                <span class="font-medium">Bills paid</span>
                            </div>
                            <div class="border border-[#E6D8E1] rounded-lg overflow-hidden">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-[#F7EEF3]">
                                        <tr>
                                            <th class="px-4 py-2 text-left font-semibold text-[#7B5B6B]">Invoice #</th>
                                            <th class="px-4 py-2 text-left font-semibold text-[#7B5B6B]">Purpose</th>
                                            <th class="px-4 py-2 text-left font-semibold text-[#7B5B6B]">Amount</th>
                                            <th class="px-4 py-2 text-left font-semibold text-[#7B5B6B]">Date</th>
                                            <th class="px-4 py-2 text-left font-semibold text-[#7B5B6B]">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($registration->registrationInvoices as $inv)
                                            <tr class="border-t border-[#E6D8E1]">
                                                <td class="px-4 py-2">
                                                    <a href="{{ route('admin.registration_invoices.show', [$registration, $inv]) }}" class="text-[#9E2469] hover:underline font-medium">
                                                        {{ $inv->invoice_number }}
                                                    </a>
                                                </td>
                                                <td class="px-4 py-2">{{ $inv->payment_purpose }}</td>
                                                <td class="px-4 py-2">${{ number_format($inv->amount, 2) }}</td>
                                                <td class="px-4 py-2">{{ $inv->issue_date?->format('M d, Y') ?? '—' }}</td>
                                                <td class="px-4 py-2">{{ $inv->status }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-sm text-[#6C5F67]">Finance user: {{ $registration->financeUser?->profile?->full_name ?? $registration->financeUser?->email ?? '—' }}</p>
                        @elseif ($registration->finance_user_id || ($status === \App\Models\ProgramRegistration::STATUS_PENDING_FINANCE && $registration->sent_to_finance_at))
                            <div class="flex items-center gap-2 text-amber-700">
                                <i class="fas fa-clock"></i>
                                <span class="font-medium">
                                    @if ($registration->finance_user_id)
                                        Sent to Finance
                                    @else
                                        Finance queue (open — any finance user can claim)
                                    @endif
                                </span>
                                @if ($registration->registrationInvoices->isEmpty())
                                    <span class="text-sm font-normal">— awaiting bills paid</span>
                                @endif
                            </div>
                            @if ($registration->finance_user_id)
                                <p class="text-sm text-[#6C5F67]">Assigned to: {{ $registration->financeUser?->profile?->full_name ?? $registration->financeUser?->email ?? '—' }}</p>
                            @endif
                            @if ($registration->sent_to_finance_at)
                                <p class="text-xs text-[#91848C]">Sent {{ $registration->sent_to_finance_at->timezone(config('app.timezone'))->format('d M Y, h:i A') }}</p>
                            @endif
                        @else
                            <p class="text-[#91848C]">Not yet routed to finance.</p>
                        @endif
                    </div>
                </div>
                @endunless

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full min-w-0">
                <div class="registration-detail-section bg-white rounded-lg p-5 md:p-6 space-y-3 border border-[#E6D8E1] w-full min-w-0 overflow-hidden">
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

                <div class="registration-detail-section bg-white rounded-lg p-5 md:p-6 space-y-3 border border-[#E6D8E1] w-full min-w-0 overflow-hidden">
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

            @include('partials.moments_that_matter_registration_details', ['registration' => $registration])

            @unless ($isMtm)
            <div class="registration-detail-section bg-white rounded-lg p-5 md:p-6 space-y-4 border border-[#E6D8E1] w-full min-w-0">
                <h3 class="text-xl font-semibold text-[#213430] app-main">Application Details</h3>
                <p class="text-base text-[#213430] app-text break-words"><span class="font-medium">Active Treatment:</span> {{ $registration->active_treatment ? 'Yes' : 'No' }}</p>
                <p class="text-base text-[#213430] app-text break-words"><span class="font-medium">Breast cancer stage:</span> {{ $registration->breast_cancer_stage ?? 'N/A' }}</p>
                <p class="text-base text-[#213430] app-text break-words"><span class="font-medium">Ethnicity:</span> {{ $registration->ethnicity ?: '—' }}</p>
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
                    @if (is_array($registration->patient_bill_line_items) && count($registration->patient_bill_line_items) > 0)
                        <div class="mt-4 overflow-x-auto">
                            <p class="font-medium text-[#213430] mb-2">Applicant bill table</p>
                            <p class="text-xs text-[#91848C] mb-2">Note: To ensure proper processing, the bill must be in the patient’s name to qualify for assistance.</p>
                            <table class="min-w-full text-sm border border-[#E6D8E1] rounded-md">
                                <thead class="bg-[#F3E8EF]"><tr>
                                    <th class="p-2 text-left">Service Provider Name</th>
                                    <th class="p-2 text-left">Bill Payment Link(s)</th>
                                    <th class="p-2 text-left">Amount Due</th>
                                    <th class="p-2 text-left">Type of Support Expenses</th>
                                    <th class="p-2 text-left">Provider Contact Information</th>
                                    <th class="p-2 text-left">Account Number</th>
                                    <th class="p-2 text-left">Notes (optional)</th>
                                </tr></thead>
                                <tbody>
                                    @foreach ($registration->patient_bill_line_items as $row)
                                        <tr class="border-t border-[#E6D8E1]">
                                            <td class="p-2">{{ $row['name'] ?? '' }}</td>
                                            <td class="p-2 break-all">{{ $row['url'] ?? '' }}</td>
                                            <td class="p-2">{{ $row['amount'] ?? '' }}</td>
                                            <td class="p-2">{{ $row['support_expense_type'] ?? '' }}</td>
                                            <td class="p-2">{{ $row['provider_contact'] ?? '' }}</td>
                                            <td class="p-2">{{ $row['account_number'] ?? '' }}</td>
                                            <td class="p-2">{{ $row['notes'] ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    @if (!empty($registration->payment_links))
                        <p class="text-base text-[#213430] app-text mt-2"><span class="font-medium">Billing payment links (case manager):</span></p>
                        <div class="text-base text-[#213430] app-text mt-1">
                            {!! \App\Support\BillingPaymentLinks::toHtml($registration->payment_links) !!}
                        </div>
                    @endif
                </div>
                <div class="text-base text-[#213430] app-text min-w-0">
                    <span class="font-medium">Signature:</span>
                    @if ($registration->signature)
                        <div class="mt-2">
                            <img src="{{ storage_url($registration->signature) }}" alt="Signature" class="h-24 max-w-full object-contain">
                        </div>
                    @else
                        <p class="text-[#6C5F67]">N/A</p>
                    @endif
                </div>
            </div>

            <div class="registration-detail-section bg-white rounded-lg p-5 md:p-6 border border-[#E6D8E1] w-full min-w-0">
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
            @endunless

            @if ($isMtm && in_array($registration->status, [\App\Models\ProgramRegistration::STATUS_PENDING, \App\Models\ProgramRegistration::STATUS_APPROVED], true))
                <div class="registration-detail-section bg-white rounded-lg p-5 md:p-6 space-y-6 border border-[#E6D8E1] w-full min-w-0">
                    <h3 class="text-xl font-semibold text-[#213430] app-main">Fulfillment — Moments That Matter</h3>
                    <p class="text-base text-[#6C5F67] app-text">When the care package has been prepared and sent, mark this application as shipped. The applicant will be notified.</p>
                    @php
                        $markShippedUrl = \Illuminate\Support\Facades\Route::has('admin.program_registrations.markShipped')
                            ? route('admin.program_registrations.markShipped', $registration)
                            : url('/admin/program-registration-requests/'.$registration->getKey().'/mark-shipped');
                    @endphp
                    @unless (\Illuminate\Support\Facades\Route::has('admin.program_registrations.markShipped'))
                        <p class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                            The server is missing the mark-shipped route. Deploy the latest <code class="text-xs">routes/web.php</code> and run <code class="text-xs">php artisan route:clear</code> on production.
                        </p>
                    @endunless
                    <form method="POST" action="{{ $markShippedUrl }}" data-action-loader class="bg-[#F8F2F6] rounded-lg p-4 space-y-3 border border-[#E6D8E1] max-w-xl">
                        @csrf
                        <label for="mtm_ship_note" class="font-semibold text-[#213430] app-main block">Optional note for the applicant</label>
                        <textarea id="mtm_ship_note" name="note" rows="3" class="w-full px-3 py-2 rounded-md border border-[#DCCFD8] bg-white text-base focus:outline-none focus:ring-2 focus:ring-[#9E2469]" placeholder="Optional tracking or message"></textarea>
                        <button type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-3 bg-[#1A6BB3] text-white rounded-md text-base font-semibold hover:bg-[#155A96] transition">
                            <span class="action-text">Mark as Shipped</span>
                            <span class="action-spinner" aria-hidden="true"></span>
                        </button>
                    </form>
                </div>
            @elseif ($isMtm && $registration->status === \App\Models\ProgramRegistration::STATUS_SHIPPED)
                <div class="registration-detail-section bg-white rounded-lg p-5 md:p-6 space-y-3 text-base text-[#213430] app-text border border-[#E6D8E1] w-full min-w-0">
                    <h3 class="text-xl font-semibold text-[#213430] app-main">Care package shipped</h3>
                    <p><span class="font-medium">Marked shipped by:</span> {{ $registration->shipper?->profile->full_name ?? $registration->shipper?->email ?? 'N/A' }}</p>
                    <p><span class="font-medium">Shipped at:</span> {{ $registration->shipped_at?->timezone(config('app.timezone'))->format('d M Y, h:i A') ?? 'N/A' }}</p>
                    @if ($registration->review_note)
                        <p><span class="font-medium">Note to applicant:</span> {{ $registration->review_note }}</p>
                    @endif
                </div>
            @elseif (! $isMtm && $registration->status === \App\Models\ProgramRegistration::STATUS_PENDING)
                <div class="registration-detail-section bg-white rounded-lg p-5 md:p-6 space-y-6 border border-[#E6D8E1] w-full min-w-0">
                    <h3 class="text-xl font-semibold text-[#213430] app-main">Admin Review</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <form method="POST" action="{{ route('admin.program_registrations.approve', $registration) }}" data-action-loader class="bg-[#F8F2F6] rounded-lg p-4 space-y-3 border border-[#E6D8E1]">
                            @csrf
                            <h4 class="font-semibold text-[#213430] app-main">Approve Application</h4>
                            <p class="text-base text-[#6C5F67] app-text">Optional: add a short note for the applicant.</p>
                            <textarea name="note" rows="3" class="w-full px-3 py-2 rounded-md border border-[#DCCFD8] bg-white text-base focus:outline-none focus:ring-2 focus:ring-[#9E2469]" placeholder="Optional note"></textarea>
                            <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-3 bg-[#20B354] text-white rounded-md text-base font-semibold hover:bg-[#1A9444] transition">
                                <span class="action-text">Approve Request</span>
                                <span class="action-spinner" aria-hidden="true"></span>
                            </button>
                        </form>
                    </div>
                </div>
            @elseif (! $isMtm)
                    <div class="registration-detail-section bg-white rounded-lg p-5 md:p-6 space-y-3 text-base text-[#213430] app-text border border-[#E6D8E1] w-full min-w-0">
                        <h3 class="text-xl font-semibold text-[#213430] app-main">Review Summary</h3>
                        <p><span class="font-medium">Reviewed By:</span> {{ $registration->reviewer?->profile->full_name ?? $registration->reviewer?->email ?? 'N/A' }}</p>
                        <p><span class="font-medium">Reviewed At:</span> {{ $registration->reviewed_at?->timezone(config('app.timezone'))->format('d M Y, h:i A') ?? 'N/A' }}</p>
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
                    <a href="{{ route('admin.registrations.index', [
                        'program_type' => $isMtm ? \App\Support\ProgramType::MOMENTS_THAT_MATTER : ($registration->program?->program_type ?? 'all'),
                        'program_status' => $status === \App\Models\ProgramRegistration::STATUS_PENDING ? 'pending' : 'all',
                    ]) }}"
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
    </div>

    <div id="toastContainer" class="fixed top-8 right-8 z-[99999] flex flex-col gap-3" style="min-width: 250px; max-width: 400px;"></div>
@endsection

@push('scripts')
    <script>
        function showAssignToast(message, type) {
            const container = document.getElementById('toastContainer');
            if (!container || !message) return;
            const toast = document.createElement('div');
            toast.className = 'toast-msg toast-' + type;
            toast.innerHTML = '<span>' + (type === 'success' ? '✓' : '!') + '</span><div style="flex:1">' + message + '</div><button class="toast-close" aria-label="Close">&times;</button>';
            container.appendChild(toast);
            toast.querySelector('.toast-close').addEventListener('click', function() {
                toast.style.opacity = '0';
                setTimeout(function() { toast.remove(); }, 300);
            });
            setTimeout(function() {
                toast.style.opacity = '0';
                setTimeout(function() { toast.remove(); }, 400);
            }, 3500);
        }

        function readCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content
                || document.querySelector('#assignCaseManagerForm input[name="_token"]')?.value
                || document.querySelector('input[name="_token"]')?.value
                || '';
        }

        async function refreshCsrfToken() {
            const appUrl = (document.querySelector('meta[name="app-url"]')?.getAttribute('content') || '').replace(/\/+$/, '');
            const url = appUrl ? appUrl + '/session/csrf-token' : '/session/csrf-token';
            try {
                const res = await fetch(url, {
                    credentials: 'same-origin',
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) return null;
                const data = await res.json();
                const token = data?.token || null;
                if (token) {
                    const meta = document.querySelector('meta[name="csrf-token"]');
                    if (meta) meta.setAttribute('content', token);
                    document.querySelectorAll('input[name="_token"]').forEach(function(input) {
                        input.value = token;
                    });
                }
                return token;
            } catch {
                return null;
            }
        }

        function resolveAssignUrl(container) {
            const path = container.getAttribute('data-assign-path') || '';
            if (!path) {
                return '';
            }
            if (path.startsWith('http://') || path.startsWith('https://')) {
                try {
                    const parsed = new URL(path);
                    return window.location.origin + parsed.pathname + parsed.search;
                } catch {
                    return path;
                }
            }
            const normalized = path.startsWith('/') ? path : '/' + path;
            return window.location.origin + normalized;
        }

        function buildAssignFormData(container) {
            const formData = new FormData();
            const select = container.querySelector('select[name="case_manager_id"]');
            const tokenInput = container.querySelector('input[name="_token"]');
            if (select) {
                formData.set('case_manager_id', select.value);
            }
            if (tokenInput) {
                formData.set('_token', tokenInput.value);
            }
            return formData;
        }

        async function submitCaseManagerAssignment(container, csrfToken) {
            const assignUrl = resolveAssignUrl(container);
            if (!assignUrl || !assignUrl.includes('/assign')) {
                throw new Error('Assignment URL is missing. Please refresh the page and try again.');
            }
            const formData = buildAssignFormData(container);
            if (csrfToken) {
                formData.set('_token', csrfToken);
            }
            const token = csrfToken || readCsrfToken();
            const response = await fetch(assignUrl, {
                method: 'POST',
                credentials: 'same-origin',
                redirect: 'manual',
                headers: {
                    'X-CSRF-TOKEN': token,
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });
            if (response.status === 419) {
                const fresh = await refreshCsrfToken();
                if (fresh) {
                    return submitCaseManagerAssignment(container, fresh);
                }
                throw new Error('Your session expired. Please refresh the page and try again.');
            }
            const data = await response.json().catch(function() { return {}; });
            if (response.status >= 200 && response.status < 300) {
                if (data.success === false) {
                    throw new Error(data.message || 'Failed to save case manager assignment.');
                }
                return data;
            }
            if (response.status >= 300 && response.status < 400) {
                return {
                    success: true,
                    message: data.message || 'Case manager assignment updated.',
                    data: data.data || {},
                };
            }
            throw new Error(data.message || 'Failed to save case manager assignment.');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const assignContainer = document.getElementById('assignCaseManagerForm');
            const submitBtn = document.getElementById('assignCaseManagerSubmit');
            const selectEl = document.getElementById('assignCaseManagerSelect');
            let assignInFlight = false;

            function selectedManagerName() {
                if (!selectEl) {
                    return 'Unassigned';
                }
                const option = selectEl.options[selectEl.selectedIndex];
                return option?.text?.trim() || 'Unassigned';
            }

            function runAssign() {
                if (!assignContainer || assignInFlight) {
                    return;
                }
                assignInFlight = true;
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Saving...';
                }
                submitCaseManagerAssignment(assignContainer, readCsrfToken())
                    .then(function(data) {
                        showAssignToast(data.message || 'Case manager assignment updated.', 'success');
                        const nameEl = document.getElementById('assignCaseManagerCurrentName');
                        const assignedName = data.data?.assigned_name || selectedManagerName();
                        if (nameEl) {
                            nameEl.textContent = assignedName;
                        }
                        const atEl = document.getElementById('assignCaseManagerAssignedAt');
                        if (atEl && data.data?.assigned_case_manager_id) {
                            atEl.textContent = '• assigned just now';
                            atEl.classList.remove('hidden');
                        } else if (atEl && !data.data?.assigned_case_manager_id) {
                            atEl.textContent = '';
                            atEl.classList.add('hidden');
                        }
                    })
                    .catch(function(error) {
                        if (error?.name === 'AbortError') {
                            return;
                        }
                        showAssignToast(error.message || 'An error occurred.', 'error');
                    })
                    .finally(function() {
                        assignInFlight = false;
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Save Assignment';
                        }
                    });
            }

            if (submitBtn && assignContainer) {
                submitBtn.addEventListener('click', runAssign);
            }
        });

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
