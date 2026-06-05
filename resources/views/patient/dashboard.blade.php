@php
    $user = auth()->user();
    $profile = $user->profile;
    $fullName = $profile ? $profile->full_name : 'Unknown User';

@endphp

@extends('patient.layouts.app')

@section('title', 'Patient-Dashboard')

@section('content')

    <!-- Dashboard Content -->
    <main class="flex-1">
        @if (!empty($financialAssistanceClosed))
            <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 md:p-5 shadow-sm">
                <p class="text-sm text-[#213430] app-text whitespace-pre-line">{{ \App\Support\ProgramApplicationCapacity::CLOSED_MESSAGE }}</p>
            </div>
        @else
            <div class="mb-6 rounded-xl border border-[#EADFF0] bg-gradient-to-r from-[#FDF7FB] via-[#FFF8FC] to-[#F3E8EF] p-4 md:p-5 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-[#9E2469] app-text">Financial assistance</p>
                        <h2 class="mt-1 text-lg font-semibold text-[#213430] app-main">Programs &amp; Services — apply for support</h2>
                        <p class="mt-1 text-sm text-[#6C5F67] app-text">Open the programs page to review eligibility and submit your application.</p>
                    </div>
                    <a href="{{ route('patient.programsAndAids') }}"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-[#9E2469] px-5 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-[#B52D75] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:ring-offset-2 app-text">
                        Apply here
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        @endif

        <div class="grid gap-6 tab-con grid-cols-[1.5fr_1.5fr] sm:grid-cols-[1.5fr_1.5fr] grid-cols-none">

            <!-- User Profile Card -->
            <div class="bg-[#F3E8EF] rounded-lg p-6">
                <div class="flex flex-col items-center mb-6">
                    <div class="w-32 h-32 rounded-full overflow-hidden mb-4">
                        <img src="{{ auth()->user()->avatar_url }}" alt="User Avatar" class="w-full h-full object-cover" />
                    </div>
                    <h3 class="text-lg font-medium app-main">{{ $patient->user->profile->full_name ?? 'N/A' }}</h3>
                    <p class="text-sm text-[#91848C] app-text">
                        {{ $patient->user->profile->date_of_birth ? \Carbon\Carbon::parse($patient->user->profile->date_of_birth)->age : 'N/A' }}
                        years, {{ $patient->user->profile->location ?? 'N/A' }}</p>
                </div>

                <div class="grid grid-cols-2 gap-6 text-center">
                    <div>
                        <p class="text-sm text-[#9E2469] mb-1 app-text">Gender</p>
                        <p class="text-md font-medium app-text">{{ ucfirst($patient->user->profile->gender ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-[#9E2469] mb-1 app-text">Date of Birth</p>
                        <p class="font-medium app-text">
                            {{ $patient->user->profile->date_of_birth ? \Carbon\Carbon::parse($patient->user->profile->date_of_birth)->format('d/m/Y') : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Application Status Card -->
            @php
                $inReviewCount = $stats['in_review_applications'] ?? ($stats['pending_applications'] ?? 0);
                $lastSubmission = $stats['last_application_date'] ?? 'N/A';
                $latestStatusRaw = $stats['latest_application_status'] ?? 'N/A';
                $latestStatus =
                    $latestStatusRaw !== 'N/A'
                        ? trim(\Illuminate\Support\Str::title(strtolower($latestStatusRaw)))
                        : 'N/A';
                $latestCode = $stats['latest_application_code'] ?? null;
                $latestProgram = $stats['latest_program_title'] ?? null;
                $latestBreastCancerStage = $stats['latest_breast_cancer_stage'] ?? null;
                $latestId = $stats['latest_application_id'] ?? null;
                $latestItemType = $stats['latest_item_type'] ?? 'application';
                $hasLatest = (bool) ($stats['has_submission'] ?? !empty($latestId));
                $detailUrl = $hasLatest
                    ? ($latestItemType === 'registration'
                        ? route('patient.programRegistrations.show', $latestId)
                        : route('patient.viewApplication', $latestId))
                    : route('patient.createApplication');
                $detailLabel = $hasLatest ? 'View Latest Application' : 'Start a New Application';
            @endphp
            <div class="bg-[#F3E8EF] rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-2 app-main">
                    Application Review Status
                </h3>
                <p class="text-md text-[#91848C] mb-6 border-b pb-4 border-[#DCCFD8] app-text">
                    @if ($hasLatest && !empty($latestStatus) && $latestStatus !== 'N/A')
                        Your most recent application
                        <span class="font-semibold text-[#213430]">{{ $latestCode }}</span>
                        is currently <span class="text-[#9E2469] font-medium">{{ $latestStatus }}</span>.
                    @else
                        You haven't submitted any applications yet. Start one to begin the review process.
                    @endif
                </p>
                <div class="space-y-6">
                    <div class="flex justify-between">
                        <span class="text-md font-semibold text-[#213430] app-text">Applications Awaiting Review</span>
                        <span class="font-normal text-[#91848C] app-text">{{ $inReviewCount }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-md font-semibold text-[#213430] app-text">Last Application Submitted On</span>
                        <span class="font-normal text-[#91848C] app-text">{{ $lastSubmission }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-md font-semibold text-[#213430] app-text">Latest Application Status</span>
                        <span class="font-normal text-[#91848C] app-text">{{ $latestStatus }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-md font-semibold text-[#213430] app-text">Program</span>
                        <span class="font-normal text-[#91848C] app-text">{{ $latestProgram ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-md font-semibold text-[#213430] app-text">Stage of Breast Cancer</span>
                        <span class="font-normal text-[#91848C] app-text">{{ $latestBreastCancerStage ?: 'N/A' }}</span>
                    </div>
                    @if (! $hasLatest && empty($financialAssistanceClosed))
                        <div class="pt-2 border-t border-[#DCCFD8]">
                            <p class="text-sm text-[#91848C] app-text mb-3">Ready to apply? Go to Programs &amp; Services and tap <span class="font-medium text-[#213430]">Apply here</span> on the menu or use the button below.</p>
                            <a href="{{ route('patient.programsAndAids') }}"
                                class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-lg bg-[#9E2469] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#B52D75] transition app-text">
                                Apply here — Programs &amp; Services
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    @endif
                    <!--<div class="flex justify-between items-center gap-4">-->
                    <!--    <span class="text-md font-semibold text-[#213430] app-text">Your Application Details</span>-->
                    <!--    <a href="{{ $detailUrl }}"-->
                    <!--        class="inline-flex items-center gap-2 px-4 py-2 rounded-md border border-[#9E2469] text-[#9E2469] hover:bg-[#9E2469] hover:text-white transition app-text">-->
                    <!--        {{ $detailLabel }}-->
                    <!--        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"-->
                    <!--            stroke="currentColor">-->
                    <!--            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"-->
                    <!--                d="M9 5l7 7-7 7" />-->
                    <!--        </svg>-->
                    <!--    </a>-->
                    <!--</div>-->
                </div>
            </div>

            <!-- Illustration -->
            <!--<div class="bg-[#F3E8EF] rounded-lg p-6 flex justify-center items-center tab-board">-->
            <!--    <img src="{{ asset('public/images/D-illustration.png') }}" alt="Review Process Illustration"-->
            <!--        class="max-h-72 object-contain" />-->
            <!--</div>-->
        </div>
    </main>

    <script src="{{ asset('js/patient/dashboard.js') }}"></script>

@endsection
