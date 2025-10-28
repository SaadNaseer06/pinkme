@extends('patient.layouts.app')

@section('title', 'Edit Application')

@section('content')
    <main>
        <div class="flex gap-6 max-w-8xl mx-auto mt-6 mb-48 form-b">
            <div class="bg-[#F3E8EF] rounded-lg p-8 w-full">
                <h2 class="text-xl text-gray-700 font-medium pb-4 border-b border-[#DCCFD8] mb-4 app-main">
                    Edit Application
                </h2>

                <form method="POST" action="{{ route('patient.updateApplication', $application->id) }}"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-[#213430] mb-1">Title:</label>
                            <input type="text" name="title" id="title"
                                value="{{ old('title', $application->title) }}"
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#F3E8EF] text-[#213430] placeholder-[#B1A4AD]">
                        </div>

                        <!-- Blood Group -->
                        <div>
                            <label for="blood_group" class="block text-[#213430] mb-1">Blood Group:</label>
                            <select name="blood_group" id="blood_group"
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#F3E8EF] text-[#B1A4AD]">
                                <option value="">Select</option>
                                @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                                    <option value="{{ $group }}"
                                        {{ old('blood_group', $application->blood_group) == $group ? 'selected' : '' }}>
                                        {{ $group }}</option>
                                @endforeach
                            </select>
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
                                        {{ old('assistance_type', $application->assistance_type) == $key ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Program -->
                        <div>
                            <label for="program_id" class="block text-[#213430] mb-1">Select Program:</label>
                            <select name="program_id" id="program_id"
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#F3E8EF] text-[#213430]">
                                <option value="" disabled {{ old('program_id', $application->program_id) ? '' : 'selected' }}>
                                    {{ $programs->isEmpty() ? 'No ongoing programs available' : 'Select a program' }}
                                </option>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->id }}"
                                        {{ old('program_id', $application->program_id) == $program->id ? 'selected' : '' }}>
                                        {{ $program->title }}
                                        @if ($program->event_date)
                                            — {{ \Carbon\Carbon::parse($program->event_date)->format('M d, Y') }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Documents -->
                        <div>
                            <label for="documents" class="block text-[#213430] mb-1">Upload Additional Documents</label>
                            <input type="file" name="documents[]" id="documents" multiple
                                class="w-full text-[#213430] file:bg-[#DCCFD8] file:border-none file:px-4 file:py-2 file:rounded-md file:text-[#8C7A87] bg-[#F3E8EF] border border-[#DCCFD8] rounded-md">
                        </div>

                        <!-- Medical Condition -->
                        <div class="md:col-span-2">
                            <label for="medical_condition" class="block text-[#213430] mb-1">Medical Condition
                                Details</label>
                            <textarea name="description" id="medical_condition" rows="6"
                                class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#F3E8EF] text-[#B1A4AD]">{{ old('description', $application->description) }}</textarea>
                        </div>
                    </div>

                    <div class="flex mt-8 space-x-4">
                        <a href="{{ route('patient.applications') }}"
                            class="px-6 py-2 bg-[#FFF7FC] text-[#91848C] border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-[#DB69A2] text-white rounded-md hover:opacity-90">
                            Update Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

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
