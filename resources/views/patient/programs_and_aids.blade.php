@extends('patient.layouts.app')

@section('title', 'Programs & Aids')

@section('content')
    @php
        // Fetch IDs of programs the current user has registered for
        $registeredProgramIds = \App\Models\ProgramRegistration::where('user_id', auth()->id())
            ->pluck('program_id')
            ->toArray();

        $upcomingPrograms = \App\Models\Program::where('status', 'upcoming')->get();
        $ongoingPrograms = \App\Models\Program::where('status', 'ongoing')->get();
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
                            <img src="{{ url('storage/' . $program->banner) ? url('storage/' . $program->banner) : url('images/program-details.png') }}" alt="{{ $program->title }}"
                                class="w-full h-full object-cover" />
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-[#213430] mb-1 program-h">{{ $program->title }}</h3>
                            <p class="text-sm text-[#91848C] program-p">{{ $program->description }}</p>
                        </div>
                    </div>
                    <button onclick="openModal({{ $program->id }})"
                        class="bg-transparent border border-[#213430] text-[#213430] hover:bg-[#DB69A2] hover:border-none hover:text-white py-4 px-8 rounded-lg program-btn">
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
                            <img src="{{ url('storage/' . $program->banner) }}" alt="{{ $program->title }}"
                                class="w-full h-full object-cover" />
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-[#213430] mb-1 program-h">{{ $program->title }}</h3>
                            <p class="text-sm text-[#91848C] program-p">{{ $program->description }}</p>
                        </div>
                    </div>
                    <button onclick="openModal({{ $program->id }})"
                        class="bg-transparent border border-[#213430] text-[#213430] hover:bg-[#DB69A2] hover:border-none hover:text-white py-4 px-8 rounded-lg program-btn">
                        View Details
                    </button>
                </div>
            @empty
                <p class="text-[#91848C]">No ongoing programs found.</p>
            @endforelse
        </div>
    </main>

    <!-- Modal -->
    <div id="registerModal" class="modal-overlay fixed inset-0 z-50 bg-black/50 hidden flex items-center">
        <div class="modal-content p-4 max-w-2xl w-full bg-[#F3E8EF] rounded-2xl shadow-lg overflow-y-auto max-h-[95vh]">
            <div class="border border-[#DCCFD8] p-2 rounded-md">
                <!-- Modal Header -->
                <div class="p-2 mb-2 border-b border-[#DCCFD8] rounded-md">
                    <h2 class="text-2xl font-semibold text-gray-900 program-main modal-title">Loading...</h2>
                </div>

                <!-- Image -->
                <div class="w-full h-64 overflow-hidden rounded-md mb-2">
                    <img src="{{ asset('public/images/program-details.png') }}" alt="Program Banner" class="modal-banner w-full h-full object-cover">
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
                        <h3 class="text-lg font-medium text-[#213430] mb-4 app-main">About This Program</h3>
                        <div class="bg-[#F3E8EF] p-4 rounded-lg space-y-2 border border-[#DCCFD8]">
                            <p class="text-[#91848C] app-text">
                                This program aims to provide support, raise awareness, and empower individuals affected by
                                breast cancer.
                            </p>
                            <ul class="list-none space-y-2 text-sm app-text modal-features">
                                <li class="flex items-center gap-2 font-medium"><span class="text-pink text-2xl">•</span>
                                    Free Breast Cancer Screenings &amp; Consultations</li>
                                <li class="flex items-center gap-2 font-medium"><span class="text-pink text-2xl">•</span>
                                    Inspirational Stories from Survivors</li>
                                <li class="flex items-center gap-2 font-medium"><span class="text-pink text-2xl">•</span>
                                    Financial Assistance Info</li>
                                <li class="flex items-center gap-2 font-medium"><span class="text-pink text-2xl">•</span>
                                    Wellness &amp; Nutrition Advice</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Sponsored By -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4 app-main">Sponsored By</h3>
                        <div class="border border-[#DCCFD8] p-2 rounded-md">
                            <div class="flex items-start gap-4 p-4 rounded-md max-w-xl">
                                <!-- Logo -->
                                <div
                                    class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center overflow-hidden">
                                    <img class="modal-sponsor-logo w-10 h-10 object-contain" src="{{ asset('public/images/logo-white.png') }}"
                                        alt="Sponsor Logo">
                                </div>

                                <!-- Right side content -->
                                <div class="flex flex-col">
                                    <!-- Heading -->
                                    <span class="font-semibold text-[#213430] mb-1 app-text modal-sponsor-name">-</span>

                                    <!-- Contact Info -->
                                    <div class="flex flex-col sm:flex-row gap-2 text-sm text-gray-600 app-text">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-phone-alt text-pink-500"></i>
                                            <span class="modal-sponsor-phone">-</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i class="far fa-envelope text-pink-500"></i>
                                            <span class="modal-sponsor-email">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="bg-[#E4D6DF] p-4 rounded-lg text-sm app-text">
                                <h4 class="font-semibold text-[#213430] mb-1">ABOUT US</h4>
                                <p class="text-[#213430] modal-sponsor-about">-</p>
                            </div> --}}
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
    <div id="popupModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <!-- Modal Box -->
        <div class="bg-[#F3E8EF] p-6 rounded-lg w-full max-w-4xl relative overflow-y-auto max-h-[90vh] shadow-lg">

            <!-- Close Button -->
            <button onclick="document.getElementById('popupModal').classList.add('hidden')"
                class="absolute top-4 right-4 text-[#91848C] hover:text-black text-2xl font-bold">
                ×
            </button>

            <!-- Modal Title -->
            <h2 class="text-lg font-medium text-black app-main mb-4">Apply for Program Assistance</h2>

            <!-- Form Start -->
            <form action="{{ route('program.register') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="program_id" id="program_id" value="">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block font-light text-md text-[#213430] mb-1 app-text">First
                            Name:</label>
                        <input type="text" id="first_name" name="first_name"
                            class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                            required>
                    </div>
                    <div>
                        <label for="last_name" class="block font-light text-md text-[#213430] mb-1 app-text">Last
                            Name:</label>
                        <input type="text" id="last_name" name="last_name"
                            class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                            required>
                    </div>
                    <div>
                        <label for="email" class="block font-light text-md text-[#213430] mb-1 app-text">Email:</label>
                        <input type="email" id="email" name="email"
                            class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                            required>
                    </div>
                    <div>
                        <label for="phone" class="block font-light text-md text-[#213430] mb-1 app-text">Phone:</label>
                        <input type="text" id="phone" name="phone"
                            class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                            required>
                    </div>
                    <div class="md:col-span-1">
                        <label for="dob" class="block font-light text-md text-[#213430] mb-1 app-text">Date of
                            Birth:</label>
                        <input type="date" id="dob" name="dob"
                            class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                            required>
                    </div>
                    <div class="md:col-span-1">
                        <label for="gender"
                            class="block font-light text-md text-[#213430] mb-1 app-text">Gender:</label>
                        <select id="gender" name="gender"
                            class="appearance-none w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                            required>
                            <option value="">Select Gender</option>
                            <option>Female</option>
                            <option>Male</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <label for="condition" class="block font-light text-md text-[#213430] mb-1 app-text">Medical
                            Condition:</label>
                        <input type="text" id="condition" name="medical_condition"
                            class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                            required>
                    </div>
                    <div class="md:col-span-1">
                        <label for="assistance" class="block font-light text-md text-[#213430] mb-1 app-text">Assistance
                            Needed:</label>
                        <select id="assistance" name="assistance_type"
                            class="appearance-none w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                            required>
                            <option value="">Select Assistance Type</option>
                            <option>Medical Bill Support</option>
                            <option>Post-Surgery Recovery</option>
                            <option>Nutrition & Wellness</option>
                            <option>Transport Aid</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="justification" class="block font-light text-md text-[#213430] mb-1 app-text">Why do
                            you need support?</label>
                        <textarea id="justification" name="justification" rows="3"
                            class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                            required></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label for="documents" class="block font-light text-md text-[#213430] mb-1 app-text">Upload
                            Supporting Documents:</label>
                        <input type="file" id="documents" name="documents[]" multiple
                            class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                            required>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex mt-8 space-x-4">
                    <button type="button" onclick="document.getElementById('popupModal').classList.add('hidden')"
                        class="px-6 py-2 bg-[#FFF7FC] text-[#91848C] border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-[#DB69A2] text-white rounded-md hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/patient/dashboard.js') }}"></script>
    <script>
        // List of program IDs the current user has already registered for
        const registeredPrograms = @json($registeredProgramIds);

        function openRegistrationModal(programId) {
            document.getElementById('popupModal').classList.remove('hidden');
            document.getElementById('program_id').value = programId;
        }

        let currentProgramId = null;

        function openModal(id) {
            currentProgramId = id; // Save for use when opening register modal

            fetch('{{ url('/patient/programs') }}/' + id, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    document.querySelector('#registerModal .modal-title').textContent = data.title || '—';
                    document.querySelector('#registerModal .modal-description').textContent = data.description || '—';
                    document.querySelector('#registerModal .modal-date').textContent = data.event_date || '—';
                    document.querySelector('#registerModal .modal-time').textContent = data.event_time || '—';

                    document.querySelector('#registerModal .modal-banner').src = data.banner || "{{ asset('public/images/program-details.png') }}";

                    // Sponsor
                    const sponsor = data.sponsor || {};
                    document.querySelector('.modal-sponsor-name').textContent = sponsor.name || '—';
                    document.querySelector('.modal-sponsor-phone').textContent = sponsor.phone || '—';
                    document.querySelector('.modal-sponsor-email').textContent = sponsor.email || '—';
                    if (sponsor.logo) {
                        const logoSrc = sponsor.logo.startsWith('http') ? sponsor.logo : "{{ asset('') }}" + sponsor.logo.replace(/^\/+/, '');
                        document.querySelector('.modal-sponsor-logo').src = logoSrc;
                    } else {
                        document.querySelector('.modal-sponsor-logo').src = "{{ asset('public/images/logo-white.png') }}";
                    }
                    // document.querySelector('.modal-sponsor-about').textContent = sponsor.about || '—';

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
                        // Not registered: ensure button is enabled and styled as primary
                        registerButton.textContent = 'Register Yourself';
                        registerButton.disabled = false;
                        registerButton.classList.remove('bg-gray-400', 'opacity-50', 'cursor-not-allowed');
                        if (!registerButton.classList.contains('bg-pink')) {
                            registerButton.classList.add('bg-pink');
                        }
                        if (!registerButton.classList.contains('hover:bg-pink-dark')) {
                            registerButton.classList.add('hover:bg-pink-dark');
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

            // hide details modal, show registration modal
            document.getElementById('registerModal').classList.add('hidden');
            document.getElementById('popupModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('registerModal').classList.add('hidden');
        }
    </script>
@endpush
