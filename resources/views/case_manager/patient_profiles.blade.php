@extends('case_manager.layouts.app')

@section('title', 'Patient Profiles')

@section('content')
    <!--- Main -->
    <main class="flex-1 p-6">
        <div class="mb-6 bg-[#F3E8EF] p-4 rounded-lg">
            <div class="text-[#213430] app-main">
                Patients Profiles List
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 bg-[#FFF8FC] cards-gap">
            @forelse ($patients as $patient)
                @php
                    $user = $patient->user;
                    $profile = $user->profile;
                    $application = $patient->applications->first(); // latest assigned application
                @endphp

                <div class="bg-[#F3E8EF] p-6 rounded-2xl text-center">
                    <div class="flex justify-center">
                        <div class="w-28 h-28 rounded-full p-1">
                            <div class="w-full h-full rounded-full overflow-hidden bg-white flex items-center justify-center">
                                <img src="{{ $profile && $profile->avatar ? asset('storage/' . $profile->avatar) : asset('images/profile.png') }}"
                                    alt="Profile" class="object-cover" />
                            </div>
                        </div>
                    </div>

                    <h2 class="text-lg font-medium text-[#213430] cards-h">
                        {{ $profile->full_name ?? 'Unknown' }}
                    </h2>

                    <p class="text-sm text-[#91848C] mt-1 cards-p">
                        {{ optional($patient->user->profile)->date_of_birth ? \Carbon\Carbon::parse($patient->user->profile->date_of_birth)->age . ' years' : '—' }}
                    </p>

                    <p class="text-[#DB69A2] text-sm mt-1 cards-p">
                        {{ $user->email }}
                    </p>

                    {{-- <p class="text-[#91848C] font-light text-sm mt-2 cards-p">
                        {{ $profile->about ?? '—' }}
                    </p> --}}

                    <div class="flex justify-center gap-2 mt-4 cards-icon">
                        <i
                            class="fab fa-facebook-f bg-[#EBDAE5] px-3 py-2 rounded-md text-[#DB69A2]
                            hover:text-white hover:bg-[#db69a2] cursor-pointer"></i>
                        <i
                            class="fab fa-twitter bg-[#EBDAE5] px-3 py-2 rounded-md text-[#DB69A2]
                            hover:text-white hover:bg-[#db69a2] cursor-pointer"></i>
                        <i
                            class="fab fa-instagram bg-[#EBDAE5] px-3 py-2 rounded-md text-[#DB69A2]
                            hover:text-white hover:bg-[#db69a2] cursor-pointer"></i>
                        <i
                            class="fab fa-google bg-[#EBDAE5] px-3 py-2 rounded-md text-[#DB69A2]
                            hover:text-white hover:bg-[#db69a2] cursor-pointer"></i>
                    </div>

                    @if ($application)
                        <a href="{{ route('case_manager.viewAssignedApplication', $application->id) }}">
                            <button
                                class="mt-6 bg-[#EBDAE5] text-[#DB69A2] px-6 py-2 rounded-md text-sm font-medium
                                hover:text-white hover:bg-[#db69a2] cards-btn">
                                View Application
                            </button>
                        </a>
                    @endif
                </div>
            @empty
                <div class="col-span-full text-center text-[#91848C] py-8">
                    No patients found assigned to you.
                </div>
            @endforelse
        </div>
    </main>

    <script src="{{ asset('js/case_manager/dashboard.js') }}"></script>
@endsection
