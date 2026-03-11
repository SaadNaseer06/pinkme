@extends('patient.layouts.app')

@section('title', 'Add Application')

@section('content')
    <main>
        <div class="flex gap-6 max-w-8xl mx-auto mt-6 mb-48 form-b">
            <div class="bg-[#F3E8EF] rounded-lg p-8 w-full">
                <h2 class="text-xl text-gray-700 font-medium pb-4 border-b border-[#DCCFD8] mb-4 app-main">
                    New Application
                </h2>

                {{-- @if (session('success'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
                        class="fixed top-6 right-6 z-50 bg-green-600 text-white px-6 py-3 rounded-md shadow-md transition-all">
                        {{ session('success') }}
                    </div>
                @endif


                @if (session('error'))
                    <div class="mb-6 px-4 py-3 text-red-800 bg-red-100 border border-red-200 rounded-md shadow-sm">
                        {{ session('error') }}
                    </div>
                @endif --}}


                <form method="POST" action="{{ route('patient.storeApplication') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div class="div">
                            <label for="title" class="block text-[#213430] mb-1">Title:</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}"
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#F3E8EF] text-[#213430] placeholder-[#B1A4AD]"
                                placeholder="Enter a title for your application">
                        </div>

                        <!-- Age -->
                        {{-- <div>
                            <label for="age" class="block text-[#213430] mb-1">Age:</label>
                            <select name="age" id="age"
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#F3E8EF] text-[#B1A4AD]">
                                <option value="">Select</option>
                                @for ($i = 1; $i <= 100; $i++)
                                    <option value="{{ $i }}" {{ old('age') == $i ? 'selected' : '' }}>
                                        {{ $i }}</option>
                                @endfor
                            </select>
                        </div> --}}

                        <!-- Blood Group -->
                        <div>
                            <label for="blood_group" class="block text-[#213430] mb-1">Blood Group:</label>
                            <div class="relative">
                                <select name="blood_group" id="blood_group"
                                    class="appearance-none w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#F3E8EF] text-[#B1A4AD] focus:outline-none focus:ring-2 focus:ring-pink-300">
                                    <option value="">Select</option>
                                    @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                                        <option value="{{ $group }}"
                                            {{ old('blood_group') == $group ? 'selected' : '' }}>{{ $group }}
                                        </option>
                                    @endforeach
                                </select>
                                <img src="{{ asset('public/images/down-arrow.svg') }}" alt="arrow"
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 w-4 h-4 pointer-events-none">
                            </div>
                        </div>

                        <!-- Assistance -->
                        <div>
                            <label for="assistance_type" class="block text-[#213430] mb-1">Assistance Needed:</label>
                            <select name="assistance_type" id="assistance_type"
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#F3E8EF] text-[#B1A4AD]">
                                <option value="">Select</option>
                                @foreach ([
            'surgery_support' => 'Surgery Support',
            'wound_care' => 'Wound Care',
            'mobility_support' => 'Mobility Support',
            'medication' => 'Medication Management',
            'iv_therapy' => 'IV Therapy',
            'catheter_care' => 'Catheter Care',
            'respiratory_support' => 'Respiratory Support',
            'feeding_assistance' => 'Feeding Assistance',
            'hygiene' => 'Personal Hygiene & Bathing',
        ] as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('assistance_type') == $key ? 'selected' : '' }}>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Program -->
                        <div>
                            <label for="program_id" class="block text-[#213430] mb-1">Select Program:</label>
                            <select name="program_id" id="program_id"
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#F3E8EF] text-[#213430]">
                                <option value="" disabled {{ old('program_id') ? '' : 'selected' }}>
                                    {{ $programs->isEmpty() ? 'No ongoing programs available' : 'Select a program' }}
                                </option>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->id }}"
                                        {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                        {{ $program->title }}
                                        @if ($program->event_date)
                                            — {{ \Carbon\Carbon::parse($program->event_date)->format('M d, Y') }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- File Upload -->
                        <div>
                            <label for="documents" class="block text-[#213430] mb-1">Upload Required Documents</label>
                            <div class="relative w-full">
                                <input type="file" name="documents[]" id="documents" multiple
                                    class="opacity-0 absolute inset-0 z-50 cursor-pointer">
                                <div
                                    class="flex items-center w-full rounded-md border border-[#DCCFD8] bg-[#F3E8EF] text-[#B1A4AD] pointer-events-none">
                                    <div class="bg-[#DCCFD8] px-4 py-2 rounded-l-md text-[#8C7A87]">Choose File</div>
                                    <div class="ml-3 truncate">No File Chosen</div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-[#213430] mb-1">Medical Condition Details</label>
                            <textarea name="description" id="description" rows="6"
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#F3E8EF] text-[#B1A4AD]">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="flex mt-8 space-x-4">
                        <a href="{{ url()->previous() }}"
                            class="px-6 py-2 bg-[#FFF7FC] text-[#91848C] border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-[#9E2469] text-white rounded-md hover:opacity-90">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script src="{{ asset('js/patient/dashboard.js') }}"></script>

    <script src="{{ asset('js/patient/dashboard.js') }}"></script>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            @if (session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true,
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: "{{ session('error') }}",
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true,
                });
            @endif
        </script>
    @endpush
@endsection
