@extends('admin.layouts.admin')

@section('content')
    @php
        $programSelectedType = $programSelectedType ?? 'all';
        $isMtmApplicationsView = $programSelectedType === \App\Support\ProgramType::MOMENTS_THAT_MATTER;
        $isFinancialApplicationsView = $programSelectedType === \App\Support\ProgramType::FINANCIAL_ASSISTANCE;
        $kpiGridClass = match (true) {
            $isMtmApplicationsView => 'admin-kpi-grid admin-kpi-grid--mtm',
            $isFinancialApplicationsView => 'admin-kpi-grid admin-kpi-grid--financial',
            default => 'admin-kpi-grid admin-kpi-grid--all',
        };
    @endphp
    <div class="admin-registrations-page max-w-8xl mx-auto w-full px-4 sm:px-6 py-6">
        <header class="mb-8">
            <h1 class="text-2xl font-semibold text-[#213430] tracking-tight">Applications Management</h1>
            <p class="mt-1 text-sm text-[#6C5F67]">Review and manage program application requests across Financial Assistance and Moments That Matter.</p>
        </header>

        <section class="mb-8" aria-labelledby="applications-overview-heading">
            <h2 id="applications-overview-heading" class="text-xs font-semibold uppercase tracking-wider text-[#91848C] mb-4">Overview</h2>
            <div class="{{ $kpiGridClass }}" id="statsCards">
            @unless ($isMtmApplicationsView)
                <article class="stat-card program-stats admin-kpi-card admin-kpi-card--pending" data-tab="programs" style="display: {{ $tab === 'programs' ? '' : 'none' }};">
                    <div class="admin-kpi-card__icon" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div class="admin-kpi-card__body">
                        <p class="admin-kpi-card__label">Pending (case review)</p>
                        <p class="admin-kpi-card__value" id="program-pending-count">{{ $programCounts['pending'] }}</p>
                    </div>
                </article>
                <article class="stat-card program-stats finance-stat-card admin-kpi-card admin-kpi-card--finance" data-tab="programs" style="display: {{ ($tab === 'programs' && ! $isMtmApplicationsView) ? '' : 'none' }};">
                    <div class="admin-kpi-card__icon" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div class="admin-kpi-card__body">
                        <p class="admin-kpi-card__label">Finance queue</p>
                        <p class="admin-kpi-card__value" id="program-finance-queue-count">{{ $programCounts['pending_finance'] ?? 0 }}</p>
                        <p class="admin-kpi-card__hint">Awaiting payment processing</p>
                    </div>
                </article>
                <article class="stat-card program-stats admin-kpi-card admin-kpi-card--approved" data-tab="programs" style="display: {{ $tab === 'programs' ? '' : 'none' }};">
                    <div class="admin-kpi-card__icon" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div class="admin-kpi-card__body">
                        <p class="admin-kpi-card__label">Approved (final)</p>
                        <p class="admin-kpi-card__value" id="program-approved-count">{{ $programCounts['approved'] }}</p>
                        <p class="admin-kpi-card__hint">After finance completes payment</p>
                    </div>
                </article>
                <article class="stat-card program-stats finance-stat-card admin-kpi-card admin-kpi-card--paid" data-tab="programs" style="display: {{ ($tab === 'programs' && ! $isMtmApplicationsView) ? '' : 'none' }};">
                    <div class="admin-kpi-card__icon" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div class="admin-kpi-card__body">
                        <p class="admin-kpi-card__label">Patient bills paid</p>
                        <p class="admin-kpi-card__value" id="program-paid-count">{{ $programCounts['paid'] ?? 0 }}</p>
                        <p class="admin-kpi-card__hint">Invoice recorded by finance</p>
                    </div>
                </article>
                <article class="stat-card program-stats admin-kpi-card admin-kpi-card--rejected" data-tab="programs" style="display: {{ $tab === 'programs' ? '' : 'none' }};">
                    <div class="admin-kpi-card__icon" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div class="admin-kpi-card__body">
                        <p class="admin-kpi-card__label">Rejected</p>
                        <p class="admin-kpi-card__value" id="program-rejected-count">{{ $programCounts['rejected'] }}</p>
                    </div>
                </article>
            @endunless

            @if ($isMtmApplicationsView)
                <article class="stat-card program-stats admin-kpi-card admin-kpi-card--pending" data-tab="programs" style="display: {{ $tab === 'programs' ? '' : 'none' }};">
                    <div class="admin-kpi-card__icon" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div class="admin-kpi-card__body">
                        <p class="admin-kpi-card__label">Awaiting shipment</p>
                        <p class="admin-kpi-card__value" id="program-pending-count">{{ $programCounts['pending'] + ($programCounts['approved'] ?? 0) }}</p>
                        <p class="admin-kpi-card__hint">Care package not yet marked shipped</p>
                    </div>
                </article>
            @endif

            @if ($isMtmApplicationsView || ($programSelectedType ?? 'all') === 'all')
                <article class="stat-card program-stats mtm-shipped-stat-card admin-kpi-card admin-kpi-card--shipped" data-tab="programs" style="display: {{ $tab === 'programs' ? '' : 'none' }};">
                    <div class="admin-kpi-card__icon" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                    </div>
                    <div class="admin-kpi-card__body">
                        <p class="admin-kpi-card__label">Shipped</p>
                        <p class="admin-kpi-card__value" id="program-shipped-count">{{ $programCounts['shipped'] ?? 0 }}</p>
                        <p class="admin-kpi-card__hint">Moments That Matter care packages</p>
                    </div>
                </article>
            @endif

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
        </section>

        <div id="programs-content" class="tab-content admin-apps-panel" style="display: block;">
            <div class="admin-apps-panel__card">
                <div class="admin-apps-panel__header">
                    <div>
                        <h2 class="text-lg font-semibold text-[#213430]">Application requests</h2>
                        <p class="text-sm text-[#6C5F67] mt-0.5">
                            Filter by program type and status, then open a row to review details.
                            @if ($isFinancialApplicationsView)
                                <button type="button" id="openPastGrantCyclesModal" class="text-[#9E2469] hover:underline font-normal">Past grant cycles</button>
                            @endif
                        </p>
                    </div>
                    <nav class="admin-segmented-tabs" aria-label="Application type" id="programTypeTabs">
                        <a href="{{ route('admin.registrations.index', ['program_type' => 'all', 'program_status' => $programSelectedStatus]) }}"
                            data-program-type-tab
                            data-program-type="all"
                            class="admin-segmented-tabs__item program-type-tab {{ $programSelectedType === 'all' ? 'is-active' : '' }}">
                            All
                        </a>
                        <a href="{{ route('admin.registrations.index', ['program_type' => \App\Support\ProgramType::FINANCIAL_ASSISTANCE, 'program_status' => $programSelectedStatus]) }}"
                            data-program-type-tab
                            data-program-type="{{ \App\Support\ProgramType::FINANCIAL_ASSISTANCE }}"
                            class="admin-segmented-tabs__item program-type-tab {{ $isFinancialApplicationsView ? 'is-active' : '' }}">
                            Financial Assistance
                        </a>
                        <a href="{{ route('admin.registrations.index', ['program_type' => \App\Support\ProgramType::MOMENTS_THAT_MATTER, 'program_status' => $programSelectedStatus]) }}"
                            data-program-type-tab
                            data-program-type="{{ \App\Support\ProgramType::MOMENTS_THAT_MATTER }}"
                            class="admin-segmented-tabs__item program-type-tab {{ $isMtmApplicationsView ? 'is-active' : '' }}">
                            Moments That Matter
                        </a>
                    </nav>
                </div>

                <div class="admin-apps-panel__toolbar">
                    <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                        <input type="hidden" id="programTypeFilter" value="{{ $programSelectedType }}">
                        <label class="admin-filter-field">
                            <span class="admin-filter-field__label">Status</span>
                            <div class="relative w-full sm:w-52">
                                <select name="program_status" id="programStatusFilter"
                                    class="admin-filter-field__select">
                                    <option value="pending" {{ $programSelectedStatus === 'pending' ? 'selected' : '' }}>{{ $isMtmApplicationsView ? 'Awaiting shipment' : 'Pending' }}</option>
                                    @unless ($isMtmApplicationsView)
                                        <option value="pending_finance" {{ $programSelectedStatus === 'pending_finance' ? 'selected' : '' }}>Finance queue</option>
                                        <option value="approved" {{ $programSelectedStatus === 'approved' ? 'selected' : '' }}>Approved</option>
                                    @endunless
                                    <option value="shipped" {{ $programSelectedStatus === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    @unless ($isMtmApplicationsView)
                                        <option value="rejected" {{ $programSelectedStatus === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    @endunless
                                    @unless ($isMtmApplicationsView)
                                        <option value="paid" {{ $programSelectedStatus === 'paid' ? 'selected' : '' }}>Patient bills paid</option>
                                    @endunless
                                    <option value="all" {{ $programSelectedStatus === 'all' ? 'selected' : '' }}>All</option>
                                </select>
                                <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </div>
                        </label>
                        @if ($programSelectedStatus !== 'all' || $programSelectedType !== 'all')
                            <a href="{{ route('admin.registrations.index', ['tab' => 'programs', 'program_type' => $programSelectedType, 'program_status' => 'all']) }}"
                                class="admin-btn-ghost">
                                Clear filters
                            </a>
                        @endif
                    </div>
                    <button type="button" id="exportProgramRegistrationsBtn" class="admin-btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Export CSV
                    </button>
                </div>

                <div id="programRegistrationsTableWrapper" class="admin-apps-panel__table">
                    @include('admin.registrations._table', [
                        'programRegistrations' => $programRegistrations,
                        'caseManagers' => $caseManagers,
                        'financeUsers' => $financeUsers,
                    ])
                </div>
            </div>
        </div>

        @include('admin.registrations._past_grant_cycles_modal', [
            'closedGrantCycles' => $closedGrantCycles ?? collect(),
        ])

        <div id="assignModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
            <div class="bg-white rounded-2xl w-full max-w-md shadow-xl p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 id="assignModalTitle" class="text-lg font-semibold text-gray-900">Assign Coordinator</h3>
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
                        <h3 class="text-lg font-semibold text-gray-900">Route to Finance</h3>
                        <p class="text-sm text-gray-500 mt-1">Send to the shared finance queue, or pick a specific finance user (override).</p>
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
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-[0.12em]">Finance user (optional)</label>
                        <select id="sendToFinanceUserId" name="finance_user_id"
                            class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <option value="">Shared queue — notify all finance users</option>
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
        .admin-registrations-page .tab-content,
        .admin-registrations-page .stat-card {
            animation: fadeIn 0.35s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .admin-kpi-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
        @media (min-width: 640px) {
            .admin-kpi-grid--mtm { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .admin-kpi-grid--financial { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .admin-kpi-grid--all { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (min-width: 1024px) {
            .admin-kpi-grid--financial { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .admin-kpi-grid--all { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
        @media (min-width: 1280px) {
            .admin-kpi-grid--financial { grid-template-columns: repeat(5, minmax(0, 1fr)); }
            .admin-kpi-grid--all { grid-template-columns: repeat(6, minmax(0, 1fr)); }
        }

        .admin-kpi-card {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            min-height: 7.25rem;
            padding: 1.25rem 1.35rem;
            background: #fff;
            border: 1px solid #E8DCE4;
            border-radius: 0.75rem;
            box-shadow: 0 1px 2px rgba(33, 52, 48, 0.04);
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .admin-kpi-card:hover {
            border-color: #D4B8C8;
            box-shadow: 0 4px 14px rgba(158, 36, 105, 0.08);
        }
        .admin-kpi-card__icon {
            flex-shrink: 0;
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .admin-kpi-card__body { flex: 1; min-width: 0; }
        .admin-kpi-card__label {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #6C5F67;
            line-height: 1.3;
        }
        .admin-kpi-card__value {
            margin-top: 0.35rem;
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1;
            color: #213430;
            font-variant-numeric: tabular-nums;
        }
        .admin-kpi-card__hint {
            margin-top: 0.5rem;
            font-size: 0.6875rem;
            line-height: 1.35;
            color: #91848C;
        }
        .admin-kpi-card--pending .admin-kpi-card__icon { background: #F3E8EF; color: #9E2469; }
        .admin-kpi-card--finance .admin-kpi-card__icon { background: #FEF3C7; color: #B45309; }
        .admin-kpi-card--shipped .admin-kpi-card__icon { background: #DBEAFE; color: #1D4ED8; }
        .admin-kpi-card--approved .admin-kpi-card__icon { background: #D1FAE5; color: #047857; }
        .admin-kpi-card--paid .admin-kpi-card__icon { background: #D1FAE5; color: #065F46; }
        .admin-kpi-card--rejected .admin-kpi-card__icon { background: #FEE2E2; color: #B91C1C; }

        .admin-apps-panel__card {
            background: #fff;
            border: 1px solid #E8DCE4;
            border-radius: 0.875rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(33, 52, 48, 0.06);
        }
        .admin-apps-panel__header {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            padding: 1.5rem 1.5rem 1.25rem;
            border-bottom: 1px solid #F0E6EC;
            background: linear-gradient(180deg, #FDF9FB 0%, #fff 100%);
        }
        @media (min-width: 768px) {
            .admin-apps-panel__header {
                flex-direction: row;
                align-items: flex-end;
                justify-content: space-between;
            }
        }
        .admin-segmented-tabs {
            display: inline-flex;
            flex-wrap: wrap;
            gap: 0;
            padding: 0.25rem;
            background: #F5EFF3;
            border-radius: 0.625rem;
            border: 1px solid #E6D8E1;
        }
        .admin-segmented-tabs__item {
            padding: 0.5rem 1rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #6C5F67;
            border-radius: 0.5rem;
            transition: color 0.15s, background 0.15s, box-shadow 0.15s;
            white-space: nowrap;
        }
        .admin-segmented-tabs__item:hover:not(.is-active) {
            color: #9E2469;
            background: rgba(255, 255, 255, 0.6);
        }
        .admin-segmented-tabs__item.is-active {
            background: #9E2469;
            color: #fff;
            box-shadow: 0 1px 3px rgba(158, 36, 105, 0.25);
        }
        .admin-apps-panel__toolbar {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding: 1rem 1.5rem;
            background: #FAF7FA;
            border-bottom: 1px solid #E6D8E1;
        }
        @media (min-width: 768px) {
            .admin-apps-panel__toolbar {
                flex-direction: row;
                align-items: flex-end;
                justify-content: space-between;
            }
        }
        .admin-filter-field__label {
            display: block;
            font-size: 0.6875rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #91848C;
            margin-bottom: 0.35rem;
        }
        .admin-filter-field__select {
            width: 100%;
            appearance: none;
            border-radius: 0.5rem;
            border: 1px solid #DCCFD8;
            background: #fff;
            padding: 0.5rem 2.25rem 0.5rem 0.875rem;
            font-size: 0.875rem;
            color: #213430;
        }
        .admin-filter-field__select:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(158, 36, 105, 0.35);
            border-color: #9E2469;
        }
        .admin-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.5rem 1.125rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #fff;
            background: #9E2469;
            border-radius: 0.5rem;
            border: none;
            box-shadow: 0 1px 2px rgba(158, 36, 105, 0.2);
            transition: background 0.15s;
            flex-shrink: 0;
        }
        .admin-btn-primary:hover { background: #7F1D55; }
        .admin-btn-ghost {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 0.875rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #6C5F67;
            border: 1px solid #DCCFD8;
            border-radius: 0.5rem;
            background: #fff;
        }
        .admin-btn-ghost:hover { border-color: #9E2469; color: #9E2469; }
        .admin-apps-panel__table { padding: 0 1rem 1rem; }
        .admin-apps-panel__table > .overflow-x-auto {
            border: none;
            border-radius: 0.5rem;
            box-shadow: none;
        }

        /* Program registrations pagination theme */
        #programRegistrationsTableWrapper nav[role="navigation"] {
            display: flex;
            justify-content: center;
        }

        #programRegistrationsTableWrapper nav[role="navigation"] > div:first-child {
            display: none;
        }

        #programRegistrationsTableWrapper nav[role="navigation"] > div:last-child span,
        #programRegistrationsTableWrapper nav[role="navigation"] > div:last-child a {
            border-radius: 0.5rem;
            border-color: #e9d3e1 !important;
            color: #7b5b6b !important;
        }

        #programRegistrationsTableWrapper nav[role="navigation"] > div:last-child a:hover {
            background-color: #fdf2f8 !important;
            color: #9e2469 !important;
        }

        #programRegistrationsTableWrapper nav[role="navigation"] span[aria-current="page"] span {
            background-color: #9e2469 !important;
            border-color: #9e2469 !important;
            color: #fff !important;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        const REGISTRATIONS_LIST_URL = "{{ route('admin.registrations.list') }}";
        const REGISTRATIONS_EXPORT_URL = "{{ route('admin.registrations.export') }}";

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

        function currentProgramTypeFilter() {
            return ($('#programTypeFilter').val() || 'all').toString();
        }

        function loadProgramRegistrations(params) {
            const merged = Object.assign({
                program_type: currentProgramTypeFilter(),
            }, params || {});
            const query = $.param(merged);
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

            $('#exportProgramRegistrationsBtn').on('click', function() {
                const status = $('#programStatusFilter').val() || 'all';
                const q = $.param({
                    program_status: status,
                    program_type: currentProgramTypeFilter(),
                });
                window.location.href = REGISTRATIONS_EXPORT_URL + (q ? ('?' + q) : '');
            });

            function loadProgramRegistrationsByHref(href) {
                if (!href) return;
                const parsedUrl = new URL(href, window.location.origin);
                const params = new URLSearchParams(parsedUrl.search);

                // Keep currently selected filter if pagination link does not include it.
                if (!params.has('program_status')) {
                    const currentStatus = ($('#programStatusFilter').val() || 'all').toString();
                    params.set('program_status', currentStatus);
                }
                if (!params.has('program_type')) {
                    params.set('program_type', currentProgramTypeFilter());
                }

                $('#programRegistrationsTableWrapper').addClass('opacity-60');
                $.get(REGISTRATIONS_LIST_URL + '?' + params.toString())
                    .done(function(html) {
                        $('#programRegistrationsTableWrapper').html(html).removeClass('opacity-60');
                    })
                    .fail(function() {
                        $('#programRegistrationsTableWrapper').removeClass('opacity-60');
                        alert('Failed to load applications.');
                    });
            }

            // Pagination: load via AJAX without full page reload
            $(document).on('click', '#programRegistrationsTableWrapper a', function(e) {
                const href = this.getAttribute('href') || '';
                const isPagination = href.indexOf('program_page=') !== -1;
                if (isPagination) {
                    e.preventDefault();
                    loadProgramRegistrationsByHref(href);
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
                        card.style.display = '';
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

            const pastGrantCyclesModal = document.getElementById('pastGrantCyclesModal');
            const openPastGrantCyclesModal = document.getElementById('openPastGrantCyclesModal');
            const pastGrantCyclesModalClose = document.getElementById('pastGrantCyclesModalClose');
            const pastGrantCyclesModalDone = document.getElementById('pastGrantCyclesModalDone');

            function openPastGrantCycles() {
                if (!pastGrantCyclesModal) return;
                pastGrantCyclesModal.classList.remove('hidden');
                pastGrantCyclesModal.classList.add('flex');
            }

            function closePastGrantCycles() {
                if (!pastGrantCyclesModal) return;
                pastGrantCyclesModal.classList.add('hidden');
                pastGrantCyclesModal.classList.remove('flex');
            }

            if (openPastGrantCyclesModal) {
                openPastGrantCyclesModal.addEventListener('click', openPastGrantCycles);
            }
            if (pastGrantCyclesModalClose) {
                pastGrantCyclesModalClose.addEventListener('click', closePastGrantCycles);
            }
            if (pastGrantCyclesModalDone) {
                pastGrantCyclesModalDone.addEventListener('click', closePastGrantCycles);
            }
            if (pastGrantCyclesModal) {
                pastGrantCyclesModal.addEventListener('click', function(e) {
                    if (e.target === pastGrantCyclesModal) {
                        closePastGrantCycles();
                    }
                });
            }
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && pastGrantCyclesModal && !pastGrantCyclesModal.classList.contains('hidden')) {
                    closePastGrantCycles();
                }
            });

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
                    const url = assignTrigger.getAttribute('data-assign-path');
                    const currentId = assignTrigger.getAttribute('data-assign-current');
                    const assignName = assignTrigger.getAttribute('data-assign-name') || '';
                    closeActionMenus();
                    if (url && assignModal && assignModalForm) {
                        assignModalForm.setAttribute('data-assign-path', url);
                        assignModalForm.action = url;
                        const select = assignModalForm.querySelector('select[name="case_manager_id"]');
                        if (select) select.value = currentId || '';
                        const titleEl = document.getElementById('assignModalTitle');
                        const subtitleEl = document.getElementById('assignModalSubtitle');
                        if (assignName) {
                            if (titleEl) titleEl.textContent = 'Change Coordinator';
                            if (subtitleEl) subtitleEl.textContent = 'Currently assigned to: ' + assignName;
                        } else {
                            if (titleEl) titleEl.textContent = 'Assign Coordinator';
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

            let assignInFlight = false;

            if (assignModalForm) {
                assignModalForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (assignInFlight) {
                        return;
                    }
                    const path = assignModalForm.getAttribute('data-assign-path') || assignModalForm.action || '';
                    if (!path || !String(path).includes('/assign')) {
                        showToast('Assignment URL is missing. Please close the modal and try again.', 'error');
                        return;
                    }
                    assignInFlight = true;
                    const submitBtn = assignModalForm.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.textContent = 'Assigning...';
                    }
                    const formData = new FormData(assignModalForm);
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                        || document.querySelector('input[name="_token"]')?.value;
                    let assignUrl;
                    if (path.startsWith('http://') || path.startsWith('https://')) {
                        try {
                            const parsed = new URL(path);
                            assignUrl = window.location.origin + parsed.pathname + parsed.search;
                        } catch {
                            assignUrl = path;
                        }
                    } else {
                        assignUrl = window.location.origin + (path.startsWith('/') ? path : '/' + path);
                    }
                    fetch(assignUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        redirect: 'manual',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData,
                    })
                        .then(async (response) => {
                            const data = await response.json().catch(() => ({}));
                            if (response.status >= 200 && response.status < 300) {
                                if (data.success === false) {
                                    throw new Error(data.message || 'Failed to assign case manager.');
                                }
                                return data;
                            }
                            if (response.status >= 300 && response.status < 400) {
                                return {
                                    success: true,
                                    message: data.message || 'Case manager assignment updated.',
                                };
                            }
                            throw new Error(data.message || 'Failed to assign case manager.');
                        })
                        .then((data) => {
                            closeAssignModal();
                            showToast(data.message || 'Case manager assigned successfully!', 'success');
                            setTimeout(function() {
                                window.location.reload();
                            }, 800);
                        })
                        .catch((error) => {
                            if (error?.name === 'AbortError') {
                                return;
                            }
                            showToast(error.message || 'An error occurred.', 'error');
                        })
                        .finally(() => {
                            assignInFlight = false;
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.textContent = 'Confirm & Assign';
                            }
                        });
                });
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
                    if (!regId) return;
                    const submitBtn = document.getElementById('sendToFinanceSubmit');
                    if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Sending...'; }
                    const formData = new FormData();
                    if (financeUserId) {
                        formData.append('finance_user_id', financeUserId);
                    }
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
