@php
    use Illuminate\Support\Facades\Auth;
    use Carbon\Carbon;
    use App\Models\Application;
    use Illuminate\Contracts\Pagination\LengthAwarePaginator;
    use App\Models\UserProfile;

    $user = Auth::user();

    // --- Filters ---
    $viewMode = request('view') === 'assigned' ? 'assigned' : 'all';
    $range = request('range');
    $statusOptions = [
        'pending' => 'Pending',
        'under_review' => 'Under Review',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];
    $selectedStatus = strtolower((string) request('status'));
    $statusFilter = array_key_exists($selectedStatus, $statusOptions) ? $selectedStatus : null;

    $startDate = match ($range) {
        'week' => Carbon::now()->subWeek(),
        'month' => Carbon::now()->subMonth(),
        default => null,
    };

    // --- Base query ---
    $apps = Application::query()
        ->with([
            'program:id,title',
            'patient:id,user_id',
            'patient.user:id,email',
            'reviewer.profile',
            'missingRequests',
        ])
        ->when($viewMode === 'assigned', fn($q) => $q->whereNotNull('reviewer_id'))
        ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
        ->when($statusFilter, fn($q) => $q->where('status', $statusFilter))
        ->latest('created_at')
        ->paginate(10)
        ->appends(request()->query());

    // $apps = Application::query()
    //     ->with([
    //         'program:id,title',
    //         'patient:id,user_id',
    //         'patient.user:id,email',
    //         'reviewer.profile', // add this to prevent N+1 in Blade
    //         'missingRequests',
    //     ])
    //     ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
    //     // portable: NULLs first, then by newest
    //     ->orderByRaw('CASE WHEN reviewer_id IS NULL THEN 0 ELSE 1 END')
    //     ->orderByDesc('created_at')
    //     ->paginate(10)
    //     ->appends(request()->query());

    // --- Helpers ---
    $fmtDate = fn($dt) => $dt ? Carbon::parse($dt)->format('Y-m-d') : '—';
    $appCode = fn($app) => $app->code ?: 'APP-' . str_pad((string) $app->id, 6, '0', STR_PAD_LEFT);

    $statusBadge = function ($status) {
        $map = [
            'approved' => ['bg' => '#C5E8D1', 'text' => '#20B354', 'label' => 'Approved'],
            'rejected' => ['bg' => '#E8C5C5', 'text' => '#B32020', 'label' => 'Rejected'],
            'under_review' => ['bg' => '#E4D7DF', 'text' => '#91848C', 'label' => 'Under Review'],
            'pending' => ['bg' => '#E4D7DF', 'text' => '#91848C', 'label' => 'Pending'],
        ];

        $key = str_replace(' ', '_', strtolower((string) $status));
        $cfg = $map[$key] ?? $map['pending'];

        return sprintf(
            '<span class="px-4 py-2 rounded-sm text-xs font-medium app-text" style="background:%s;color:%s">%s</span>',
            e($cfg['bg']),
            e($cfg['text']),
            e($cfg['label']),
        );
    };

    $caseManagerRoleId = App\Models\Role::where('name', 'casemanager')->value('id');

    $caseManagers = App\Models\User::with('profile')
        ->where('role_id', $caseManagerRoleId)
        ->whereHas('profile', function ($query) {
            $query->where('status', 1); // Ensure that the profile's status is 1
        })
        ->get();
@endphp

@extends('admin.layouts.admin')

@section('title', 'Applications')

<style>
    /* Escaped version of .bg-[#db69a2] */
    button.bg-\[\#db69a2\] {
        color: #fff !important;
    }
</style>


