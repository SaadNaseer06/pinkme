@php
    use App\Models\ProgramRegistration;
    $registration->loadMissing([
        'program',
        'program.sponsorships.sponsor.profile',
        'program.sponsorships.sponsor.sponsorDetail',
    ]);

    $status = strtolower($registration->status ?? ProgramRegistration::STATUS_PENDING);
    $badgeClass = match ($status) {
        ProgramRegistration::STATUS_APPROVED => 'bg-[#C5E8D1] text-[#20B354] border border-[#A5D0B7]',
        ProgramRegistration::STATUS_REJECTED => 'bg-[#FAD4D4] text-[#B32020] border border-[#E6A5A5]',
        default => 'bg-[#FDE8F3] text-[#DB69A2] border border-[#F4BBD5]',
    };

    $program = $registration->program;
    $latestSponsorship = optional($program)->sponsorships->sortByDesc('date')->sortByDesc('id')->first();
    $sponsorModel = optional($latestSponsorship)->sponsor;
    $sponsorProfile = optional($sponsorModel)->profile;
    $sponsorDetail = optional($sponsorModel)->sponsorDetail;
@endphp

@extends('patient.layouts.app')

@section('title', 'Program Registration')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-[#F3E8EF] rounded-2xl shadow-sm p-6 space-y-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-[#213430] app-main">Program Registration</h1>
                    <p class="text-sm text-[#91848C] app-text">
                        Submitted on {{ optional($registration->created_at)->format('d M Y, h:i A') ?? 'N/A' }}
                    </p>
                </div>
                <span class="px-4 py-1 rounded-full text-sm font-semibold {{ $badgeClass }} app-text">
                    {{ ucfirst($status) }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white/70 rounded-xl p-4 space-y-2">
                    <h2 class="text-lg font-semibold text-[#213430] app-main">Program Details</h2>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Title:</span>
                        {{ optional($program)->title ?? 'N/A' }}</p>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Description:</span>
                        {{ optional($program)->description ?? 'N/A' }}</p>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Event Date:</span>
                        {{ optional(optional($program)->event_date)->format('d M Y') ?? 'N/A' }}</p>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Event Time:</span>
                        {{ optional($program)->event_time ?? 'N/A' }}</p>
                </div>

                <div class="bg-white/70 rounded-xl p-4 space-y-2">
                    <h2 class="text-lg font-semibold text-[#213430] app-main">Applicant</h2>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Name:</span>
                        {{ $registration->full_name }}</p>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Email:</span>
                        {{ $registration->email ?? 'N/A' }}</p>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Phone:</span>
                        {{ $registration->phone ?? 'N/A' }}</p>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Date of Birth:</span>
                        {{ optional($registration->dob)->format('d M Y') ?? 'N/A' }}</p>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Gender:</span>
                        {{ ucfirst($registration->gender ?? 'N/A') }}</p>
                    <p class="text-sm text-[#213430] app-text"><span class="font-medium">Blood Group:</span>
                        {{ strtoupper($registration->blood_group ?? 'N/A') }}</p>
                </div>
            </div>

            <div class="bg-white/70 rounded-xl p-4 space-y-3">
                <h2 class="text-lg font-semibold text-[#213430] app-main">Medical & Assistance Details</h2>
                <p class="text-sm text-[#213430] app-text"><span class="font-medium">Medical Condition:</span>
                    {{ $registration->medical_condition ?? 'N/A' }}</p>
                <p class="text-sm text-[#213430] app-text"><span class="font-medium">Assistance Type:</span>
                    {{ $registration->assistance_type ?? 'N/A' }}</p>
                <div>
                    <span class="text-sm font-medium text-[#213430] app-main">Justification</span>
                    <p class="text-sm text-[#91848C] whitespace-pre-line app-text mt-1">
                        {{ $registration->justification ?? 'No justification provided.' }}</p>
                </div>
            </div>

            @if (!empty($registration->document_paths))
                <div class="bg-white/70 rounded-xl p-4 space-y-3">
                    <h2 class="text-lg font-semibold text-[#213430] app-main">Uploaded Documents</h2>
                    <ul class="space-y-2 text-sm app-text">
                        @foreach ($registration->documents as $document)
                            <li class="flex items-center justify-between gap-3 bg-white/80 rounded-md px-3 py-2">
                                <span class="text-[#213430] truncate">{{ $document['filename'] }}</span>
                                <a href="{{ $document['url'] }}" target="_blank"
                                    class="inline-flex items-center text-[#DB69A2] hover:underline">Download</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white/70 rounded-xl p-4 space-y-3">
                <h2 class="text-lg font-semibold text-[#213430] app-main">Sponsor</h2>
                <div class="flex items-center gap-3">
                    <img src="{{ $sponsorProfile && $sponsorProfile->avatar ? asset('storage/' . $sponsorProfile->avatar) : asset('images/default_sponsor.png') }}"
                        alt="Sponsor logo" class="w-12 h-12 rounded-full object-cover">
                    <div class="text-sm text-[#213430] app-text">
                        <p><span class="font-medium">Name:</span>
                            {{ optional($sponsorDetail)->company_name ?? ($sponsorProfile->full_name ?? 'N/A') }}</p>
                        <p><span class="font-medium">Phone:</span>
                            {{ optional($sponsorDetail)->company_phone ?? ($sponsorProfile->phone ?? 'N/A') }}</p>
                        <p><span class="font-medium">Email:</span>
                            {{ optional($sponsorDetail)->company_email ?? (optional($program)->contact_email ?? 'N/A') }}</p>
                    </div>
                </div>
            </div>

            @if ($registration->review_note)
                <div class="bg-white/70 border border-[#DCCFD8] rounded-xl p-4">
                    <h2 class="text-lg font-semibold text-[#213430] app-main">Admin Note</h2>
                    <p class="text-sm text-[#91848C] whitespace-pre-line app-text">{{ $registration->review_note }}</p>
                </div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('patient.programsAndAids') }}"
                    class="px-5 py-3 border border-[#DCCFD8] text-[#91848C] rounded-md hover:bg-[#F3E8EF] transition app-text">
                    Back to Programs
                </a>
            </div>
        </div>
    </div>
@endsection
