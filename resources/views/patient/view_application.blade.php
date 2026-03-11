@extends('patient.layouts.app')


@section('title', 'View Application')


@section('content')

    <!-- Dashboard Content -->
    <main>
        <div class="max-w-8xl mx-auto mt-6 px-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 info-patient">
                <!-- Application Card 1 -->
                <div class="bg-[#F3E8EF] p-6 rounded-xl w-full info-patient-1">
                    <h3 class="text-lg font-semibold text-[#213430] mb-3 app-main">
                        Patient Info
                    </h3>
                    <hr class="border-[#DCCFD8] mb-4" />
                    <div class="space-y-5">
                        <div class="flex justify-between">
                            <span class="text-[#213430] text-[16px] font-medium app-text">Full Name</span>
                            <span class="text-[#91848C] text-[16px] font-normal app-text">{{ $application->patient->user->profile->full_name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#213430] text-[16px] font-medium app-text">Patient ID</span>
                            <span class="text-[#91848C] text-[16px] font-normal app-text">{{ $application->patient->id ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#213430] text-[16px] font-medium app-text">Email</span>
                            <span class="text-[#91848C] text-[16px] font-normal app-text">{{ $application->patient->user->email ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#213430] text-[16px] font-medium app-text">Date of Birth</span>
                            <span class="text-[#91848C] text-[16px] font-normal app-text">{{ $application->patient->user->profile->date_of_birth ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#213430] text-[16px] font-medium app-text">Age</span>
                            <span class="text-[#91848C] text-[16px] font-normal app-text">
                                @if(isset($application->patient->user->profile->date_of_birth))
                                    {{ \Carbon\Carbon::parse($application->patient->user->profile->date_of_birth)->age }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Application Card 2 -->
                <div class="bg-[#F3E8EF] p-6 rounded-xl w-full info-patient-2">
                    <h3 class="text-lg font-semibold text-[#213430] mb-3 app-main">
                        Application Info
                    </h3>
                    <hr class="border-[#DCCFD8] mb-4" />
                    <div class="space-y-5">
                        <div class="flex justify-between">
                            <span class="text-[#213430] text-[16px] font-medium app-text">App Title</span>
                            <span class="text-[#91848C] text-[16px] font-normal app-text">{{ $application->title }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#213430] text-[16px] font-medium app-text">Submission Date</span>
                            <span class="text-[#91848C] text-[16px] font-normal app-text">{{ $application->submission_date ? \Carbon\Carbon::parse($application->submission_date)->format('F d, Y') : 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#213430] text-[16px] font-medium app-text">Application ID</span>
                            <span class="text-[#91848C] text-[16px] font-normal app-text">{{ $application->id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#213430] text-[16px] font-medium app-text">Application Status</span>
                            <span class="text-[#91848C] text-[16px] font-normal app-text">{{ $application->status }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#213430] text-[16px] font-medium app-text">Assigned Reviewer</span>
                            <span class="text-[#91848C] text-[16px] font-normal app-text">{{ $application->reviewer->profile->full_name ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Application Card 3 -->
                <div class="bg-[#F3E8EF] p-6 rounded-xl w-full info-patient-3">
                    <h3 class="text-lg font-semibold text-[#213430] mb-3 app-main">
                        Program & Description
                    </h3>
                    <hr class="border-[#DCCFD8] mb-4" />
                    <div class="space-y-5">
                        <div class="flex justify-between">
                            <span class="text-[#213430] text-[16px] font-medium app-text">Program</span>
                            <span class="text-[#91848C] text-[16px] font-normal app-text">{{ $application->program->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[#213430] text-[16px] font-medium app-text mb-1">Description</span>
                            <span class="text-[#91848C] text-[16px] font-normal app-text">{{ $application->description ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="flex gap-6 max-w-8xl mx-auto mt-2 mb-12 px-5 patient-disease">
            <!-- Disease Overview Section -->
            <div class="bg-[#F3E8EF] rounded-lg p-6 w-2/3 mt-6 patient-disease-1">
                <h2 class="text-[#213430] text-lg font-semibold app-main">
                    Status & Reason
                </h2>

                <hr class="border-[#DCCFD8] mt-4 mb-4" />

                <h3 class="text-[#213430] text-md font-semibold mb-2 app-main">
                    Status:
                </h3>
                <p class="text-[#91848C] text-sm font-light mb-6 mr-16 app-text">
                    {{ $application->status }}
                </p>

                @if($application->status === 'Rejected' && $application->rejection_reason)
                <hr class="border-[#DCCFD8] mb-4" />
                <h3 class="text-[#213430] text-md font-semibold mb-2 app-main">
                    Rejection Reason:
                </h3>
                <p class="text-[#91848C] text-sm font-light mb-6 mr-16 app-text">
                    {{ $application->rejection_reason }}
                </p>
                @endif
            </div>

            <!-- Files Section -->
            <div class="bg-[#F3E8EF] rounded-lg p-6 w-1/3 mt-6 patient-disease-2">
                <div class="flex justify-between items-center">
                    <div class="flex flex-col ">
                        <h2 class="text-[#213430] text-lg font-semibold app-main">Files</h2>
                        <p class="text-[14px] text-[#91848C] font-light">Securely view your documents</p>
                    </div>
                </div>

                <hr class="border-[#DCCFD8] mb-4 mt-4" />

                <ul class="space-y-6">
                    @forelse($application->documents as $doc)
                        <li class="flex justify-between items-center">
                            <div class="flex items-center gap-2 group cursor-pointer">
                                <img src="{{ asset('public/images/document.svg') }}" alt="" class="w-6 h-6 block group-hover:hidden" />
                                <img src="{{ asset('public/images/document-pink.svg') }}" alt=""
                                    class="w-6 h-6 hidden group-hover:block" />
                                <span class="text-[#91848C] text-md font-light app-text group-hover:text-[#9E2469]">
                                    {{ $doc->filename }}
                                </span>
                            </div>
                            <div class="flex items-center">
                                <a href="{{ asset('storage/' . ltrim($doc->filepath, '/')) }}" target="_blank" class="text-pink-500 mr-2 group relative w-6 h-6">
                                    <img src="{{ asset('public/images/eye.svg') }}" alt="" class="w-6 h-6 block group-hover:hidden" />
                                    <img src="{{ asset('public/images/eye-pink.svg') }}" alt=""
                                        class="w-6 h-6 hidden group-hover:block absolute top-0 left-0" />
                                </a>
                            </div>
                        </li>
                    @empty
                        <li class="text-[#91848C] text-md font-light app-text">No documents uploaded.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </main>

@endsection
<script src="{{ asset('js/patient/dashboard.js') }}"></script>
