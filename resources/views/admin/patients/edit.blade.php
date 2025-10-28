@extends('admin.layouts.admin')

@section('title', 'Edit Patient')

@section('content')
    <div class="flex-1 flex flex-col">
        <main class="flex-1">
            <div class="max-w-8xl mx-auto">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-semibold text-[#213430] app-main">
                        Edit Patient Details
                    </h1>
                    <a href="{{ route('admin.patients.show', $patient) }}"
                        class="px-4 py-2 bg-[#DB69A2] text-white rounded-md hover:bg-[#c25891] transition">
                        View Profile
                    </a>
                </div>

                <div class="bg-[#F3E8EF] rounded-lg p-6">
                    <!--@if ($errors->any())-->
                    <!--    <div class="mb-4 rounded-md bg-red-100 px-4 py-3 text-sm text-red-700">-->
                    <!--        <ul class="list-disc pl-4 space-y-1">-->
                    <!--            @foreach ($errors->all() as $error)-->
                    <!--                <li>{{ $error }}</li>-->
                    <!--            @endforeach-->
                    <!--        </ul>-->
                    <!--    </div>-->
                    <!--@endif-->

                    <!--@if (session('success'))-->
                    <!--    <div class="mb-4 rounded-md bg-green-100 px-4 py-3 text-sm text-green-700">-->
                    <!--        {{ session('success') }}-->
                    <!--    </div>-->
                    <!--@endif-->

                    <form action="{{ route('admin.patients.update', $patient) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-[#213430] mb-1">Full Name</label>
                                <input type="text" name="full_name"
                                    value="{{ old('full_name', $patient->user?->profile?->full_name) }}"
                                    class="w-full rounded-md border border-[#DCCFD8] bg-white px-3 py-2 text-sm text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#DB69A2]"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[#213430] mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email', $patient->user?->email) }}"
                                    class="w-full rounded-md border border-[#DCCFD8] bg-white px-3 py-2 text-sm text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#DB69A2]"
                                    required>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-[#213430] mb-1">Phone</label>
                                <input type="text" name="phone"
                                    value="{{ old('phone', $patient->user?->profile?->phone) }}"
                                    class="w-full rounded-md border border-[#DCCFD8] bg-white px-3 py-2 text-sm text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#DB69A2]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[#213430] mb-1">Blood Group</label>
                                <select name="blood_group"
                                    class="w-full rounded-md border border-[#DCCFD8] bg-white px-3 py-2 text-sm text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#DB69A2]">
                                    <option value="">Select</option>
                                    @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                                        <option value="{{ $group }}"
                                            {{ old('blood_group', $patient->blood_group) === $group ? 'selected' : '' }}>
                                            {{ $group }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-[#213430] mb-1">Disease Type</label>
                                <input type="text" name="disease_type"
                                    value="{{ old('disease_type', $patient->disease_type) }}"
                                    class="w-full rounded-md border border-[#DCCFD8] bg-white px-3 py-2 text-sm text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#DB69A2]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[#213430] mb-1">Disease Stage</label>
                                <input type="text" name="disease_stage"
                                    value="{{ old('disease_stage', $patient->disease_stage) }}"
                                    class="w-full rounded-md border border-[#DCCFD8] bg-white px-3 py-2 text-sm text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#DB69A2]">
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-[#213430] mb-1">Diagnosis</label>
                                <input type="text" name="diagnosis"
                                    value="{{ old('diagnosis', $patient->diagnosis) }}"
                                    class="w-full rounded-md border border-[#DCCFD8] bg-white px-3 py-2 text-sm text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#DB69A2]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[#213430] mb-1">Diagnosis Date</label>
                                @php
                                    $diagnosisDate = $patient->diagnosis_date
                                        ? \Illuminate\Support\Carbon::parse($patient->diagnosis_date)
                                        : null;
                                @endphp
                                <input type="date" name="diagnosis_date"
                                    value="{{ old('diagnosis_date', $diagnosisDate?->format('Y-m-d')) }}"
                                    class="w-full rounded-md border border-[#DCCFD8] bg-white px-3 py-2 text-sm text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#DB69A2]">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#213430] mb-1">Genetic Test</label>
                            <input type="text" name="genetic_test"
                                value="{{ old('genetic_test', $patient->genetic_test) }}"
                                class="w-full rounded-md border border-[#DCCFD8] bg-white px-3 py-2 text-sm text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#DB69A2]">
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4">
                            <a href="{{ route('admin.patients.show', $patient) }}"
                                class="px-4 py-2 border border-[#DCCFD8] text-[#91848C] rounded-md hover:bg-[#F6EDF5] transition">
                                Cancel
                            </a>
                            <button type="submit"
                                class="px-4 py-2 bg-[#DB69A2] text-white rounded-md hover:bg-[#c25891] transition">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
@endsection
