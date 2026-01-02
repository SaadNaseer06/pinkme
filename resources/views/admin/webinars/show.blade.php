@extends('admin.layouts.admin')

@section('title', $webinar->title)

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-[#213430]">{{ $webinar->title }}</h1>
            <p class="text-[#6C5B68]">Scheduled {{ $webinar->date_label ?? 'TBD' }} {{ $webinar->time_label ? 'at ' . $webinar->time_label : '' }}</p>
        </div>
        <div class="space-x-3">
            <a href="{{ route('admin.webinars.edit', $webinar) }}" class="px-4 py-2 rounded-lg border border-[#E6D7E7] text-[#213430]">Edit</a>
            <a href="{{ route('admin.webinars.index') }}" class="px-4 py-2 rounded-lg bg-pink text-white font-medium shadow hover:bg-pink-dark transition">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-[#F1E4EC] p-6 space-y-4">
            <div class="flex flex-wrap items-center gap-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                    @if($webinar->status === 'cancelled') bg-red-100 text-red-700
                    @elseif($webinar->status === 'completed' || ($webinar->scheduled_at && $webinar->scheduled_at->isPast()))
                        bg-green-100 text-green-700
                    @else
                        bg-pink-light text-pink
                    @endif">
                    {{ $webinar->status_label }}
                </span>
                <span class="text-xs font-semibold text-[#213430] px-3 py-1 rounded-full bg-[#F9F4F8]">
                    {{ $webinar->audience_label }}
                </span>
                @if($webinar->presenter)
                    <span class="text-sm text-[#6C5B68]">Presenter: {{ $webinar->presenter }}</span>
                @endif
                @if($webinar->duration_minutes)
                    <span class="text-sm text-[#6C5B68]">{{ $webinar->duration_minutes }} minutes</span>
                @endif
            </div>

            @if($webinar->join_url)
                <div class="p-4 bg-[#F9F4F8] rounded-lg text-sm text-[#213430]">
                    <span class="font-semibold">Join URL:</span>
                    <a href="{{ $webinar->join_url }}" target="_blank" class="text-pink break-words">{{ $webinar->join_url }}</a>
                </div>
            @endif

            <div>
                <h3 class="text-lg font-semibold text-[#213430] mb-2">Description</h3>
                <p class="text-[#6C5B68] whitespace-pre-line">{{ $webinar->description ?? 'No description provided.' }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-[#F1E4EC] p-6">
            <h3 class="text-lg font-semibold text-[#213430] mb-4">Attendees</h3>
            <div class="flex items-center justify-between text-sm mb-4">
                <span class="text-[#6C5B68]">Registered</span>
                <span class="font-semibold text-[#213430]">{{ $webinar->registrations->where('status', 'registered')->count() }}</span>
            </div>
            @if($webinar->max_attendees)
                <div class="flex items-center justify-between text-sm mb-4">
                    <span class="text-[#6C5B68]">Capacity</span>
                    <span class="font-semibold text-[#213430]">{{ $webinar->max_attendees }}</span>
                </div>
            @endif

            <div class="space-y-3 max-h-80 overflow-y-auto">
                @forelse($webinar->registrations as $registration)
                    <div class="border border-[#F1E4EC] rounded-lg p-3">
                        <div class="font-semibold text-[#213430]">
                            {{ $registration->user->profile->full_name ?? $registration->user->email }}
                        </div>
                        <div class="text-xs text-[#6C5B68] capitalize">
                            Role: {{ $registration->role_name ?? 'N/A' }} &nbsp;•&nbsp; Status: {{ $registration->status_label }}
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-[#6C5B68]">No registrations yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