@section('content')
    <div class="flex-1 flex flex-col">
        <main class="flex-1">
            <div class="max-w-8xl mx-auto">
                @include('admin.partials.cards')

                <div class="mt-6 bg-[#F3E8EF] rounded-lg p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4 bg-white border border-[#e5d7df] shadow-sm rounded-xl p-4">
                        <div class="space-y-2">
                            <div class="flex flex-wrap items-center gap-3">
                                <h2 id="applicationsHeading" class="text-xl font-semibold text-[#213430] app-main">
                                    {{ $viewMode === 'assigned' ? 'Assigned Applications' : 'Applications' }}
                                </h2>
                                <div class="relative">
                                    <select name="view" id="viewFilter"
                                        class="appearance-none rounded-md px-4 py-2 pr-12 text-sm text-[#213430] bg-white border border-[#DCCFD8] shadow-sm focus:outline-none focus:ring-2 focus:ring-[#DB69A2] transition">
                                        <option value="all" {{ $viewMode === 'all' ? 'selected' : '' }}>All Applications</option>
                                        <option value="assigned" {{ $viewMode === 'assigned' ? 'selected' : '' }}>Assigned Applications</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[#91848C]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-[#5F4E57]">
                                Toggle between everything received and only the cases already assigned to reviewers.
                            </p>
                        </div>

                        <form id="applicationsFilters" method="GET"
                            class="flex flex-col w-full lg:w-auto gap-3 lg:gap-2 lg:flex-row lg:items-center">
                            <input type="hidden" name="view" id="viewFilterHidden" value="{{ $viewMode }}">
                            <div class="flex flex-col sm:flex-row gap-3 lg:gap-2 lg:items-center">
                                <div class="relative w-full sm:w-[160px] lg:w-[180px]">
                                    <select name="range" id="rangeFilter"
                                        class="w-full appearance-none rounded-md px-3 py-2 pr-10 text-sm text-[#213430] bg-white border border-[#DCCFD8] focus:outline-none focus:ring-1 focus:ring-[#DB69A2]">
                                        <option value="">All Time</option>
                                        <option value="week" {{ $range === 'week' ? 'selected' : '' }}>Last Week</option>
                                        <option value="month" {{ $range === 'month' ? 'selected' : '' }}>Last Month</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[#91848C]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="relative w-full sm:w-[180px] lg:w-[200px]">
                                    <select name="status" id="statusFilter"
                                        class="w-full appearance-none rounded-md px-3 py-2 pr-10 text-sm text-[#213430] bg-white border border-[#DCCFD8] focus:outline-none focus:ring-1 focus:ring-[#DB69A2]">
                                        <option value="">All Statuses</option>
                                        @foreach ($statusOptions as $value => $label)
                                            <option value="{{ $value }}" {{ $selectedStatus === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[#91848C]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="relative w-full sm:w-[240px] lg:w-[260px]">
                                    <input type="text" name="q" id="searchInput" value="{{ request('q') }}"
                                        placeholder="Search by name, email, code, ID"
                                        class="w-full rounded-md px-3 py-2 text-sm text-[#213430] bg-[#F8F4F7] border border-[#DCCFD8] focus:outline-none focus:ring-1 focus:ring-[#DB69A2]"
                                        autocomplete="off" />
                                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[#91848C]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 lg:gap-2">
                                <button type="button" id="exportApplicationsBtn"
                                    class="px-4 py-2 bg-[#DB69A2] text-white rounded-md app-text shadow-sm hover:bg-[#c95791] focus:ring-2 focus:ring-offset-1 focus:ring-[#DB69A2] transition">
                                    Export Excel
                                </button>
                                @if (request()->filled('range') || request()->filled('q') || request()->filled('status') || $viewMode === 'assigned')
                                    <a href="{{ route('admin.applications') }}"
                                        class="px-3 py-2 border border-[#DCCFD8] text-[#213430] rounded-md app-text hover:border-[#DB69A2] hover:text-[#DB69A2] transition">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    {{-- AJAX Table Wrapper --}}
                    <div id="applicationsTableWrapper">
                        @include('admin.applications._table', ['apps' => $apps, 'range' => $range])
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- Reject Modal --}}
    <div id="rejectModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
        <div class="bg-[#F9EEF6] rounded-lg shadow-lg p-6 w-full max-w-[20rem] md:max-w-md mx-auto text-left">
            <h2 class="text-lg font-semibold text-[#1F2937] mb-4 text-center">Delete Application</h2>
            <p class="text-md text-center text-black mb-4">
                Are you sure you want to delete this application?
            </p>
            <div class="flex justify-center gap-3 pt-2">
                <button id="deleteConfirmBtn"
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

    {{-- Assign Reviewer Modal --}}
    <div id="assignModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-[#F9EEF6] rounded-xl shadow-lg w-full max-w-[20rem] md:max-w-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Assign to:</h2>
            <input id="reviewerSearch" type="text" placeholder="Enter Name To Find The Reviewer"
                class="w-full mb-4 px-4 py-2 rounded-md border border-[#E4D2DB] bg-transparent text-sm placeholder-[#B1A4AD] focus:outline-none focus:ring-2 focus:ring-[#DB69A2]" />
            <div id="caseManagersList" class="space-y-3 max-h-60 overflow-y-auto pr-2">
                @foreach ($caseManagers as $manager)
                    <div class="flex items-center justify-between manager-row" data-user-id="{{ $manager->id }}">
                        <div class="flex items-center space-x-3">
                            @if (!empty($manager->profile?->avatar))
                                <img src="{{ $manager ? $manager->avatar_url : asset('public/images/profile.png') }}"
                                    alt="Reviewer" class="w-10 h-10 rounded-full" />
                            @else
                                <img src="{{ asset('public/images/profile.png') }}" alt="Reviewer"
                                    class="w-10 h-10 rounded-full" />
                            @endif
                            <span class="text-sm text-gray-800">{{ $manager->profile->full_name ?? 'Unknown' }}</span>
                        </div>
                        <button
                            class="selectReviewer text-sm border border-[#DCCFD8] text-[#91848C] text-white px-4 py-1.5 rounded-md hover:bg-[#db69a2] hover:text-white"
                            data-user-id="{{ $manager->id }}">
                            Select
                        </button>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between mt-6">
                <button id="confirmAssignBtn"
                    class="bg-[#DB69A2] hover:bg-[#FE6EB6] text-white font-semibold text-sm px-3 py-3 rounded-md transition"
                    disabled>
                    CONFIRM & ASSIGN
                </button>
                <button type="button" onclick="closeAssignModal()"
                    class="border border-[#D6C6CE] text-[#8B7E88] font-semibold text-sm px-6 py-3 rounded-md hover:bg-[#DCCFD8] transition">
                    CANCEL
                </button>
            </div>
        </div>
    </div>

    <!-- Toasts Container -->
    <div id="toastContainer"
        style="position:fixed; top:30px; right:30px; z-index:99999; display:flex; flex-direction:column; gap:12px;">
    </div>


    @if (session('success'))
        <script>
            $(function() {
                showToast(@json(session('success')), 'success');
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            $(function() {
                showToast(@json(session('error')), 'error');
            });
        </script>
    @endif


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        /* =========================
                           Utilities
                        ========================= */
        const CSRF_TOKEN = '{{ csrf_token() }}';
        const LIST_URL = "{{ route('admin.applications.list') }}";
        const APPLICATIONS_BASE_URL = "{{ url('admin/applications') }}";
        const EXPORT_URL = "{{ route('admin.applications.export') }}";

        function showToast(message, type = 'success') {
            const icon = type === 'success' ?
                `<svg width="22" height="22" fill="none"><circle cx="11" cy="11" r="11" fill="#20b354"/><path d="M7 12l3 3 5-6" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>` :
                `<svg width="22" height="22" fill="none"><circle cx="11" cy="11" r="11" fill="#d8000c"/><path d="M7 7l8 8M15 7l-8 8" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg>`;

            const $toast = $(`
    <div class="toast-msg toast-${type}">
      <span>${icon}</span>
      <div style="flex:1">${message}</div>
      <button class="toast-close" aria-label="Close">&times;</button>
    </div>
  `);

            $('#toastContainer').append($toast);

            // Close handlers
            $toast.find('.toast-close').on('click', () => {
                $toast.fadeOut(300, () => $toast.remove());
            });

            setTimeout(() => {
                $toast.fadeOut(400, () => $toast.remove());
            }, 3500);
        }

        /* =========================
           Delete Application Modal
        ========================= */
        let currentDeleteApplicationId = null;

        function openDeleteModal(appId) {
            currentDeleteApplicationId = appId;
            $('#rejectModal').removeClass('hidden');
        }

        function closeDeleteModal() {
            currentDeleteApplicationId = null;
            $('#rejectModal').addClass('hidden');
        }

        // Expose for inline onclick in blade (if used)
        window.openRejectModal = openDeleteModal;
        window.closeRejectModal = closeDeleteModal;

        // Confirm delete (single binding)
        $('#deleteConfirmBtn').on('click', function() {
            if (!currentDeleteApplicationId) return;

            const $btn = $(this).prop('disabled', true).text('Deleting...');
            $.ajax({
                url: `${APPLICATIONS_BASE_URL}/${currentDeleteApplicationId}`,
                type: 'DELETE',
                data: {
                    _token: CSRF_TOKEN
                },
                success: () => {
                    // Let server flash message show after reload
                    location.reload();
                },
                error: (xhr) => {
                    closeDeleteModal();
                    showToast('Failed to delete application: ' + (xhr.responseJSON?.message ||
                        'Unknown error'), 'error');
                    $btn.prop('disabled', false).text('Yes');
                },
            });
        });

        /* =========================
           Assign Reviewer Modal
        ========================= */
        let currentApplicationId = null;
        let assignedReviewerId = null;
        let selectedReviewerId = null;

        function openAssignModal(applicationId, reviewerId) {
            currentApplicationId = applicationId;
            assignedReviewerId = reviewerId ?? null;
            selectedReviewerId = null;

            // Reset search and show all rows
            $('#reviewerSearch').val('');
            $('.manager-row').show();

            // Update buttons
            $('.selectReviewer').each(function() {
                const userId = $(this).data('user-id');
                if (userId == assignedReviewerId) {
                    $(this)
                        .addClass('bg-[#db69a2] text-white font-bold cursor-not-allowed')
                        .text('Assigned')
                        .prop('disabled', true);
                } else {
                    $(this)
                        .removeClass('bg-[#db69a2] text-white font-bold cursor-not-allowed')
                        .text('Select')
                        .prop('disabled', false);
                }
            });

            $('#confirmAssignBtn').prop('disabled', true);
            $('#assignModal').removeClass('hidden');
        }

        function closeAssignModal() {
            $('#assignModal').addClass('hidden');
        }

        // Expose for inline onclick in blade
        window.openAssignModal = openAssignModal;
        window.closeAssignModal = closeAssignModal;

        // Live search reviewers
        $('#reviewerSearch').on('input', function() {
            const value = $(this).val().toLowerCase();
            $('.manager-row').each(function() {
                const name = $(this).find('span').text().toLowerCase();
                $(this).toggle(name.includes(value));
            });
        });

        // Select reviewer (delegated)
        $(document).on('click', '.selectReviewer', function() {
            if ($(this).prop('disabled')) return;

            $('.selectReviewer')
                .removeClass('bg-[#db69a2] text-white font-bold cursor-not-allowed')
                .text('Select');

            $(this)
                .addClass('bg-[#db69a2] text-white font-bold cursor-not-allowed')
                .text('Selected');

            selectedReviewerId = $(this).data('user-id');
            $('#confirmAssignBtn').prop('disabled', false);
        });

        // Confirm & assign (single binding)
        $('#confirmAssignBtn').on('click', function(e) {
            e.preventDefault();
            if (!selectedReviewerId) return; // guard
            const $btn = $(this).prop('disabled', true).text('Assigning...');
            $.ajax({
                url: `${APPLICATIONS_BASE_URL}/${currentApplicationId}/assign-reviewer`,
                type: 'POST',
                data: {
                    reviewer_id: selectedReviewerId,
                    _token: CSRF_TOKEN
                },
                success: (resp) => {
                    closeAssignModal();
                    showToast(resp?.message || 'Reviewer assigned successfully!', 'success');
                    setTimeout(() => location.reload(), 800);
                },
                error: (xhr) => {
                    const msg = xhr?.responseJSON?.message || 'Failed to assign reviewer.';
                    showToast(msg, 'error');
                    $btn.prop('disabled', false).text('CONFIRM & ASSIGN');
                },
            });
        });


        /* =========================
           Action Dropdowns
        ========================= */
        function toggleDropdown(btn) {
            const dropdown = btn.nextElementSibling;
            dropdown.classList.toggle('hidden');

            // Hide others
            document.querySelectorAll('td .absolute').forEach((el) => {
                if (el !== dropdown) el.classList.add('hidden');
            });
        }
        window.toggleDropdown = toggleDropdown;

        // Close dropdowns on outside click / ESC
        document.addEventListener('click', (e) => {
            const isTrigger = e.target.closest('button[onclick^="toggleDropdown"]');
            const isMenu = e.target.closest('td .absolute');
            if (!isTrigger && !isMenu) {
                document.querySelectorAll('td .absolute').forEach((el) => el.classList.add('hidden'));
            }
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                // Close modals
                closeAssignModal();
                closeDeleteModal();
                // Close dropdowns
                document.querySelectorAll('td .absolute').forEach((el) => el.classList.add('hidden'));
            }
        });

        /* =========================
           AJAX Search + Filters
        ========================= */
        function debounce(fn, wait) {
            let t;
            return function(...args) {
                clearTimeout(t);
                t = setTimeout(() => fn.apply(this, args), wait);
            };
        }

        function loadApplications(params) {
            const query = $.param(params);
            $('#applicationsTableWrapper').addClass('opacity-60');
            return $.get(LIST_URL + (query ? ('?' + query) : ''), function(html) {
                $('#applicationsTableWrapper').html(html).removeClass('opacity-60');
            }).fail(function(xhr) {
                $('#applicationsTableWrapper').removeClass('opacity-60');
                showToast('Failed to load applications: ' + (xhr.responseJSON?.message || 'Unknown error'),
                    'error');
            });
        }

        function getFilterParams(extra = {}) {
            return Object.assign({
                q: $('#searchInput').val(),
                view: $('#viewFilter').val(),
                range: $('#rangeFilter').val(),
                status: $('#statusFilter').val(),
            }, extra);
        }

        // Handle typing in search
        const handleSearch = debounce(function() {
            loadApplications(getFilterParams());
        }, 300);

        $('#searchInput').on('input', handleSearch);

        // Switch between all vs assigned via dropdown
        $('#viewFilter').on('change', function() {
            const view = $(this).val();
            $('#viewFilterHidden').val(view);
            $('#applicationsHeading').text(view === 'assigned' ? 'Assigned Applications' : 'Applications');
            loadApplications(getFilterParams({
                view,
                page: 1
            }));
        });

        // Change range/status filters immediately
        $('#rangeFilter, #statusFilter').on('change', function() {
            loadApplications(getFilterParams());
        });

        // Handle filter submit via Enter key (AJAX)
        $('#applicationsFilters').on('submit', function(e) {
            e.preventDefault();
            loadApplications(getFilterParams());
        });

        $('#exportApplicationsBtn').on('click', function() {
            const params = getFilterParams();
            const query = $.param(params);
            window.location.href = EXPORT_URL + (query ? ('?' + query) : '');
        });

        // Intercept pagination links inside the wrapper
        $(document).on('click', '#applicationsTableWrapper a', function(e) {
            const href = $(this).attr('href');
            if (!href) return;
            const isPagination = /[?&]page=/.test(href) || $(this).closest('.flex.justify-end').length > 0;
            if (isPagination) {
                e.preventDefault();
                // Preserve current filters while paginating
                const url = new URL(href, window.location.origin);
                const params = getFilterParams({
                    page: url.searchParams.get('page') || 1
                });
                loadApplications(params);
            }
        });
    </script>

@endsection
