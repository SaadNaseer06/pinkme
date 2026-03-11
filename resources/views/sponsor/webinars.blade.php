@extends('sponsor.layouts.app')

@php use Illuminate\Support\Str; @endphp

@section('title', 'Webinars')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-[#213430]">Webinars</h1>
            <p class="text-[#6C5B68]">Join upcoming webinars hosted by the admin team.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @forelse($webinars as $webinar)
            @php
                $registration = $webinar->current_registration;
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-[#F1E4EC] p-5 flex flex-col gap-3">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h3 class="text-lg font-semibold text-[#213430]">{{ $webinar->title }}</h3>
                        <p class="text-sm text-[#6C5B68]">
                            {{ $webinar->date_label ?? 'Date TBD' }}
                            @if($webinar->time_label)
                                • {{ $webinar->time_label }}
                            @endif
                        </p>
                        @if($webinar->presenter)
                            <p class="text-sm text-[#6C5B68]">Presenter: {{ $webinar->presenter }}</p>
                        @endif
                    </div>
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
                        @if($webinar->status === 'cancelled') bg-red-100 text-red-700
                        @elseif($webinar->status === 'completed' || ($webinar->scheduled_at && $webinar->scheduled_at->isPast()))
                            bg-green-100 text-green-700
                        @else
                            bg-pink-light text-pink
                        @endif">
                        {{ $webinar->status_label }}
                    </span>
                </div>

                <p class="text-sm text-[#6C5B68] line-clamp-3">
                    {{ $webinar->description ? Str::limit($webinar->description, 220) : 'No description added yet.' }}
                </p>

                <div class="flex items-center justify-between text-sm text-[#6C5B68]">
                    <span>Attendees: <strong class="text-[#213430]">{{ $webinar->attendee_count ?? 0 }}</strong></span>
                    @if($webinar->max_attendees)
                        <span>Capacity: {{ $webinar->max_attendees }}</span>
                    @endif
                </div>

                @if($registration && $registration->isRegistered())
                    <div class="p-3 bg-[#F9F4F8] rounded-lg text-sm text-[#213430]">
                        <p class="font-semibold">You are registered.</p>
                        @if($webinar->join_url)
                            <p class="mt-1">
                                Join link:
                                <a href="{{ $webinar->join_url }}" target="_blank" class="text-pink break-words">
                                    {{ $webinar->join_url }}
                                </a>
                            </p>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('sponsor.webinars.cancel', $webinar) }}"
                          onsubmit="return confirm('Cancel your registration?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="mt-2 inline-flex justify-center px-4 py-2 border border-[#E6D7E7] rounded-lg text-[#213430] hover:border-pink hover:text-pink transition">
                            Cancel Registration
                        </button>
                    </form>
                @elseif(!$webinar->can_join)
                    <div class="text-sm text-red-600">Registration closed.</div>
                @else
                    <form method="POST" action="{{ route('sponsor.webinars.register', $webinar) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex justify-center px-4 py-2 rounded-lg bg-pink text-white font-medium shadow hover:bg-pink-dark transition">
                            Join Webinar
                        </button>
                    </form>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-[#F1E4EC] p-6">
                <p class="text-[#6C5B68]">No webinars are available right now. Please check back soon.</p>
            </div>
        @endforelse
    </div>
@endsection
