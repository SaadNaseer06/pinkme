@extends('patient.layouts.app')

@section('title', 'Programs & Aids')

@section('content')
    @php
        // Fetch IDs of programs the current user has registered for
        $registeredProgramIds = \App\Models\ProgramRegistration::where('user_id', auth()->id())
            ->pluck('program_id')
            ->toArray();
        // $upcomingPrograms and $ongoingPrograms come from controller (effective date-based status)
    @endphp
    <main class="flex-1 overflow-hidden">
        {{-- @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-md mb-6" role="alert">
                <h4 class="font-semibold mb-1">Thank you!</h4>
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if ($errors->any())
            <div class="text-red-500 mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}

        <!-- All Programs Header -->
        <div class="bg-[#F3E8EF] p-4 rounded-lg mb-6">
            <h2 class="text-lg font-medium text-[#91848C] app-main">All Programs</h2>
        </div>

        <!-- Upcoming Programs Section -->
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-[#213430] mb-4 program-main">
                Upcoming Programs
            </h2>

            @forelse($upcomingPrograms as $program)
                <div class="bg-[#F3E8EF] rounded-lg p-4 mb-4 flex items-center justify-between md:flex hidden">
                    <div class="flex items-center">
                        <div
                            class="flex flex-col items-center justify-center w-20 h-20 border-2 border-pink rounded-lg mr-4 bg-[#FFF7FC]">
                            <span
                                class="text-sm text-pink">{{ \Carbon\Carbon::parse($program->event_date)->format('M') }}</span>
                            <span
                                class="text-4xl font-bold text-pink">{{ \Carbon\Carbon::parse($program->event_date)->format('d') }}</span>
                        </div>
                        <div class="w-20 h-20 rounded-lg overflow-hidden mr-4">
                            @php
                                $bannerUrl = $program->banner ? asset('storage/' . ltrim($program->banner, '/')) : asset('public/images/program-3.png');
                                $fallbackImg = asset('public/images/program-3.png');
                            @endphp
                            <img src="{{ $bannerUrl }}" alt="{{ $program->title }}"
                                class="w-full h-full object-cover" onerror="this.src='{{ $fallbackImg }}'" />
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-[#213430] mb-1 program-h">{{ $program->title }}</h3>
                            <p class="text-sm text-[#91848C] program-p">{{ $program->description }}</p>
                        </div>
                    </div>
                    <button onclick="openModal({{ $program->id }})"
                        class="bg-transparent border border-[#213430] text-[#213430] hover:bg-[#9E2469] hover:border-none hover:text-white py-4 px-8 rounded-lg program-btn">
                        View Details
                    </button>
                </div>
            @empty
                <p class="text-[#91848C]">No upcoming programs found.</p>
            @endforelse
        </div>

        <!-- Ongoing Programs Section -->
        <div>
            <h2 class="text-2xl font-semibold text-[#213430] mb-4 program-main">Ongoing Programs</h2>

            @forelse($ongoingPrograms as $program)
                <div class="bg-[#F3E8EF] rounded-lg p-4 mb-4 flex items-center justify-between md:flex hidden">
                    <div class="flex items-center">
                        <div
                            class="flex flex-col items-center justify-center w-20 h-20 border-2 border-pink rounded-lg mr-4 bg-[#FFF7FC]">
                            <span
                                class="text-sm text-pink">{{ \Carbon\Carbon::parse($program->event_date)->format('M') }}</span>
                            <span
                                class="text-4xl font-bold text-pink">{{ \Carbon\Carbon::parse($program->event_date)->format('d') }}</span>
                        </div>
                        <div class="w-20 h-20 rounded-lg overflow-hidden mr-4">
                            @php
                                $bannerUrl = $program->banner ? asset('storage/' . ltrim($program->banner, '/')) : asset('public/images/program-3.png');
                                $fallbackImg = asset('public/images/program-3.png');
                            @endphp
                            <img src="{{ $bannerUrl }}" alt="{{ $program->title }}"
                                class="w-full h-full object-cover" onerror="this.src='{{ $fallbackImg }}'" />
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-[#213430] mb-1 program-h">{{ $program->title }}</h3>
                            <p class="text-sm text-[#91848C] program-p">{{ $program->description }}</p>
                        </div>
                    </div>
                    <button onclick="openModal({{ $program->id }})"
                        class="bg-transparent border border-[#213430] text-[#213430] hover:bg-[#9E2469] hover:border-none hover:text-white py-4 px-8 rounded-lg program-btn">
                        View Details
                    </button>
                </div>
            @empty
                <p class="text-[#91848C]">No ongoing programs found.</p>
            @endforelse
        </div>
    </main>

    <!-- Modal -->
    <div id="registerModal" class="modal-overlay fixed inset-0 z-50 bg-black/50 hidden flex items-start justify-end overflow-y-auto">
        <div class="modal-content p-4 sm:p-5 w-full sm:w-[520px] md:w-[560px] bg-[#F3E8EF] rounded-2xl shadow-lg overflow-y-auto max-h-[100vh]">
            <div class="border border-[#DCCFD8] p-2 rounded-md">
                <!-- Modal Header -->
                <div class="p-2 mb-2 border-b border-[#DCCFD8] rounded-md">
                    <h2 class="text-2xl font-semibold text-gray-900 program-main modal-title">Loading...</h2>
                </div>

                <!-- Image -->
                <div class="w-full h-64 overflow-hidden rounded-md mb-2">
                    <img src="{{ asset('public/images/program-3.png') }}" alt="Program Banner" class="modal-banner w-full h-full object-cover" onerror="this.src='{{ asset('public/images/program-3.png') }}'">
                </div>

                <!-- Modal Body -->
                <div class="py-3 text-md text-gray-800 space-y-6">
                    <p class="text-[#91848C] app-text modal-description">Loading description...</p>

                    <!-- Date & Time -->
                    <div>
                        <h3 class="text-lg font-medium text-[#213430] mb-4 app-main">Date And Time</h3>
                        <div class="flex justify-between gap-6 border border-[#DCCFD8] py-4 px-4 rounded-lg">
                            <!-- Date Section -->
                            <div class="flex flex-col gap-2 text-[#91848C] text-sm app-text">
                                <div>
                                    <i class="far fa-calendar font-bold text-[#91848C]"></i>
                                    <span>Date</span>
                                </div>
                                <p class="text-[#91848C] modal-date">-</p>
                            </div>

                            <!-- Time Section -->
                            <div class="flex flex-col gap-2 text-[#91848C] text-sm app-text">
                                <div>
                                    <i class="far fa-clock font-bold text-[#91848C]"></i>
                                    <span>Time</span>
                                </div>
                                <p class="text-[#91848C] modal-time">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- About Program -->
                    <div>
                        <h3 class="text-lg font-medium text-[#213430] mb-4 app-main">Program Details</h3>
                        <div class="bg-[#F3E8EF] p-4 rounded-lg space-y-3 border border-[#DCCFD8]">
                            <div id="program-status-row" class="flex items-start justify-between gap-3 rounded-lg border border-[#DCCFD8] bg-white px-3 py-3">
                                <span class="text-sm font-semibold text-[#213430]">Status</span>
                                <span class="modal-effective-status text-sm font-medium text-[#213430]">-</span>
                            </div>
                            <div id="program-application-window-row" class="flex flex-col gap-1 rounded-lg border border-[#DCCFD8] bg-white px-3 py-3 hidden">
                                <div class="flex justify-between gap-3">
                                    <span class="text-sm font-semibold text-[#213430]">Application opening</span>
                                    <span class="modal-application-start text-sm text-[#213430]">-</span>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <span class="text-sm font-semibold text-[#213430]">Application closing</span>
                                    <span class="modal-application-end text-sm text-[#213430]">-</span>
                                </div>
                            </div>
                            <div data-custom-fields class="space-y-3"></div>
                            <p data-custom-fields-empty class="text-sm text-[#91848C] app-text">No additional details have been added yet.</p>
                        </div>
                    </div>

                    <!-- Registration Summary -->
                    <div id="registration-info" class="hidden border border-[#DCCFD8] p-4 rounded-lg space-y-2">
                        <h3 class="text-lg font-medium text-[#213430] app-main">Your Registration</h3>
                        <p class="text-sm text-[#213430] app-text">Status: <span class="font-semibold registration-status">-</span></p>
                        <p class="text-sm text-[#213430] app-text">Submitted: <span class="registration-submitted">-</span></p>
                        <div class="registration-note hidden">
                            <p class="text-sm font-medium text-[#213430] app-text">Admin Note</p>
                            <p class="text-sm text-[#91848C] app-text registration-note-text"></p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row justify-between gap-3 pt-4">
                        <button onclick="closeModal()"
                            class="px-5 py-3 bg-transparent border border-[#DCCFD8] text-[#91848C] rounded-md app-text">
                            Cancel
                        </button>
                        <div class="flex items-center gap-3">
                            <a id="registration-view-btn" href="#" target="_self"
                                class="hidden px-5 py-3 border border-[#213430] text-[#213430] rounded-md hover:bg-[#213430] hover:text-white transition app-text">
                                View Details
                            </a>
                            <button type="button" id="register-btn" onclick="openRegistrationForm()"
                                class="px-6 py-2 bg-pink text-white rounded-lg hover:bg-pink-dark transition app-text">
                                Register Yourself
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Popup Modal -->
    <div id="popupModal" class="fixed inset-0 z-50 hidden flex items-start sm:items-center justify-center bg-black/60 px-4 py-6 overflow-y-auto">
        <!-- Modal Box -->
        <div class="bg-[#F3E8EF] p-6 rounded-lg w-full max-w-4xl relative overflow-y-auto max-h-[90vh] shadow-xl border border-[#DCCFD8]">

            <!-- Close Button -->
            <button onclick="document.getElementById('popupModal').classList.add('hidden')"
                class="absolute top-4 right-4 text-[#91848C] hover:text-black text-2xl font-bold">
                &times;
            </button>

            <!-- Modal Title -->
            <h2 class="text-lg font-medium text-black app-main mb-4">Financial Assistance Pre-Qualification</h2>

            <!-- Form Start -->
            <form action="{{ route('program.register') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                <input type="hidden" name="program_id" id="program_id" value="">

                <div class="border border-[#DCCFD8] bg-white/60 rounded-lg p-4 space-y-3">
                    <h3 class="text-md font-semibold text-[#213430] app-main">Application Periods</h3>
                    <p class="text-sm text-[#213430] app-text">
                        The Breast Cancer Treatment Financial Assistance Program opens during the following application windows:
                    </p>
                    <ul class="list-disc pl-5 text-sm text-[#213430] app-text space-y-1">
                        <li><span class="font-medium">Option 1:</span> May through June</li>
                        <li><span class="font-medium">Option 2:</span> November through December</li>
                    </ul>
                    <div class="text-sm text-[#213430] app-text space-y-1">
                        <p>Breast Cancer Treatment Assistance Program (up to $500)</p>
                        <p>Survivor Health and Wellness Assistance Program (up to $250)</p>
                    </div>
                </div>

                <div class="border border-[#DCCFD8] bg-white/60 rounded-lg p-4 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm app-text text-[#213430]">
                        <div class="space-y-3">
                            <p class="font-medium">Choose one application period (select only one option; grants are paid over the course of 3 months): *</p>
                            <p class="text-xs text-[#91848C] app-text">Option 1: May through June · Option 2: November through December</p>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="quarter" value="option1" class="text-[#9E2469]" required>
                                    <span>Option 1: May through June</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="quarter" value="option2" class="text-[#9E2469]" required>
                                    <span>Option 2: November through December</span>
                                </label>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <p class="font-medium">Please indicate which program you are applying for: *</p>
                            <p class="text-xs text-[#91848C] app-text">
                                Selected from the program list: <span id="selected-program-name" class="font-medium text-[#213430]">-</span>
                            </p>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="programs_applied[]" value="Breast Cancer Treatment Assistance Program (up to $500)" class="text-[#9E2469]">
                                    <span>Breast Cancer Treatment Assistance Program (up to $500)</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="programs_applied[]" value="Survivor Health and Wellness Assistance Program (up to $250)" class="text-[#9E2469]">
                                    <span>Survivor Health and Wellness Assistance Program (up to $250)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border border-[#DCCFD8] bg-white/60 rounded-lg p-4 space-y-4">
                    <h3 class="text-md font-semibold text-[#213430] app-main">Health background</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm app-text text-[#213430]">
                        <div class="space-y-2">
                            <p class="font-medium">Are you in active treatment?</p>
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="active_treatment" value="1" class="text-[#9E2469]" required>
                                    <span>Yes</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="active_treatment" value="0" class="text-[#9E2469]">
                                    <span>No</span>
                                </label>
                            </div>
                            <p class="text-xs text-[#91848C] app-text">
                                Active treatment is defined as the period after a positive diagnosis of breast cancer has been made (with a diagnostic biopsy),
                                and during which therapies are being administered, including surgical procedures to remove the cancer (e.g., single or bi-lateral
                                mastectomy, lumpectomy, axillary dissection, or sentinel node biopsy), chemotherapy or radiation. Active treatment does not include
                                reconstruction surgeries or long-term hormonal therapies.
                            </p>
                        </div>
                        <div>
                            <label class="block font-medium mb-1">Family history of breast cancer?</label>
                            <input type="text" name="family_history" placeholder="Add answer here"
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-pink-300">
                        </div>
                        <div>
                            <label class="block font-medium mb-1">Received financial assistance from us before?</label>
                            <input type="text" name="assistance_history" placeholder="Add answer here"
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-pink-300">
                        </div>
                        <div>
                            <label class="block font-medium mb-1">How did you hear about us?</label>
                            <input type="text" name="heard_about" placeholder="Referral, friend, web search..."
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-pink-300">
                        </div>
                        <div class="space-y-2">
                            <p class="font-medium">Select one</p>
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="referral_type" value="self" class="text-[#9E2469]" required>
                                    <span>Self referral</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="referral_type" value="facility" class="text-[#9E2469]">
                                    <span>Healthcare facility referral</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border border-[#DCCFD8] bg-white/60 rounded-lg p-4 space-y-4">
                    <h3 class="text-md font-semibold text-[#213430] app-main">Contact & treatment details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium mb-1 text-sm">First Name *</label>
                            <input type="text" name="first_name" id="first_name" required
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-pink-300">
                        </div>
                        <div>
                            <label class="block font-medium mb-1 text-sm">Last Name *</label>
                            <input type="text" name="last_name" id="last_name" required
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-pink-300">
                        </div>
                        <div>
                            <label class="block font-medium mb-1 text-sm">Date of Birth *</label>
                            <input type="date" name="dob" id="dob" required
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] focus:outline-none focus:ring-2 focus:ring-pink-300">
                        </div>
                        <div>
                            <label class="block font-medium mb-1 text-sm">Email Address *</label>
                            <input type="email" name="email" id="email" required
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-pink-300">
                        </div>
                        <div>
                            <label class="block font-medium mb-1 text-sm">Phone *</label>
                            <input type="text" name="phone" id="phone" required
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-pink-300">
                        </div>
                        <div>
                            <label class="block font-medium mb-1 text-sm">Treatment Facility Name *</label>
                            <input type="text" name="treatment_facility_name" id="treatment_facility_name" required
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-pink-300">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block font-medium mb-1 text-sm">Street Address *</label>
                            <input type="text" name="street_address" required
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-pink-300">
                        </div>
                        <div>
                            <label class="block font-medium mb-1 text-sm">City *</label>
                            <input type="text" name="city" required
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-pink-300">
                        </div>
                        <div>
                            <label class="block font-medium mb-1 text-sm">State *</label>
                            <input type="text" name="state" required
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-pink-300">
                        </div>
                        <div>
                            <label class="block font-medium mb-1 text-sm">Postal / Zip code *</label>
                            <input type="text" name="postal_code" required
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-pink-300">
                        </div>
                    </div>
                </div>

                <div class="border border-[#DCCFD8] bg-white/60 rounded-lg p-4 space-y-3">
                    <h3 class="text-md font-semibold text-[#213430] app-main">Proof of income / employment status *</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm text-[#213430] app-text">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="proof_of_income_status[]" value="employed" class="text-[#9E2469]">
                            <span>Employed</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="proof_of_income_status[]" value="self_employed" class="text-[#9E2469]">
                            <span>Self Employed</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="proof_of_income_status[]" value="disabled" class="text-[#9E2469]">
                            <span>Disabled</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="proof_of_income_status[]" value="retired" class="text-[#9E2469]">
                            <span>Retired</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="proof_of_income_status[]" value="student" class="text-[#9E2469]">
                            <span>Student</span>
                        </label>
                    </div>
                    <p class="text-xs text-[#91848C] app-text">
                        Provide one: last two years W-2, last two months recent pay stubs, last three months bank statements, or a written signed statement. Upload documents below.
                    </p>
                </div>

                <div class="border border-[#DCCFD8] bg-white/60 rounded-lg p-4 space-y-3">
                    <h3 class="text-md font-semibold text-[#213430] app-main">Your story *</h3>
                    <p class="text-sm text-[#91848C] app-text">Please share your breast cancer journey. This helps us make a funding decision and will not be shared unless you authorize it below. Maximum 1000 words.</p>
                    <textarea name="story" id="story-field" rows="5" required
                        class="w-full px-4 py-3 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-pink-300"
                        data-max-words="1000"></textarea>
                    <p class="text-xs text-[#91848C] app-text"><span id="story-word-count">0</span> / 1000 words</p>
                </div>

                <div class="border border-[#DCCFD8] bg-white/60 rounded-lg p-4 space-y-3">
                    <h3 class="text-md font-semibold text-[#213430] app-main">Authorization</h3>
                    <p class="text-sm text-[#91848C] app-text">
                        If approved, may we use parts of your story to help others? This does not affect your eligibility.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 text-sm text-[#213430] app-text">
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="authorization_choice" value="allow" class="text-[#9E2469]" checked>
                            <span>Yes, you may share the items I select below.</span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="authorization_choice" value="decline" class="text-[#9E2469]">
                            <span>No, do not use my information or images.</span>
                        </label>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-[#213430] app-text" data-auth-permissions>
                        <label class="flex items-start gap-2">
                            <input type="checkbox" name="authorization_permissions[]" value="full_name" class="mt-1 text-[#9E2469]">
                            <span>Use my full name</span>
                        </label>
                        <label class="flex items-start gap-2">
                            <input type="checkbox" name="authorization_permissions[]" value="story_anonymous" class="mt-1 text-[#9E2469]">
                            <span>Share part of my story anonymously</span>
                        </label>
                        <label class="flex items-start gap-2">
                            <input type="checkbox" name="authorization_permissions[]" value="story_full" class="mt-1 text-[#9E2469]">
                            <span>Share my story with my name</span>
                        </label>
                        <label class="flex items-start gap-2">
                            <input type="checkbox" name="authorization_permissions[]" value="photos" class="mt-1 text-[#9E2469]">
                            <span>Use photos / media of me or my journey</span>
                        </label>
                        <label class="flex items-start gap-2">
                            <input type="checkbox" name="authorization_permissions[]" value="contact_details" class="mt-1 text-[#9E2469]">
                            <span>Contact me for follow-ups related to my story</span>
                        </label>
                    </div>
                </div>

                <div class="border border-[#DCCFD8] bg-white/60 rounded-lg p-4 space-y-4">
                    <h3 class="text-md font-semibold text-[#213430] app-main">Billing & verification</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block font-medium mb-1 text-sm">Billing address / online payment details</label>
                            <textarea name="billing_details" rows="3"
                                class="w-full px-4 py-3 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-pink-300"></textarea>
                            <p class="text-xs text-[#91848C] app-text mt-1">To help us with your bill payments and submissions, please provide the billing address or the necessary information for making online payments.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="block font-medium mb-1 text-sm">Signature *</label>
                            <div class="border border-dashed border-[#DCCFD8] rounded-lg bg-white">
                                <canvas id="signature-pad" class="w-full h-40" style="touch-action: none;"></canvas>
                            </div>
                            <div class="flex items-center justify-between text-xs text-[#91848C] app-text">
                                <span>Sign inside the box above.</span>
                                <button type="button" id="signature-clear" class="px-3 py-1 rounded-md border border-[#9E2469] text-[#9E2469] hover:bg-[#FDE8F3]">Clear</button>
                            </div>
                            <input type="hidden" name="signature_data" id="signature_data" required>
                        </div>
                    </div>
                </div>

                <div class="border border-[#DCCFD8] bg-white/60 rounded-lg p-4 space-y-4">
                    <h3 class="text-md font-semibold text-[#213430] app-main">Upload documentation</h3>
                    <div class="space-y-3 text-sm text-[#213430] app-text">
                        <div>
                            <label class="block font-medium mb-1">Upload Treatment Verification Letter *</label>
                            <input type="file" name="treatment_verification_letter" accept=".pdf,.jpg,.jpeg,.png" required
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <p class="text-xs text-[#91848C]">Must be on facility letterhead.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="block font-medium">Upload Bill Statements *</label>
                            <input type="file" name="bill_statements[]" accept=".pdf,.jpg,.jpeg,.png" required
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <input type="file" name="bill_statements[]" accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <input type="file" name="bill_statements[]" accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <p class="text-xs text-[#91848C]">All bills must be submitted at one time; partial submissions will not be accepted.</p>
                        </div>
                        <div>
                            <label class="block font-medium mb-1">Proof of income documents (optional)</label>
                            <input type="file" name="income_documents[]" accept=".pdf,.jpg,.jpeg,.png" multiple
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] focus:outline-none focus:ring-2 focus:ring-pink-300">
                        </div>
                        <div>
                            <label class="block font-medium mb-1">Additional supporting documents (optional)</label>
                            <input type="file" name="documents[]" accept=".pdf,.jpg,.jpeg,.png" multiple
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] focus:outline-none focus:ring-2 focus:ring-pink-300">
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex mt-4 space-x-4">
                    <button type="button" onclick="document.getElementById('popupModal').classList.add('hidden')"
                        class="px-6 py-2 bg-[#FFF7FC] text-[#91848C] border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-[#9E2469] text-white rounded-md hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/patient/dashboard.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.2.0/dist/signature_pad.umd.min.js"></script>
    <script>
        // List of program IDs the current user has already registered for
        const registeredPrograms = @json($registeredProgramIds);
        const customFieldsContainer = document.querySelector('[data-custom-fields]');
        const customFieldsEmptyState = document.querySelector('[data-custom-fields-empty]');
        const defaultLabels = {
            title: 'Title',
            description: 'Description',
            event_date: 'Date',
            event_time: 'Time',
            status: 'Status',
            custom_note: 'Note',
            link: 'Link',
        };

        const buildFieldValueNode = (field) => {
            const type = field?.type || 'short_text';
            const rawValue = field?.value;
            const valueNode = type === 'link' ? document.createElement('a') : document.createElement('span');
            const isTruthy = rawValue === true || rawValue === '1' || rawValue === 1 || rawValue === 'true' || rawValue === 'yes';

            const asNumber = Number(rawValue);
            const isNumeric = Number.isFinite(asNumber);

            switch (type) {
                case 'money':
                    valueNode.textContent = isNumeric
                        ? `$${asNumber.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
                        : (rawValue ?? 'â€”');
                    break;
                case 'number':
                    valueNode.textContent = isNumeric ? asNumber.toLocaleString() : (rawValue ?? 'â€”');
                    break;
                case 'date': {
                    const parsed = rawValue ? new Date(rawValue) : null;
                    valueNode.textContent = parsed && !isNaN(parsed.valueOf())
                        ? parsed.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' })
                        : (rawValue ?? 'â€”');
                    break;
                }
                case 'time':
                    valueNode.textContent = typeof rawValue === 'string' && rawValue.length >= 5
                        ? rawValue.slice(0, 5)
                        : (rawValue ?? 'â€”');
                    break;
                case 'link':
                    valueNode.href = rawValue || '#';
                    valueNode.textContent = rawValue || 'Open link';
                    valueNode.className = 'text-sm text-[#9E2469] underline break-all';
                    valueNode.target = '_blank';
                    valueNode.rel = 'noopener noreferrer';
                    break;
                case 'boolean':
                    valueNode.textContent = isTruthy ? 'Yes' : 'No';
                    valueNode.className = isTruthy
                        ? 'text-sm text-[#1B7B3A] font-semibold'
                        : 'text-sm text-[#B32020] font-semibold';
                    break;
                default:
                    valueNode.textContent = rawValue ?? 'â€”';
            }

            if (!valueNode.className) {
                valueNode.className = 'text-sm font-medium text-[#213430] text-right';
            }

            return valueNode;
        };

        const renderCustomFields = (fields) => {
            if (!customFieldsContainer || !customFieldsEmptyState) {
                return;
            }

            const filteredFields = Array.isArray(fields)
                ? fields.filter((field) => !['payment_type', 'program_fund', 'max_applications', 'status'].includes(field?.name))
                : [];

            customFieldsContainer.innerHTML = '';

            if (filteredFields.length === 0) {
                customFieldsEmptyState.classList.remove('hidden');
                return;
            }

            customFieldsEmptyState.classList.add('hidden');

            filteredFields.forEach((field) => {
                const row = document.createElement('div');
                row.className = 'flex items-start justify-between gap-3 rounded-lg border border-[#DCCFD8] bg-white px-3 py-3';

                const labelWrap = document.createElement('div');
                labelWrap.className = 'flex flex-col gap-1';

                const labelRow = document.createElement('div');
                labelRow.className = 'flex items-center gap-2';

                const label = document.createElement('span');
                label.className = 'text-sm font-semibold text-[#213430]';
                label.textContent = field?.label || defaultLabels[field?.name] || 'Detail';

                labelRow.appendChild(label);

                if (field?.required) {
                    const badge = document.createElement('span');
                    badge.className = 'inline-flex items-center rounded-full bg-[#9E2469]/10 px-2 py-0.5 text-[10px] font-semibold text-[#9E2469]';
                    badge.textContent = 'Important';
                    labelRow.appendChild(badge);
                }

                labelWrap.appendChild(labelRow);

                if (field?.help_text) {
                    const help = document.createElement('p');
                    help.className = 'text-xs text-[#91848C]';
                    help.textContent = field.help_text;
                    labelWrap.appendChild(help);
                }

                const valueWrap = document.createElement('div');
                valueWrap.className = 'flex items-center justify-end text-right';
                valueWrap.appendChild(buildFieldValueNode(field));

                row.appendChild(labelWrap);
                row.appendChild(valueWrap);
                customFieldsContainer.appendChild(row);
            });
        };

        function openRegistrationModal(programId) {
            document.getElementById('popupModal').classList.remove('hidden');
            document.getElementById('program_id').value = programId;
        }

        let currentProgramId = null;
        let currentProgramTitle = "";

        const syncProgramSelection = () => {
            const checkboxes = document.querySelectorAll('input[name="programs_applied[]"]');
            if (!checkboxes.length) {
                return;
            }
            const hasChecked = Array.from(checkboxes).some((checkbox) => checkbox.checked);
            const normalizedTitle = (currentProgramTitle || '').trim().toLowerCase();
            if (!normalizedTitle || hasChecked) {
                return;
            }
            checkboxes.forEach((checkbox) => {
                const normalizedValue = (checkbox.value || '').trim().toLowerCase();
                checkbox.checked = normalizedValue === normalizedTitle;
            });
        };

        function openModal(id) {
            currentProgramId = id; // Save for use when opening register modal

            renderCustomFields([]);

            fetch('{{ url('/patient/programs') }}/' + id, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    currentProgramTitle = data.title || "";
                    document.querySelector('#registerModal .modal-title').textContent = data.title || 'â€”';
                    document.querySelector('#registerModal .modal-description').textContent = data.description || 'â€”';
                    document.querySelector('#registerModal .modal-date').textContent = data.event_date || 'â€”';
                    document.querySelector('#registerModal .modal-time').textContent = data.event_time || 'â€”';

                    const effectiveStatusEl = document.querySelector('#registerModal .modal-effective-status');
                    if (effectiveStatusEl) {
                        effectiveStatusEl.textContent = data.effective_status_label || 'Upcoming';
                    }
                    const appWindowRow = document.getElementById('program-application-window-row');
                    const appStartEl = document.querySelector('#registerModal .modal-application-start');
                    const appEndEl = document.querySelector('#registerModal .modal-application-end');
                    if (appWindowRow && appStartEl && appEndEl) {
                        if (data.application_start_date || data.application_end_date) {
                            appStartEl.textContent = data.application_start_date || '-';
                            appEndEl.textContent = data.application_end_date || '-';
                            appWindowRow.classList.remove('hidden');
                        } else {
                            appWindowRow.classList.add('hidden');
                        }
                    }

                    const fallbackBanner = "{{ asset('public/images/program-3.png') }}";
                    const bannerEl = document.querySelector('#registerModal .modal-banner');
                    const bannerSrc = data.banner ? data.banner : fallbackBanner;
                    if (bannerEl) {
                        bannerEl.src = bannerSrc;
                        bannerEl.onerror = () => {
                            bannerEl.src = fallbackBanner;
                        };
                    }

                    renderCustomFields(data.custom_fields || []);
                    const selectedProgramName = document.getElementById('selected-program-name');
                    if (selectedProgramName) {
                        selectedProgramName.textContent = currentProgramTitle || 'N/A';
                    }
                    syncProgramSelection();
                    // Update register button state based on registration history
                    const registerButton = document.getElementById('register-btn');
                    const viewButton = document.getElementById('registration-view-btn');
                    const registrationPanel = document.getElementById('registration-info');
                    const registrationStatus = registrationPanel.querySelector('.registration-status');
                    const registrationSubmitted = registrationPanel.querySelector('.registration-submitted');
                    const registrationNote = registrationPanel.querySelector('.registration-note');
                    const registrationNoteText = registrationPanel.querySelector('.registration-note-text');
                    const registrationInfo = data.registration || null;
                    const alreadyRegistered = registrationInfo !== null || registeredPrograms.includes(id);

                    if (alreadyRegistered) {
                        // Already registered: disable the button and update text/style
                        registerButton.textContent = 'Registered';
                        registerButton.disabled = true;
                        registerButton.classList.remove('bg-pink', 'hover:bg-pink-dark');
                        registerButton.classList.add('bg-gray-400', 'opacity-50', 'cursor-not-allowed');

                        if (registrationInfo && registrationInfo.view_url) {
                            viewButton.href = registrationInfo.view_url;
                            viewButton.classList.remove('hidden');
                        } else {
                            viewButton.href = '{{ route('patient.programsAndAids') }}';
                            viewButton.classList.remove('hidden');
                        }

                        registrationPanel.classList.remove('hidden');
                        registrationStatus.textContent = registrationInfo?.status_label || 'Pending';
                        registrationSubmitted.textContent = registrationInfo?.submitted_at || '-';
                        if (registrationInfo?.review_note) {
                            registrationNote.classList.remove('hidden');
                            registrationNoteText.textContent = registrationInfo.review_note;
                        } else {
                            registrationNote.classList.add('hidden');
                            registrationNoteText.textContent = '';
                        }
                    } else {
                        // Not registered: enable Register only when applications are open
                        const applicationOpen = data.is_application_open !== false;
                        if (applicationOpen) {
                            registerButton.textContent = 'Register Yourself';
                            registerButton.disabled = false;
                            registerButton.classList.remove('bg-gray-400', 'opacity-50', 'cursor-not-allowed');
                            registerButton.classList.add('bg-pink', 'hover:bg-pink-dark');
                            registerButton.onclick = () => openRegistrationForm();
                        } else {
                            registerButton.textContent = data.effective_status === 'upcoming' ? 'Not yet open' : 'Applications closed';
                            registerButton.disabled = true;
                            registerButton.classList.remove('bg-pink', 'hover:bg-pink-dark');
                            registerButton.classList.add('bg-gray-400', 'opacity-50', 'cursor-not-allowed');
                            registerButton.onclick = null;
                        }

                        viewButton.classList.add('hidden');
                        viewButton.href = '#';
                        registrationPanel.classList.add('hidden');
                        registrationStatus.textContent = '-';
                        registrationSubmitted.textContent = '-';
                        registrationNote.classList.add('hidden');
                        registrationNoteText.textContent = '';
                    }
                    document.getElementById('registerModal').classList.remove('hidden');
                })
                .catch(err => {
                    alert("Failed to load program data.");
                    console.error(err);
                });
        }

        function openRegistrationForm() {
            // set the hidden input value in the popup modal form
            document.getElementById('program_id').value = currentProgramId;
            const selectedProgramName = document.getElementById('selected-program-name');
            if (selectedProgramName) {
                selectedProgramName.textContent = currentProgramTitle || 'N/A';
            }
            syncProgramSelection();

            // hide details modal, show registration modal
            document.getElementById('registerModal').classList.add('hidden');
            document.getElementById('popupModal').classList.remove('hidden');
            // ensure signature canvas sizes correctly after showing
            setTimeout(() => {
                initSignaturePad();
            }, 50);
        }

        function closeModal() {
            document.getElementById('registerModal').classList.add('hidden');
        }

        const authRadios = document.querySelectorAll('input[name="authorization_choice"]');
        const permissionSection = document.querySelector('[data-auth-permissions]');
        const permissionCheckboxes = permissionSection ? permissionSection.querySelectorAll('input[type="checkbox"]') : [];
        const syncPermissions = () => {
            if (!permissionSection) return;
            const allowSelected = Array.from(authRadios).some((r) => r.checked && r.value === 'allow');
            permissionSection.classList.toggle('opacity-60', !allowSelected);
            permissionSection.classList.toggle('pointer-events-none', !allowSelected);
            permissionCheckboxes.forEach((cb) => {
                cb.disabled = !allowSelected;
            });
        };

        authRadios.forEach((radio) => radio.addEventListener('change', syncPermissions));
        syncPermissions();

        // Story word count (max 1000 words)
        const storyField = document.getElementById('story-field');
        const storyWordCountEl = document.getElementById('story-word-count');
        const MAX_STORY_WORDS = 1000;

        const countWords = (text) => (text || '').trim().split(/\s+/).filter(Boolean).length;

        const updateStoryWordCount = () => {
            if (!storyField || !storyWordCountEl) return;
            const text = storyField.value;
            let words = countWords(text);
            if (words > MAX_STORY_WORDS) {
                const trimmed = text.trim().split(/\s+/).slice(0, MAX_STORY_WORDS).join(' ');
                storyField.value = trimmed;
                words = MAX_STORY_WORDS;
            }
            storyWordCountEl.textContent = words;
            storyWordCountEl.classList.toggle('text-[#B32020]', words > MAX_STORY_WORDS);
        };

        if (storyField) {
            storyField.addEventListener('input', updateStoryWordCount);
            storyField.addEventListener('paste', () => setTimeout(updateStoryWordCount, 0));
            updateStoryWordCount();
        }

        // Signature pad
        const signatureCanvas = document.getElementById('signature-pad');
        const signatureInput = document.getElementById('signature_data');
        const clearBtn = document.getElementById('signature-clear');
        let signaturePad = null;

        const resizeSignatureCanvas = () => {
            if (!signatureCanvas) return;
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            const width = signatureCanvas.offsetWidth || 400;
            const height = signatureCanvas.offsetHeight || 160;
            signatureCanvas.width = width * ratio;
            signatureCanvas.height = height * ratio;
            const ctx = signatureCanvas.getContext('2d');
            ctx.scale(ratio, ratio);
        };

        const syncSignature = () => {
            if (!signaturePad || !signatureInput) return;
            signatureInput.value = signaturePad.isEmpty() ? '' : signaturePad.toDataURL('image/png');
        };

        const initSignaturePad = () => {
            if (!signatureCanvas || !window.SignaturePad) return;
            if (!signaturePad) {
                resizeSignatureCanvas();
                signaturePad = new SignaturePad(signatureCanvas, {
                    backgroundColor: '#ffffff',
                    penColor: '#9E2469'
                });
                signaturePad.onEnd = syncSignature;
            }
            signaturePad.clear();
            syncSignature();
        };

        clearBtn?.addEventListener('click', () => {
            signaturePad?.clear();
            syncSignature();
        });

        // Ensure the form won't submit without a signature
        const form = document.querySelector('#popupModal form');
        form?.addEventListener('submit', (e) => {
            syncSignature();
            const quarterRadios = document.querySelectorAll('input[name="quarter"]');
            const hasQuarterSelected = Array.from(quarterRadios).some((r) => r.checked);
            if (!hasQuarterSelected) {
                e.preventDefault();
                alert('Please select an application period before submitting.');
                return;
            }
            const programCheckboxes = Array.from(document.querySelectorAll('input[name="programs_applied[]"]'));
            const hasProgramSelected = programCheckboxes.some((checkbox) => checkbox.checked);
            if (!hasProgramSelected) {
                e.preventDefault();
                alert('Please select at least one program before submitting.');
                return;
            }
            if (storyField && countWords(storyField.value) > MAX_STORY_WORDS) {
                e.preventDefault();
                alert('Your story may not exceed 1000 words. Please shorten it.');
                return;
            }
            if (!signatureInput.value) {
                e.preventDefault();
                alert('Please add your signature before submitting.');
            }
        });
    </script>
@endpush
