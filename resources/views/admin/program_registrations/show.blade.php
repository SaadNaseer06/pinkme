@php
    use App\Models\ProgramRegistration;
    $registration->loadMissing(['program', 'user', 'reviewer']);
    $status = strtolower($registration->status);
    $badgeClasses = match ($status) {
        ProgramRegistration::STATUS_APPROVED => 'bg-[#C5E8D1] text-[#20B354] border border-[#A5D0B7]',
        ProgramRegistration::STATUS_REJECTED => 'bg-[#FAD4D4] text-[#B32020] border border-[#E6A5A5]',
        default => 'bg-[#FDE8F3] text-[#DB69A2] border border-[#F4BBD5]',
    };
@endphp

@extends('admin.layouts.admin')

@section('title', 'Program Registration Request')

@section('content')
    <main class="flex-1">
        <div class="max-w-5xl mx-auto">
            <div class="mt-6 bg-[#F3E8EF] rounded-lg p-6 space-y-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-[#DCCFD8] pb-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-[#213430] app-main">Registration Details</h2>
                        <p class="text-sm text-[#91848C] app-text mt-1">
                            Submitted on {{ $registration->created_at?->format('d M Y, h:i A') ?? 'N/A' }}
                        </p>
                    </div>
                    <span class="px-4 py-1 rounded-full text-sm font-semibold app-text {{ $badgeClasses }}">
                        Status: {{ ucfirst($status) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white/60 rounded-lg p-4 space-y-2">
                        <h3 class="text-lg font-semibold text-[#213430] app-main">Applicant</h3>
                        <div class="text-sm text-[#213430] app-text">
                            <p><span class="font-medium">Name:</span> {{ $registration->full_name }}</p>
                            <p><span class="font-medium">Email:</span> {{ $registration->email ?? 'N/A' }}</p>
                            <p><span class="font-medium">Phone:</span> {{ $registration->phone ?? 'N/A' }}</p>
                            <p><span class="font-medium">Date of Birth:</span> {{ $registration->dob?->format('d M Y') ?? 'N/A' }}</p>
                            <p><span class="font-medium">Gender:</span> {{ ucfirst($registration->gender ?? 'N/A') }}</p>
                            <p><span class="font-medium">Blood Group:</span> {{ strtoupper($registration->blood_group ?? 'N/A') }}</p>
                            <p><span class="font-medium">Username:</span> {{ $registration->username }}</p>
                        </div>
                    </div>

                    <div class="bg-white/60 rounded-lg p-4 space-y-2">
                        <h3 class="text-lg font-semibold text-[#213430] app-main">Program</h3>
                        <div class="text-sm text-[#213430] app-text">
                            <p><span class="font-medium">Program Title:</span> {{ $registration->program->title ?? 'N/A' }}</p>
                            <p><span class="font-medium">Medical Condition:</span> {{ $registration->medical_condition ?? 'N/A' }}</p>
                            <p><span class="font-medium">Assistance Type:</span> {{ $registration->assistance_type ?? 'N/A' }}</p>
                        </div>
                        <div class="mt-3">
                            <h4 class="font-medium text-[#213430] text-sm mb-1 app-text">Justification</h4>
                            <p class="text-sm text-[#91848C] app-text whitespace-pre-line">
                                {{ $registration->justification ?? 'No justification provided.' }}
                            </p>
                        </div>
                    </div>
                </div>

                @if (!empty($registration->documents))
                    <div class="bg-white/60 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-[#213430] app-main mb-3">Supporting Documents</h3>
                        <ul class="space-y-2 text-sm app-text">
                            @foreach ($registration->documents as $document)
                                <li class="flex items-center justify-between gap-3 bg-white/70 rounded-md px-3 py-2">
                                    <span class="text-[#213430] truncate">{{ $document['filename'] }}</span>
                                    <a href="{{ $document['url'] }}" target="_blank"
                                        class="inline-flex items-center text-[#DB69A2] hover:underline">Download</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($registration->status === ProgramRegistration::STATUS_PENDING)
                    <div class="bg-white/60 rounded-lg p-4 space-y-6">
                        <h3 class="text-lg font-semibold text-[#213430] app-main">Admin Review</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <form method="POST" action="{{ route('admin.program_registrations.approve', $registration) }}" class="bg-[#F6EDF5] rounded-lg p-4 space-y-3">
                                @csrf
                                <h4 class="font-semibold text-[#213430] app-main">Approve Registration</h4>
                                <p class="text-sm text-[#91848C] app-text">Optional: add a short note for the applicant (visible internally).</p>
                                <textarea name="note" rows="3" class="w-full px-3 py-2 rounded-md border border-[#DCCFD8] bg-transparent text-sm focus:outline-none focus:ring-2 focus:ring-[#DB69A2]" placeholder="Optional note"></textarea>
                                <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-[#20B354] text-white rounded-md text-sm font-semibold hover:bg-[#1A9444] transition">
                                    Approve Request
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.program_registrations.reject', $registration) }}" class="bg-[#F6EDF5] rounded-lg p-4 space-y-3">
                                @csrf
                                <h4 class="font-semibold text-[#213430] app-main">Reject Registration</h4>
                                <p class="text-sm text-[#91848C] app-text">Provide a short reason to keep for the record.</p>
                                <textarea name="note" rows="3" required class="w-full px-3 py-2 rounded-md border border-[#DCCFD8] bg-transparent text-sm focus:outline-none focus:ring-2 focus:ring-[#DB69A2]" placeholder="Reason for rejection"></textarea>
                                <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-[#B32020] text-white rounded-md text-sm font-semibold hover:bg-[#8F1A1A] transition">
                                    Reject Request
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="bg-white/60 rounded-lg p-4 space-y-2 text-sm text-[#213430] app-text">
                        <h3 class="text-lg font-semibold text-[#213430] app-main">Review Summary</h3>
                        <p><span class="font-medium">Reviewed By:</span> {{ $registration->reviewer?->profile->full_name ?? $registration->reviewer?->email ?? 'N/A' }}</p>
                        <p><span class="font-medium">Reviewed At:</span> {{ $registration->reviewed_at?->format('d M Y, h:i A') ?? 'N/A' }}</p>
                        @if ($registration->review_note)
                            <div>
                                <span class="font-medium">Review Note:</span>
                                <p class="text-[#91848C] whitespace-pre-line app-text">{{ $registration->review_note }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="flex justify-between items-center pt-4 border-t border-[#DCCFD8]">
                    <a href="{{ route('admin.program_registrations.index', ['status' => $status === 'pending' ? 'pending' : 'all']) }}"
                        class="inline-flex items-center gap-2 text-sm text-[#91848C] hover:text-[#213430] app-text">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to list
                    </a>
                </div>
            </div>
        </div>
    </main>
@endsection
