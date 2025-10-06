@extends('admin.layouts.admin')

@section('title', 'Sponsors')

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
    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen">
        <!---Main -->
        <main class="flex-1 pb-8">
            <div class="max-w-8xl mx-auto space-y-8">
                <!-- Navigation Tabs -->
                <div class="flex flex-wrap">
                    <div class="w-full md:w-1/2">
                        <button onclick="showTab('sponsors')" id="sponsors-tab"
                            class="tab-btn w-full bg-[#DB69A2] text-white py-4 px-6 font-normal text-center rounded-t-lg md:rounded-tr-none md:rounded-l-lg transition-colors duration-200">
                            Sponsors
                        </button>
                    </div>
                    <div class="w-full md:w-1/2">
                        <button onclick="showTab('programs')" id="programs-tab"
                            class="tab-btn w-full bg-[#F3E8EF] text-[#91848C] py-4 px-6 font-normal text-center rounded-b-lg md:rounded-b-none md:rounded-tl-none md:rounded-r-lg transition-colors duration-200">
                            Support Programs & Events
                        </button>
                    </div>
                </div>
                <div class="h-[1px] bg-[#DCCFD8] -mt-[1px]"></div>

                <div id="tabContents">
                    <!-- Tab 1: Sponsors -->
                    <div id="sponsors" class="tab-content active">
                        <div class="mt-6 bg-[#F3E8EF] rounded-lg p-6">
                            <div x-data="{ showFilters: false }" class="mb-4 ml-3">
                                <!-- Header -->
                                <div class="flex justify-between items-center">
                                    <h2 class="text-xl font-semibold text-[#213430] app-main">
                                        Sponsors Lists
                                    </h2>
                                    <div class="flex items-center gap-3">
                                        <!-- Mobile Filters Button -->
                                        <button @click="showFilters = !showFilters"
                                            class="flex items-center border border-[#91848C] text-[#91848C] text-sm px-3 py-1.5 rounded-md app-h md:hidden">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 019 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                                            </svg>
                                        </button>
                                        <!-- Desktop Filters Button -->
                                        <button @click="showFilters = !showFilters"
                                            class="hidden md:flex items-center border border-[#91848C] text-[#91848C] text-sm px-3 py-1.5 rounded-md app-h">
                                            Filters
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 ml-1" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 019 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                                            </svg>
                                        </button>
                                        <!-- Mobile Export Button -->
                                        <button class="bg-[#db69a2] px-4 py-2 rounded-md text-sm md:hidden">
                                            <img src="/images/export.svg" alt="" class="w-4 h-4">
                                        </button>
                                        <!-- Desktop Export Button -->
                                        <button
                                            class="hidden md:flex items-center bg-[#db69a2] text-white text-sm px-4 py-1.5 rounded-md app-h">
                                            Export
                                            <img src="/images/export.svg" alt="" class="w-3 h-3 ml-1">
                                        </button>
                                    </div>
                                </div>
                                <!-- Filter Dropdowns -->
                                <div x-show="showFilters" class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 max-w-8xl">
                                    <div class="relative w-full">
                                        <select name="email_filter"
                                            class="bg-transparent border border-[#91848C] text-[#91848C] text-sm px-4 py-2 pr-8 rounded-md w-full appearance-none focus:outline-none">
                                            <option value="">Search By Email</option>
                                            @if (isset($sponsors))
                                                @foreach ($sponsors as $sponsor)
                                                    <option value="{{ $sponsor->email }}">{{ $sponsor->email }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-[#91848C]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="relative w-full">
                                        <select name="sponsor_type"
                                            class="bg-transparent border border-[#91848C] text-[#91848C] text-sm px-4 py-2 rounded-md w-full appearance-none focus:outline-none">
                                            <option value="">Filter By Sponsor Type</option>
                                            @if (isset($sponsorTypes))
                                                @foreach ($sponsorTypes as $type)
                                                    <option value="{{ $type }}">{{ $type }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-[#91848C]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="relative w-full">
                                        <select name="status_filter"
                                            class="bg-transparent border border-[#91848C] text-[#91848C] text-sm px-4 py-2 pr-8 rounded-md w-full appearance-none focus:outline-none">
                                            <option value="">Filter By Status</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-[#91848C]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-container">
                                <table class="min-w-full text-sm text-left mt-6">
                                    <thead>
                                        <tr class="border-t border-[#e0cfd8]">
                                            <th class="p-2">
                                                <input type="checkbox"
                                                    class="accent-[#DB69A2] w-4 h-4 border border-[#91848C] rounded appearance-none checked:appearance-auto focus:ring-0" />
                                            </th>
                                            <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">
                                                All Sponsors
                                            </th>
                                            <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h pad-left">
                                                Sponsors Type
                                            </th>
                                            <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">
                                                Company / Individual
                                            </th>
                                            <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">
                                                Contact
                                            </th>
                                            <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">
                                                Funds Given
                                            </th>
                                            <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">
                                                Email
                                            </th>
                                            <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">
                                                Status
                                            </th>
                                            <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700">
                                        @forelse($sponsors ?? [] as $sponsor)
                                            <tr
                                                class="border-t border-[#e0cfd8] hover:bg-[#F6EDF5] transition-colors duration-200">
                                                <td class="p-2">
                                                    <input type="checkbox" name="selected_sponsors[]"
                                                        value="{{ $sponsor->id }}"
                                                        class="accent-[#DB69A2] w-4 h-4 border border-[#91848C] rounded appearance-none checked:appearance-auto focus:ring-0" />
                                                </td>
                                                <td class="p-2">
                                                    <div class="flex items-center gap-3">
                                                        <img src="{{ $sponsor->profile_image ?? '/images/default-profile.png' }}"
                                                            alt="{{ $sponsor->name }}"
                                                            class="w-8 h-8 rounded-full object-cover" />
                                                        <span
                                                            class="text-[#91848C] text-[16px] font-light app-text">{{ $sponsor->name }}</span>
                                                    </div>
                                                </td>
                                                <td
                                                    class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text pad-left">
                                                    {{ $sponsor->type }}
                                                </td>
                                                <td
                                                    class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                                    {{ $sponsor->company_name ?? 'Individual' }}
                                                </td>
                                                <td
                                                    class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                                    {{ $sponsor->contact_number }}
                                                </td>
                                                <td
                                                    class="p-2 align-middle text-[#91848C] text-[16px] font

System: -light app-text">
                                                    ${{ number_format($sponsor->total_funds, 2) }}
                                                </td>
                                                <td
                                                    class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                                    {{ $sponsor->email }}
                                                </td>
                                                <td class="p-2 align-middle">
                                                    <span class="inline-flex items-center gap-1 text-[#8E7C93] text-sm">
                                                        <span class="w-2 h-2 rounded-full bg-[#20B354]"></span>
                                                        Active
                                                    </span>
                                                </td>
                                                <td class="p-2 relative">
                                                    <button onclick="toggleDropdown(this)"
                                                        class="text-[#213430] p-2 rounded-md focus:outline-none">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                        </svg>
                                                    </button>
                                                    <div
                                                        class="absolute right-[28px] top-10 w-[250px] max-w-none bg-[#F6EDF5] rounded-lg shadow-lg py-2 z-20 hidden">
                                                        <a href="#"
                                                            class="flex items-center px-4 py-2 text-[#91848C] hover:bg-pink-100 text-sm">
                                                            <i class="fas fa-eye mr-2"></i> View Profile
                                                        </a>
                                                        <a href="#"
                                                            class="flex items-center px-4 py-2 text-[#91848C] hover:bg-pink-100 text-sm gap-2">
                                                            <i class="fa-solid fa-pen"></i> Edit Sponsors Details
                                                        </a>
                                                        <a href="#" onclick="openRejectModal()"
                                                            class="flex items-center px-4 py-2 gap-2 text-[#91848C] text-sm transition-colors">
                                                            <i class="fa-solid fa-trash"></i> Remove Sponsors
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr class="border-t border-[#e0cfd8]">
                                                <td colspan="9" class="p-8 text-center text-[#91848C] app-text">
                                                    No sponsors found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                            <div class="flex justify-between items-center mt-6">
                                <div class="flex justify-start">
                                    <h1 class="text-md text-[#91848C] font-light app-text">
                                        @if (isset($sponsors) && $sponsors->count() > 0)
                                            Showing {{ $sponsors->firstItem() ?? 0 }} to {{ $sponsors->lastItem() ?? 0 }}
                                            of {{ $sponsors->total() }} Sponsors
                                        @else
                                            Showing 0 to 0 of 0 Sponsors
                                        @endif
                                    </h1>
                                </div>
                                <div class="flex justify-end space-x-1">
                                    @if (isset($sponsors) && method_exists($sponsors, 'onFirstPage'))
                                        @if ($sponsors->onFirstPage())
                                            <button disabled
                                                class="px-3 py-1 rounded-md bg-transparent text-[#91848C] border border-[#B9B1B6] opacity-50 cursor-not-allowed">
                                                &lt;
                                            </button>
                                        @else
                                            <a href="{{ $sponsors->previousPageUrl() }}"
                                                class="px-3 py-1 rounded-md bg-transparent text-[#91848C] border border-[#B9B1B6] hover:bg-[#F6EDF5] transition-colors duration-200">
                                                &lt;
                                            </a>
                                        @endif
                                        @foreach ($sponsors->getUrlRange(1, $sponsors->lastPage()) as $page => $url)
                                            <a href="{{ $url }}"
                                                class="px-4 py-1 rounded-md {{ $page == $sponsors->currentPage() ? 'bg-[#DB69A2] text-white' : 'bg-transparent text-[#91848C] border border-[#B9B1B6] hover:bg-[#F6EDF5]' }} transition-colors duration-200">
                                                {{ $page }}
                                            </a>
                                        @endforeach
                                        @if ($sponsors->hasMorePages())
                                            <a href="{{ $sponsors->nextPageUrl() }}"
                                                class="px-3 py-1 rounded-md bg-transparent text-[#91848C] border border-[#B9B1B6] hover:bg-[#F6EDF5] transition-colors duration-200">
                                                &gt;
                                            </a>
                                        @else
                                            <button disabled
                                                class="px-3 py-1 rounded-md bg-transparent text-[#91848C] border border-[#B9B1B6] opacity-50 cursor-not-allowed">
                                                &gt;
                                            </button>
                                        @endif
                                    @else
                                        <button disabled
                                            class="px-3 py-1 rounded-md bg-transparent text-[#91848C] border border-[#B9B1B6] opacity-50 cursor-not-allowed">
                                            &lt;
                                        </button>
                                        <button class="px-4 py-1 rounded-md bg-[#DB69A2] text-white">
                                            1
                                        </button>
                                        <button disabled
                                            class="px-3 py-1 rounded-md bg-transparent text-[#91848C] border border-[#B9B1B6] opacity-50 cursor-not-allowed">
                                            &gt;
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Programs & Events -->
                    <div id="programs" class="tab-content">
                        <!-- Upcoming Events Section -->
                        <div class="mb-6 mt-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-2xl font-semibold text-[#213430]">Upcoming Events</h2>
                                <div class="flex items-center space-x-4">
                                    {{-- <button
                                        class="flex items-center border border-[#91848C] text-[#91848C] px-4 py-2 rounded-lg hover:bg-[#F6EDF5] transition-colors duration-200">
                                        <span class="text-sm">Filters</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586l-7.293 7.293a1 1 0 00-.293.707V21l-4-4v-6.586a1 1 0 00-.293-.707L3 6.586V4z" />
                                        </svg>
                                    </button> --}}
                                    <a href="{{ route('events.create') }}"
                                        class="flex items-center bg-[#db69a2] text-white text-sm px-4 py-2 rounded-lg hover:bg-[#c25891] transition-colors duration-200">
                                        <span>Add New Event</span>
                                    </a>
                                </div>
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
                                        $detail = [
                                            'type' => 'event',
                                            'title' => $event->title,
                                            'description' => $event->description,
                                            'image' => asset('images/program-details.png'),
                                            'date' => $eventDate ? $eventDate->format('l, F d, Y') : null,
                                            'time' => $eventDate ? $eventDate->format('h:i A') : null,
                                            'location' => $event->location,
                                            'sponsor_count' =>
                                                $event->sponsors_count ?? ($event->sponsors?->count() ?? 0),
                                            'total_raised' => $event->total_raised ?? 0,
                                            'fund_goal' => null,
                                            'show_url' => route('events.show', $event),
                                        ];
                                    @endphp
                                    <div
                                        class="bg-[#F3E8EF] rounded-lg p-6 hover:shadow-lg transition-shadow duration-200">
                                        <div class="flex items-start gap-4">
                                            <div class="flex-none">
                                                <div
                                                    class="flex flex-col items-center justify-center w-20 h-20 border-2 border-[#DB69A2] rounded-lg bg-[#FFF7FC]">
                                                    <span
                                                        class="text-sm text-[#DB69A2]">{{ $eventDate?->format('M') }}</span>
                                                    <span
                                                        class="text-4xl font-bold text-[#DB69A2]">{{ $eventDate?->format('d') }}</span>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="text-xl font-semibold text-[#213430] mb-2">{{ $event->title }}
                                                </h3>
                                                <p class="text-sm text-[#91848C] mb-4 line-clamp-2">
                                                    {{ $event->description }}</p>
                                                <div class="flex flex-wrap items-center gap-4 mb-4 text-sm text-[#91848C]">
                                                    <div class="flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
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
                                                    <span class="font-medium">Sponsors:
                                                        {{ $event->sponsors_count ?? ($event->sponsors?->count() ?? 0) }}</span>
                                                    <span class="font-medium">Raised:
                                                        ${{ number_format($event->total_raised ?? 0, 2) }}</span>
                                                </div>
                                                <div class="mt-4 flex flex-wrap justify-end gap-2">
                                                    <a href="{{ route('events.edit', $event) }}"
                                                        class="inline-flex items-center justify-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium text-[#213430] shadow-sm hover:bg-[#F6EDF5] transition">Edit</a>
                                                    <form action="{{ route('events.destroy', $event) }}" method="POST"
                                                        onsubmit="return confirm('Are you sure you want to delete this event?');"
                                                        class="inline-flex">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="inline-flex items-center justify-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium text-red-600 shadow-sm hover:bg-red-50 transition">Delete</button>
                                                    </form>
                                                    <button onclick='openDetailModal(@json($detail))'
                                                        class="inline-flex items-center justify-center rounded-md border border-transparent bg-[#DB69A2] px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-[#c25891] transition">View</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    @php
                                        $sampleEventDetail = [
                                            'type' => 'event',
                                            'title' => 'Breast Cancer Awareness Walk',
                                            'description' =>
                                                'A community walk to raise awareness about breast cancer prevention and early detection.',
                                            'image' => asset('images/program-details.png'),
                                            'date' => 'Saturday, March 30, 2025',
                                            'time' => '10:00 AM',
                                            'location' => 'Central Park, NYC',
                                            'sponsor_count' => 8,
                                            'total_raised' => 12500,
                                            'show_url' => null,
                                        ];
                                    @endphp
                                    <div
                                        class="bg-[#F3E8EF] rounded-lg p-6 hover:shadow-lg transition-shadow duration-200">
                                        <div class="flex items-start gap-4">
                                            <div class="flex-none">
                                                <div
                                                    class="flex flex-col items-center justify-center w-20 h-20 border-2 border-[#DB69A2] rounded-lg bg-[#FFF7FC]">
                                                    <span class="text-sm text-[#DB69A2]">Mar</span>
                                                    <span class="text-4xl font-bold text-[#DB69A2]">30</span>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="text-xl font-semibold text-[#213430] mb-2">Breast Cancer
                                                    Awareness Walk</h3>
                                                <p class="text-sm text-[#91848C] mb-4 line-clamp-2">A community walk to
                                                    raise awareness about breast cancer prevention and early detection.</p>
                                                <div class="flex flex-wrap items-center gap-4 mb-4 text-sm text-[#91848C]">
                                                    <div class="flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                        <span>Central Park, NYC</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span>10:00 AM</span>
                                                    </div>
                                                </div>
                                                <div
                                                    class="flex flex-wrap items-center justify-between gap-3 border border-[#E0D0DA] bg-white/60 px-3 py-2 rounded-lg text-xs text-[#6C5B68]">
                                                    <span class="font-medium">Sponsors: 8</span>
                                                    <span class="font-medium">Raised: $12,500.00</span>
                                                </div>
                                                <div class="mt-4 flex justify-end">
                                                    <button onclick='openDetailModal(@json($sampleEventDetail))'
                                                        class="inline-flex items-center justify-center rounded-md border border-transparent bg-[#DB69A2] px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-[#c25891] transition">View</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Support Programs Section -->
                        <div class="mt-12 mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-2xl font-semibold text-[#213430] program-main">Support Programs</h2>
                                <a href="{{ route('programs.create') }}"
                                    class="bg-[#db69a2] text-white px-4 py-2 rounded-lg hover:bg-[#c25891] transition-colors inline-block">
                                    Add New Program
                                </a>
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
                                        $image = $program->banner
                                            ? asset('storage/' . $program->banner)
                                            : $program->image_url ?? asset('images/program-3.png');
                                        $detail = [
                                            'type' => 'program',
                                            'title' => $program->title,
                                            'description' => $program->description,
                                            'image' => $image,
                                            'date' => $programDate ? $programDate->format('l, F d, Y') : null,
                                            'time' => $programTime ? $programTime->format('h:i A') : null,
                                            'status' => $program->status,
                                            'registrations' => $program->registrations_count ?? 0,
                                            'total_raised' => $program->total_raised ?? 0,
                                            'fund_goal' => $program->program_fund,
                                            'show_url' => route('programs.edit', $program),
                                        ];
                                    @endphp
                                    <!-- Desktop Program Card -->
                                    <div
                                        class="bg-[#F3E8EF] rounded-lg p-4 mb-4 hidden md:flex items-center justify-between">
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
                                                        class="inline-flex items-center rounded-full bg-white/60 px-3 py-1 text-xs font-medium text-[#DB69A2] capitalize">{{ $program->status }}</span>
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
                                                <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-[#6C5B68]">
                                                    <span
                                                        class="inline-flex items-center rounded-md bg-white/70 px-2.5 py-1 font-medium">Raised:
                                                        ${{ number_format($program->total_raised ?? 0, 2) }}</span>
                                                    <span
                                                        class="inline-flex items-center rounded-md bg-white/70 px-2.5 py-1 font-medium">Goal:
                                                        ${{ number_format($program->program_fund ?? 0, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('programs.edit', $program) }}"
                                                class="bg-white px-4 py-2 rounded-lg text-sm font-medium text-[#213430] shadow-sm hover:bg-[#F6EDF5] transition">Edit</a>
                                            <button onclick='openDetailModal(@json($detail))'
                                                class="bg-[#DB69A2] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#c25891] transition">View
                                                Details</button>
                                        </div>
                                    </div>
                                    <!-- Mobile Program Card -->
                                    <div class="bg-[#F3E8EF] rounded-lg p-3 mb-4 md:hidden">
                                        <div class="flex gap-3">
                                            <div class="w-[80px] h-[80px] rounded-lg overflow-hidden flex-shrink-0">
                                                <img src="{{ $image }}" alt="{{ $program->title }}"
                                                    class="w-full h-full object-cover" />
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between">
                                                    <h3 class="text-lg font-semibold text-[#213430] program-h">
                                                        {{ $program->title }}</h3>
                                                    <span
                                                        class="inline-flex items-center rounded-full bg-white/60 px-2 py-0.5 text-[10px] font-semibold text-[#DB69A2] capitalize">{{ $program->status }}</span>
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
                                                    <span><i
                                                            class="fas fa-hand-holding-usd mr-1"></i>${{ number_format($program->total_raised ?? 0, 2) }}
                                                        / ${{ number_format($program->program_fund ?? 0, 2) }}</span>
                                                </div>
                                                <div class="flex gap-2">
                                                    <a href="{{ route('programs.edit', $program) }}"
                                                        class="flex-1 text-center border border-[#213430] text-[#213430] text-xs py-2 rounded-md program-btn">Edit</a>
                                                    <button onclick='openDetailModal(@json($detail))'
                                                        class="flex-1 text-center bg-[#DB69A2] text-white text-xs py-2 rounded-md program-btn">View</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <!-- Sample Program Card -->
                                @php
                                    $sampleProgramDetail = [
                                        'type' => 'program',
                                        'title' => 'Stronger Together: Cancer Support Group',
                                        'description' =>
                                            'A community-driven program offering emotional and financial support for cancer patients and survivors.',
                                        'image' => asset('images/program-3.png'),
                                        'date' => 'Saturday, March 15, 2025',
                                        'time' => '10:00 AM',
                                        'status' => 'ongoing',
                                        'registrations' => 42,
                                        'total_raised' => 48000,
                                        'fund_goal' => 60000,
                                        'show_url' => null,
                                    ];
                                @endphp
                                <div class="bg-[#F3E8EF] rounded-lg p-4 mb-4 hidden md:flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-20 h-20 rounded-lg overflow-hidden">
                                            <img src="{{ asset('images/program-3.png') }}" alt="Support Program"
                                                class="w-full h-full object-cover" />
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <h3 class="text-xl font-semibold text-[#213430] program-h">Stronger
                                                    Together: Cancer Support Group</h3>
                                                <span
                                                    class="inline-flex items-center rounded-full bg-white/60 px-3 py-1 text-xs font-medium text-[#DB69A2] capitalize">Ongoing</span>
                                            </div>
                                            <p class="text-sm text-[#91848C] program-p">A community-driven program offering
                                                emotional and financial support for cancer patients and survivors.</p>
                                            <div class="mt-3 flex flex-wrap items-center gap-4 text-xs text-[#6C5B68]">
                                                <span><i class="fas fa-calendar mr-1"></i>Mar 15,
                                                    2025</span>
                                                <span><i class="far fa-clock mr-1"></i>10:00 AM</span>
                                                <span><i class="fas fa-users mr-1"></i>Registrations: 42</span>
                                            </div>
                                            <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-[#6C5B68]">
                                                <span
                                                    class="inline-flex items-center rounded-md bg-white/70 px-2.5 py-1 font-medium">Raised:
                                                    $48,000.00</span>
                                                <span
                                                    class="inline-flex items-center rounded-md bg-white/70 px-2.5 py-1 font-medium">Goal:
                                                    $60,000.00</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('programs.create') }}"
                                            class="bg-white px-4 py-2 rounded-lg text-sm font-medium text-[#213430] shadow-sm hover:bg-[#F6EDF5] transition">Add
                                            Program</a>
                                        <button onclick='openDetailModal(@json($sampleProgramDetail))'
                                            class="bg-[#DB69A2] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#c25891] transition">View
                                            Details</button>
                                    </div>
                                </div>
                                <div class="bg-[#F3E8EF] rounded-lg p-3 mb-4 md:hidden">
                                    <div class="flex gap-3">
                                        <div class="w-[80px] h-[80px] rounded-lg overflow-hidden flex-shrink-0">
                                            <img src="{{ asset('images/program-3.png') }}" alt="Support Program"
                                                class="w-full h-full object-cover" />
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <h3 class="text-[15px] font-semibold text-[#213430] mb-1">Stronger
                                                    Together: Cancer Support Group</h3>
                                                <span
                                                    class="inline-flex items-center rounded-full bg-white/60 px-2 py-0.5 text-[10px] font-semibold text-[#DB69A2] capitalize">Ongoing</span>
                                            </div>
                                            <p class="text-[13px] font-light text-[#91848C] mb-2">A community-driven
                                                program offering emotional and financial support.</p>
                                            <div class="text-xs text-[#6C5B68] mb-2 grid grid-cols-2 gap-2">
                                                <span><i class="fas fa-calendar mr-1"></i>Mar 15,
                                                    2025</span>
                                                <span><i class="far fa-clock mr-1"></i>10:00 AM</span>
                                                <span><i class="fas fa-users mr-1"></i>Regs: 42</span>
                                                <span><i class="fas fa-hand-holding-usd mr-1"></i>$48K / $60K</span>
                                            </div>
                                            <div class="flex gap-2">
                                                <a href="{{ route('programs.create') }}"
                                                    class="flex-1 text-center border border-[#213430] text-[#213430] text-xs py-2 rounded-md program-btn">Add
                                                    Program</a>
                                                <button onclick='openDetailModal(@json($sampleProgramDetail))'
                                                    class="flex-1 text-center bg-[#DB69A2] text-white text-xs py-2 rounded-md program-btn">View</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Modals -->
        <!-- Reject Modal -->
        <div id="rejectModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
            <div class="bg-[#F9EEF6] rounded-lg shadow-lg p-6 w-full max-w-[20rem] md:max-w-md mx-auto text-left">
                <h2 class="text-lg font-semibold text-[#1F2937] mb-4 text-center app-main">
                    Delete Application
                </h2>
                <p class="text-md text-center text-black mb-4 app-text">
                    Are you sure you want to delete
                </p>
                <div class="flex justify-center items-center gap-3 pt-2">
                    <button
                        class="px-6 py-2 bg-[#DB69A2] hover:bg-[#FE6EB6] text-white rounded-md text-sm font-semibold transition">
                        Yes
                    </button>
                    <button onclick="closeRejectModal()"
                        class="px-6 py-2 border border-[#D6C6CE] text-[#8B7E88] rounded-md text-sm font-semibold hover:bg-[#DCCFD8] transition">
                        No
                    </button>
                </div>
            </div>
        </div>

        <!-- Event/Program Details Modal -->
        <div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
            <div id="detailModalPanel"
                class="fixed top-0 right-0 h-full w-full max-w-xl bg-[#F3E8EF] shadow-lg rounded-l-2xl transform translate-x-full transition-transform duration-300 ease-in-out overflow-y-auto">
                <div class="p-4">
                    <div class="border border-[#DCCFD8] rounded-xl bg-white/70 shadow-sm">
                        <div class="flex items-start justify-between p-5 border-b border-[#DCCFD8]">
                            <div>
                                <p class="text-xs uppercase tracking-wider text-[#91848C]" id="detailModalType">Loading
                                    type...</p>
                                <h2 class="text-2xl font-semibold text-gray-900 program-main" id="detailModalTitle">
                                    Loading...
                                </h2>
                            </div>
                            <button onclick="closeDetailModal()" class="text-[#91848C] hover:text-[#213430] transition"
                                aria-label="Close details">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 8.586l4.95-4.95a1 1 0 111.414 1.414L11.414 10l4.95 4.95a1 1 0 01-1.414 1.414L10 11.414l-4.95 4.95a1 1 0 01-1.414-1.414L8.586 10l-4.95-4.95A1 1 0 115.05 3.636L10 8.586z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <div class="w-full h-64 overflow-hidden">
                            <img id="detailModalImage" src="/images/program-details.png" alt=""
                                class="w-full h-full object-cover" />
                        </div>
                        <div class="p-5 space-y-6 text-sm">
                            <p class="text-[#4A3F47] leading-relaxed" id="detailModalDescription">Loading description...
                            </p>

                            <div id="detailModalScheduleWrapper">
                                <h3 class="text-lg font-medium text-[#213430] mb-3 app-main">Schedule</h3>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="flex flex-col gap-1 rounded-lg border border-[#DCCFD8] bg-white px-4 py-3">
                                        <span class="text-xs uppercase tracking-wide text-[#91848C]">Date</span>
                                        <p class="text-[#213430]" id="detailModalDate">Loading date...</p>
                                    </div>
                                    <div class="flex flex-col gap-1 rounded-lg border border-[#DCCFD8] bg-white px-4 py-3"
                                        id="detailModalTimeWrapper">
                                        <span class="text-xs uppercase tracking-wide text-[#91848C]">Time</span>
                                        <p class="text-[#213430]" id="detailModalTime">Loading time...</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-[#213430] mb-3 app-main">At a glance</h3>
                                <div class="grid gap-3 sm:grid-cols-2" id="detailModalInfoGrid">
                                    <div class="flex flex-col gap-1 rounded-lg border border-[#DCCFD8] bg-white px-4 py-3"
                                        id="detailModalLocationWrapper" hidden>
                                        <span class="text-xs uppercase tracking-wide text-[#91848C]">Location</span>
                                        <p class="text-[#213430]" id="detailModalLocation">—</p>
                                    </div>
                                    <div class="flex flex-col gap-1 rounded-lg border border-[#DCCFD8] bg-white px-4 py-3"
                                        id="detailModalStatusWrapper" hidden>
                                        <span class="text-xs uppercase tracking-wide text-[#91848C]">Status</span>
                                        <p class="text-[#213430]" id="detailModalStatus">—</p>
                                    </div>
                                    <div class="flex flex-col gap-1 rounded-lg border border-[#DCCFD8] bg-white px-4 py-3"
                                        id="detailModalSponsorsWrapper" hidden>
                                        <span class="text-xs uppercase tracking-wide text-[#91848C]">Sponsors</span>
                                        <p class="text-[#213430]" id="detailModalSponsors">—</p>
                                    </div>
                                    <div class="flex flex-col gap-1 rounded-lg border border-[#DCCFD8] bg-white px-4 py-3"
                                        id="detailModalRegistrationsWrapper" hidden>
                                        <span class="text-xs uppercase tracking-wide text-[#91848C]">Registrations</span>
                                        <p class="text-[#213430]" id="detailModalRegistrations">—</p>
                                    </div>
                                </div>
                            </div>

                            <div id="detailModalFundingWrapper"
                                class="rounded-lg border border-[#DCCFD8] bg-white px-4 py-4" hidden>
                                <div class="flex flex-wrap items-center gap-4">
                                    <div>
                                        <span class="text-xs uppercase tracking-wide text-[#91848C]">Raised so far</span>
                                        <p class="text-lg font-semibold text-[#213430]" id="detailModalRaised">$0.00</p>
                                    </div>
                                    <div id="detailModalGoalWrapper">
                                        <span class="text-xs uppercase tracking-wide text-[#91848C]">Funding goal</span>
                                        <p class="text-lg font-semibold text-[#213430]" id="detailModalGoal">$0.00</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-wrap justify-end gap-3 pt-2">
                                <button onclick="closeDetailModal()"
                                    class="px-5 py-3 bg-transparent border border-[#DCCFD8] text-[#91848C] rounded-md app-text">Cancel</button>
                                <a id="detailModalPrimaryLink" href="#"
                                    class="px-6 py-3 bg-[#DB69A2] text-white rounded-lg hover:bg-[#c25891] transition app-text hidden"
                                    target="_self">Open record</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const activeTab = localStorage.getItem('activeTab') || 'sponsors';
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
                btn.classList.remove("bg-[#DB69A2]", "text-white");
                btn.classList.add("bg-[#F3E8EF]", "text-[#91848C]");
            });

            const activeBtn = document.querySelector(`[onclick="showTab('${tabId}')"]`);
            if (activeBtn) {
                activeBtn.classList.remove("bg-[#F3E8EF]", "text-[#91848C]");
                activeBtn.classList.add("bg-[#DB69A2]", "text-white");
            }

            localStorage.setItem('activeTab', tabId);
        }

        // Dropdown functionality
        function toggleDropdown(btn) {
            const dropdown = btn.parentElement.querySelector("div");
            if (dropdown) {
                dropdown.classList.toggle("hidden");
            }

            document.querySelectorAll("td .absolute").forEach((el) => {
                if (el !== dropdown) el.classList.add("hidden");
            });
        }

        // Universal Detail Modal functions
        function openDetailModal(payload) {
            const data = payload || {};
            const modal = document.getElementById('detailModal');
            const panel = document.getElementById('detailModalPanel');

            const title = data.title || 'Record details';
            const type = data.type ? data.type.toString().replace(/_/g, ' ') : '';
            const prettyType = type ? type.charAt(0).toUpperCase() + type.slice(1) : 'Record';

            const description = data.description || 'No description available';
            const imageUrl = data.image || '/images/program-details.png';
            const date = data.date || null;
            const time = data.time || null;

            document.getElementById('detailModalTitle').textContent = title;
            document.getElementById('detailModalType').textContent = prettyType;
            document.getElementById('detailModalDescription').textContent = description;
            const imageEl = document.getElementById('detailModalImage');
            imageEl.src = imageUrl;
            imageEl.alt = title;

            const scheduleWrapper = document.getElementById('detailModalScheduleWrapper');
            const timeWrapper = document.getElementById('detailModalTimeWrapper');
            const dateEl = document.getElementById('detailModalDate');
            const timeEl = document.getElementById('detailModalTime');

            if (date) {
                dateEl.textContent = date;
                scheduleWrapper.removeAttribute('hidden');
            } else {
                dateEl.textContent = 'Date not specified';
                scheduleWrapper.removeAttribute('hidden');
            }

            if (time) {
                timeEl.textContent = time;
                timeWrapper.removeAttribute('hidden');
            } else {
                timeEl.textContent = 'Time not specified';
                timeWrapper.setAttribute('hidden', 'hidden');
            }

            const applyValue = (wrapperId, value, formatter) => {
                const wrapper = document.getElementById(wrapperId);
                if (!wrapper) return;
                const target = wrapper.querySelector('p');
                if (value === undefined || value === null || value === '') {
                    wrapper.setAttribute('hidden', 'hidden');
                    if (target) target.textContent = 'N/A';
                    return;
                }
                wrapper.removeAttribute('hidden');
                if (target) target.textContent = typeof formatter === 'function' ? formatter(value) : value;
            };

            applyValue('detailModalLocationWrapper', data.location, (val) => val);
            applyValue('detailModalStatusWrapper', data.status, (val) => {
                const str = val.toString();
                return str.charAt(0).toUpperCase() + str.slice(1);
            });
            applyValue('detailModalSponsorsWrapper', data.sponsor_count, (val) =>
                `${val} sponsor${parseInt(val, 10) === 1 ? '' : 's'}`);
            applyValue('detailModalRegistrationsWrapper', data.registrations, (val) => `${val}`);

            const fundingWrapper = document.getElementById('detailModalFundingWrapper');
            const goalWrapper = document.getElementById('detailModalGoalWrapper');
            const raisedEl = document.getElementById('detailModalRaised');
            const goalEl = document.getElementById('detailModalGoal');

            const toCurrency = (val) => {
                const num = typeof val === 'number' ? val : parseFloat(val);
                if (Number.isFinite(num)) {
                    return `$${num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                }
                return null;
            };

            const raisedText = toCurrency(data.total_raised);
            const goalText = toCurrency(data.fund_goal);

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

            const primaryLink = document.getElementById('detailModalPrimaryLink');
            if (data.show_url) {
                primaryLink.href = data.show_url;
                primaryLink.textContent = data.type === 'event' ? 'Open event' : (data.type === 'program' ? 'Open program' :
                    'Open record');
                primaryLink.classList.remove('hidden');
            } else {
                primaryLink.href = '#';
                primaryLink.classList.add('hidden');
            }

            if (modal && panel) {
                modal.classList.remove('hidden');
                requestAnimationFrame(() => {
                    panel.classList.remove('translate-x-full');
                    panel.classList.add('translate-x-0');
                });
            }
        }

        function closeDetailModal() {
            const modal = document.getElementById('detailModal');
            const panel = document.getElementById('detailModalPanel');
            if (modal && panel) {
                panel.classList.remove('translate-x-0');
                panel.classList.add('translate-x-full');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 220);
            }
        }

        // Legacy helpers routed to the new modal signature
        function openModal() {
            openDetailModal({
                type: 'program',
                title: 'Breast Cancer Awareness',
                description: 'A nonprofit initiative supporting women battling breast cancer, raising awareness about early detection and survivorship.',
                image: '/images/program-details.png',
                date: 'Saturday, March 30, 2025',
                time: '10:00 AM',
                total_raised: 0,
                fund_goal: 0,
            });
        }

        function closeModal() {
            closeDetailModal();
        }

        function openRegisterModal(title, description, imageUrl, date, time) {
            openDetailModal({
                title,
                description,
                image: imageUrl,
                date,
                time
            });
        }

        // Reject Modal functions
        function openRejectModal() {
            const modal = document.getElementById("rejectModal");
            if (modal) {
                modal.classList.remove("hidden");
            }
        }

        function closeRejectModal() {
            const modal = document.getElementById("rejectModal");
            if (modal) {
                modal.classList.add("hidden");
            }
        }

        // Close dropdowns and modals when clicking outside
        window.addEventListener("click", function(e) {
            // Close dropdowns
            if (!e.target.closest("td")) {
                document.querySelectorAll("td .absolute").forEach((el) => el.classList.add("hidden"));
            }

            // Close reject modal
            const rejectModal = document.getElementById("rejectModal");
            if (e.target === rejectModal) {
                closeRejectModal();
            }

            // Close detail modal
            const detailModal = document.getElementById('detailModal');
            if (e.target === detailModal) {
                closeDetailModal();
            }
        });
    </script>
@endpush
