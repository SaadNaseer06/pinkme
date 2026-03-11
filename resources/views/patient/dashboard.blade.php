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

                <div class="grid grid-cols-3 gap-6 text-center">
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
                    <div>
                        <p class="text-sm text-[#9E2469] mb-1 app-text">Condition</p>
                        <p class="font-medium app-text">{{ $patient->diagnosis ?? 'N/A' }}</p>
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
                $latestId = $stats['latest_application_id'] ?? null;
                $hasLatest = !empty($latestId);
                $detailUrl = $hasLatest
                    ? route('patient.viewApplication', $latestId)
                    : route('patient.createApplication');
                $detailLabel = $hasLatest ? 'View Latest Application' : 'Start a New Application';
                $reviewEta = $inReviewCount > 0 ? 'Typically 5-7 business days' : 'No applications awaiting review';
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
                        <span class="text-md font-semibold text-[#213430] app-text">Estimated Time for Review</span>
                        <span class="font-normal text-[#91848C] app-text">{{ $reviewEta }}</span>
                    </div>
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
