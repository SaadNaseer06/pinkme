@extends('admin.layouts.admin')

@section('content')
    <div class="container mx-auto py-6">
        <!-- Header -->
        <div class="flex justify-between flex-col gap-4 md:flex-row md:items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Applications Management</h1>
                <p class="text-gray-600">Review and manage all program application requests</p>
            </div>
        </div>

        <!-- Tab Navigation - Event tab commented: sponsor-related -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px" aria-label="Tabs">
                    <div class="flex-1 py-4 px-6 text-center border-b-2 border-pink-600">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Program Applications
                            @if($programCounts['pending'] > 0)
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                    {{ $programCounts['pending'] }}
                                </span>
                            @endif
                        </div>
                    </div>
                </nav>
            </div>
        </div>
  
        <!-- Dynamic Stats Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6" id="statsCards">
            <!-- Program Stats (shown when programs tab is active) -->
            <div class="stat-card program-stats" data-tab="programs" style="display: {{ $tab === 'programs' ? 'block' : 'none' }}; transition: opacity 0.3s ease;">
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-pink-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Pending Approval</p>
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
            {{-- Rejected stat removed per client request --}}
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

            {{-- Event Stats - commented: sponsor-related, sponsor not wanted for now
            <div class="stat-card event-stats" data-tab="events" style="display: {{ $tab === 'events' ? 'block' : 'none' }}; transition: opacity 0.3s ease;">
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-pink-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Pending Approval</p>
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
            --}}
        </div>

        <!-- Program Registrations Tab Content -->
        <div id="programs-content" class="tab-content" style="display: block;">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-pink-50 border-b border-pink-200">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                    <h3 class="text-lg font-medium text-gray-900">Program Applications</h3>
                    <p class="text-sm text-gray-600 mt-1">Manage patient program application requests</p>
                        </div>
                        <div class="flex flex-wrap gap-2 text-sm text-gray-600">
                            <span>Pending: <strong class="text-pink-600">{{ $programCounts['pending'] }}</strong></span>
                            <span>Approved: <strong class="text-green-600">{{ $programCounts['approved'] }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex flex-col md:flex-row md:items-center gap-3">
                        <div class="relative w-full md:w-48">
                            <select name="program_status" id="programStatusFilter"
                                class="w-full appearance-none rounded-md border border-gray-300 bg-white px-4 py-2 pr-10 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <option value="pending" {{ $programSelectedStatus === 'pending' ? 'selected' : '' }}>Pending Approval</option>
                                <option value="approved" {{ $programSelectedStatus === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="all" {{ $programSelectedStatus === 'all' ? 'selected' : '' }}>All</option>
                            </select>
                            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                        @if ($programSelectedStatus !== 'all')
                            <a href="{{ route('admin.registrations.index', ['tab' => 'programs', 'program_status' => 'all']) }}"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md text-sm hover:bg-gray-50 transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Table (AJAX-loaded) -->
                <div id="programRegistrationsTableWrapper">
                    @include('admin.registrations._table', [
                        'programRegistrations' => $programRegistrations,
                        'caseManagers' => $caseManagers,
                        'financeUsers' => $financeUsers,
                    ])
                </div>
            </div>
        </div>

        <div id="assignModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
            <div class="bg-white rounded-2xl w-full max-w-md shadow-xl p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 id="assignModalTitle" class="text-lg font-semibold text-gray-900">Assign Case Manager</h3>
                        <p id="assignModalSubtitle" class="text-sm text-gray-500 mt-1">Select a case manager to handle this registration.</p>
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

        <!-- Toast Container -->
        <div id="toastContainer" class="fixed top-8 right-8 z-[99999] flex flex-col gap-3" style="min-width: 250px; max-width: 400px;"></div>

        {{-- Send to Finance Modal --}}
        <div id="sendToFinanceModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
            <div class="bg-white rounded-2xl w-full max-w-md shadow-xl p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Send to Finance User</h3>
                        <p class="text-sm text-gray-500 mt-1">Select a finance user to allocate budget for this registration.</p>
                    </div>
                    <button type="button" id="sendToFinanceModalClose"
                        class="h-8 w-8 inline-flex items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-gray-50">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="sendToFinanceModalForm" class="mt-5 space-y-4">
                    @csrf
                    <input type="hidden" id="sendToFinanceRegistrationId" name="registration_id" value="">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-[0.12em]">Assign To Finance User</label>
                        <select id="sendToFinanceUserId" name="finance_user_id"
                            class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <option value="">Select finance user</option>
                            @foreach ($financeUsers ?? [] as $fu)
                                <option value="{{ $fu->id }}">{{ $fu->profile->full_name ?? $fu->email }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" id="sendToFinanceModalCancel"
                            class="px-4 py-2 rounded-md border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" id="sendToFinanceSubmit"
                            class="px-4 py-2 rounded-md bg-pink-600 text-white text-sm font-semibold hover:bg-pink-700">
                            Send to Finance
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Event Registrations Tab Content - commented: sponsor-related, sponsor not wanted for now
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
                            <h3 class="text-lg font-medium text-gray-900">All Event Applications</h3>
                            <p class="text-sm text-gray-600 mt-1">Manage sponsor application requests for events</p>
                        </div>
                        <div class="flex flex-wrap gap-2 text-sm text-gray-600">
                            <span>Pending Approval: <strong class="text-pink-600">{{ $eventCounts['pending'] }}</strong></span>
                            <span>Approved: <strong class="text-green-600">{{ $eventCounts['confirmed'] }}</strong></span>
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
        --}}
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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        const REGISTRATIONS_LIST_URL = "{{ route('admin.registrations.list') }}";

        function showToast(message, type) {
            type = type || 'success';
            const container = document.getElementById('toastContainer');
            if (!container) return;
            const toast = document.createElement('div');
            toast.className = 'toast-msg toast-' + type;
            toast.innerHTML = '<span>' + (type === 'success' ? '✓' : '!') + '</span><div style="flex:1">' + (message || '') + '</div><button class="toast-close" aria-label="Close">&times;</button>';
            container.appendChild(toast);
            toast.querySelector('.toast-close').addEventListener('click', function() {
                toast.style.opacity = '0';
                setTimeout(function() { toast.remove(); }, 300);
            });
            setTimeout(function() {
                toast.style.opacity = '0';
                setTimeout(function() { toast.remove(); }, 400);
            }, 3500);
        }

        function loadProgramRegistrations(params) {
            const query = $.param(params || {});
            $('#programRegistrationsTableWrapper').addClass('opacity-60');
            $.get(REGISTRATIONS_LIST_URL + (query ? ('?' + query) : ''))
                .done(function(html) {
                    $('#programRegistrationsTableWrapper').html(html).removeClass('opacity-60');
                })
                .fail(function() {
                    $('#programRegistrationsTableWrapper').removeClass('opacity-60');
                    alert('Failed to load applications.');
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Filter: load without page reload
            $('#programStatusFilter').on('change', function() {
                const status = $(this).val();
                loadProgramRegistrations({ program_status: status, program_page: 1 });
            });

            // Pagination: load via AJAX without reload
            $(document).on('click', '#programRegistrationsTableWrapper a', function(e) {
                const href = this.getAttribute('href') || '';
                const isPagination = href.indexOf('program_page') !== -1 || href.indexOf('registrations/list') !== -1;
                if (isPagination) {
                    e.preventDefault();
                    const query = href.indexOf('?') !== -1 ? href.substring(href.indexOf('?')) : '';
                    const url = href.indexOf('http') === 0 ? href : REGISTRATIONS_LIST_URL + (href.indexOf('?') !== -1 ? href.substring(href.indexOf('?')) : '');
                    $('#programRegistrationsTableWrapper').addClass('opacity-60');
                    $.get(url)
                        .done(function(html) { $('#programRegistrationsTableWrapper').html(html).removeClass('opacity-60'); })
                        .fail(function() { $('#programRegistrationsTableWrapper').removeClass('opacity-60'); alert('Failed to load applications.'); });
                }
            });

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

            const assignModal = document.getElementById('assignModal');
            const assignModalForm = document.getElementById('assignModalForm');
            const assignModalClose = document.getElementById('assignModalClose');
            const assignModalCancel = document.getElementById('assignModalCancel');
            const sendToFinanceModal = document.getElementById('sendToFinanceModal');
            const sendToFinanceRegistrationId = document.getElementById('sendToFinanceRegistrationId');

            function closeActionMenus() {
                document.querySelectorAll('[data-actions-menu]').forEach(menu => menu.classList.add('hidden'));
                document.querySelectorAll('[data-actions-toggle]').forEach(toggle => toggle.setAttribute('aria-expanded', 'false'));
            }

            // Event delegation for dynamically loaded table rows
            document.addEventListener('click', function(e) {
                const actionsToggle = e.target.closest('[data-actions-toggle]');
                if (actionsToggle) {
                    e.stopPropagation();
                    const menu = actionsToggle.parentElement.querySelector('[data-actions-menu]');
                    if (menu) {
                        const isOpen = !menu.classList.contains('hidden');
                        closeActionMenus();
                        if (!isOpen) {
                            menu.classList.remove('hidden');
                            actionsToggle.setAttribute('aria-expanded', 'true');
                        }
                    }
                    return;
                }
                const assignTrigger = e.target.closest('[data-assign-trigger]');
                if (assignTrigger) {
                    e.preventDefault();
                    const url = assignTrigger.getAttribute('data-assign-url');
                    const currentId = assignTrigger.getAttribute('data-assign-current');
                    const assignName = assignTrigger.getAttribute('data-assign-name') || '';
                    closeActionMenus();
                    if (url && assignModal && assignModalForm) {
                        assignModalForm.action = url;
                        const select = assignModalForm.querySelector('select[name="case_manager_id"]');
                        if (select) select.value = currentId || '';
                        const titleEl = document.getElementById('assignModalTitle');
                        const subtitleEl = document.getElementById('assignModalSubtitle');
                        if (assignName) {
                            if (titleEl) titleEl.textContent = 'Change Case Manager';
                            if (subtitleEl) subtitleEl.textContent = 'Currently assigned to: ' + assignName;
                        } else {
                            if (titleEl) titleEl.textContent = 'Assign Case Manager';
                            if (subtitleEl) subtitleEl.textContent = 'Select a case manager to handle this registration.';
                        }
                        assignModal.classList.remove('hidden');
                        assignModal.classList.add('flex');
                    }
                    return;
                }
                const sendFinanceTrigger = e.target.closest('[data-send-finance-trigger]');
                if (sendFinanceTrigger) {
                    e.preventDefault();
                    const regId = sendFinanceTrigger.getAttribute('data-registration-id');
                    closeActionMenus();
                    if (regId && sendToFinanceRegistrationId) {
                        sendToFinanceRegistrationId.value = regId;
                        if (sendToFinanceModal) {
                            sendToFinanceModal.classList.remove('hidden');
                            sendToFinanceModal.classList.add('flex');
                        }
                    }
                    return;
                }
                if (!e.target.closest('[data-actions-menu]') && !e.target.closest('[data-actions-toggle]')) {
                    closeActionMenus();
                }
            });

            function openAssignModal(url, currentId) {
                if (!assignModal || !assignModalForm) return;
                assignModalForm.action = url;
                const select = assignModalForm.querySelector('select[name="case_manager_id"]');
                if (select) select.value = currentId || '';
                assignModal.classList.remove('hidden');
                assignModal.classList.add('flex');
            }

            function closeAssignModal() {
                if (!assignModal) return;
                assignModal.classList.add('hidden');
                assignModal.classList.remove('flex');
            }

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

            // Send to Finance Modal
            const sendToFinanceForm = document.getElementById('sendToFinanceModalForm');
            const sendToFinanceModalClose = document.getElementById('sendToFinanceModalClose');
            const sendToFinanceModalCancel = document.getElementById('sendToFinanceModalCancel');

            function closeSendToFinanceModal() {
                if (sendToFinanceModal) {
                    sendToFinanceModal.classList.add('hidden');
                    sendToFinanceModal.classList.remove('flex');
                }
            }

            if (sendToFinanceModal) {
                sendToFinanceModal.addEventListener('click', function(e) {
                    if (e.target === sendToFinanceModal) closeSendToFinanceModal();
                });
            }
            if (sendToFinanceModalClose) sendToFinanceModalClose.addEventListener('click', closeSendToFinanceModal);
            if (sendToFinanceModalCancel) sendToFinanceModalCancel.addEventListener('click', closeSendToFinanceModal);

            if (sendToFinanceForm) {
                sendToFinanceForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const regId = sendToFinanceRegistrationId?.value;
                    const financeUserId = document.getElementById('sendToFinanceUserId')?.value;
                    if (!regId || !financeUserId) return;
                    const submitBtn = document.getElementById('sendToFinanceSubmit');
                    if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Sending...'; }
                    const formData = new FormData();
                    formData.append('finance_user_id', financeUserId);
                    formData.append('_token', document.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.content);
                    const sendToFinanceUrl = @json(rtrim(url('/admin/program-registration-requests'), '/'));
                    fetch(`${sendToFinanceUrl}/${regId}/send-to-finance`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value,
                            'Accept': 'application/json',
                        },
                        body: formData
                    }).then(r => r.json()).then(data => {
                        closeSendToFinanceModal();
                        if (data.success) {
                            showToast(data.message || 'Sent to finance successfully!', 'success');
                            setTimeout(function() { window.location.reload(); }, 800);
                        } else {
                            showToast(data.message || 'Failed to send.', 'error');
                        }
                    }).catch(() => {
                        showToast('An error occurred.', 'error');
                    }).finally(() => {
                        if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Send to Finance'; }
                    });
                });
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
