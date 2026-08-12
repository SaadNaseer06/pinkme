@extends('admin.layouts.admin')

@section('title', 'Programs')

@push('styles')
    <style>
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-8xl mx-auto space-y-8">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-semibold text-[#213430] app-main">Programs</h1>
                </div>

                @if ($errors->any())
                    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <ul class="list-disc pl-5 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mt-6 bg-[#F3E8EF] rounded-lg p-6">
                    {{-- Fund Raising Events tab - commented for now
                    <div class="flex flex-wrap">
                        <div class="w-full md:w-1/2">
                            <button onclick="showTab('programs')" id="programs-tab"
                                class="tab-btn w-full bg-[#9E2469] text-white py-4 px-6 font-normal text-center border-b-4 border-[#9E2469] transition-colors duration-200">
                                Programs
                            </button>
                        </div>
                        <div class="w-full md:w-1/2">
                            <button onclick="showTab('events')" id="events-tab"
                                class="tab-btn w-full bg-[#F3E8EF] text-[#91848C] py-4 px-6 font-normal text-center border-b-4 border-[#DCCFD8] transition-colors duration-200">
                                Fund Raising Events
                            </button>
                        </div>
                    </div>
                    --}}

                    <div id="tabContents" class="w-full space-y-8">
                    <!-- Programs Tab -->
                    <div id="programs" class="tab-content active">
                        <!-- Support Programs Section -->
                        <div class="mt-6 mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-2xl font-semibold text-[#213430] program-main">PINK “ME” Assistance Programs</h2>
                                @if ($programs->count() > 0)
                                    <div class="flex items-center space-x-4">
                                        <form method="GET" class="flex items-center gap-2">
                                            <label for="programSort" class="text-sm text-[#6C5B68]">Sort by</label>
                                            <select id="programSort" name="program_sort" onchange="this.form.submit()"
                                                class="rounded-md border border-[#DCCFD8] bg-white px-3 py-2 text-sm text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#9E2469]">
                                                <option value="latest" @selected(($programSort ?? 'latest') === 'latest')>Latest</option>
                                                <option value="oldest" @selected(($programSort ?? 'latest') === 'oldest')>Oldest</option>
                                                <option value="date_desc" @selected(($programSort ?? 'latest') === 'date_desc')>Date (newest first)</option>
                                                <option value="date_asc" @selected(($programSort ?? 'latest') === 'date_asc')>Date (oldest first)</option>
                                            </select>
                                        </form>
                                        <a href="{{ route('programs.create') }}"
                                            class="flex items-center bg-[#9E2469] text-white text-sm px-4 py-2 rounded-lg hover:bg-[#B52D75] transition-colors duration-200">
                                            <span>Add New Program</span>
                                        </a>
                                    </div>
                                @endif
                            </div>

                            @if (isset($programs) && $programs->count() > 0)
                                @foreach ($programs as $program)
                                    @php
                                        $programDate =
                                            $program->event_date instanceof \Carbon\Carbon
                                                ? $program->event_date
                                                : ($program->event_date
                                                    ? \Carbon\Carbon::parse($program->event_date)
                                                    : null);
                                        $programTime =
                                            $program->event_time instanceof \Carbon\Carbon
                                                ? $program->event_time
                                                : ($program->event_time
                                                    ? \Carbon\Carbon::parse($program->event_time)
                                                    : null);
                                        $fallbackImage = asset('public/images/program-3.png');
                                        $image = $program->banner
                                            ? storage_url(ltrim($program->banner, '/'))
                                            : $fallbackImage;
                                        $detail = [
                                            'type' => 'program',
                                            'title' => $program->title,
                                            'description' => $program->description,
                                            'image' => $image,
                                            'image_fallback' => $fallbackImage,
                                            'date' => $programDate ? $programDate->format('l, F d, Y') : null,
                                            'time' => $programTime ? $programTime->format('h:i A') : null,
                                            'status' => $program->effective_status_label ?? $program->status,
                                            'program_type_label' => $program->programTypeLabel(),
                                            'registrations' => $program->registrations_count ?? 0,
                                            'show_url' => route('programs.edit', $program),
                                            'sponsor_name' => $program->sponsor_name,
                                            'sponsor_logo' => $program->sponsorLogoUrl(),
                                        ];
                                        $duplicatePayload = [
                                            'id' => $program->id,
                                            'title' => $program->title,
                                            'event_date' => optional($program->event_date)->format('Y-m-d'),
                                            'event_time' => $programTime ? $programTime->format('H:i') : '09:00',
                                            'application_start_date' => optional($program->application_start_date)->format('Y-m-d'),
                                            'application_end_date' => optional($program->application_end_date)->format('Y-m-d'),
                                            'status' => 'upcoming',
                                            'action' => route('programs.duplicate', $program),
                                        ];
                                        $formPreviewPayload = [
                                            'id' => $program->id,
                                            'title' => $program->title,
                                            'schema' => $program->resolvedApplicationFormSchema(),
                                        ];
                                        $canPreviewForm = $program->hasDynamicApplicationForm();
                                    @endphp
                                    <!-- Desktop Program Card -->
                                    <div
                                        class="bg-[#F3E8EF] rounded-lg p-4 mb-4 hidden md:flex items-center justify-between w-full overflow-visible relative z-0">
                                        <div class="flex items-center gap-4">
                                            <div class="w-20 h-20 rounded-lg overflow-hidden">
                                                <img src="{{ $image }}" alt="{{ $program->title }}"
                                                    class="w-full h-full object-cover" />
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h3 class="text-xl font-semibold text-[#213430] program-h">
                                                        {{ $program->title }}</h3>
                                                    <span
                                                        class="inline-flex items-center rounded-full bg-white/60 px-3 py-1 text-xs font-medium text-[#9E2469] capitalize">{{ $program->status }}</span>
                                                </div>
                                                <p class="text-sm text-[#91848C] program-p">
                                                    {{ Str::limit($program->description, 150) }}</p>
                                                <div class="mt-3 flex flex-wrap items-center gap-4 text-xs text-[#6C5B68]">
                                                    <span><i
                                                            class="fas fa-calendar mr-1"></i>{{ $programDate?->format('M d, Y') ?? 'Date TBA' }}</span>
                                                    <span><i
                                                            class="far fa-clock mr-1"></i>{{ $programTime?->format('h:i A') ?? 'Time TBA' }}</span>
                                                    <span><i class="fas fa-users mr-1"></i>Registrations:
                                                        {{ $program->registrations_count ?? 0 }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <a href="{{ route('programs.edit', $program) }}"
                                                class="bg-white px-4 py-2 rounded-lg text-sm font-medium text-[#213430] shadow-sm hover:bg-[#F6EDF5] transition">Edit</a>
                                            <button type="button" onclick='openProgramDetailModal(@json($detail))'
                                                class="bg-[#9E2469] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#B52D75] transition">View</button>
                                            <div class="relative shrink-0" data-program-actions>
                                                <button type="button" data-program-actions-toggle
                                                    class="bg-white px-3 py-2 rounded-lg text-sm font-medium text-[#213430] shadow-sm hover:bg-[#F6EDF5] transition"
                                                    aria-haspopup="true" aria-expanded="false" title="More actions">⋯</button>
                                                <div data-program-actions-menu
                                                    class="hidden fixed min-w-[11rem] rounded-xl border border-[#E6D8E1] bg-white shadow-xl py-1"
                                                    style="z-index: 9999;">
                                                    @if ($canPreviewForm)
                                                        <button type="button"
                                                            onclick='previewApplicationFormAsPatient(@json($formPreviewPayload)); closeProgramActionMenus();'
                                                            class="block w-full text-left px-4 py-2.5 text-sm text-[#213430] hover:bg-[#FDF0F7] whitespace-nowrap">Preview form</button>
                                                    @endif
                                                    <button type="button"
                                                        onclick='openDuplicateProgramModal(@json($duplicatePayload));'
                                                        class="block w-full text-left px-4 py-2.5 text-sm text-[#213430] hover:bg-[#FDF0F7] whitespace-nowrap">Duplicate</button>
                                                    <button type="button"
                                                        data-delete-form="program-delete-{{ $program->id }}"
                                                        data-confirm-message="Delete this program?{{ ($program->registrations_count ?? 0) > 0 ? ' '.$program->registrations_count.' application(s) will be removed too.' : '' }} This cannot be undone."
                                                        onclick="submitProgramDelete(this)"
                                                        class="block w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 whitespace-nowrap">Delete</button>
                                                </div>
                                            </div>
                                            <form id="program-delete-{{ $program->id }}" action="{{ route('programs.destroy', $program) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </div>
                                    <!-- Mobile Program Card -->
                                    <div class="bg-[#F3E8EF] rounded-lg p-3 mb-4 md:hidden w-full">
                                        <div class="flex gap-3">
                                            <div class="w-[80px] h-[80px] rounded-lg overflow-hidden flex-shrink-0">
                                                <img src="{{ $image }}" alt="{{ $program->title }}"
                                                    class="w-full h-full object-cover" />
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-start justify-between gap-2">
                                                    <h3 class="text-lg font-semibold text-[#213430] program-h">
                                                        {{ $program->title }}</h3>
                                                    <div class="flex flex-col items-end gap-1">
                                                        <span
                                                            class="inline-flex items-center rounded-full bg-white/60 px-2 py-0.5 text-[10px] font-semibold text-[#9E2469] capitalize">{{ $program->status }}</span>
                                                    </div>
                                                </div>
                                                <p class="text-xs text-[#91848C] mt-1 program-p">
                                                    {{ Str::limit($program->description, 90) }}</p>
                                                <div class="mt-3 grid grid-cols-2 gap-2 text-[11px] text-[#6C5B68]">
                                                    <span><i
                                                            class="fas fa-calendar mr-1"></i>{{ $programDate?->format('M d, Y') ?? 'Date TBA' }}</span>
                                                    <span><i
                                                            class="far fa-clock mr-1"></i>{{ $programTime?->format('h:i A') ?? 'Time TBA' }}</span>
                                                    <span><i class="fas fa-users mr-1"></i>Regs:
                                                        {{ $program->registrations_count ?? 0 }}</span>
                                                </div>
                                                <div class="flex flex-wrap gap-2">
                                                    <a href="{{ route('programs.edit', $program) }}"
                                                        class="flex-1 min-w-[4.5rem] text-center border border-[#213430] text-[#213430] text-xs py-2 rounded-md program-btn">Edit</a>
                                                    <button type="button" onclick='openProgramDetailModal(@json($detail))'
                                                        class="flex-1 min-w-[4.5rem] text-center bg-[#9E2469] text-white text-xs py-2 rounded-md program-btn">View</button>
                                                    <div class="relative shrink-0" data-program-actions>
                                                        <button type="button" data-program-actions-toggle
                                                            class="px-3 text-center border border-[#DCCFD8] text-[#213430] text-xs py-2 rounded-md program-btn"
                                                            aria-haspopup="true" aria-expanded="false" title="More actions">⋯</button>
                                                        <div data-program-actions-menu
                                                            class="hidden fixed min-w-[10rem] rounded-lg border border-[#E6D8E1] bg-white shadow-xl py-1"
                                                            style="z-index: 9999;">
                                                            @if ($canPreviewForm)
                                                                <button type="button"
                                                                    onclick='previewApplicationFormAsPatient(@json($formPreviewPayload)); closeProgramActionMenus();'
                                                                    class="block w-full text-left px-3 py-2 text-xs text-[#213430] hover:bg-[#FDF0F7] whitespace-nowrap">Preview form</button>
                                                            @endif
                                                            <button type="button"
                                                                onclick='openDuplicateProgramModal(@json($duplicatePayload));'
                                                                class="block w-full text-left px-3 py-2 text-xs text-[#213430] hover:bg-[#FDF0F7] whitespace-nowrap">Duplicate</button>
                                                            <button type="button"
                                                                data-delete-form="program-delete-{{ $program->id }}"
                                                                data-confirm-message="Delete this program?{{ ($program->registrations_count ?? 0) > 0 ? ' '.$program->registrations_count.' application(s) will be removed too.' : '' }} This cannot be undone."
                                                                onclick="submitProgramDelete(this)"
                                                                class="block w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-red-50 whitespace-nowrap">Delete</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                    <div class="bg-white rounded-lg p-6 text-center shadow-sm">
                                        <p class="text-[#91848C] text-lg font-medium mb-4">No support programs found.</p>
                                        <p class="text-[#6C5B68] text-sm mb-6">Create a new program to feature it here.</p>
                                    <a href="{{ route('programs.create') }}"
                                        class="inline-flex items-center justify-center rounded-md bg-[#9E2469] px-6 py-3 text-sm font-medium text-white shadow-sm hover:bg-[#B52D75] transition-colors duration-200">
                                        Create New Program
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Fund Raising Events Tab - commented for now
                    <div id="events" class="tab-content">
                        <!-- Fund Raising Events Section -->
                        <div class="mb-6 mt-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-2xl font-semibold text-[#213430]">Fund Raising Events</h2>

                                @if ($events->count() > 0)
                                    <div class="flex items-center gap-2">
                                        @if (\App\Models\EventSponsorship::count() > 0)
                                            <a href="{{ route('events.registrations.index') }}"
                                                class="bg-[#9E2469] text-white text-sm px-4 py-2 rounded-lg hover:bg-[#B52D75] transition-colors duration-200">
                                                <span>View Sponsors</span>
                                            </a>
                                        @endif
                                        <a href="{{ route('events.create') }}"
                                            class="flex items-center bg-[#9E2469] text-white text-sm px-4 py-2 rounded-lg hover:bg-[#B52D75] transition-colors duration-200">
                                            <span>Add New Event</span>
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Events Grid -->
                            @php
                                $eventCount = isset($events) ? $events->count() : 0;
                                $eventGridClasses = $eventCount > 1 ? 'md:grid-cols-2 xl:grid-cols-3' : '';
                            @endphp
                            <div class="grid grid-cols-1 gap-4 mb-8 {{ $eventGridClasses }}">
                                @forelse($events ?? [] as $event)
                                    @php
                                        $eventDate =
                                            $event->date instanceof \Carbon\Carbon
                                                ? $event->date
                                                : ($event->date
                                                    ? \Carbon\Carbon::parse($event->date)
                                                    : null);
                                        $eventEnd =
                                            $event->registration_deadline instanceof \Carbon\Carbon
                                                ? $event->registration_deadline
                                                : ($event->registration_deadline
                                                    ? \Carbon\Carbon::parse($event->registration_deadline)
                                                    : null);
                                        $eventStatus = $event->status;
                                        if (!$eventStatus && $eventDate) {
                                            if (now()->lt($eventDate)) {
                                                $eventStatus = 'upcoming';
                                            } elseif ($eventEnd && now()->gt($eventEnd)) {
                                                $eventStatus = 'completed';
                                            } else {
                                                $eventStatus = 'ongoing';
                                            }
                                        }
                                        $rawDescription = $event->description ?? '';
                                        $eventDescriptionText = trim(strip_tags($rawDescription));
                                        $eventDescriptionHtml = trim(
                                            strip_tags(
                                                $rawDescription,
                                                '<p><br><strong><em><u><ol><ul><li><a><span><div><blockquote>',
                                            ),
                                        );
                                        $eventImage = $event->image
                                            ? storage_url($event->image)
                                            : asset('public/images/program-details.png');
                                        $detail = [
                                            'type' => 'event',
                                            'title' => $event->title,
                                            'description' => $eventDescriptionText,
                                            'description_html' => $eventDescriptionHtml,
                                            'image' => $eventImage,
                                            'date' => $eventDate ? $eventDate->format('l, F d, Y') : null,
                                            'time' => $eventDate ? $eventDate->format('h:i A') : null,
                                            'end_date' => $eventEnd ? $eventEnd->format('l, F d, Y') : null,
                                            'end_time' => $eventEnd ? $eventEnd->format('h:i A') : null,
                                            'location' => $event->location,
                                            'status' => $eventStatus,
                                            'sponsor_count' =>
                                                $event->sponsors_count ?? ($event->sponsors?->count() ?? 0),
                                            'total_raised' => $event->total_raised ?? 0,
                                            'fund_goal' => $event->funding_goal,
                                            'payment_type' => $event->payment_type,
                                            'show_url' => route('events.show', $event),
                                        ];
                                    @endphp
                                    <div
                                        class="bg-[#F3E8EF] rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-200">
                                        <div class="relative h-48 w-full">
                                            <img src="{{ $eventImage }}" alt="{{ $event->title }}"
                                                class="h-full w-full object-cover">
                                            @if ($eventDate)
                                                <div
                                                    class="absolute top-4 left-4 flex flex-col items-center justify-center w-20 h-20 rounded-xl bg-white/90 text-[#9E2469] shadow-md">
                                                    <span class="text-sm font-semibold tracking-wide">
                                                        {{ $eventDate->format('M') }}
                                                    </span>
                                                    <span class="text-3xl font-bold">
                                                        {{ $eventDate->format('d') }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="p-6">
                                            <div class="flex items-start gap-4">
                                                <div class="flex-1">
                                                    <h3 class="text-xl font-semibold text-[#213430] mb-2">
                                                        {{ $event->title }}
                                                    </h3>
                                                    <div
                                                        class="text-sm text-[#91848C] mb-4 line-clamp-2 prose prose-sm max-w-none">
                                                        {!! \Illuminate\Support\Str::limit($eventDescriptionText, 180) !!}
                                                    </div>
                                                    <div
                                                        class="flex flex-wrap items-center gap-4 mb-4 text-sm text-[#91848C]">
                                                        <div class="flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            </svg>
                                                            <span>{{ $event->location ?? 'Location TBA' }}</span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            <span>{{ $eventDate?->format('h:i A') ?? 'Time TBA' }}</span>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="flex flex-wrap items-center justify-between gap-3 border border-[#E0D0DA] bg-white/60 px-3 py-2 rounded-lg text-xs text-[#6C5B68]">
                                                        <span class="font-medium">Raised:
                                                            ${{ number_format($event->total_raised ?? 0, 2) }}</span>
                                                    </div>
                                                    <div class="mt-4 flex flex-wrap justify-end gap-2">
                                                        <a href="{{ route('events.edit', $event) }}"
                                                            class="inline-flex items-center justify-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium text-[#213430] shadow-sm hover:bg-[#F6EDF5] transition">Edit</a>
                                                        <form action="{{ route('events.destroy', $event) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Are you sure you want to delete this event?');"
                                                            class="inline-flex">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="inline-flex items-center justify-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium text-red-600 shadow-sm hover:bg-red-50 transition">Delete</button>
                                                        </form>
                                                        <button onclick='openEventDetailModal(@json($detail))'
                                                            class="inline-flex items-center justify-center rounded-md border border-transparent bg-[#9E2469] px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-[#B52D75] transition">View</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="bg-white rounded-lg p-6 text-center shadow-sm">
                                        <p class="text-[#91848C] text-lg font-medium mb-4">No fund raising events found.</p>
                                        <p class="text-[#6C5B68] text-sm mb-6">Get started by creating a new event to
                                            engage your community.</p>
                                        <a href="{{ route('events.create') }}"
                                            class="inline-flex items-center justify-center rounded-md bg-[#9E2469] px-6 py-3 text-sm font-medium text-white shadow-sm hover:bg-[#B52D75] transition-colors duration-200">
                                            Create New Event
                                        </a>
                                    </div>
                                @endforelse
                            </div>
                        </div>


                    </div>

                    </div>
                    --}}
                </div>
            </div>

        <!-- Modals -->
        <!-- Duplicate Program Modal -->
        <div id="duplicateProgramModal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center p-4">
            <div class="bg-white rounded-2xl w-full max-w-lg shadow-xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-start justify-between gap-4 p-6 border-b border-[#E6D8E1]">
                    <div>
                        <h3 class="text-lg font-semibold text-[#213430]">Duplicate program</h3>
                        <p class="text-sm text-[#6C5F67] mt-1">Creates a full copy (type, form, listing, banner, sponsor). Only update the title and dates for the new cycle.</p>
                    </div>
                    <button type="button" onclick="closeDuplicateProgramModal()"
                        class="h-8 w-8 inline-flex items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-gray-50"
                        aria-label="Close">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="duplicateProgramForm" method="POST" action="" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label for="duplicate_title" class="block text-sm font-medium text-[#213430] mb-1">Program title *</label>
                        <input type="text" name="title" id="duplicate_title" required
                            class="w-full rounded-lg border border-[#DCCFD8] bg-white px-3 py-2 text-sm text-[#213430] focus:border-[#9E2469] focus:ring-1 focus:ring-[#9E2469]">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="duplicate_event_date" class="block text-sm font-medium text-[#213430] mb-1">Event date</label>
                            <input type="date" name="event_date" id="duplicate_event_date"
                                class="w-full rounded-lg border border-[#DCCFD8] bg-white px-3 py-2 text-sm text-[#213430] focus:border-[#9E2469] focus:ring-1 focus:ring-[#9E2469]">
                        </div>
                        <div>
                            <label for="duplicate_event_time" class="block text-sm font-medium text-[#213430] mb-1">Event time</label>
                            <input type="time" name="event_time" id="duplicate_event_time"
                                class="w-full rounded-lg border border-[#DCCFD8] bg-white px-3 py-2 text-sm text-[#213430] focus:border-[#9E2469] focus:ring-1 focus:ring-[#9E2469]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="duplicate_application_start_date" class="block text-sm font-medium text-[#213430] mb-1">Application start date</label>
                            <input type="date" name="application_start_date" id="duplicate_application_start_date"
                                class="w-full rounded-lg border border-[#DCCFD8] bg-white px-3 py-2 text-sm text-[#213430] focus:border-[#9E2469] focus:ring-1 focus:ring-[#9E2469]">
                        </div>
                        <div>
                            <label for="duplicate_application_end_date" class="block text-sm font-medium text-[#213430] mb-1">Application end date</label>
                            <input type="date" name="application_end_date" id="duplicate_application_end_date"
                                class="w-full rounded-lg border border-[#DCCFD8] bg-white px-3 py-2 text-sm text-[#213430] focus:border-[#9E2469] focus:ring-1 focus:ring-[#9E2469]">
                        </div>
                    </div>

                    <div>
                        <label for="duplicate_status" class="block text-sm font-medium text-[#213430] mb-1">Status</label>
                        <select name="status" id="duplicate_status"
                            class="w-full rounded-lg border border-[#DCCFD8] bg-white px-3 py-2 text-sm text-[#213430] focus:border-[#9E2469] focus:ring-1 focus:ring-[#9E2469]">
                            <option value="upcoming">Upcoming</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>

                    @error('title')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('application_end_date')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" onclick="closeDuplicateProgramModal()"
                            class="px-4 py-2 rounded-md border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 rounded-md bg-[#9E2469] text-white text-sm font-semibold hover:bg-[#B52D75]">
                            Create duplicate
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Program Details Modal -->
        <div id="programDetailModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
            <div id="programDetailModalPanel"
                class="fixed top-0 right-0 h-full w-full max-w-xl bg-[#F3E8EF] shadow-lg rounded-l-2xl transform translate-x-full transition-transform duration-300 ease-in-out overflow-y-auto">
                <div class="p-4">
                    <div class="border border-[#DCCFD8] rounded-xl bg-white/70 shadow-sm">
                        <div class="flex items-start justify-between p-5 border-b border-[#DCCFD8]">
                            <div>
                                <p class="text-xs uppercase tracking-wider text-[#91848C]" id="programDetailModalType">Program</p>
                                <h2 class="text-2xl font-semibold text-gray-900 program-main" id="programDetailModalTitle">
                                    Loading...
                                </h2>
                            </div>
                            <button onclick="closeProgramDetailModal()"
                                class="text-[#91848C] hover:text-[#213430] transition" aria-label="Close program details">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 8.586l4.95-4.95a1 1 0 111.414 1.414L11.414 10l4.95 4.95a1 1 0 01-1.414 1.414L10 11.414l-4.95 4.95a1 1 0 01-1.414-1.414L8.586 10l-4.95-4.95A1 1 0 115.05 3.636L10 8.586z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <div class="w-full h-64 overflow-hidden bg-[#F3E8EF]">
                            <img id="programDetailModalImage" src="{{ asset('public/images/program-3.png') }}"
                                alt="" class="w-full h-full object-cover"
                                onerror="this.onerror=null;this.src=this.dataset.fallback||'{{ asset('public/images/program-3.png') }}';" />
                        </div>

                        <div class="px-5 pt-4">
                            @include('patient.programs.partials.sponsor_modal_block', ['prefix' => 'programDetailModal'])
                        </div>

                        <div class="p-5 space-y-6 text-sm">
                            <p class="text-[#4A3F47] leading-relaxed" id="programDetailModalDescription">Loading description...</p>

                            <div id="programDetailModalScheduleWrapper">
                                <h3 class="text-lg font-medium text-[#213430] mb-3 app-main">Schedule</h3>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="flex flex-col gap-1 rounded-lg border border-[#DCCFD8] bg-white px-4 py-3">
                                        <span class="text-xs uppercase tracking-wide text-[#91848C]">Date</span>
                                        <p class="text-[#213430]" id="programDetailModalDate">Loading date...</p>
                                    </div>
                                    <div class="flex flex-col gap-1 rounded-lg border border-[#DCCFD8] bg-white px-4 py-3"
                                        id="programDetailModalTimeWrapper">
                                        <span class="text-xs uppercase tracking-wide text-[#91848C]">Time</span>
                                        <p class="text-[#213430]" id="programDetailModalTime">Loading time...</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-[#213430] mb-3 app-main">At a glance</h3>
                                <div class="grid gap-3 sm:grid-cols-2" id="programDetailModalInfoGrid">
                                    <div class="flex flex-col gap-1 rounded-lg border border-[#DCCFD8] bg-white px-4 py-3"
                                        id="programDetailModalStatusWrapper" hidden>
                                        <span class="text-xs uppercase tracking-wide text-[#91848C]">Status</span>
                                        <p class="text-[#213430]" id="programDetailModalStatus">&mdash;</p>
                                    </div>
                                    <div class="flex flex-col gap-1 rounded-lg border border-[#DCCFD8] bg-white px-4 py-3"
                                        id="programDetailModalRegistrationsWrapper" hidden>
                                        <span class="text-xs uppercase tracking-wide text-[#91848C]">Applications Received</span>
                                        <p class="text-[#213430]" id="programDetailModalRegistrations">0</p>
                                    </div>
                                    <div class="flex flex-col gap-1 rounded-lg border border-[#DCCFD8] bg-white px-4 py-3"
                                        id="programDetailModalTypeWrapper" hidden>
                                        <span class="text-xs uppercase tracking-wide text-[#91848C]">Program type</span>
                                        <p class="text-[#213430]" id="programDetailModalProgramType">&mdash;</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-wrap justify-end gap-3 pt-2">
                                <button onclick="closeProgramDetailModal()"
                                    class="px-5 py-3 bg-transparent border border-[#DCCFD8] text-[#91848C] rounded-md app-text">Cancel</button>
                                <a id="programDetailModalPrimaryLink" href="#"
                                    class="px-6 py-3 bg-[#9E2469] text-white rounded-lg hover:bg-[#B52D75] transition app-text hidden"
                                    target="_self">Open program</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Details Modal -->
        <div id="eventDetailModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
            <div id="eventDetailModalPanel"
                class="fixed top-0 right-0 h-full w-full max-w-xl bg-[#F3E8EF] shadow-lg rounded-l-2xl transform translate-x-full transition-transform duration-300 ease-in-out overflow-y-auto">
                <div class="p-4">
                    <div class="border border-[#DCCFD8] rounded-xl bg-white/70 shadow-sm">
                        <div class="flex items-start justify-between p-5 border-b border-[#DCCFD8]">
                            <div>
                                <p class="text-xs uppercase tracking-wider text-[#91848C]" id="eventDetailModalType">Event</p>
                                <h2 class="text-2xl font-semibold text-gray-900 program-main" id="eventDetailModalTitle">
                                    Loading...
                                </h2>
                            </div>
                            <button onclick="closeEventDetailModal()"
                                class="text-[#91848C] hover:text-[#213430] transition" aria-label="Close event details">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 8.586l4.95-4.95a1 1 0 111.414 1.414L11.414 10l4.95 4.95a1 1 0 01-1.414 1.414L10 11.414l-4.95 4.95a1 1 0 01-1.414-1.414L8.586 10l-4.95-4.95A1 1 0 115.05 3.636L10 8.586z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <div class="w-full h-64 overflow-hidden">
                            <img id="eventDetailModalImage" src="{{ asset('public/images/program-details.png') }}"
                                alt="" class="w-full h-full object-cover" />
                        </div>
                        <div class="p-5 space-y-6 text-sm">
                            <p class="text-[#4A3F47] leading-relaxed" id="eventDetailModalDescription">Loading description...</p>

                            <div id="eventDetailModalScheduleWrapper">
                                <h3 class="text-lg font-medium text-[#213430] mb-3 app-main">Schedule</h3>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="flex flex-col gap-1 rounded-lg border border-[#DCCFD8] bg-white px-4 py-3">
                                        <span class="text-xs uppercase tracking-wide text-[#91848C]">Date</span>
                                        <p class="text-[#213430]" id="eventDetailModalDate">Loading date...</p>
                                    </div>
                                    <div class="flex flex-col gap-1 rounded-lg border border-[#DCCFD8] bg-white px-4 py-3"
                                        id="eventDetailModalTimeWrapper">
                                        <span class="text-xs uppercase tracking-wide text-[#91848C]">Time</span>
                                        <p class="text-[#213430]" id="eventDetailModalTime">Loading time...</p>
                                    </div>
                                    <div class="flex flex-col gap-1 rounded-lg border border-[#DCCFD8] bg-white px-4 py-3"
                                        id="eventDetailModalEndDateWrapper" hidden>
                                        <span class="text-xs uppercase tracking-wide text-[#91848C]">End Date</span>
                                        <p class="text-[#213430]" id="eventDetailModalEndDate">Loading end date...</p>
                                    </div>
                                    <div class="flex flex-col gap-1 rounded-lg border border-[#DCCFD8] bg-white px-4 py-3"
                                        id="eventDetailModalEndTimeWrapper" hidden>
                                        <span class="text-xs uppercase tracking-wide text-[#91848C]">End Time</span>
                                        <p class="text-[#213430]" id="eventDetailModalEndTime">Loading end time...</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-[#213430] mb-3 app-main">At a glance</h3>
                                <div class="grid gap-3 sm:grid-cols-2" id="eventDetailModalInfoGrid">
                                    <div class="flex flex-col gap-1 rounded-lg border border-[#DCCFD8] bg-white px-4 py-3"
                                        id="eventDetailModalStatusWrapper" hidden>
                                        <span class="text-xs uppercase tracking-wide text-[#91848C]">Status</span>
                                        <p class="text-[#213430]" id="eventDetailModalStatus">&mdash;</p>
                                    </div>
                                    <div class="flex flex-col gap-1 rounded-lg border border-[#DCCFD8] bg-white px-4 py-3"
                                        id="eventDetailModalPaymentWrapper" hidden>
                                        <span class="text-xs uppercase tracking-wide text-[#91848C]">Payment Type</span>
                                        <p class="text-[#213430]" id="eventDetailModalPayment">&mdash;</p>
                                    </div>
                                </div>
                            </div>

                            <div id="eventDetailModalFundingWrapper"
                                class="rounded-lg border border-[#DCCFD8] bg-white px-4 py-4" hidden>
                                <div class="flex flex-wrap items-center gap-4">
                                    <div>
                                        <span class="text-xs uppercase tracking-wide text-[#91848C]">Raised so far</span>
                                        <p class="text-lg font-semibold text-[#213430]" id="eventDetailModalRaised">$0.00</p>
                                    </div>
                                    <div id="eventDetailModalGoalWrapper">
                                        <span class="text-xs uppercase tracking-wide text-[#91848C]">Funding goal</span>
                                        <p class="text-lg font-semibold text-[#213430]" id="eventDetailModalGoal">$0.00</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-wrap justify-end gap-3 pt-2">
                                <button onclick="closeEventDetailModal()"
                                    class="px-5 py-3 bg-transparent border border-[#DCCFD8] text-[#91848C] rounded-md app-text">Cancel</button>
                                <a id="eventDetailModalPrimaryLink" href="#"
                                    class="px-6 py-3 bg-[#9E2469] text-white rounded-lg hover:bg-[#B52D75] transition app-text hidden"
                                    target="_self">Open event</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @include('admin.programs.partials.application_form_patient_preview')
@endsection

@push('scripts')
    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const activeTab = localStorage.getItem('programEventsActiveTab') || 'programs';
            showTab(activeTab);
        });

        function showTab(tabId) {
            // Hide all tab contents
            const tabs = document.querySelectorAll(".tab-content");
            tabs.forEach(tab => {
                tab.classList.remove("active");
                tab.style.display = 'none';
            });

            // Show the selected tab content
            const selectedTab = document.getElementById(tabId);
            if (selectedTab) {
                selectedTab.classList.add("active");
                selectedTab.style.display = 'block';
            }

            // Update button states
            document.querySelectorAll(".tab-btn").forEach(btn => {
                btn.classList.remove("bg-[#9E2469]", "text-white", "border-[#9E2469]");
                btn.classList.add("bg-[#F3E8EF]", "text-[#91848C]", "border-[#DCCFD8]");
            });

            const activeBtn = document.querySelector(`[onclick="showTab('${tabId}')"]`);
            if (activeBtn) {
                activeBtn.classList.remove("bg-[#F3E8EF]", "text-[#91848C]", "border-[#DCCFD8]");
                activeBtn.classList.add("bg-[#9E2469]", "text-white", "border-[#9E2469]");
            }

            localStorage.setItem('programEventsActiveTab', tabId);
        }

        // Detail modal functions
        const applyValue = (wrapperId, valueId, value, formatter) => {
            const wrapper = document.getElementById(wrapperId);
            if (!wrapper) return;
            const target = valueId ? document.getElementById(valueId) : wrapper.querySelector('p');
            if (value === undefined || value === null || value === '') {
                wrapper.setAttribute('hidden', 'hidden');
                if (target) target.textContent = 'N/A';
                return;
            }
            wrapper.removeAttribute('hidden');
            if (target) target.textContent = typeof formatter === 'function' ? formatter(value) : value;
        };

        const toCurrency = (val) => {
            const num = typeof val === 'number' ? val : parseFloat(val);
            if (Number.isFinite(num)) {
                return `$${num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            }
            return null;
        };

        const toTitleCase = (value) => {
            if (value === undefined || value === null) return '';
            const str = value.toString().replace(/_/g, ' ');
            return str.charAt(0).toUpperCase() + str.slice(1);
        };

        function updateProgramSponsor(prefix, data) {
            const block = document.getElementById(prefix + '-sponsor-block');
            const logoWrap = document.getElementById(prefix + '-sponsor-logo-wrap');
            const logo = document.getElementById(prefix + '-sponsor-logo');
            const nameEl = document.getElementById(prefix + '-sponsor-name');
            if (!block) return;

            const name = (data?.sponsor_name || '').trim();
            const logoUrl = (data?.sponsor_logo || '').trim();
            const hasSponsor = !!(name || logoUrl);

            block.classList.toggle('hidden', !hasSponsor);

            if (nameEl) {
                nameEl.textContent = name;
                nameEl.classList.toggle('hidden', !name);
            }

            if (logo && logoWrap) {
                if (logoUrl) {
                    logo.src = logoUrl;
                    logo.alt = name ? (name + ' logo') : 'Sponsor logo';
                    logo.onerror = () => logoWrap.classList.add('hidden');
                    logoWrap.classList.remove('hidden');
                } else {
                    logo.src = '';
                    logoWrap.classList.add('hidden');
                }
            }
        }

        function submitProgramDelete(button) {
            const formId = button?.getAttribute('data-delete-form');
            const message = button?.getAttribute('data-confirm-message') || 'Delete this program? This cannot be undone.';
            const form = formId ? document.getElementById(formId) : null;
            if (!form) return;
            if (window.confirm(message)) {
                form.submit();
            }
        }

        function closeProgramActionMenus() {
            document.querySelectorAll('[data-program-actions-menu]').forEach((menu) => {
                const host = menu._programActionsHost;
                if (host && menu.parentElement === document.body) {
                    host.appendChild(menu);
                }
                menu.classList.add('hidden');
                menu.style.top = '';
                menu.style.left = '';
                menu.style.right = '';
                menu.style.visibility = '';
            });
            document.querySelectorAll('[data-program-actions-toggle]').forEach((btn) => btn.setAttribute('aria-expanded', 'false'));
        }

        function positionProgramActionMenu(toggle, menu) {
            const host = toggle.closest('[data-program-actions]');
            if (host) {
                menu._programActionsHost = host;
            }
            document.body.appendChild(menu);
            menu.style.visibility = 'hidden';
            menu.classList.remove('hidden');
            const rect = toggle.getBoundingClientRect();
            const menuWidth = Math.max(menu.offsetWidth || 176, 176);
            const menuHeight = Math.max(menu.offsetHeight || 88, 88);
            const gap = 8;
            const spaceBelow = window.innerHeight - rect.bottom - gap;
            const openUp = spaceBelow < menuHeight && rect.top > menuHeight + gap;
            const top = openUp
                ? Math.round(rect.top - menuHeight - gap)
                : Math.round(rect.bottom + gap);
            const left = Math.min(
                Math.max(8, rect.right - menuWidth),
                window.innerWidth - menuWidth - 8
            );
            menu.style.position = 'fixed';
            menu.style.top = `${Math.max(8, top)}px`;
            menu.style.left = `${Math.round(left)}px`;
            menu.style.right = 'auto';
            menu.style.zIndex = '9999';
            menu.style.visibility = '';
        }

        document.addEventListener('click', function(e) {
            const toggle = e.target.closest('[data-program-actions-toggle]');
            if (toggle) {
                e.preventDefault();
                e.stopPropagation();
                const wrap = toggle.closest('[data-program-actions]');
                let targetMenu = wrap?.querySelector('[data-program-actions-menu]');
                if (!targetMenu) {
                    targetMenu = Array.from(document.querySelectorAll('[data-program-actions-menu]'))
                        .find((m) => m._programActionsHost === wrap);
                }
                const wasOpen = targetMenu && !targetMenu.classList.contains('hidden');
                closeProgramActionMenus();
                if (targetMenu && !wasOpen) {
                    positionProgramActionMenu(toggle, targetMenu);
                    toggle.setAttribute('aria-expanded', 'true');
                }
                return;
            }
            if (e.target.closest('[data-program-actions-menu]')) {
                return;
            }
            closeProgramActionMenus();
        });

        window.addEventListener('resize', closeProgramActionMenus);
        window.addEventListener('scroll', closeProgramActionMenus, true);

        function openDuplicateProgramModal(payload) {
            closeProgramActionMenus();
            const modal = document.getElementById('duplicateProgramModal');
            const form = document.getElementById('duplicateProgramForm');
            if (!modal || !form || !payload) return;

            form.action = payload.action || '';
            document.getElementById('duplicate_title').value = payload.title || '';
            document.getElementById('duplicate_event_date').value = payload.event_date || '';
            document.getElementById('duplicate_event_time').value = payload.event_time || '09:00';
            document.getElementById('duplicate_application_start_date').value = payload.application_start_date || '';
            document.getElementById('duplicate_application_end_date').value = payload.application_end_date || '';
            document.getElementById('duplicate_status').value = payload.status || 'upcoming';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDuplicateProgramModal() {
            const modal = document.getElementById('duplicateProgramModal');
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.getElementById('duplicateProgramModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDuplicateProgramModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDuplicateProgramModal();
            }
        });

        function openProgramDetailModal(payload) {
            openDetailModal(payload, {
                modalId: 'programDetailModal',
                panelId: 'programDetailModalPanel',
                typeId: 'programDetailModalType',
                titleId: 'programDetailModalTitle',
                descriptionId: 'programDetailModalDescription',
                imageId: 'programDetailModalImage',
                scheduleWrapperId: 'programDetailModalScheduleWrapper',
                dateId: 'programDetailModalDate',
                timeId: 'programDetailModalTime',
                timeWrapperId: 'programDetailModalTimeWrapper',
                statusWrapperId: 'programDetailModalStatusWrapper',
                statusValueId: 'programDetailModalStatus',
                registrationsWrapperId: 'programDetailModalRegistrationsWrapper',
                registrationsValueId: 'programDetailModalRegistrations',
                primaryLinkId: 'programDetailModalPrimaryLink',
                typeLabel: 'Program',
                showEndDates: false,
            });
            updateProgramSponsor('programDetailModal', payload);
            applyValue('programDetailModalTypeWrapper', 'programDetailModalProgramType', payload?.program_type_label);
        }

        function openEventDetailModal(payload) {
            openDetailModal(payload, {
                modalId: 'eventDetailModal',
                panelId: 'eventDetailModalPanel',
                typeId: 'eventDetailModalType',
                titleId: 'eventDetailModalTitle',
                descriptionId: 'eventDetailModalDescription',
                imageId: 'eventDetailModalImage',
                scheduleWrapperId: 'eventDetailModalScheduleWrapper',
                dateId: 'eventDetailModalDate',
                timeId: 'eventDetailModalTime',
                timeWrapperId: 'eventDetailModalTimeWrapper',
                endDateWrapperId: 'eventDetailModalEndDateWrapper',
                endDateId: 'eventDetailModalEndDate',
                endTimeWrapperId: 'eventDetailModalEndTimeWrapper',
                endTimeId: 'eventDetailModalEndTime',
                statusWrapperId: 'eventDetailModalStatusWrapper',
                statusValueId: 'eventDetailModalStatus',
                paymentWrapperId: 'eventDetailModalPaymentWrapper',
                paymentValueId: 'eventDetailModalPayment',
                registrationsWrapperId: 'eventDetailModalRegistrationsWrapper',
                registrationsValueId: 'eventDetailModalRegistrations',
                fundingWrapperId: 'eventDetailModalFundingWrapper',
                raisedId: 'eventDetailModalRaised',
                goalWrapperId: 'eventDetailModalGoalWrapper',
                goalId: 'eventDetailModalGoal',
                primaryLinkId: 'eventDetailModalPrimaryLink',
                typeLabel: 'Event',
                showEndDates: true,
            });
        }

        function openDetailModal(payload, config) {
            const data = payload || {};
            const modal = document.getElementById(config.modalId);
            const panel = document.getElementById(config.panelId);

            const title = data.title || 'Record details';
            const type = data.type ? data.type.toString().replace(/_/g, ' ') : '';
            const prettyType = type ? type.charAt(0).toUpperCase() + type.slice(1) : 'Record';

            const description = data.description || 'No description available';
            const descriptionHtml = data.description_html || null;
            const imageUrl = data.image || "{{ asset('public/images/program-details.png') }}";
            const date = data.date || null;
            const time = data.time || null;

            const typeEl = document.getElementById(config.typeId);
            if (typeEl) {
                typeEl.textContent = config.typeLabel || prettyType;
            }
            const titleEl = document.getElementById(config.titleId);
            if (titleEl) {
                titleEl.textContent = title;
            }
            const descriptionEl = document.getElementById(config.descriptionId);
            if (descriptionEl) {
                if (descriptionHtml) {
                    descriptionEl.innerHTML = descriptionHtml;
                } else {
                    descriptionEl.textContent = description;
                }
            }
            const imageEl = document.getElementById(config.imageId);
            if (imageEl) {
                imageEl.src = imageUrl;
                imageEl.alt = title;
                if (data.image_fallback) {
                    imageEl.dataset.fallback = data.image_fallback;
                }
                imageEl.onerror = function() {
                    this.onerror = null;
                    this.src = this.dataset.fallback || "{{ asset('public/images/program-3.png') }}";
                };
            }

            const scheduleWrapper = document.getElementById(config.scheduleWrapperId);
            const timeWrapper = document.getElementById(config.timeWrapperId);
            const dateEl = document.getElementById(config.dateId);
            const timeEl = document.getElementById(config.timeId);

            if (dateEl) {
                if (date) {
                    dateEl.textContent = date;
                } else {
                    dateEl.textContent = 'Date not specified';
                }
            }
            if (scheduleWrapper) {
                scheduleWrapper.removeAttribute('hidden');
            }

            if (timeEl && timeWrapper) {
                if (time) {
                    timeEl.textContent = time;
                    timeWrapper.removeAttribute('hidden');
                } else {
                    timeEl.textContent = 'Time not specified';
                    timeWrapper.setAttribute('hidden', 'hidden');
                }
            }

            if (config.showEndDates) {
                const endDateWrapper = document.getElementById(config.endDateWrapperId);
                const endTimeWrapper = document.getElementById(config.endTimeWrapperId);
                const endDateEl = document.getElementById(config.endDateId);
                const endTimeEl = document.getElementById(config.endTimeId);

                if (endDateEl && endDateWrapper) {
                    if (data.end_date) {
                        endDateEl.textContent = data.end_date;
                        endDateWrapper.removeAttribute('hidden');
                    } else {
                        endDateEl.textContent = '';
                        endDateWrapper.setAttribute('hidden', 'hidden');
                    }
                }

                if (endTimeEl && endTimeWrapper) {
                    if (data.end_time) {
                        endTimeEl.textContent = data.end_time;
                        endTimeWrapper.removeAttribute('hidden');
                    } else {
                        endTimeEl.textContent = '';
                        endTimeWrapper.setAttribute('hidden', 'hidden');
                    }
                }
            }

            applyValue(config.statusWrapperId, config.statusValueId, data.status, (val) => toTitleCase(val));

            if (config.paymentWrapperId && config.paymentValueId) {
                const paymentRaw = data.payment_label || data.payment_type;
                const hasPaymentLabel = Boolean(data.payment_label);
                applyValue(config.paymentWrapperId, config.paymentValueId, paymentRaw, (val) => {
                    if (hasPaymentLabel) {
                        return val;
                    }
                    return toTitleCase(val);
                });
            }

            applyValue(
                config.registrationsWrapperId,
                config.registrationsValueId,
                data.registrations,
                (val) => `${val}`
            );

            const fundingWrapper = document.getElementById(config.fundingWrapperId);
            const goalWrapper = document.getElementById(config.goalWrapperId);
            const raisedEl = document.getElementById(config.raisedId);
            const goalEl = document.getElementById(config.goalId);

            const raisedText = toCurrency(data.total_raised);
            const goalText = toCurrency(data.fund_goal);

            if (fundingWrapper && raisedEl && goalEl && goalWrapper) {
                if (raisedText || goalText) {
                    fundingWrapper.removeAttribute('hidden');
                    raisedEl.textContent = raisedText || '$0.00';
                    if (goalText) {
                        goalWrapper.removeAttribute('hidden');
                        goalEl.textContent = goalText;
                    } else {
                        goalWrapper.setAttribute('hidden', 'hidden');
                        goalEl.textContent = '';
                    }
                } else {
                    fundingWrapper.setAttribute('hidden', 'hidden');
                    raisedEl.textContent = '$0.00';
                    goalWrapper.setAttribute('hidden', 'hidden');
                    goalEl.textContent = '';
                }
            }

            const primaryLink = document.getElementById(config.primaryLinkId);
            if (primaryLink) {
                if (data.show_url) {
                    primaryLink.href = data.show_url;
                    primaryLink.textContent = data.type === 'event' ? 'Open event' : (data.type === 'program' ? 'Open program' :
                        'Open record');
                    primaryLink.classList.remove('hidden');
                } else {
                    primaryLink.href = '#';
                    primaryLink.classList.add('hidden');
                }
            }

            if (modal && panel) {
                modal.classList.remove('hidden');
                requestAnimationFrame(() => {
                    panel.classList.remove('translate-x-full');
                    panel.classList.add('translate-x-0');
                });
            }
        }

        function closeDetailModal(modalId, panelId) {
            const modal = document.getElementById(modalId);
            const panel = document.getElementById(panelId);
            if (modal && panel) {
                panel.classList.remove('translate-x-0');
                panel.classList.add('translate-x-full');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 220);
            }
        }

        function closeProgramDetailModal() {
            closeDetailModal('programDetailModal', 'programDetailModalPanel');
        }

        function closeEventDetailModal() {
            closeDetailModal('eventDetailModal', 'eventDetailModalPanel');
        }

        // Close detail modal when clicking outside the panel
        window.addEventListener("click", function(e) {
            const programModal = document.getElementById('programDetailModal');
            const eventModal = document.getElementById('eventDetailModal');
            if (e.target === programModal) {
                closeProgramDetailModal();
            }
            if (e.target === eventModal) {
                closeEventDetailModal();
            }
        });
    </script>
@endpush
