@extends('admin.layouts.admin')

@php
    $eventDate = $event->date ? \Carbon\Carbon::parse($event->date) : null;
    $formattedDate = $eventDate ? $eventDate->format('l, F d, Y • h:i A') : 'Date to be announced';
    $sponsorCount = $event->sponsorships->count();
    $totalRaised = number_format($event->sponsorship_total, 2);
    $locationLabel = $event->location ?: 'Location to be announced';
    $descriptionHtml = $event->description
        ? strip_tags($event->description, '<p><br><strong><em><u><ol><ul><li><a><span><div><blockquote>')
        : null;
    $descriptionText = strip_tags($event->description ?? '');
    $isLongDescription = strlen($descriptionText) > 300;
@endphp

@section('content')
    <div class="max-w-8xl mx-auto space-y-8">
        {{-- Event hero --}}
        <div
            class="rounded-2xl bg-gradient-to-r from-[#C63A85] via-[#DB69A2] to-[#F9C6E2] text-white p-8 shadow-xl relative overflow-hidden">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="space-y-4 max-w-3xl">
                    <div class="flex items-center gap-3 text-xs uppercase tracking-[0.3em] text-white/70">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/15">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-2.21 0-4 1.567-4 3.5S9.79 15 12 15s4-1.567 4-3.5S14.21 8 12 8z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5.457 18.168A8.966 8.966 0 0112 16c2.448 0 4.675.984 6.543 2.583M15 12h5.5M4 12H9" />
                            </svg>
                        </span>
                        <span>Event Overview</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-semibold">{{ $event->title }}</h1>
                    <div class="text-sm md:text-base text-white/90 leading-relaxed max-w-2xl">
                        @if ($descriptionHtml)
                            <div id="eventDescription" class="prose prose-sm prose-invert max-w-none {{ $isLongDescription ? 'line-clamp-4' : '' }}">
                                {!! $descriptionHtml !!}
                            </div>
                            @if ($isLongDescription)
                                <button onclick="toggleDescription()" id="readMoreBtn" 
                                    class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-white/90 hover:text-white underline underline-offset-4 transition">
                                    <span id="readMoreText">Read More</span>
                                    <svg id="readMoreIcon" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            @endif
                        @else
                            <p>No description has been added for this event yet.</p>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-3 text-sm">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                            </svg>
                            {{ $formattedDate }}
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $locationLabel }}
                        </span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 min-w-[12rem]">
                    <div class="rounded-2xl bg-white/15 px-4 py-3 text-center backdrop-blur">
                        <p class="text-xs uppercase tracking-wide text-white/70">Sponsors</p>
                        <p class="mt-1 text-2xl font-semibold">{{ $sponsorCount }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/15 px-4 py-3 text-center backdrop-blur">
                        <p class="text-xs uppercase tracking-wide text-white/70">Raised</p>
                        <p class="mt-1 text-2xl font-semibold">${{ $totalRaised }}</p>
                    </div>
                    <a href="{{ route('events.edit', $event) }}"
                        class="col-span-2 inline-flex items-center justify-center gap-2 rounded-xl bg-white text-[#DB69A2] px-4 py-2 text-sm font-semibold shadow-lg shadow-white/30 hover:bg-[#FFF6FB] transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Event
                    </a>
                </div>
            </div>
        </div>

        {{-- Primary content --}}
        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,3fr)_2fr] gap-6">
            <section class="rounded-2xl bg-white shadow-sm border border-[#F1E5EF]">
                <header class="flex flex-wrap items-center justify-between gap-4 border-b border-[#F1E5EF] px-6 py-5">
                    <div>
                        <h2 class="text-lg font-semibold text-[#213430]">Sponsorship Ledger</h2>
                        <p class="text-sm text-[#6C5B68]">Track every contribution connected to this event.</p>
                    </div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-[#FBE7F1] px-4 py-2 text-sm text-[#C63A85] font-medium">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l2.5 2.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Total Raised: ${{ $totalRaised }}
                    </span>
                </header>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#F1E5EF] text-sm">
                        <thead class="bg-[#FDF7FB]">
                            <tr class="text-[#6C5B68] uppercase tracking-wide text-xs">
                                <th class="px-6 py-3 text-left">Sponsor</th>
                                <th class="px-6 py-3 text-left">Amount</th>
                                <th class="px-6 py-3 text-left">Recorded</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1E5EF] text-[#213430]">
                            @forelse($event->sponsorships as $row)
                                @php
                                    $recordedAt = $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('M d, Y • h:i A') : '—';
                                @endphp
                                <tr class="hover:bg-[#FEF6FB]/60 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold">{{ $row->sponsor->name ?? $row->sponsor->email }}</div>
                                        <div class="text-xs text-[#6C5B68]">Sponsor ID: {{ $row->sponsor_id }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold">${{ number_format($row->amount, 2) }}</td>
                                    <td class="px-6 py-4 text-sm text-[#6C5B68]">{{ $recordedAt }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST" class="inline-flex"
                                            action="{{ route('events.sponsorships.destroy', [$event, $row]) }}"
                                            onsubmit="return confirm('Remove this sponsorship?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 rounded-lg border border-[#F4C9DD] px-3 py-1.5 text-xs font-medium text-[#C63A85] hover:bg-[#FBE7F1] transition">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center">
                                        <div class="inline-flex flex-col items-center gap-3 text-[#6C5B68]">
                                            <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-[#FBE7F1] text-[#C63A85]">
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m2 0a2 2 0 002-2v-5a2 2 0 00-1-1.732l-6-3.464a2 2 0 00-2 0l-6 3.464A2 2 0 005 10v5a2 2 0 002 2m10 0H7" />
                                                </svg>
                                            </span>
                                            <p class="text-sm font-medium">No sponsorships recorded yet.</p>
                                            <p class="text-xs text-[#91848C]">Add the first contribution to highlight early supporters.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Add sponsorship form --}}
            <aside class="rounded-2xl bg-white shadow-sm border border-[#F1E5EF] h-fit">
                <div class="border-b border-[#F1E5EF] px-6 py-5">
                    <h3 class="text-lg font-semibold text-[#213430]">Add Sponsorship</h3>
                    <p class="text-sm text-[#6C5B68]">Log a new commitment from a sponsor and keep totals up to date.</p>
                </div>
                <form method="POST" action="{{ route('events.sponsorships.store', $event) }}" class="space-y-5 px-6 py-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-[#213430]">Sponsor</label>
                        <div class="relative">
                            <select name="sponsor_id"
                                class="w-full appearance-none rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-sm text-[#213430] outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70">
                                @foreach ($sponsors as $s)
                                    <option value="{{ $s->id }}">{{ $s->name ?? $s->email }} (ID: {{ $s->id }})</option>
                                @endforeach
                            </select>
                            <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-[#91848C]">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                        @error('sponsor_id')
                            <p class="text-xs text-[#DB69A2]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-[#213430]">Amount ($)</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#91848C]">$</span>
                            <input name="amount" type="number" min="0.01" step="0.01" placeholder="2500"
                                class="w-full rounded-xl border border-[#DCCFD8] bg-white pl-8 pr-4 py-3 text-sm outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70">
                            <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-[#91848C]">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                        @error('amount')
                            <p class="text-xs text-[#DB69A2]">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#C63A85] to-[#DB69A2] px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-[#DB69A2]/30 transition hover:from-[#D1478A] hover:to-[#E06CAB]">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Record Sponsorship
                    </button>
                </form>
            </aside>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4 pt-4">
            <a href="{{ route('events.index') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-[#DCCFD8] px-4 py-2 text-sm font-medium text-[#6C5B68] hover:bg-[#FDF7FB] transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Events
            </a>
            <form action="{{ route('events.destroy', $event) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to delete this event?');"
                class="inline-flex">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-100">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Event
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function toggleDescription() {
        const description = document.getElementById('eventDescription');
        const readMoreText = document.getElementById('readMoreText');
        const readMoreIcon = document.getElementById('readMoreIcon');
        
        if (description.classList.contains('line-clamp-4')) {
            description.classList.remove('line-clamp-4');
            readMoreText.textContent = 'Read Less';
            readMoreIcon.classList.add('rotate-180');
        } else {
            description.classList.add('line-clamp-4');
            readMoreText.textContent = 'Read More';
            readMoreIcon.classList.remove('rotate-180');
        }
    }
</script>
@endpush