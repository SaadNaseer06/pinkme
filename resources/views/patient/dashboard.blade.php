@extends('patient.layouts.app')

@section('title', 'Patient-Dashboard')

@section('content')

    <!-- Dashboard Content -->
    <main class="flex-1">
        <div class="grid gap-6 tab-con grid-cols-[1.5fr_1.5fr_1fr] sm:grid-cols-[1.5fr_1.5fr_1fr] grid-cols-none">

            <!-- User Profile Card -->
            <div class="bg-[#F3E8EF] rounded-lg p-6">
                <div class="flex flex-col items-center mb-6">
                    <div class="w-32 h-32 rounded-full overflow-hidden mb-4">
                        <img src="{{ asset('/images/D-profile.png') }}" alt="User Avatar" class="w-full h-full object-cover" />
                    </div>
                    <h3 class="text-lg font-medium app-main">{{ $patient->user->profile->full_name ?? 'N/A' }}</h3>
                    <p class="text-sm text-[#91848C] app-text">{{ $patient->user->profile->date_of_birth ? \Carbon\Carbon::parse($patient->user->profile->date_of_birth)->age : 'N/A' }} years, {{ $patient->user->profile->location ?? 'N/A' }}</p>
                </div>

                <div class="grid grid-cols-3 gap-6 text-center">
                    <div>
                        <p class="text-sm text-[#DB69A2] mb-1 app-text">Gender</p>
                        <p class="text-md font-medium app-text">{{ ucfirst($patient->user->profile->gender ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-[#DB69A2] mb-1 app-text">Date of Birth</p>
                        <p class="font-medium app-text">{{ $patient->user->profile->date_of_birth ? \Carbon\Carbon::parse($patient->user->profile->date_of_birth)->format('d/m/Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-[#DB69A2] mb-1 app-text">Condition</p>
                        <p class="font-medium app-text">{{ $patient->diagnosis ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Application Status Card -->
            <div class="bg-[#F3E8EF] rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-2 app-main">
                    Application Review Status
                </h3>
                <p class="text-md text-[#91848C] mb-6 border-b pb-4 border-[#DCCFD8] app-text">
                    Your application is being reviewed by our team
                </p>
                <div class="space-y-6">
                    <div class="flex justify-between">
                        <span class="text-md font-semibold text-[#213430] app-text">Total Applications Under Review</span>
                        <span class="font-normal text-[#91848C] app-text">{{ $stats['total_applications'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-md font-semibold text-[#213430] app-text">Last Application Submitted On</span>
                        <span class="font-normal text-[#91848C] app-text">{{ $stats['last_application_date'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-md font-semibold text-[#213430] app-text">Estimated Time for Review</span>
                        <span class="font-normal text-[#91848C] app-text">In 5-7 Days</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-md font-semibold text-[#213430] app-text">Your Application Details</span>
                        <a href="#" class="text-pink font-normal text-[#91848C] hover:underline app-text">View</a>
                    </div>
                </div>
            </div>

            <!-- Illustration -->
            <div class="bg-[#F3E8EF] rounded-lg p-6 flex justify-center items-center tab-board">
                <img src="{{ asset('/images/D-illustration.png') }}" alt="Review Process Illustration" class="max-h-72 object-contain" />
            </div>
        </div>
    </main>

    <script src="{{ asset('js/patient/dashboard.js') }}"></script>

@endsection
