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
            <div class="max-w-8xl mx-auto">
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
                                                    class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
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
                                    <button
                                        class="flex items-center border border-[#91848C] text-[#91848C] px-4 py-2 rounded-lg hover:bg-[#F6EDF5] transition-colors duration-200">
                                        <span class="text-sm">Filters</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586l-7.293 7.293a1 1 0 00-.293.707V21l-4-4v-6.586a1 1 0 00-.293-.707L3 6.586V4z" />
                                        </svg>
                                    </button>
                                    <a href="{{ route('events.create') }}"
                                        class="flex items-center bg-[#db69a2] text-white text-sm px-4 py-2 rounded-lg hover:bg-[#c25891] transition-colors duration-200">
                                        <span>Add New Event</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Events Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                                @if (isset($events) && $events->count() > 0)
                                    @foreach ($events as $event)
                                        <div
                                            class="bg-[#F3E8EF] rounded-lg p-6 hover:shadow-lg transition-shadow duration-200">
                                            <div class="flex items-start gap-4">
                                                <div class="flex-none">
                                                    <div
                                                        class="flex flex-col items-center justify-center w-20 h-20 border-2 border-[#DB69A2] rounded-lg bg-[#FFF7FC]">
                                                        <span
                                                            class="text-sm text-[#DB69A2]">{{ \Carbon\Carbon::parse($event->start_date)->format('M') }}</span>
                                                        <span
                                                            class="text-4xl font-bold text-[#DB69A2]">{{ \Carbon\Carbon::parse($event->start_date)->format('d') }}</span>
                                                    </div>
                                                </div>
                                                <div class="flex-1">
                                                    <h3 class="text-xl font-semibold text-[#213430] mb-2">
                                                        {{ $event->title }}</h3>
                                                    <p class="text-sm text-[#91848C] mb-4 line-clamp-2">
                                                        {{ $event->description }}</p>
                                                    <div class="flex items-center gap-4 mb-4">
                                                        <div class="flex items-center text-[#91848C]">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            </svg>
                                                            <span class="text-sm">{{ $event->location }}</span>
                                                        </div>
                                                        <div class="flex items-center text-[#91848C]">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            <span
                                                                class="text-sm">{{ \Carbon\Carbon::parse($event->start_date)->format('h:i A') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="flex justify-end gap-3">
                                                        <button
                                                            class="text-[#91848C] hover:text-[#736870] transition-colors duration-200">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </button>
                                                        <button
                                                            class="text-red-500 hover:text-red-700 transition-colors duration-200">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                        <button
                                                            onclick="openDetailModal('{{ addslashes($event->name) }}', '{{ addslashes($event->description) }}', '/images/program-details.png', '{{ $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('l, F d, Y') : '' }}', '{{ $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('h:i A') : '' }}')"
                                                            class="text-[#DB69A2] hover:text-[#c25891] transition-colors duration-200">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <!-- Sample Event Card -->
                                    <div
                                        class="bg-[#F3E8EF] rounded-lg p-6 hover:shadow-lg transition-shadow duration-200">
                                        <div class="flex items-start gap-4">
                                            <div class="flex-none">
                                                <div
                                                    class="flex flex-col items-center justify-center w-20 h-20 border-2 border-[#DB69A2] rounded-lg bg-[#FFF7FC]">
                                                    <span class="text-sm text-[#DB69A2]">Mar</span>
                                                    <span class="text-4xl font-bold text-[#DB69A2]">28</span>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="text-xl font-semibold text-[#213430] mb-2">Breast Cancer
                                                    Awareness Walk</h3>
                                                <p class="text-sm text-[#91848C] mb-4 line-clamp-2">A community walk to
                                                    raise awareness about breast cancer prevention and early detection.</p>
                                                <div class="flex items-center gap-4 mb-4">
                                                    <div class="flex items-center text-[#91848C]">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                        <span class="text-sm">Central Park</span>
                                                    </div>
                                                    <div class="flex items-center text-[#91848C]">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span class="text-sm">10:00 AM</span>
                                                    </div>
                                                </div>
                                                <div class="flex justify-end gap-3">
                                                    <button
                                                        class="text-[#91848C] hover:text-[#736870] transition-colors duration-200">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                    <button
                                                        class="text-red-500 hover:text-red-700 transition-colors duration-200">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                    <button
                                                        onclick="openDetailModal('Breast Cancer Awareness Walk', 'A community walk to raise awareness about breast cancer prevention and early detection.', '/images/program-details.png', 'Saturday, March 30, 2025', '10:00 AM')"
                                                        class="text-[#DB69A2] hover:text-[#c25891] transition-colors duration-200">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
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
                                        <!-- Desktop Program Card -->
                                        <div
                                            class="bg-[#F3E8EF] rounded-lg p-4 mb-4 hidden md:flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="w-20 h-20 rounded-lg overflow-hidden mr-4">
                                                    <img src="{{ $program->image_url ?? '/images/program-3.png' }}"
                                                        alt="{{ $program->title }}" class="w-full h-full object-cover" />
                                                </div>
                                                <div>
                                                    <h3 class="text-xl font-semibold text-[#213430] mb-1 program-h">
                                                        {{ $program->title }}</h3>
                                                    <p class="text-sm text-[#91848C] program-p">
                                                        {{ Str::limit($program->description, 150) }}</p>
                                                    @if ($program->start_date)
                                                        <div class="mt-2 text-sm text-[#91848C]">
                                                            <span class="mr-4"><i
                                                                    class="fas fa-calendar mr-1"></i>Starts:
                                                                {{ \Carbon\Carbon::parse($program->start_date)->format('M d, Y') }}</span>
                                                            @if ($program->end_date)
                                                                <span><i class="fas fa-calendar-check mr-1"></i>Ends:
                                                                    {{ \Carbon\Carbon::parse($program->end_date)->format('M d, Y') }}</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <button
                                                onclick="openDetailModal('{{ addslashes($program->title) }}', '{{ addslashes($program->description) }}', '{{ $program->image_url ?? '/images/program-3.png' }}', '{{ $program->start_date ? \Carbon\Carbon::parse($program->start_date)->format('l, F d, Y') : 'Date not specified' }}', '{{ $program->start_date ? \Carbon\Carbon::parse($program->start_date)->format('h:i A') : 'Time not specified' }}')"
                                                class="bg-transparent border border-[#213430] text-[#213430] hover:bg-[#db69a2] hover:text-white hover:border-transparent py-3 px-6 program-btn rounded-lg transition-colors">
                                                View Details
                                            </button>
                                        </div>

                                        <!-- Mobile Program Card -->
                                        <div class="bg-[#F3E8EF] rounded-lg p-3 mb-4 md:hidden">
                                            <div class="flex gap-3">
                                                <div class="w-[80px] h-[80px] rounded-lg overflow-hidden flex-shrink-0">
                                                    <img src="{{ $program->image_url ?? '/images/program-3.png' }}"
                                                        alt="{{ $program->title }}" class="w-full h-full object-cover" />
                                                </div>
                                                <div class="flex-1">
                                                    <h3 class="text-[15px] font-semibold text-[#213430] mb-1">
                                                        {{ $program->title }}</h3>
                                                    <p class="text-[13px] font-light text-[#91848C] mb-2">
                                                        {{ Str::limit($program->description, 100) }}</p>
                                                    @if ($program->start_date)
                                                        <div class="text-xs text-[#91848C] mb-2">
                                                            <span><i
                                                                    class="fas fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($program->start_date)->format('M d, Y') }}</span>
                                                        </div>
                                                    @endif
                                                    <button
                                                        onclick="openDetailModal('{{ addslashes($program->title) }}', '{{ addslashes($program->description) }}', '{{ $program->image_url ?? '/images/program-3.png' }}', '{{ $program->start_date ? \Carbon\Carbon::parse($program->start_date)->format('l, F d, Y') : 'Date not specified' }}', '{{ $program->start_date ? \Carbon\Carbon::parse($program->start_date)->format('h:i A') : 'Time not specified' }}')"
                                                        class="bg-transparent border border-[#213430] text-[#213430] hover:bg-[#db69a2] hover:text-white py-2 px-4 rounded-lg text-sm transition-colors">
                                                        View Details
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <!-- Sample Program Card -->
                                    <div
                                        class="bg-[#F3E8EF] rounded-lg p-4 mb-4 hidden md:flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-20 h-20 rounded-lg overflow-hidden mr-4">
                                                <img src="/images/program-3.png" alt="Support Program"
                                                    class="w-full h-full object-cover" />
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-semibold text-[#213430] mb-1 program-h">Stronger
                                                    Together: Cancer Support Group</h3>
                                                <p class="text-sm text-[#91848C] program-p">A community-driven program
                                                    offering emotional and financial support for cancer patients and
                                                    survivors.</p>
                                                <div class="mt-2 text-sm text-[#91848C]">
                                                    <span class="mr-4"><i class="fas fa-calendar mr-1"></i>Starts: Mar
                                                        15, 2025</span>
                                                    <span><i class="fas fa-calendar-check mr-1"></i>Ends: Dec 15,
                                                        2025</span>
                                                </div>
                                            </div>
                                        </div>
                                        <button
                                            onclick="openDetailModal('Stronger Together: Cancer Support Group', 'A community-driven program offering emotional and financial support for cancer patients and survivors.', '/images/program-3.png', 'Saturday, March 15, 2025', '10:00 AM')"
                                            class="bg-transparent border border-[#213430] text-[#213430] hover:bg-[#db69a2] hover:text-white hover:border-transparent py-3 px-6 program-btn rounded-lg transition-colors">
                                            View Details
                                        </button>
                                    </div>

                                    <!-- Mobile Program Card -->
                                    <div class="bg-[#F3E8EF] rounded-lg p-3 mb-4 md:hidden">
                                        <div class="flex gap-3">
                                            <div class="w-[80px] h-[80px] rounded-lg overflow-hidden flex-shrink-0">
                                                <img src="/images/program-3.png" alt="Support Program"
                                                    class="w-full h-full object-cover" />
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="text-[15px] font-semibold text-[#213430] mb-1">Stronger
                                                    Together: Cancer Support Group</h3>
                                                <p class="text-[13px] font-light text-[#91848C] mb-2">A community-driven
                                                    program offering emotional and financial support.</p>
                                                <div class="text-xs text-[#91848C] mb-2">
                                                    <span><i class="fas fa-calendar mr-1"></i>Mar 15, 2025</span>
                                                </div>
                                                <button
                                                    onclick="openDetailModal('Stronger Together: Cancer Support Group', 'A community-driven program offering emotional and financial support for cancer patients and survivors.', '/images/program-3.png', 'Saturday, March 15, 2025', '10:00 AM')"
                                                    class="bg-transparent border border-[#213430] text-[#213430] hover:bg-[#db69a2] hover:text-white py-2 px-4 rounded-lg text-sm transition-colors">
                                                    View Details
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

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
                <div class="border border-[#DCCFD8] p-2 rounded-md">
                    <div class="p-2 mb-2 border-b border-[#DCCFD8] rounded-md">
                        <h2 class="text-2xl font-semibold text-gray-900 program-main" id="detailModalTitle">Loading...
                        </h2>
                    </div>
                    <div class="w-full h-64 overflow-hidden rounded-md mb-2">
                        <img id="detailModalImage" src="/images/program-details.png" alt=""
                            class="w-full h-full object-cover" />
                    </div>
                    <div class="py-3 text-md text-gray-800 space-y-6">
                        <p class="text-[#91848C] app-text" id="detailModalDescription">Loading description...</p>

                        <!-- Date & Time -->
                        <div>
                            <h3 class="text-lg font-medium text-[#213430] mb-4 app-main">Date And Time</h3>
                            <div class="flex justify-between gap-6 border border-[#DCCFD8] py-4 px-4 rounded-lg">
                                <div class="flex flex-col gap-2 text-[#91848C] text-sm app-text">
                                    <div>
                                        <i class="far fa-calendar font-bold text-[#91848C]"></i>
                                        <span>Date</span>
                                    </div>
                                    <p class="text-[#91848C]" id="detailModalDate">Loading date...</p>
                                </div>
                                <div class="flex flex-col gap-2 text-[#91848C] text-sm app-text">
                                    <div>
                                        <i class="far fa-clock font-bold text-[#91848C]"></i>
                                        <span>Time</span>
                                    </div>
                                    <p class="text-[#91848C]" id="detailModalTime">Loading time...</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between gap-4 pt-4">
                            <button onclick="closeDetailModal()"
                                class="px-5 py-3 bg-transparent border border-[#DCCFD8] text-[#91848C] rounded-md app-text">
                                Cancel
                            </button>
                            <button
                                class="px-6 py-2 bg-[#DB69A2] text-white rounded-lg hover:bg-[#c25891] transition app-text">
                                Register Yourself
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
        function openDetailModal(title, description, imageUrl, date, time) {
            const modal = document.getElementById('detailModal');
            const panel = document.getElementById('detailModalPanel');

            // Update modal content
            document.getElementById('detailModalTitle').textContent = title || 'Event Details';
            document.getElementById('detailModalDescription').textContent = description || 'No description available';
            document.getElementById('detailModalImage').src = imageUrl || '/images/program-details.png';
            document.getElementById('detailModalImage').alt = title || 'Event Image';
            document.getElementById('detailModalDate').textContent = date || 'Date not specified';
            document.getElementById('detailModalTime').textContent = time || 'Time not specified';

            if (modal && panel) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    panel.classList.remove('translate-x-full');
                    panel.classList.add('translate-x-0');
                }, 10);
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
                }, 300);
            }
        }

        // Legacy functions for backward compatibility
        function openModal() {
            openDetailModal('Breast Cancer Awareness',
                'A nonprofit initiative supporting women battling breast cancer, raising awareness about early detection and survivorship.',
                '/images/program-details.png', 'Saturday, March 30, 2025', '10:00 AM');
        }

        function closeModal() {
            closeDetailModal();
        }

        function openRegisterModal(title, description, imageUrl, date, time) {
            openDetailModal(title, description, imageUrl, date, time);
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
@endsection
