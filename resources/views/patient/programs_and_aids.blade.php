@extends('patient.layouts.app')

@section('title', 'Programs & Services')

@section('content')
    @push('head')
        <style>
            /* Program cards: limit description height when Tailwind line-clamp is unavailable */
            .program-card-desc-clamp {
                display: -webkit-box;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 3;
                overflow: hidden;
                word-break: break-word;
            }
        </style>
    @endpush
    @php
        // Fetch IDs of programs the current user has registered for (integers for consistent matching)
        $registeredProgramIds = \App\Models\ProgramRegistration::where('user_id', auth()->id())
            ->pluck('program_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->toArray();
        $programsAtCapacityIds = $programsAtCapacityIds ?? [];
        $financialAssistanceClosed = $financialAssistanceClosed ?? false;
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

        <!-- Programs & Services header -->
        <div class="bg-[#F3E8EF] p-4 rounded-lg mb-6">
            <h2 class="text-lg font-medium text-[#91848C] app-main">Programs & Services</h2>
        </div>

        @if (!empty($financialAssistanceClosed))
            <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 md:p-5 shadow-sm">
                <p class="text-sm text-[#213430] app-text whitespace-pre-line">{{ \App\Support\ProgramApplicationCapacity::CLOSED_MESSAGE }}</p>
            </div>
        @endif

        <!-- Upcoming Programs Section -->
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-[#213430] mb-4 program-main">
                Upcoming Programs
            </h2>

            @forelse($upcomingPrograms as $program)
                <div class="bg-[#F3E8EF] rounded-lg p-4 mb-4 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-start min-w-0 flex-1 w-full">
                        <div
                            class="flex flex-col items-center justify-center w-20 h-20 shrink-0 border-2 border-pink rounded-lg mr-4 bg-[#FFF7FC]">
                            <span
                                class="text-sm text-pink">{{ \Carbon\Carbon::parse($program->event_date)->format('M') }}</span>
                            <span
                                class="text-4xl font-bold text-pink">{{ \Carbon\Carbon::parse($program->event_date)->format('d') }}</span>
                        </div>
                        <div class="w-20 h-20 shrink-0 rounded-lg overflow-hidden mr-4">
                            @php
                                $bannerUrl = $program->banner ? storage_url(ltrim($program->banner, '/')) : asset('public/images/program-3.png');
                                $fallbackImg = asset('public/images/program-3.png');
                            @endphp
                            <img src="{{ $bannerUrl }}" alt="{{ $program->title }}"
                                class="w-full h-full object-cover" onerror="this.src='{{ $fallbackImg }}'" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-xl font-semibold text-[#213430] mb-1 program-h">{{ $program->title }}</h3>
                            <p class="text-sm text-[#91848C] program-p program-card-desc-clamp leading-snug">{{ $program->description }}</p>
                        </div>
                    </div>
                    <div class="flex flex-row items-center gap-3 w-full md:w-auto md:justify-end">
                        <button type="button" onclick="openModal({{ $program->id }})"
                            class="bg-transparent border border-[#213430] text-[#213430] hover:bg-[#9E2469] hover:border-none hover:text-white py-2 px-5 md:py-4 md:px-8 rounded-lg program-btn">
                            View Details
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-[#91848C]">No upcoming programs found.</p>
            @endforelse
        </div>

        <!-- Ongoing Programs Section -->
        <div>
            <h2 class="text-2xl font-semibold text-[#213430] mb-4 program-main">Ongoing Programs</h2>

            @forelse($ongoingPrograms as $program)
                <div class="bg-[#F3E8EF] rounded-lg p-4 mb-4 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-start min-w-0 flex-1 w-full">
                        <div
                            class="flex flex-col items-center justify-center w-20 h-20 shrink-0 border-2 border-pink rounded-lg mr-4 bg-[#FFF7FC]">
                            <span
                                class="text-sm text-pink">{{ \Carbon\Carbon::parse($program->event_date)->format('M') }}</span>
                            <span
                                class="text-4xl font-bold text-pink">{{ \Carbon\Carbon::parse($program->event_date)->format('d') }}</span>
                        </div>
                        <div class="w-20 h-20 shrink-0 rounded-lg overflow-hidden mr-4">
                            @php
                                $bannerUrl = $program->banner ? storage_url(ltrim($program->banner, '/')) : asset('public/images/program-3.png');
                                $fallbackImg = asset('public/images/program-3.png');
                            @endphp
                            <img src="{{ $bannerUrl }}" alt="{{ $program->title }}"
                                class="w-full h-full object-cover" onerror="this.src='{{ $fallbackImg }}'" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-xl font-semibold text-[#213430] mb-1 program-h">{{ $program->title }}</h3>
                            <p class="text-sm text-[#91848C] program-p program-card-desc-clamp leading-snug">{{ $program->description }}</p>
                        </div>
                    </div>
                    <div class="flex flex-row items-center gap-3 w-full md:w-auto md:justify-end">
                        @php
                            // FA closure banner is global; only FA program cards should be blocked by it (not Moments That Matter).
                            $cardApplicationsClosed = ! $program->isAcceptingApplications()
                                || (! empty($financialAssistanceClosed) && $program->isFinancialAssistance());
                        @endphp
                        @if ($cardApplicationsClosed)
                            <button type="button" disabled
                                title="Applications are closed for this program."
                                class="py-2 px-5 md:py-4 md:px-8 rounded-lg program-btn font-medium bg-gray-400 text-white border border-gray-400 opacity-70 cursor-not-allowed whitespace-nowrap">
                                Applications closed
                            </button>
                        @elseif (!in_array($program->id, $registeredProgramIds, true))
                            <button type="button" onclick="openApplyFromCard({{ $program->id }})"
                                class="py-2 px-5 md:py-4 md:px-8 rounded-lg program-btn font-medium bg-pink text-white border border-pink hover:bg-[#9E2469] hover:border-[#9E2469] whitespace-nowrap">
                                Apply
                            </button>
                        @else
                            <button type="button" disabled
                                title="Our records indicate that an application has already been submitted for this program. Please note only one submission is permitted per applicant during each application cycle. Thank you allowing PINK “ME” to support you during your journey."
                                class="py-2 px-5 md:py-4 md:px-8 rounded-lg program-btn font-medium bg-gray-400 text-white border border-gray-400 opacity-70 cursor-not-allowed whitespace-nowrap">
                                You already applied
                            </button>
                        @endif
                        <button type="button" onclick="openModal({{ $program->id }})"
                            class="bg-transparent border border-[#213430] text-[#213430] hover:bg-[#9E2469] hover:border-none hover:text-white py-2 px-5 md:py-4 md:px-8 rounded-lg program-btn">
                            View Details
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-[#91848C]">No ongoing programs found.</p>
            @endforelse
        </div>
    </main>

    @include('patient.programs.partials.moments_that_matter_form')

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

                @include('patient.programs.partials.sponsor_modal_block', ['prefix' => 'modal'])

                <!-- Modal Body -->
                <div class="py-3 text-md text-gray-800 space-y-6">
                    <p class="text-[#91848C] app-text modal-description whitespace-pre-line leading-relaxed text-sm">Loading description...</p>

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
                        <div id="modal-application-range" class="mt-3 pt-3 border-t border-[#DCCFD8] text-sm text-[#91848C] hidden app-text"></div>
                    </div>

                    <!-- About Program -->
                    <div>
                        <h3 class="text-lg font-medium text-[#213430] mb-4 app-main">Program Details</h3>
                        <div class="bg-[#F3E8EF] p-4 rounded-lg space-y-3 border border-[#DCCFD8]">
                            <div id="program-status-row" class="flex items-start justify-between gap-3 rounded-lg border border-[#DCCFD8] bg-white px-3 py-3">
                                <span class="text-sm font-semibold text-[#213430]">Status</span>
                                <span class="modal-effective-status text-sm font-medium text-[#213430]">-</span>
                            </div>
                            <div data-custom-fields class="space-y-3"></div>
                            <p data-custom-fields-empty class="text-sm text-[#91848C] app-text">No additional details have been added yet.</p>
                        </div>
                    </div>

                    <!-- Application summary -->
                    <div id="registration-info" class="hidden border border-[#DCCFD8] p-4 rounded-lg space-y-2">
                        <h3 class="text-lg font-medium text-[#213430] app-main">Your application</h3>
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
                                Apply now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Application form modal -->
    <div id="popupModal" class="fixed inset-0 z-50 hidden flex items-start sm:items-center justify-center bg-black/60 px-4 py-6 overflow-y-auto">
        <!-- Modal Box -->
        <div class="bg-[#F3E8EF] p-6 rounded-lg w-full max-w-4xl min-w-0 relative overflow-y-auto max-h-[90vh] shadow-xl border border-[#DCCFD8]">

            <!-- Close Button -->
            <button onclick="document.getElementById('popupModal').classList.add('hidden')"
                class="absolute top-4 right-4 text-[#91848C] hover:text-black text-2xl font-bold">
                &times;
            </button>

            <!-- Modal Title -->
            <h2 class="text-lg font-medium text-black app-main mb-4">Financial Assistance Pre-Qualification</h2>

            <!-- Form Start -->
            <form action="{{ route('program.register') }}" method="POST" enctype="multipart/form-data" class="space-y-8 min-w-0 max-w-full">
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
                            <p class="font-medium">Choose one application period (select only one option): *</p>
                            <p class="text-xs text-[#91848C] app-text">Grants are distributed in <strong>June</strong> for option 1 and <strong>December</strong> for option 2.</p>
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
                            <p class="font-medium">Please select one program (only one may be chosen): *</p>
                            <p class="text-xs text-[#91848C] app-text">
                                Selected from the program list: <span id="selected-program-name" class="font-medium text-[#213430]">-</span>
                            </p>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="programs_applied" value="Breast Cancer Treatment Assistance Program (up to $500)" class="text-[#9E2469]" required>
                                    <span>Breast Cancer Treatment Assistance Program (up to $500)</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="programs_applied" value="Survivor Health and Wellness Assistance Program (up to $250)" class="text-[#9E2469]">
                                    <span>Survivor Health and Wellness Assistance Program (up to $250)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 rounded-lg border border-[#EADFF0] bg-[#FDF7FB] p-4 text-sm text-[#213430] app-text space-y-2">
                        <p class="font-semibold app-main">Program(s) Summary</p>
                        <p>PINK “ME” offers financial assistance through two programs:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Breast Cancer Treatment Grant – Up to $500</li>
                            <li>Survivor Health and Wellness Grant – Up to $250</li>
                        </ul>
                        <p>Payments are made through the patient portal to service providers or as direct bill payments. Partial payments are not available.</p>
                        <p class="text-[#B32020] font-medium">(Please do not submit partial payments.)</p>
                    </div>
                </div>

                <div class="border border-[#DCCFD8] bg-white/60 rounded-lg p-4 space-y-4">
                    <h3 class="text-md font-semibold text-[#213430] app-main">Health background</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm app-text text-[#213430] min-w-0">
                        <div class="space-y-2 min-w-0">
                            <p class="font-medium">Are you in active treatment?</p>
                            <div class="flex flex-wrap items-center gap-4">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="active_treatment" value="1" class="text-[#9E2469]" required>
                                    <span>Yes</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="active_treatment" value="0" class="text-[#9E2469]">
                                    <span>No</span>
                                </label>
                            </div>
                            <p class="text-xs text-[#91848C] app-text break-words">
                                Active treatment is defined as the period after a positive diagnosis of breast cancer has been made (with a diagnostic biopsy),
                                and during which therapies are being administered, including surgical procedures to remove the cancer (e.g., single or bi-lateral
                                mastectomy, lumpectomy, axillary dissection, or sentinel node biopsy), chemotherapy or radiation. Active treatment does not include
                                reconstruction surgeries or long-term hormonal therapies.
                            </p>
                        </div>
                        <div class="space-y-2 min-w-0 md:col-span-2">
                            <p class="font-medium">Stage of Breast Cancer *</p>
                            <select name="breast_cancer_stage" required
                                class="w-full max-w-md px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] focus:outline-none focus:ring-2 focus:ring-pink-300">
                                <option value="">Select one…</option>
                                <option value="0">0 (Non-Invasive)</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4 (Metastatic)</option>
                                <option value="unknown">Unknown</option>
                            </select>
                        </div>
                        <div class="space-y-2 min-w-0 md:col-span-2">
                            <label class="block font-medium">Race / Ethnicity (select one) *</label>
                            <select name="ethnicity" required
                                class="w-full max-w-md px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] focus:outline-none focus:ring-2 focus:ring-pink-300">
                                <option value="">Select one…</option>
                                @foreach (\App\Support\ProgramApplicationEthnicityOptions::OPTIONS as $ethnicityOption)
                                    <option value="{{ $ethnicityOption }}">{{ $ethnicityOption }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2 min-w-0">
                            <p class="font-medium">Family history of breast cancer? *</p>
                            <div class="flex flex-wrap items-center gap-4">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="family_history" value="Yes" class="text-[#9E2469]" required>
                                    <span>Yes</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="family_history" value="No" class="text-[#9E2469]">
                                    <span>No</span>
                                </label>
                            </div>
                        </div>
                        <div class="space-y-2 min-w-0">
                            <p class="font-medium">Received financial assistance from us before? *</p>
                            <div class="flex flex-wrap items-center gap-4">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="assistance_history" value="Yes" class="text-[#9E2469]" required>
                                    <span>Yes</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="assistance_history" value="No" class="text-[#9E2469]">
                                    <span>No</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block font-medium mb-1">How did you hear about us? *</label>
                            <input type="text" name="heard_about" placeholder="Referral, friend, web search..." required
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-pink-300">
                        </div>
                        <div class="space-y-2 min-w-0 md:col-span-2">
                            <p class="font-medium">Select one *</p>
                            <div class="flex flex-wrap items-center gap-4">
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
                    <h3 class="text-md font-semibold text-[#213430] app-main">Applicant Information &amp; Treatment Details</h3>
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
                    <h3 class="text-md font-semibold text-[#213430] app-main">Proof of Income/Employment Status *</h3>
                    <p class="text-sm text-[#213430] app-text">
                        To help us better understand your needs, income verification is required. Please provide paystubs, W-2s or other proof of income. Upload documents below.
                    </p>
                    <p class="text-sm font-medium text-[#213430]">Select one employment category *</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm text-[#213430] app-text">
                        <label class="flex items-center gap-2">
                            <input type="radio" name="proof_of_income_status" value="employed" class="text-[#9E2469]" required>
                            <span>Employed</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="proof_of_income_status" value="self_employed" class="text-[#9E2469]">
                            <span>Self Employed</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="proof_of_income_status" value="disabled" class="text-[#9E2469]">
                            <span>Disabled</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="proof_of_income_status" value="retired" class="text-[#9E2469]">
                            <span>Retired</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="proof_of_income_status" value="student" class="text-[#9E2469]">
                            <span>Student</span>
                        </label>
                    </div>
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
                    <h3 class="text-md font-semibold text-[#213430] app-main">Sharing your story (optional)</h3>
                    <p class="text-sm text-[#91848C] app-text">
                        If your application is approved, would you like us to consider sharing your story to inspire others?
                        Your answer does not affect eligibility.
                    </p>
                    <div class="flex flex-col gap-3 text-sm text-[#213430] app-text">
                        <label class="inline-flex items-start gap-2 min-w-0">
                            <input type="radio" name="authorization_choice" value="allow" class="text-[#9E2469] mt-1 shrink-0">
                            <span class="break-words">Yes—show optional sharing fields below.</span>
                        </label>
                        <label class="inline-flex items-start gap-2 min-w-0">
                            <input type="radio" name="authorization_choice" value="decline" class="text-[#9E2469] mt-1 shrink-0" checked>
                            <span class="break-words">No thanks—please do not use my information or images.</span>
                        </label>
                    </div>
                    <div class="space-y-3 mt-4 pt-4 border-t border-[#EADFF0]" data-auth-extra>
                        <label class="block text-sm font-medium text-[#213430]">Optional Photo Upload</label>
                        <input type="file" name="story_media[]" accept=".pdf,.jpg,.jpeg,.png" multiple
                            class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] focus:outline-none focus:ring-2 focus:ring-pink-300">
                        <p class="text-xs text-[#91848C]">Photo related to your breast cancer journey of yourself.</p>
                        <label class="block text-sm font-medium text-[#213430] mt-2">Note area</label>
                        <textarea name="story_notes" rows="4"
                            class="w-full px-4 py-3 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-pink-300"
                            placeholder="Your team story can help inspire and support others. Participation is completely optional."></textarea>
                    </div>
                    <p class="text-xs text-[#91848C] app-text mt-2">Your team story can help inspire and support others. Participation is completely optional.</p>
                </div>

                <div class="border border-[#DCCFD8] bg-white/60 rounded-lg p-4 space-y-4">
                    <h3 class="text-md font-semibold text-[#213430] app-main">Billing Address / Online Payment Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 min-w-0">
                        <div class="md:col-span-2">
                            {{-- <label class="block font-medium mb-1 text-sm">Billing Address / Online Payment Details</label> --}}
                            {{-- <textarea name="billing_details" rows="3"
                                class="w-full px-4 py-3 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-pink-300"></textarea> --}}
                            {{-- <p class="text-xs text-[#91848C] app-text mt-1">To help us with your bill payments and submissions, please provide the billing address or the necessary information for making online payments.</p> --}}
                        </div>
                        <div class="md:col-span-2 w-full min-w-0 max-w-full">
                            {{-- <p class="text-sm font-medium text-[#213430] mb-1">Bills to be considered</p> --}}
                            <p class="text-xs text-[#91848C] mb-3">Note: To ensure proper processing, the bill must be in your name to qualify for assistance.To help us with your bill payments and submissions, please provide the billing address or the necessary information for making online payments.</p>
                            <div id="patient-bill-blocks" class="space-y-3 w-full min-w-0">
                                <div class="patient-bill-block rounded-md border border-[#DCCFD8] bg-[#F3E8EF] p-3 sm:p-4 w-full min-w-0 max-w-full box-border">
                                    <div class="flex items-center justify-between gap-2 mb-3">
                                        <span class="text-xs font-semibold text-[#213430] patient-bill-block-label">Bill 1</span>
                                        <button type="button" class="patient-bill-remove shrink-0 px-3 py-1.5 text-xs rounded-md border border-[#91848C] text-[#213430] hover:bg-white/80 disabled:opacity-40 disabled:cursor-not-allowed" title="Remove this bill" disabled>Remove</button>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 w-full min-w-0">
                                        <div class="min-w-0 sm:col-span-2">
                                            <label class="block text-xs font-medium text-[#213430] mb-1">Service Provider Name</label>
                                            <input type="text" name="bill_name[]" autocomplete="organization"
                                                class="w-full min-w-0 box-border px-3 py-2 text-sm rounded-md border border-[#DCCFD8] bg-white text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-[#9E2469]/30">
                                        </div>
                                        <div class="min-w-0 sm:col-span-2">
                                            <label class="block text-xs font-medium text-[#213430] mb-1">Bill Payment Link(s)</label>
                                            <input type="text" name="bill_url[]" placeholder="https://"
                                                class="w-full min-w-0 box-border px-3 py-2 text-sm rounded-md border border-[#DCCFD8] bg-white text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-[#9E2469]/30">
                                        </div>
                                        <div class="min-w-0">
                                            <label class="block text-xs font-medium text-[#213430] mb-1">Amount Due</label>
                                            <input type="number" name="bill_amount[]" inputmode="decimal" placeholder="0.00" min="0" max="500" step="0.01"
                                                class="w-full min-w-0 box-border px-3 py-2 text-sm rounded-md border border-[#DCCFD8] bg-white text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#9E2469]/30">
                                        </div>
                                        <div class="min-w-0">
                                            <label class="block text-xs font-medium text-[#213430] mb-1">Type of Support Expenses</label>
                                            <select name="bill_support_expense[]" class="w-full min-w-0 box-border px-3 py-2 text-sm rounded-md border border-[#DCCFD8] bg-white text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#9E2469]/30">
                                                <option value="">Select type…</option>
                                                @foreach (\App\Support\PatientBillSupportExpenseTypes::OPTIONS as $expenseOption)
                                                    <option value="{{ $expenseOption }}">{{ $expenseOption }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="min-w-0 sm:col-span-2">
                                            <label class="block text-xs font-medium text-[#213430] mb-1">Provider Contact Information</label>
                                            <input type="text" name="bill_provider_contact[]" autocomplete="off" placeholder="Phone or email"
                                                class="w-full min-w-0 box-border px-3 py-2 text-sm rounded-md border border-[#DCCFD8] bg-white text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-[#9E2469]/30">
                                        </div>
                                        <div class="min-w-0 sm:col-span-2">
                                            <label class="block text-xs font-medium text-[#213430] mb-1">Account Number</label>
                                            <input type="text" name="bill_account[]" autocomplete="off"
                                                class="w-full min-w-0 box-border px-3 py-2 text-sm rounded-md border border-[#DCCFD8] bg-white text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#9E2469]/30">
                                        </div>
                                        <div class="min-w-0 sm:col-span-2">
                                            <label class="block text-xs font-medium text-[#213430] mb-1">Notes (optional)</label>
                                            <input type="text" name="bill_notes[]"
                                                class="w-full min-w-0 box-border px-3 py-2 text-sm rounded-md border border-[#DCCFD8] bg-white text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#9E2469]/30">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="patient-bill-add" class="mt-3 px-4 py-2 rounded-md border border-[#9E2469] text-[#9E2469] text-sm font-medium hover:bg-[#FDE8F3] w-full sm:w-auto">
                                Add another bill
                            </button>
                            <p class="text-xs text-[#91848C] mt-2">Amount Due cannot be more than $500 per bill entry.</p>
                            <div id="food-assistance-followup" class="mt-3 hidden rounded-md border border-[#DCCFD8] bg-white px-3 py-3">
                                <p class="text-sm font-medium text-[#213430]">Do you need support with Food Assistance?</p>
                                <div class="mt-2 flex items-center gap-4 text-sm text-[#213430]">
                                    <label class="flex items-center gap-2">
                                        <input type="radio" name="needs_food_assistance" value="yes" class="text-[#9E2469]">
                                        <span>Yes</span>
                                    </label>
                                    <label class="flex items-center gap-2">
                                        <input type="radio" name="needs_food_assistance" value="no" class="text-[#9E2469]">
                                        <span>No</span>
                                    </label>
                                </div>
                            </div>
                            <div id="medical-bills-followup" class="mt-3 hidden rounded-md border border-[#DCCFD8] bg-white px-3 py-3">
                                <p class="text-sm font-medium text-[#213430]">Do you need support with Medical Bill(s) Assistance?</p>
                                <div class="mt-2 flex items-center gap-4 text-sm text-[#213430]">
                                    <label class="flex items-center gap-2">
                                        <input type="radio" name="needs_medical_bills_assistance" value="yes" class="text-[#9E2469]">
                                        <span>Yes</span>
                                    </label>
                                    <label class="flex items-center gap-2">
                                        <input type="radio" name="needs_medical_bills_assistance" value="no" class="text-[#9E2469]">
                                        <span>No</span>
                                    </label>
                                </div>
                            </div>

                            <template id="patient-bill-block-template">
                                <div class="patient-bill-block rounded-md border border-[#DCCFD8] bg-[#F3E8EF] p-3 sm:p-4 w-full min-w-0 max-w-full box-border">
                                    <div class="flex items-center justify-between gap-2 mb-3">
                                        <span class="text-xs font-semibold text-[#213430] patient-bill-block-label">Bill</span>
                                        <button type="button" class="patient-bill-remove shrink-0 px-3 py-1.5 text-xs rounded-md border border-[#91848C] text-[#213430] hover:bg-white/80 disabled:opacity-40 disabled:cursor-not-allowed" title="Remove this bill">Remove</button>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 w-full min-w-0">
                                        <div class="min-w-0 sm:col-span-2">
                                            <label class="block text-xs font-medium text-[#213430] mb-1">Service Provider Name</label>
                                            <input type="text" name="bill_name[]" autocomplete="organization"
                                                class="w-full min-w-0 box-border px-3 py-2 text-sm rounded-md border border-[#DCCFD8] bg-white text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-[#9E2469]/30">
                                        </div>
                                        <div class="min-w-0 sm:col-span-2">
                                            <label class="block text-xs font-medium text-[#213430] mb-1">Bill Payment Link(s)</label>
                                            <input type="text" name="bill_url[]" placeholder="https://"
                                                class="w-full min-w-0 box-border px-3 py-2 text-sm rounded-md border border-[#DCCFD8] bg-white text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-[#9E2469]/30">
                                        </div>
                                        <div class="min-w-0">
                                            <label class="block text-xs font-medium text-[#213430] mb-1">Amount Due</label>
                                            <input type="number" name="bill_amount[]" inputmode="decimal" placeholder="0.00" min="0" max="500" step="0.01"
                                                class="w-full min-w-0 box-border px-3 py-2 text-sm rounded-md border border-[#DCCFD8] bg-white text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#9E2469]/30">
                                        </div>
                                        <div class="min-w-0">
                                            <label class="block text-xs font-medium text-[#213430] mb-1">Type of Support Expenses</label>
                                            <select name="bill_support_expense[]" class="w-full min-w-0 box-border px-3 py-2 text-sm rounded-md border border-[#DCCFD8] bg-white text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#9E2469]/30">
                                                <option value="">Select type…</option>
                                                @foreach (\App\Support\PatientBillSupportExpenseTypes::OPTIONS as $expenseOption)
                                                    <option value="{{ $expenseOption }}">{{ $expenseOption }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="min-w-0 sm:col-span-2">
                                            <label class="block text-xs font-medium text-[#213430] mb-1">Provider Contact Information</label>
                                            <input type="text" name="bill_provider_contact[]" autocomplete="off" placeholder="Phone or email"
                                                class="w-full min-w-0 box-border px-3 py-2 text-sm rounded-md border border-[#DCCFD8] bg-white text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-[#9E2469]/30">
                                        </div>
                                        <div class="min-w-0 sm:col-span-2">
                                            <label class="block text-xs font-medium text-[#213430] mb-1">Account Number</label>
                                            <input type="text" name="bill_account[]" autocomplete="off"
                                                class="w-full min-w-0 box-border px-3 py-2 text-sm rounded-md border border-[#DCCFD8] bg-white text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#9E2469]/30">
                                        </div>
                                        <div class="min-w-0 sm:col-span-2">
                                            <label class="block text-xs font-medium text-[#213430] mb-1">Notes (optional)</label>
                                            <input type="text" name="bill_notes[]"
                                                class="w-full min-w-0 box-border px-3 py-2 text-sm rounded-md border border-[#DCCFD8] bg-white text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#9E2469]/30">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="md:col-span-2 space-y-2 w-full min-w-0">
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
                    <h3 class="text-md font-semibold text-[#213430] app-main">Upload Documentation</h3>
                    <p class="text-xs text-[#91848C] app-text">Each file can be up to 25MB (PDF, JPG, or PNG).</p>
                    <div class="space-y-3 text-sm text-[#213430] app-text">
                        <div>
                            <label class="block font-medium mb-1" title="Letter from your doctor or hospital confirming diagnosis/treatment, on official letterhead">Upload Treatment Verification Letter *</label>
                            <input type="file" name="treatment_verification_letter" accept=".pdf,.jpg,.jpeg,.png" required
                                title="Official letter from your treatment facility confirming you are under care (letterhead required)."
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <p class="text-xs text-[#91848C]">Must be on facility letterhead—confirms you are receiving care for your condition.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="block font-medium" title="Recent medical bills or statements of charges you need help with">Upload Bill Statements *</label>
                            <input type="file" name="bill_statements[]" accept=".pdf,.jpg,.jpeg,.png" required
                                title="Upload itemized bills or statements from your provider (PDF or image)."
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <input type="file" name="bill_statements[]" accept=".pdf,.jpg,.jpeg,.png"
                                title="Optional second bill document."
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <input type="file" name="bill_statements[]" accept=".pdf,.jpg,.jpeg,.png"
                                title="Optional third bill document."
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <p class="text-xs text-[#91848C]">Up to 3 files—upload all related bills together; partial submissions are not accepted.</p>
                        </div>
                        <div>
                            <label class="block font-medium mb-1" title="W-2, pay stubs, bank statements, or signed income statement for eligibility review">Proof of income documents (optional)</label>
                            <input type="file" name="income_documents[]" accept=".pdf,.jpg,.jpeg,.png" multiple
                                title="W-2, pay stubs, or bank records that show household income (optional)."
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-[#213430] focus:outline-none focus:ring-2 focus:ring-pink-300">
                        </div>
                        <div>
                            <label class="block font-medium mb-1" title="Any extra paperwork that supports your application">Additional supporting documents (optional)</label>
                            <input type="file" name="documents[]" accept=".pdf,.jpg,.jpeg,.png" multiple
                                title="Other documents you want the review team to see (optional)."
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
        const financialAssistanceClosed = @json($financialAssistanceClosed ?? false);
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

            /* Omit fields duplicated elsewhere; omit Program date (event_date) when empty — Date & Time above may still show derived dates. */
            const filteredFields = Array.isArray(fields)
                ? fields.filter((field) => {
                    if (!field?.name) {
                        return false;
                    }
                    if (['payment_type', 'program_fund', 'max_applications', 'status', 'description'].includes(field.name)) {
                        return false;
                    }
                    if (field.name === 'event_date') {
                        const v = field.value;
                        if (v === null || v === undefined || String(v).trim() === '') {
                            return false;
                        }
                    }
                    return true;
                })
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

        /**
         * From program list: open application form when eligible; otherwise show details modal.
         */
        function openApplyFromCard(programId) {
            currentProgramId = programId;
            fetch('{{ url('/patient/programs') }}/' + programId, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    currentProgramTitle = data.title || '';
                    const registrationInfo = data.registration || null;
                    const alreadyRegistered = registrationInfo !== null || registeredPrograms.map(Number).includes(Number(programId));
                    const applicationOpen = data.is_application_open !== false;
                    const atCapacity = data.is_at_capacity === true;
                    currentProgramType = data.program_type || 'financial_assistance';

                    if (alreadyRegistered || !applicationOpen || atCapacity) {
                        openModal(programId);
                        return;
                    }

                    if (currentProgramType === 'moments_that_matter') {
                        document.getElementById('mtm_program_id').value = programId;
                        const mtmTitle = document.getElementById('mtm-modal-program-title');
                        if (mtmTitle) {
                            mtmTitle.textContent = currentProgramTitle || 'Care package application';
                        }
                        openMtmApplicationModal();
                        return;
                    }

                    document.getElementById('program_id').value = programId;
                    const selectedProgramName = document.getElementById('selected-program-name');
                    if (selectedProgramName) {
                        selectedProgramName.textContent = currentProgramTitle || 'N/A';
                    }
                    syncProgramSelection();
                    document.getElementById('registerModal').classList.add('hidden');
                    document.getElementById('popupModal').classList.remove('hidden');
                    setTimeout(() => {
                        initSignaturePad();
                    }, 50);
                })
                .catch(err => {
                    alert('Failed to load program.');
                    console.error(err);
                });
        }

        let currentProgramId = null;
        let currentProgramTitle = "";
        let currentProgramType = 'financial_assistance';

        function openMtmApplicationModal() {
            document.getElementById('registerModal')?.classList.add('hidden');
            document.getElementById('popupModal')?.classList.add('hidden');
            const modal = document.getElementById('mtmPopupModal');
            if (modal) {
                modal.classList.remove('hidden');
            }
            initMtmSignaturePad();
        }

        function updateProgramSponsor(prefix, data) {
            const block = document.getElementById(prefix + '-sponsor-block');
            const logoWrap = document.getElementById(prefix + '-sponsor-logo-wrap');
            const logo = document.getElementById(prefix + '-sponsor-logo');
            const nameEl = document.getElementById(prefix + '-sponsor-name');
            if (!block) return;

            const name = (data?.sponsor_name || '').trim();
            const logoUrl = (data?.sponsor_logo || '').trim();
            const hasSponsor = !!(name || logoUrl);

            block.classList.toggle('hidden', !hasSponsor);

            if (nameEl) {
                nameEl.textContent = name;
                nameEl.classList.toggle('hidden', !name);
            }

            if (logo && logoWrap) {
                if (logoUrl) {
                    logo.src = logoUrl;
                    logo.alt = name ? (name + ' logo') : 'Sponsor logo';
                    logo.onerror = () => {
                        logoWrap.classList.add('hidden');
                    };
                    logoWrap.classList.remove('hidden');
                } else {
                    logo.src = '';
                    logoWrap.classList.add('hidden');
                }
            }
        }

        const syncProgramSelection = () => {
            const radios = document.querySelectorAll('input[name="programs_applied"]');
            if (!radios.length) {
                return;
            }
            const hasChecked = Array.from(radios).some((r) => r.checked);
            const normalizedTitle = (currentProgramTitle || '').trim().toLowerCase();
            if (!normalizedTitle || hasChecked) {
                return;
            }
            radios.forEach((radio) => {
                const normalizedValue = (radio.value || '').trim().toLowerCase();
                if (normalizedValue === normalizedTitle) {
                    radio.checked = true;
                }
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
                    currentProgramType = data.program_type || 'financial_assistance';
                    updateProgramSponsor('modal', data);
                    document.querySelector('#registerModal .modal-title').textContent = data.title || '—';
                    document.querySelector('#registerModal .modal-description').textContent = data.description || 'â€”';
                    document.querySelector('#registerModal .modal-date').textContent = data.event_date || 'â€”';
                    document.querySelector('#registerModal .modal-time').textContent = data.event_time || 'â€”';

                    const effectiveStatusEl = document.querySelector('#registerModal .modal-effective-status');
                    if (effectiveStatusEl) {
                        effectiveStatusEl.textContent = data.effective_status_label || 'Upcoming';
                    }
                    const rangeEl = document.getElementById('modal-application-range');
                    if (rangeEl) {
                        if (data.application_start_date || data.application_end_date) {
                            const start = data.application_start_date || '—';
                            const end = data.application_end_date || '—';
                            rangeEl.textContent = 'Application window: ' + start + ' – ' + end;
                            rangeEl.classList.remove('hidden');
                        } else {
                            rangeEl.textContent = '';
                            rangeEl.classList.add('hidden');
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
                    const alreadyRegistered = registrationInfo !== null || registeredPrograms.map(Number).includes(Number(id));

                    if (alreadyRegistered) {
                        registerButton.classList.remove('hidden');
                        // Already registered: disable the button and update text/style
                        registerButton.textContent = 'Application submitted';
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
                        // Not registered: Apply only when program is effectively ongoing (applications open)
                        const applicationOpen = data.is_application_open !== false;
                        if (data.effective_status === 'upcoming') {
                            registerButton.classList.add('hidden');
                            registerButton.disabled = true;
                            registerButton.onclick = null;
                        } else if (applicationOpen) {
                            registerButton.classList.remove('hidden');
                            registerButton.textContent = 'Apply now';
                            registerButton.disabled = false;
                            registerButton.classList.remove('bg-gray-400', 'opacity-50', 'cursor-not-allowed');
                            registerButton.classList.add('bg-pink', 'hover:bg-pink-dark');
                            registerButton.onclick = () => openRegistrationForm();
                        } else {
                            registerButton.classList.remove('hidden');
                            registerButton.textContent = 'Applications closed';
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
            if (currentProgramType === 'moments_that_matter') {
                document.getElementById('mtm_program_id').value = currentProgramId;
                const mtmTitle = document.getElementById('mtm-modal-program-title');
                if (mtmTitle) {
                    mtmTitle.textContent = currentProgramTitle || 'Care package application';
                }
                openMtmApplicationModal();
                return;
            }

            document.getElementById('program_id').value = currentProgramId;
            const selectedProgramName = document.getElementById('selected-program-name');
            if (selectedProgramName) {
                selectedProgramName.textContent = currentProgramTitle || 'N/A';
            }
            syncProgramSelection();

            document.getElementById('registerModal').classList.add('hidden');
            document.getElementById('popupModal').classList.remove('hidden');
            setTimeout(() => initSignaturePad(), 50);
        }

        function closeModal() {
            document.getElementById('registerModal').classList.add('hidden');
        }

        const authRadios = document.querySelectorAll('input[name="authorization_choice"]');
        const authExtra = document.querySelector('[data-auth-extra]');
        const syncPermissions = () => {
            if (!authExtra) return;
            const allowSelected = Array.from(authRadios).some((r) => r.checked && r.value === 'allow');
            authExtra.classList.toggle('hidden', !allowSelected);
        };

        authRadios.forEach((radio) => radio.addEventListener('change', syncPermissions));
        syncPermissions();

        (function () {
            const billBlocksContainer = document.getElementById('patient-bill-blocks');
            const billBlockTemplate = document.getElementById('patient-bill-block-template');
            const billAddBtn = document.getElementById('patient-bill-add');
            const foodAssistFollowup = document.getElementById('food-assistance-followup');
            const medicalBillsFollowup = document.getElementById('medical-bills-followup');
            const BILL_MAX_AMOUNT = 500;

            const renumberBillBlocks = () => {
                if (!billBlocksContainer) {
                    return;
                }
                const blocks = billBlocksContainer.querySelectorAll('.patient-bill-block');
                const n = blocks.length;
                blocks.forEach((block, i) => {
                    const label = block.querySelector('.patient-bill-block-label');
                    if (label) {
                        label.textContent = 'Bill ' + (i + 1);
                    }
                    const btn = block.querySelector('.patient-bill-remove');
                    if (btn) {
                        btn.disabled = n <= 1;
                    }
                });
            };

            const appendBillBlock = () => {
                if (!billBlocksContainer || !billBlockTemplate?.content?.firstElementChild) {
                    return;
                }
                const node = billBlockTemplate.content.firstElementChild.cloneNode(true);
                node.querySelectorAll('input').forEach((el) => {
                    el.value = '';
                });
                const sel = node.querySelector('select[name="bill_support_expense[]"]');
                if (sel) {
                    sel.selectedIndex = 0;
                }
                billBlocksContainer.appendChild(node);
                renumberBillBlocks();
                const amountInput = node.querySelector('input[name="bill_amount[]"]');
                if (amountInput) {
                    amountInput.setAttribute('max', String(BILL_MAX_AMOUNT));
                }
                node.querySelector('input[name="bill_name[]"]')?.focus();
            };

            const syncAssistanceFollowups = () => {
                if (!billBlocksContainer) {
                    return;
                }
                const selectedValues = Array.from(
                    billBlocksContainer.querySelectorAll('select[name="bill_support_expense[]"]')
                ).map((el) => (el.value || '').trim().toLowerCase());
                const hasMedicalBills = selectedValues.includes('medical bills');
                const hasFoodAssistance = selectedValues.includes('food assistance');

                foodAssistFollowup?.classList.toggle('hidden', !hasMedicalBills);
                medicalBillsFollowup?.classList.toggle('hidden', !hasFoodAssistance);
            };

            billAddBtn?.addEventListener('click', appendBillBlock);
            billBlocksContainer?.addEventListener('click', (e) => {
                const btn = e.target.closest('.patient-bill-remove');
                if (!btn || btn.disabled) {
                    return;
                }
                const block = btn.closest('.patient-bill-block');
                if (!block || billBlocksContainer.querySelectorAll('.patient-bill-block').length <= 1) {
                    return;
                }
                block.remove();
                renumberBillBlocks();
                syncAssistanceFollowups();
            });
            billBlocksContainer?.addEventListener('change', (e) => {
                if (e.target.matches('select[name="bill_support_expense[]"]')) {
                    syncAssistanceFollowups();
                }
            });
            billBlocksContainer?.addEventListener('input', (e) => {
                if (!e.target.matches('input[name="bill_amount[]"]')) {
                    return;
                }
                const raw = (e.target.value || '').toString().trim();
                if (raw === '') {
                    return;
                }
                const value = Number(raw);
                if (Number.isNaN(value)) {
                    return;
                }
                if (value > BILL_MAX_AMOUNT) {
                    e.target.value = String(BILL_MAX_AMOUNT);
                    e.target.setCustomValidity('Amount cannot exceed $500.');
                    e.target.reportValidity();
                } else {
                    e.target.setCustomValidity('');
                }
            });
            billBlocksContainer?.querySelectorAll('input[name="bill_amount[]"]').forEach((input) => {
                input.setAttribute('max', String(BILL_MAX_AMOUNT));
            });

            renumberBillBlocks();
            syncAssistanceFollowups();
        })();

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

        const mtmSignatureCanvas = document.getElementById('mtm-signature-pad');
        const mtmSignatureInput = document.getElementById('mtm_signature_data');
        const mtmClearBtn = document.getElementById('mtm-signature-clear');
        let mtmSignaturePad = null;

        const syncMtmSignature = () => {
            if (!mtmSignaturePad || !mtmSignatureInput) return;
            mtmSignatureInput.value = mtmSignaturePad.isEmpty() ? '' : mtmSignaturePad.toDataURL('image/png');
        };

        const initMtmSignaturePad = () => {
            if (!mtmSignatureCanvas || !window.SignaturePad) return;

            const setup = () => {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const width = mtmSignatureCanvas.offsetWidth || mtmSignatureCanvas.parentElement?.clientWidth || 400;
                const height = 160;
                mtmSignatureCanvas.width = width * ratio;
                mtmSignatureCanvas.height = height * ratio;
                mtmSignatureCanvas.style.width = width + 'px';
                mtmSignatureCanvas.style.height = height + 'px';
                const ctx = mtmSignatureCanvas.getContext('2d');
                ctx.setTransform(1, 0, 0, 1, 0, 0);
                ctx.scale(ratio, ratio);

                if (!mtmSignaturePad) {
                    mtmSignaturePad = new SignaturePad(mtmSignatureCanvas, {
                        backgroundColor: '#ffffff',
                        penColor: '#9E2469',
                    });
                    mtmSignaturePad.addEventListener('endStroke', syncMtmSignature);
                }
                mtmSignaturePad.clear();
                syncMtmSignature();
            };

            requestAnimationFrame(() => requestAnimationFrame(setup));
        };

        if (mtmClearBtn) {
            mtmClearBtn.addEventListener('click', () => {
                if (mtmSignaturePad) {
                    mtmSignaturePad.clear();
                    syncMtmSignature();
                }
            });
        }

        const mtmForm = document.querySelector('#mtmPopupModal form');
        mtmForm?.addEventListener('submit', (e) => {
            syncMtmSignature();
            if (!mtmSignatureInput?.value) {
                e.preventDefault();
                alert('Please add your signature before submitting.');
            }
        });

        document.querySelectorAll('input[name="applying_for"]').forEach((radio) => {
            radio.addEventListener('change', () => {
                const wrap = document.getElementById('mtm-loved-one-wrap');
                if (!wrap) return;
                const val = document.querySelector('input[name="applying_for"]:checked')?.value;
                wrap.classList.toggle('hidden', val === 'self' || !val);
            });
        });

        document.querySelectorAll('input[name="mtm_treatment_status"]').forEach((radio) => {
            radio.addEventListener('change', () => {
                const wrap = document.getElementById('mtm-treatment-other-wrap');
                if (!wrap) return;
                wrap.classList.toggle('hidden', document.querySelector('input[name="mtm_treatment_status"]:checked')?.value !== 'other');
            });
        });

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
            const programRadios = document.querySelectorAll('input[name="programs_applied"]');
            const hasProgramSelected = Array.from(programRadios).some((r) => r.checked);
            if (!hasProgramSelected) {
                e.preventDefault();
                alert('Please select one program before submitting.');
                return;
            }
            if (storyField && countWords(storyField.value) > MAX_STORY_WORDS) {
                e.preventDefault();
                alert('Your story may not exceed 1000 words. Please shorten it.');
                return;
            }
            const amountInputs = document.querySelectorAll('input[name="bill_amount[]"]');
            for (const input of amountInputs) {
                const raw = (input.value || '').toString().trim();
                if (raw === '') {
                    continue;
                }
                const value = Number(raw);
                if (!Number.isNaN(value) && value > 500) {
                    e.preventDefault();
                    alert('Amount Due cannot exceed $500 per bill entry.');
                    input.focus();
                    return;
                }
            }
            if (!signatureInput.value) {
                e.preventDefault();
                alert('Please add your signature before submitting.');
            }
        });
    </script>
@endpush
