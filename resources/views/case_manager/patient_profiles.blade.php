@extends('case_manager.layouts.app')

@section('title', 'Patient Profiles')

@section('content')
    <!--- Main -->
    <main class="flex-1 p-4 sm:p-6 w-full min-w-0 overflow-x-hidden">
        <div class="mb-6 bg-[#F3E8EF] p-4 rounded-lg">
            <div class="text-[#213430] app-main mb-3">
                Patient profiles
            </div>
            <form method="GET" action="{{ route('case_manager.patientProfiles') }}" class="flex flex-col sm:flex-row gap-2 w-full min-w-0">
                <div class="relative flex-1 min-w-0">
                    <label for="patientProfileSearch" class="sr-only">Search patients</label>
                    <input type="search" name="search" id="patientProfileSearch" value="{{ request('search') }}"
                        placeholder="Search by name, state, phone, or email"
                        autocomplete="off"
                        class="w-full min-w-0 rounded-md px-3 py-2 pl-10 text-sm text-[#213430] bg-white border border-[#91848C] placeholder:text-[#91848C] focus:outline-none focus:border-[#9E2469] focus:ring-1 focus:ring-[#9E2469]" />
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#91848C]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                        </svg>
                    </span>
                </div>
                <div class="flex gap-2 shrink-0">
                    <button type="submit" class="px-4 py-2 bg-[#9E2469] text-white rounded-md text-sm font-medium hover:bg-[#B52D75] transition">Search</button>
                    @if (request()->filled('search'))
                        <a href="{{ route('case_manager.patientProfiles') }}" class="px-4 py-2 border border-[#DCCFD8] text-[#91848C] rounded-md text-sm hover:bg-[#F9EFF5] transition">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6 sm:gap-8 bg-[#FFF8FC] cards-gap w-full min-w-0">
            @forelse ($patientUsers as $user)
                @php
                    $profile = $user->profile;
                    $registration = $user->programRegistrations->first();
                    $application = $user->patient?->applications->first();
                    $displayName = optional($profile)->full_name ?? ($registration?->full_name) ?? $user->email;
                    $dobSource = optional($profile)->date_of_birth ?? $registration?->dob;
                @endphp

                <div class="bg-[#F3E8EF] p-6 rounded-2xl text-center min-w-0">
                    <div class="flex justify-center">
                        <div class="w-28 h-28 rounded-full p-1">
                            <div class="w-full h-full rounded-full overflow-hidden bg-white flex items-center justify-center">
                                <img src="{{ $user->avatar_url }}"
                                    alt="Profile" class="object-cover w-full h-full" />
                            </div>
                        </div>
                    </div>

                    <h2 class="text-lg font-medium text-[#213430] cards-h truncate px-1" title="{{ $displayName }}">
                        {{ $displayName }}
                    </h2>

                    <p class="text-sm text-[#91848C] mt-1 cards-p">
                        {{ $dobSource ? \Carbon\Carbon::parse($dobSource)->age . ' years' : '—' }}
                    </p>

                    <p class="text-[#9E2469] text-sm mt-1 cards-p truncate px-1" title="{{ $user->email }}">
                        {{ $user->email }}
                    </p>

                    {{-- <p class="text-[#91848C] font-light text-sm mt-2 cards-p">
                        {{ $profile->about ?? '—' }}
                    </p> --}}

                    <div class="flex justify-center gap-2 mt-4 cards-icon">
                        <i
                            class="fab fa-facebook-f bg-[#EBDAE5] px-3 py-2 rounded-md text-[#9E2469]
                            hover:text-white hover:bg-[#9E2469] cursor-pointer"></i>
                        <i
                            class="fab fa-twitter bg-[#EBDAE5] px-3 py-2 rounded-md text-[#9E2469]
                            hover:text-white hover:bg-[#9E2469] cursor-pointer"></i>
                        <i
                            class="fab fa-instagram bg-[#EBDAE5] px-3 py-2 rounded-md text-[#9E2469]
                            hover:text-white hover:bg-[#9E2469] cursor-pointer"></i>
                        <i
                            class="fab fa-google bg-[#EBDAE5] px-3 py-2 rounded-md text-[#9E2469]
                            hover:text-white hover:bg-[#9E2469] cursor-pointer"></i>
                    </div>

                    @if ($application || $registration)
                        <a href="{{ $application ? route('case_manager.viewAssignedApplication', $application->id) : route('case_manager.program_registrations.show', $registration) }}">
                            <button
                                class="mt-6 bg-[#EBDAE5] text-[#9E2469] px-6 py-2 rounded-md text-sm font-medium
                                hover:text-white hover:bg-[#9E2469] cards-btn">
                                View Details
                            </button>
                        </a>
                    @endif
                </div>
            @empty
                <div class="col-span-full text-center text-[#91848C] py-8">
                    No patients found assigned to you. Program-only applicants appear here once they have an account linked to the application.
                </div>
            @endforelse
        </div>

        @if ($patientUsers->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $patientUsers->withQueryString()->links() }}
            </div>
        @endif
    </main>

    <script src="{{ asset('js/case_manager/dashboard.js') }}"></script>
@endsection
