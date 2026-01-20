@extends('admin.layouts.admin')

@section('content')
    <div class="container mx-auto py-6">
        <!-- Header -->
        <div class="flex justify-between flex-col gap-4 md:flex-row md:items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Registrations Management</h1>
                <p class="text-gray-600">Review and manage all program and event registration requests</p>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px" aria-label="Tabs">
                    <button type="button" 
                            data-tab="programs"
                            class="tab-button flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors {{ $tab === 'programs' ? 'border-pink-600 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Program Registrations
                            @if($programCounts['pending'] > 0)
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800 program-pending-badge">
                                    {{ $programCounts['pending'] }}
                                </span>
                            @endif
                        </div>
                    </button>
                    <button type="button"
                            data-tab="events"
                            class="tab-button flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors {{ $tab === 'events' ? 'border-pink-600 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Event Registrations
                            @if($eventCounts['pending'] > 0)
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800 event-pending-badge">
                                    {{ $eventCounts['pending'] }}
                                </span>
                            @endif
                        </div>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Dynamic Stats Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6" id="statsCards">
            <!-- Program Stats (shown when programs tab is active) -->
            <div class="stat-card program-stats" data-tab="programs" style="display: {{ $tab === 'programs' ? 'block' : 'none' }}; transition: opacity 0.3s ease;">
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-pink-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Pending</p>
                            <p class="text-2xl font-semibold text-gray-900 mt-1" id="program-pending-count">
                                {{ $programCounts['pending'] }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="stat-card program-stats" data-tab="programs" style="display: {{ $tab === 'programs' ? 'block' : 'none' }};">
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Approved</p>
                            <p class="text-2xl font-semibold text-gray-900 mt-1" id="program-approved-count">
                                {{ $programCounts['approved'] }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="stat-card program-stats" data-tab="programs" style="display: {{ $tab === 'programs' ? 'block' : 'none' }};">
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Rejected</p>
                            <p class="text-2xl font-semibold text-gray-900 mt-1" id="program-rejected-count">
                                {{ $programCounts['rejected'] }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="stat-card program-stats" data-tab="programs" style="display: {{ $tab === 'programs' ? 'block' : 'none' }};">
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total</p>
                            <p class="text-2xl font-semibold text-gray-900 mt-1" id="program-total-count">
                                {{ $programCounts['all'] }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Stats (shown when events tab is active) -->
            <div class="stat-card event-stats" data-tab="events" style="display: {{ $tab === 'events' ? 'block' : 'none' }}; transition: opacity 0.3s ease;">
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-pink-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Pending</p>
                            <p class="text-2xl font-semibold text-gray-900 mt-1" id="event-pending-count">
                                {{ $eventCounts['pending'] }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="stat-card event-stats" data-tab="events" style="display: {{ $tab === 'events' ? 'block' : 'none' }};">
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Confirmed</p>
                            <p class="text-2xl font-semibold text-gray-900 mt-1" id="event-confirmed-count">
                                {{ $eventCounts['confirmed'] }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="stat-card event-stats" data-tab="events" style="display: {{ $tab === 'events' ? 'block' : 'none' }};">
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Cancelled</p>
                            <p class="text-2xl font-semibold text-gray-900 mt-1" id="event-cancelled-count">
                                {{ $eventCounts['cancelled'] }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="stat-card event-stats" data-tab="events" style="display: {{ $tab === 'events' ? 'block' : 'none' }};">
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total</p>
                            <p class="text-2xl font-semibold text-gray-900 mt-1" id="event-total-count">
                                {{ $eventCounts['all'] }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Program Registrations Tab Content -->
        <div id="programs-content" class="tab-content" style="display: {{ $tab === 'programs' ? 'block' : 'none' }}; transition: opacity 0.3s ease;">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-pink-50 border-b border-pink-200">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Program Registrations</h3>
                            <p class="text-sm text-gray-600 mt-1">Manage patient program registration requests</p>
                        </div>
                        <div class="flex flex-wrap gap-2 text-sm text-gray-600">
                            <span>Pending: <strong class="text-pink-600">{{ $programCounts['pending'] }}</strong></span>
                            <span>Approved: <strong class="text-green-600">{{ $programCounts['approved'] }}</strong></span>
                            <span>Rejected: <strong class="text-red-600">{{ $programCounts['rejected'] }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <form method="GET" class="flex flex-col md:flex-row md:items-center gap-3">
                        <input type="hidden" name="tab" value="programs">
                        <div class="relative w-full md:w-48">
                            <select name="program_status" onchange="this.form.submit()"
                                class="w-full appearance-none rounded-md border border-gray-300 bg-white px-4 py-2 pr-10 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <option value="pending" {{ $programSelectedStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ $programSelectedStatus === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ $programSelectedStatus === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="all" {{ $programSelectedStatus === 'all' ? 'selected' : '' }}>All</option>
                            </select>
                            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                        @if ($programSelectedStatus !== 'pending')
                            <a href="{{ route('admin.registrations.index', ['tab' => 'programs']) }}"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md text-sm hover:bg-gray-50 transition">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto overflow-y-visible border border-gray-200 rounded-xl bg-white shadow-sm">
                    <table class="min-w-full text-sm">
                        <thead class="bg-[#F7EEF3] border-b border-pink-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold text-[#7B5B6B] uppercase tracking-[0.14em]">Applicant</th>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold text-[#7B5B6B] uppercase tracking-[0.14em]">Program</th>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold text-[#7B5B6B] uppercase tracking-[0.14em]">Submitted</th>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold text-[#7B5B6B] uppercase tracking-[0.14em]">Status</th>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold text-[#7B5B6B] uppercase tracking-[0.14em]">Assigned</th>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold text-[#7B5B6B] uppercase tracking-[0.14em] w-24">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($programRegistrations as $registration)
                                <tr class="hover:bg-[#FBF5F8] transition">
                                    <td class="px-6 py-4 align-top">
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-gray-900">{{ $registration->full_name }}</span>
                                            <span class="text-xs text-gray-500">{{ $registration->email }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 align-top">
                                        {{ $registration->program->title ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 align-top">
                                        {{ $registration->created_at?->format('M d, Y h:i A') ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        @php
                                            $status = strtolower($registration->status);
                                            $badgeClasses = match ($status) {
                                                'approved' => 'bg-green-100 text-green-800',
                                                'rejected' => 'bg-red-100 text-red-800',
                                                default => 'bg-pink-100 text-pink-800',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-semibold rounded-full {{ $badgeClasses }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 align-top">
                                        {{ $registration->assignedCaseManager?->profile?->full_name ?? $registration->assignedCaseManager?->email ?? 'Unassigned' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium align-top w-24">
                                        <div class="inline-flex">
                                            <button type="button" data-actions-toggle
                                                class="inline-flex items-center justify-center h-8 w-8 rounded-full border border-gray-400 text-gray-600 hover:bg-gray-100 transition"
                                                aria-haspopup="true" aria-expanded="false" title="Actions">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6h.01M12 12h.01M12 18h.01" />
                                                </svg>
                                            </button>
                                            <div data-actions-menu
                                                class="hidden absolute right-0 mt-2 w-48 rounded-xl border border-gray-200 bg-white shadow-xl z-30 max-h-56 overflow-y-auto">
                                                <div class="p-2">
                                                    <a href="{{ route('admin.program_registrations.show', $registration) }}"
                                                        class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md">
                                                        {{-- <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-pink-100 text-pink-600 text-xs">V</span> --}}
                                                        View Details
                                                    </a>
                                                    <button type="button"
                                                        data-assign-trigger
                                                        data-assign-url="{{ route('admin.program_registrations.assign', $registration) }}"
                                                        data-assign-current="{{ $registration->assigned_case_manager_id ?? '' }}"
                                                        class="w-full flex items-center gap-2 px-2 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md">
                                                        {{-- <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-pink-100 text-pink-600 text-xs">A</span> --}}
                                                        Assign Case Manager
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No program registrations found for the selected filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($programRegistrations->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $programRegistrations->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div id="assignModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
            <div class="bg-white rounded-2xl w-full max-w-md shadow-xl p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Assign Case Manager</h3>
                        <p class="text-sm text-gray-500 mt-1">Select a case manager to handle this registration.</p>
                    </div>
                    <button type="button" id="assignModalClose"
                        class="h-8 w-8 inline-flex items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-gray-50">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="assignModalForm" method="POST" action="" class="mt-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-[0.12em]">Assign To</label>
                        <select name="case_manager_id"
                            class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <option value="">Unassigned</option>
                            @foreach ($caseManagers as $manager)
                                <option value="{{ $manager->id }}">
                                    {{ $manager->profile->full_name ?? $manager->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" id="assignModalCancel"
                            class="px-4 py-2 rounded-md border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 rounded-md bg-pink-600 text-white text-sm font-semibold hover:bg-pink-700">
                            Confirm & Assign
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Event Registrations Tab Content -->
        <div id="events-content" class="tab-content" style="display: {{ $tab === 'events' ? 'block' : 'none' }}; transition: opacity 0.3s ease;">
            <!-- Pending Registrations Section -->
            @if ($pendingEventRegistrations->count() > 0)
                <div class="bg-pink-50 border border-pink-200 rounded-lg p-6 mb-8">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-pink-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                        <h2 class="text-lg font-semibold text-pink-800">Pending Approvals ({{ $pendingEventRegistrations->count() }})</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        @foreach ($pendingEventRegistrations as $registration)
                            <div class="bg-white rounded-lg border border-pink-300 p-4 shadow-sm">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-800">{{ $registration->event->title }}</h3>
                                        <p class="text-sm text-gray-600">
                                            Sponsor: {{ $registration->sponsor->{$displayCol} ?? $registration->sponsor->email }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            Amount: <span class="font-semibold">${{ number_format($registration->amount, 2) }}</span>
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Registered: {{ $registration->formatted_registered_at }}
                                        </p>
                                    </div>
                                    <div class="flex flex-col space-y-2">
                                        <form method="POST" action="{{ route('events.registrations.approve', $registration) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                onclick="return confirm('Are you sure you want to approve this registration?')"
                                                class="bg-pink-600 text-white px-3 py-1 text-sm rounded hover:bg-pink-700 transition">
                                                Approve
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('events.registrations.reject', $registration) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                onclick="return confirm('Are you sure you want to reject this registration?')"
                                                class="bg-red-600 text-white px-3 py-1 text-sm rounded hover:bg-red-700 transition">
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @if ($registration->message)
                                    <div class="bg-gray-50 p-3 rounded text-sm">
                                        <strong>Message:</strong> {{ $registration->message }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-pink-50 border border-pink-200 rounded-lg p-6 mb-8">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-pink-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-pink-800 font-medium">No pending event registrations at the moment!</p>
                    </div>
                </div>
            @endif

            <!-- All Event Registrations Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-pink-50 border-b border-pink-200">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">All Event Registrations</h3>
                            <p class="text-sm text-gray-600 mt-1">Manage sponsor registration requests for events</p>
                        </div>
                        <div class="flex flex-wrap gap-2 text-sm text-gray-600">
                            <span>Pending: <strong class="text-pink-600">{{ $eventCounts['pending'] }}</strong></span>
                            <span>Confirmed: <strong class="text-green-600">{{ $eventCounts['confirmed'] }}</strong></span>
                            <span>Cancelled: <strong class="text-red-600">{{ $eventCounts['cancelled'] }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <form method="GET" class="flex flex-col md:flex-row md:items-center gap-3">
                        <input type="hidden" name="tab" value="events">
                        <div class="relative w-full md:w-64">
                            <select name="event_id" onchange="this.form.submit()"
                                class="w-full appearance-none rounded-md border border-gray-300 bg-white px-4 py-2 pr-10 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <option value="">All Events</option>
                                @foreach ($eventsForFilter as $eventOption)
                                    <option value="{{ $eventOption->id }}"
                                        {{ (string) $eventSelectedId === (string) $eventOption->id ? 'selected' : '' }}>
                                        {{ $eventOption->title }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                        @if ($eventSelectedId)
                            <a href="{{ route('admin.registrations.index', ['tab' => 'events']) }}"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md text-sm hover:bg-gray-50 transition">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gradient-to-r from-pink-50 via-pink-100 to-pink-50 border-b border-pink-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-[11px] font-semibold text-pink-900 uppercase tracking-[0.08em]">Event & Sponsor</th>
                                <th class="px-6 py-4 text-left text-[11px] font-semibold text-pink-900 uppercase tracking-[0.08em]">Amount</th>
                                <th class="px-6 py-4 text-left text-[11px] font-semibold text-pink-900 uppercase tracking-[0.08em]">Status</th>
                                <th class="px-6 py-4 text-left text-[11px] font-semibold text-pink-900 uppercase tracking-[0.08em]">Dates</th>
                                <th class="px-6 py-4 text-left text-[11px] font-semibold text-pink-900 uppercase tracking-[0.08em]">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($eventRegistrations as $registration)
                                <tr class="hover:bg-pink-50/60 transition">
                                    <td class="px-6 py-4 align-top">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $registration->event->title }}
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                {{ $registration->sponsor->{$displayCol} ?? $registration->sponsor->email }}
                                            </div>
                                            @if ($registration->message)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    "{{ Str::limit($registration->message, 50) }}"
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 align-top">
                                        ${{ number_format($registration->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        @php
                                            $statusClasses = match ($registration->registration_status) {
                                                'confirmed' => 'bg-green-100 text-green-800',
                                                'pending' => 'bg-pink-100 text-pink-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full {{ $statusClasses }}">
                                            {{ $registration->status_text }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 align-top">
                                        <div>Registered: {{ $registration->formatted_registered_at }}</div>
                                        @if ($registration->confirmed_at)
                                            <div>Confirmed: {{ $registration->formatted_confirmed_at }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium align-top">
                                        <div class="flex flex-wrap gap-2">
                                            @if ($registration->canBeApproved())
                                                <form method="POST" action="{{ route('events.registrations.approve', $registration) }}">
                                                    @csrf
                                                    <button type="submit"
                                                        onclick="return confirm('Approve this registration?')"
                                                        class="inline-flex items-center px-3 py-1.5 text-green-700 hover:text-green-900 font-semibold rounded-md hover:bg-green-50 transition-colors">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Approve
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($registration->canBeRejected())
                                                <form method="POST" action="{{ route('events.registrations.reject', $registration) }}">
                                                    @csrf
                                                    <button type="submit" onclick="return confirm('Reject this registration?')"
                                                        class="inline-flex items-center px-3 py-1.5 text-red-700 hover:text-red-900 font-semibold rounded-md hover:bg-red-50 transition-colors">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        {{ $registration->registration_status === 'confirmed' ? 'Cancel' : 'Reject' }}
                                                    </button>
                                                </form>
                                            @endif

                                            <a href="{{ route('events.show', $registration->event) }}"
                                                class="inline-flex items-center px-3 py-1.5 text-pink-700 hover:text-pink-900 font-semibold rounded-md hover:bg-pink-50 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                View Event
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No event registrations found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($eventRegistrations->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $eventRegistrations->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .tab-content {
            animation: fadeIn 0.3s ease-in;
        }
        
        .stat-card {
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            const statCards = document.querySelectorAll('.stat-card');
            
            function switchTab(activeTab) {
                // Update tab buttons
                tabButtons.forEach(button => {
                    const tab = button.getAttribute('data-tab');
                    if (tab === activeTab) {
                        button.classList.add('border-pink-600', 'text-pink-600');
                        button.classList.remove('border-transparent', 'text-gray-500');
                    } else {
                        button.classList.remove('border-pink-600', 'text-pink-600');
                        button.classList.add('border-transparent', 'text-gray-500');
                    }
                });

                // Show/hide tab contents
                tabContents.forEach(content => {
                    if (content.id === `${activeTab}-content`) {
                        content.style.display = 'block';
                    } else {
                        content.style.display = 'none';
                    }
                });

                // Show/hide stat cards
                statCards.forEach(card => {
                    const cardTab = card.getAttribute('data-tab');
                    if (cardTab === activeTab) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Update URL without reload
                const url = new URL(window.location);
                url.searchParams.set('tab', activeTab);
                window.history.pushState({ tab: activeTab }, '', url);
            }

            // Add click handlers to tab buttons
            tabButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const tab = this.getAttribute('data-tab');
                    switchTab(tab);
                });
            });

            const actionToggles = document.querySelectorAll('[data-actions-toggle]');
            const assignTriggers = document.querySelectorAll('[data-assign-trigger]');
            const assignModal = document.getElementById('assignModal');
            const assignModalForm = document.getElementById('assignModalForm');
            const assignModalClose = document.getElementById('assignModalClose');
            const assignModalCancel = document.getElementById('assignModalCancel');

            function closeActionMenus() {
                document.querySelectorAll('[data-actions-menu]').forEach(menu => {
                    menu.classList.add('hidden');
                });
                document.querySelectorAll('[data-actions-toggle]').forEach(toggle => {
                    toggle.setAttribute('aria-expanded', 'false');
                });
            }

            actionToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const menu = this.parentElement.querySelector('[data-actions-menu]');
                    if (!menu) {
                        return;
                    }
                    const isOpen = !menu.classList.contains('hidden');
                    closeActionMenus();
                    if (!isOpen) {
                        menu.classList.remove('hidden');
                        this.setAttribute('aria-expanded', 'true');
                    }
                });
            });

            document.addEventListener('click', function() {
                closeActionMenus();
            });

            function openAssignModal(url, currentId) {
                if (!assignModal || !assignModalForm) {
                    return;
                }
                assignModalForm.action = url;
                const select = assignModalForm.querySelector('select[name="case_manager_id"]');
                if (select) {
                    select.value = currentId || '';
                }
                assignModal.classList.remove('hidden');
                assignModal.classList.add('flex');
            }

            function closeAssignModal() {
                if (!assignModal) {
                    return;
                }
                assignModal.classList.add('hidden');
                assignModal.classList.remove('flex');
            }

            assignTriggers.forEach(trigger => {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('data-assign-url');
                    const currentId = this.getAttribute('data-assign-current');
                    closeActionMenus();
                    if (url) {
                        openAssignModal(url, currentId);
                    }
                });
            });

            if (assignModal) {
                assignModal.addEventListener('click', function(e) {
                    if (e.target === assignModal) {
                        closeAssignModal();
                    }
                });
            }

            if (assignModalClose) {
                assignModalClose.addEventListener('click', closeAssignModal);
            }

            if (assignModalCancel) {
                assignModalCancel.addEventListener('click', closeAssignModal);
            }

            // Handle browser back/forward buttons
            window.addEventListener('popstate', function(event) {
                const urlParams = new URLSearchParams(window.location.search);
                const tab = urlParams.get('tab') || 'programs';
                switchTab(tab);
            });
        });
    </script>
@endsection
