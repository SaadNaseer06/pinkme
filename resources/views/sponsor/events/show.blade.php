@extends('sponsor.layouts.app')

@section('title', $event->title)

@php
    use App\Support\EventHighlightFormatter;

    $descriptionHtml = $event->description
        ? strip_tags($event->description, '<p><br><strong><em><u><ol><ul><li><a><span><div><blockquote>')
        : null;
    $eventHighlightsHtml = EventHighlightFormatter::format($event->event_highlights);
@endphp

@section('content')
    <main class="flex-1">
        <div class="max-w-8xl mx-auto">


            <!-- Event Header -->
            <div
                class="bg-gradient-to-r from-[#C63A85] via-[#DB69A2] to-[#F9C6E2] rounded-2xl p-8 text-white shadow-lg mb-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span
                                class="bg-white/20 backdrop-blur text-white text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wide">
                                {{ ucfirst($event->status) }}
                            </span>
                            @if ($event->registration_deadline && $event->registration_deadline->isFuture())
                                <span
                                    class="bg-white/20 backdrop-blur text-white text-xs font-medium px-3 py-1 rounded-full">
                                    Registration Deadline: {{ $event->registration_deadline->format('M d, Y') }}
                                </span>
                            @endif
                        </div>
                        <h1 class="text-3xl font-bold mb-2" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);">
                            {{ $event->title }}</h1>
                        <div class="flex flex-wrap gap-4 text-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span>{{ $event->date->format('F j, Y \a\t g:i A') }}</span>
                            </div>
                            @if ($event->location)
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span>{{ $event->location }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Event Description -->
                    @if ($descriptionHtml)
                        <div class="bg-white rounded-2xl border border-[#E9DCE7] p-6 shadow-sm">
                            <h2 class="text-xl font-semibold text-[#213430] mb-4">About This Event</h2>
                            <div class="text-[#6C5B68] leading-relaxed">
                                {!! $descriptionHtml !!}
                            </div>
                        </div>
                    @else
                        <div class="bg-white rounded-2xl border border-[#E9DCE7] p-6 shadow-sm">
                            <h2 class="text-xl font-semibold text-[#213430] mb-4">About This Event</h2>
                            <p class="text-[#6C5B68]">No description has been added for this event yet.</p>
                        </div>
                    @endif

                    <!-- Event Highlights -->
                    @if ($eventHighlightsHtml)
                        <div class="bg-white rounded-2xl border border-[#E9DCE7] p-6 shadow-sm">
                            <h2 class="text-xl font-semibold text-[#213430] mb-4">Event Highlights</h2>
                            <div class="text-[#6C5B68]">
                                {!! $eventHighlightsHtml !!}
                            </div>
                        </div>
                    @endif

                    <!-- Current Sponsors -->
                    @if ($event->confirmedSponsors->isNotEmpty())
                        <div class="bg-white rounded-2xl border border-[#E9DCE7] p-6 shadow-sm">
                            <h2 class="text-xl font-semibold text-[#213430] mb-4">Current Sponsors</h2>
                            <div class="grid gap-4">
                                @foreach ($event->confirmedSponsors as $sponsor)
                                    <div class="flex items-center gap-4 p-4 bg-[#FDF7FB] rounded-xl">
                                        <div class="w-12 h-12 bg-[#DB69A2] rounded-full flex items-center justify-center">
                                            <span class="text-white font-semibold">
                                                {{ strtoupper(substr($sponsor->profile->first_name ?? $sponsor->email, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-[#213430]">
                                                {{ $sponsor->sponsorDetail->company_name ?? ($sponsor->profile->full_name ?? $sponsor->email) }}
                                            </h3>
                                            <p class="text-sm text-[#6C5B68]">
                                                Sponsored ${{ number_format($sponsor->pivot->amount, 2) }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Funding Progress -->
                    @if ($event->funding_goal)
                        <div class="bg-white rounded-2xl border border-[#E9DCE7] p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-[#213430] mb-4">Funding Progress</h3>

                            <div class="mb-4">
                                <div class="flex justify-between text-sm text-[#6C5B68] mb-2">
                                    <span>Progress</span>
                                    <span>{{ $fundingProgress }}%</span>
                                </div>
                                <div class="w-full bg-[#F1E5EF] rounded-full h-3">
                                    <div class="bg-gradient-to-r from-[#DB69A2] to-[#C63A85] h-3 rounded-full transition-all duration-300"
                                        style="width: {{ $fundingProgress }}%"></div>
                                </div>
                            </div>

                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-[#6C5B68]">Goal:</span>
                                    <span
                                        class="font-semibold text-[#213430]">${{ number_format($event->funding_goal, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-[#6C5B68]">Raised:</span>
                                    <span
                                        class="font-semibold text-[#213430]">${{ number_format($event->confirmed_sponsorship_total, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-[#6C5B68]">Remaining:</span>
                                    <span
                                        class="font-semibold text-[#213430]">${{ number_format($remainingFunding, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Registration Status -->
                    <div class="bg-white rounded-2xl border border-[#E9DCE7] p-6 shadow-sm">
                        @if ($currentRegistration)
                            <!-- Already Registered -->
                            <div class="text-center">
                                <div
                                    class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-[#213430] mb-2">You're Registered!</h3>
                                <p class="text-[#6C5B68] mb-4">
                                    Status: <span
                                        class="font-semibold capitalize">{{ $currentRegistration->registration_status }}</span>
                                </p>
                                <p class="text-[#6C5B68] mb-4">
                                    Sponsorship Amount: <span
                                        class="font-semibold">${{ number_format($currentRegistration->amount, 2) }}</span>
                                </p>
                                @if ($currentRegistration->registration_status !== 'cancelled' && $event->date && now()->isBefore($event->date))
                                    <form method="POST" action="{{ route('sponsor.events.cancel', $event) }}"
                                        onsubmit="return confirm('Are you sure you want to cancel your registration?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full px-4 py-2 bg-red-100 text-red-700 rounded-xl hover:bg-red-200 transition-colors">
                                            Cancel Registration
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @elseif($event->isRegistrationOpen())
                            <!-- Registration Form -->
                            <h3 class="text-lg font-semibold text-[#213430] mb-4">Sponsor This Event</h3>

                            <form method="POST" action="{{ route('sponsor.events.register', $event) }}">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label for="amount"
                                            class="block text-sm font-medium text-[#213430] mb-1">Sponsorship Amount
                                            ($)</label>
                                        <div class="relative">
                                            <span
                                                class="absolute inset-y-0 left-3 flex items-center text-[#6C5B68]">$</span>
                                            <input type="number" id="amount" name="amount" min="0.01"
                                                step="0.01" required
                                                class="w-full pl-8 pr-4 py-3 border border-[#DCCFD8] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#DB69A2] focus:border-transparent"
                                                placeholder="1000.00"
                                                @if ($event->funding_goal) max="{{ $remainingFunding }}" @endif>
                                        </div>
                                        @if ($event->funding_goal)
                                            <p class="text-xs text-[#6C5B68] mt-1">Maximum:
                                                ${{ number_format($remainingFunding, 2) }}</p>
                                        @endif
                                        @error('amount')
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="message"
                                            class="block text-sm font-medium text-[#213430] mb-1">Message
                                            (Optional)</label>
                                        <textarea id="message" name="message" rows="3"
                                            class="w-full px-4 py-3 border border-[#DCCFD8] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#DB69A2] focus:border-transparent"
                                            placeholder="Share why you're supporting this event..."></textarea>
                                        @error('message')
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <button type="submit"
                                        style="width: 100%; padding: 12px 16px; background: linear-gradient(to right, #DB69A2, #C63A85); color: white; border: none; border-radius: 12px; font-weight: 600; font-size: 1rem; transition: all 0.2s ease; cursor: pointer;"
                                        onmouseover="this.style.background='linear-gradient(to right, #C63A85, #B02A72)'"
                                        onmouseout="this.style.background='linear-gradient(to right, #DB69A2, #C63A85)'">
                                        Submit Registration
                                    </button>
                                </div>
                            </form>
                        @else
                            <!-- Registration Closed -->
                            <div class="text-center">
                                <div
                                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m0 0v3m0-3h3m-3 0h-3m-3-6l3-3M6 12l3 3m6-6l3 3M18 12l-3 3"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-[#213430] mb-2">Registration Closed</h3>
                                <p class="text-[#6C5B68]">
                                    @if ($event->registration_deadline && now()->isAfter($event->registration_deadline))
                                        The registration deadline has passed.
                                    @elseif($event->isFullyFunded())
                                        This event is fully funded.
                                    @elseif($event->max_sponsors && $event->confirmedSponsorships->count() >= $event->max_sponsors)
                                        Maximum sponsors reached.
                                    @else
                                        Registration is currently closed.
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Event Stats -->
                    <div class="bg-[#FDF7FB] rounded-2xl border border-[#F1E5EF] p-6">
                        <h3 class="text-lg font-semibold text-[#213430] mb-4">Event Statistics</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-[#6C5B68]">Total Sponsors:</span>
                                <span
                                    class="font-semibold text-[#213430]">{{ $event->confirmedSponsorships->count() }}</span>
                            </div>
                            @if ($event->max_sponsors)
                                <div class="flex justify-between">
                                    <span class="text-[#6C5B68]">Max Sponsors:</span>
                                    <span class="font-semibold text-[#213430]">{{ $event->max_sponsors }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-[#6C5B68]">Event Date:</span>
                                <span class="font-semibold text-[#213430]">{{ $event->date->format('M j, Y') }}</span>
                            </div>
                            @if ($event->registration_deadline)
                                <div class="flex justify-between">
                                    <span class="text-[#6C5B68]">Registration Ends:</span>
                                    <span
                                        class="font-semibold text-[#213430]">{{ $event->registration_deadline->format('M j, Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
