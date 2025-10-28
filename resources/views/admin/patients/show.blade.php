@extends('admin.layouts.admin')

@section('title', 'Patient Profile')

@section('content')
    <div class="flex-1 flex flex-col">
        <main class="flex-1">
            <div class="max-w-8xl mx-auto">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-semibold text-[#213430] app-main">
                        Patient Profile
                    </h1>
                    <a href="{{ route('admin.patients') }}"
                        class="px-4 py-2 bg-[#DB69A2] text-white rounded-md hover:bg-[#c25891] transition">
                        Back to Patients
                    </a>
                </div>

                <div class="bg-[#F3E8EF] rounded-lg p-6 space-y-6">
                    <div class="flex items-center gap-4">
                        <img src="{{ $patient->user?->avatar_url ?? asset('public/images/profile.png') }}"
                            alt="{{ $patient->user?->profile?->full_name ?? 'Patient' }}"
                            class="w-16 h-16 rounded-full object-cover">
                        <div>
                            <p class="text-xl font-semibold text-[#213430]">
                                {{ $patient->user?->profile?->full_name ?? 'Unknown Patient' }}
                            </p>
                            <p class="text-sm text-[#91848C]">{{ $patient->user?->email ?? 'No email on file' }}</p>
                            <p class="text-sm text-[#91848C]">{{ $patient->user?->profile?->phone ?? 'No phone on file' }}</p>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="bg-white/70 rounded-lg border border-[#DCCFD8] p-4">
                            <h3 class="text-sm font-semibold text-[#91848C] uppercase tracking-wide">Medical Details</h3>
                            <dl class="mt-3 space-y-2 text-sm text-[#213430]">
                                <div class="flex justify-between">
                                    <dt>Blood Group</dt>
                                    <dd>{{ $patient->blood_group ?? 'Not specified' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt>Disease Type</dt>
                                    <dd>{{ $patient->disease_type ?? 'Not specified' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt>Disease Stage</dt>
                                    <dd>{{ $patient->disease_stage ?? 'Not specified' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt>Diagnosis</dt>
                                    <dd>{{ $patient->diagnosis ?? 'Not specified' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt>Diagnosis Date</dt>
                                    @php
                                        $diagnosisDate = $patient->diagnosis_date
                                            ? \Illuminate\Support\Carbon::parse($patient->diagnosis_date)
                                            : null;
                                    @endphp
                                    <dd>{{ $diagnosisDate?->format('M d, Y') ?? 'Not specified' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt>Genetic Test</dt>
                                    <dd>{{ $patient->genetic_test ?? 'Not specified' }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div class="bg-white/70 rounded-lg border border-[#DCCFD8] p-4">
                            <h3 class="text-sm font-semibold text-[#91848C] uppercase tracking-wide">Application Summary</h3>
                            <div class="mt-3 space-y-2 text-sm text-[#213430]">
                                <p>Total Applications: {{ $applicationsCount }}</p>
                                @if ($applicationsCount)
                                    <a href="{{ route('admin.patients.applications', $patient) }}"
                                        class="inline-flex items-center text-[#DB69A2] hover:underline">
                                        View all applications
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('admin.patients.edit', $patient) }}"
                            class="px-4 py-2 bg-[#DB69A2] text-white rounded-md hover:bg-[#c25891] transition">
                            Edit Patient Details
                        </a>
                        <a href="{{ route('admin.patients.applications', $patient) }}"
                            class="px-4 py-2 border border-[#DB69A2] text-[#DB69A2] rounded-md hover:bg-[#F6EDF5] transition">
                            View Applications
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
