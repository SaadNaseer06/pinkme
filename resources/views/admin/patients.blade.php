@php
    use Illuminate\Support\Facades\Auth;
    use Carbon\Carbon;
    use Illuminate\Contracts\Pagination\LengthAwarePaginator;
    use App\Models\Patient; // ensure model exists
    // expects relations: Patient->user() and User->profile()

    $user = Auth::user();
    $range = request('range');
    $q = trim((string) request('q', ''));

    $startDate = match ($range) {
        'week' => Carbon::now()->subWeek(),
        'month' => Carbon::now()->subMonth(),
        default => null,
    };

    // Base query with eager loads for performance
    $patients = Patient::query()
        ->with(['user:id,email', 'user.profile:id,user_id,full_name,phone,avatar,status'])
        ->when($startDate, fn($qb) => $qb->where('created_at', '>=', $startDate))
        ->when($q !== '', function ($qb) use ($q) {
            $qb->where(function ($w) use ($q) {
                $w->whereHas('user.profile', fn($qq) => $qq->where('full_name', 'like', "%{$q}%"))
                    ->orWhereHas('user', fn($qq) => $qq->where('email', 'like', "%{$q}%"))
                    ->orWhere('disease_type', 'like', "%{$q}%")
                    ->orWhere('genetic_test', 'like', "%{$q}%");
                if (ctype_digit($q)) {
                    $w->orWhere('id', (int) $q);
                }
            });
        })
        ->latest('created_at')
        ->paginate(10)
        ->appends(request()->query());
@endphp

@extends('admin.layouts.admin')

@section('title', 'Patients')

@section('content')
    <div class="flex-1 flex flex-col">
        <main class="flex-1">
            <div class="max-w-8xl mx-auto">
                @include('admin.partials.cards')
                <div class="mt-6 bg-[#F3E8EF] rounded-lg p-6">
                    {{-- Header + Filters --}}
                    <div class="flex justify-between flex-col md:flex-row items-center mb-4 ml-3">
                        <h2 class="text-xl font-semibold text-[#213430] app-main mb-2 md:mb-0">Patients</h2>

                        {{-- ROUTE: adjust if your route name differs --}}
                        <form id="patientsFiltersForm" method="GET" action="{{ route('admin.patients') }}"
                            class="flex space-x-3 items-center">
                            <div class="relative w-[140px] md:w-[200px]">
                                <select id="patientsRangeSelect" name="range"
                                    class="w-full appearance-none rounded-md px-3 py-2 pr-10 text-sm text-[#91848C] bg-transparent border border-[#91848C] focus:outline-none">
                                    <option value="">All Time</option>
                                    <option value="week" {{ $range === 'week' ? 'selected' : '' }}>Last Week</option>
                                    <option value="month" {{ $range === 'month' ? 'selected' : '' }}>Last Month</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[#91848C]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>

                            <div class="relative w-[200px] md:w-[260px]">
                                <input id="patientsSearchInput" name="q" type="text" value="{{ $q }}"
                                    placeholder="Search name, email, disease, test or ID"
                                    class="w-full px-3 py-2 rounded-md border border-[#91848C] bg-transparent text-sm placeholder-[#B1A4AD] focus:outline-none">
                                <span class="absolute right-3 top-2.5 text-[#91848C]">
                                    <i class="fa fa-search"></i>
                                </span>
                            </div>

                            <button type="submit"
                                class="px-4 py-2 bg-[#DB69A2] text-white rounded-md app-text hover:bg-pink-600">
                                Apply
                            </button>

                            @if (request()->filled('range') || request()->filled('q'))
                                {{-- ROUTE: adjust if your route name differs --}}
                                <a id="patientsResetFilters" href="{{ route('admin.patients') }}"
                                    class="px-3 py-2 border border-[#DCCFD8] text-[#91848C] rounded-md app-text">Reset</a>
                            @endif
                        </form>
                    </div>

                    {{-- Table + Pagination (AJAX swapped only here) --}}
                    <div id="patientsContainer" class="table-container min-h-[120px] relative">
                        <div id="patientsLoading" class="hidden absolute inset-0 flex items-center justify-center">
                            <div class="animate-pulse text-[#91848C]">Loading…</div>
                        </div>

                        <div id="patientsTableWrap">
                            <table class="min-w-full text-sm text-left mt-6">
                                <thead>
                                    <tr class="border-t border-[#e0cfd8]">
                                        {{-- <th class="p-2">
                                            <input id="patientsSelectAll" type="checkbox"
                                                class="accent-[#DB69A2] w-4 h-4 border border-[#91848C] rounded appearance-none checked:appearance-auto focus:ring-0" />
                                        </th> --}}
                                        <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">Patient Name
                                        </th>
                                        <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h pad-left">
                                            Patient ID</th>
                                        <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">Email</th>
                                        <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">Contact</th>
                                        <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">Age</th>
                                        <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">Disease</th>
                                        <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">Status</th>
                                        <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    @forelse ($patients as $p)
                                        @php
                                            $profile = $p->user?->profile;
                                            $name = $profile->full_name ?? 'Unknown';
                                            $email = $p->user?->email ?? '—';
                                            $phone = $profile->phone ?? '—';
                                            // age may be stored on patient or profile
                                            $age = $p->age ?? ($profile->age ?? '—');
                                            $disease = $p->disease_type ?? '—';

                                            // Status: prefer patient.status, fallback to profile.status
                                            $rawStatus = $p->status ?? ($profile->status ?? null);
                                            $isActive = is_null($rawStatus)
                                                ? true
                                                : (string) $rawStatus === '1' ||
                                                    strtolower((string) $rawStatus) === 'active';
                                            $statusLbl = $isActive ? 'Active' : 'Inactive';
                                            $statusDot = $isActive ? '#20B354' : '#B32020';

                                            $pid = 'P-' . str_pad((string) $p->id, 4, '0', STR_PAD_LEFT);

                                            $avatarPath = !empty($profile?->avatar)
                                                ? asset('storage/' . $profile->avatar)
                                                : asset('images/profile.png');
                                        @endphp

                                        <tr class="border-t border-[#e0cfd8] {{ $loop->last ? 'border-b' : '' }}">
                                            {{-- <td class="p-2">
                                                <input type="checkbox" name="row_select[]" value="{{ $p->id }}"
                                                    class="patientsRowCheck accent-[#DB69A2] w-4 h-4 border border-[#91848C] rounded appearance-none checked:appearance-auto focus:ring-0" />
                                            </td> --}}

                                            <td class="p-2">
                                                <div class="flex items-center gap-3">
                                                    <img src="{{ $avatarPath }}" alt="Patient"
                                                        class="w-8 h-8 rounded-full" />
                                                    <span
                                                        class="text-[#91848C] text-[16px] font-light app-text">{{ $name }}</span>
                                                </div>
                                            </td>

                                            <td
                                                class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text pad-left">
                                                {{ $pid }}
                                            </td>
                                            <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                                {{ $email }}</td>
                                            <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                                {{ $phone }}</td>
                                            <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                                {{ $age }}</td>
                                            <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                                {{ $disease }}</td>

                                            <td class="p-2 align-middle">
                                                <span class="inline-flex items-center gap-1 text-[#8E7C93] text-sm">
                                                    <span class="w-2 h-2 rounded-full"
                                                        style="background: {{ $statusDot }}"></span>
                                                    {{ $statusLbl }}
                                                </span>
                                            </td>

                                            <td class="p-2 relative">
                                                <button onclick="togglePatientsDropdown(this)"
                                                    class="text-[#213430] p-2 rounded-md focus:outline-none">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                    </svg>
                                                </button>
                                                <div
                                                    class="absolute right-[28px] top-10 w-[250px] max-w-none bg-[#F6EDF5] rounded-lg shadow-lg py-2 z-20 hidden">
                                                    {{-- ROUTES: adjust if your route names differ --}}
                                                    <a href="#"
                                                        class="flex items-center px-4 py-2 text-[#91848C] hover:bg-pink-100 text-sm">
                                                        <i class="fas fa-eye mr-2"></i> View Profile
                                                    </a>
                                                    <a href="#"
                                                        class="flex items-center px-4 py-2 text-[#91848C] hover:bg-pink-100 text-sm gap-2">
                                                        <i class="fa-solid fa-pen"></i> Edit Patients Details
                                                    </a>
                                                    <a href="{{ route('case_manager.myApplication', ['patient_id' => $p->id]) }}"
                                                        class="flex items-center px-4 py-2 text-[#91848C] hover:bg-pink-100 text-sm gap-2">
                                                        <img src="/images/assign.svg" alt=""> Applications
                                                    </a>
                                                    {{-- Optional delete: wire if needed
                                                <a href="#" onclick="openPatientDeleteModal({{ $p->id }})"
                                                   class="flex items-center px-4 py-2 gap-2 text-[#91848C] text-sm transition-colors">
                                                    <i class="fa-solid fa-trash"></i> Delete Patient
                                                </a> --}}
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="border-t border-[#e0cfd8]">
                                            <td colspan="9" class="p-6 text-center text-[#91848C] app-text">
                                                No patients found{{ $range ? ' in the selected range' : '' }}.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            {{-- Pagination --}}
                            @if ($patients instanceof LengthAwarePaginator && $patients->hasPages())
                                @php
                                    $current = $patients->currentPage();
                                    $last = $patients->lastPage();
                                    $start = max(1, $current - 1);
                                    $end = min($last, $start + 2);
                                    $start = max(1, $end - 2);
                                    $pages = range($start, $end);
                                @endphp
                                <div class="mt-6 flex justify-end space-x-1" data-pagination="true">
                                    @if ($patients->onFirstPage())
                                        <span
                                            class="px-3 py-1 rounded-md border border-[#B9B1B6] text-[#91848C] opacity-60 cursor-not-allowed">&lt;</span>
                                    @else
                                        <a href="{{ $patients->previousPageUrl() }}"
                                            class="px-3 py-1 rounded-md border border-[#B9B1B6] text-[#91848C] ajax-patients-page">&lt;</a>
                                    @endif

                                    @foreach ($pages as $page)
                                        @if ($page == $current)
                                            <span
                                                class="px-4 py-1 rounded-md bg-[#DB69A2] text-white">{{ $page }}</span>
                                        @else
                                            <a href="{{ $patients->url($page) }}"
                                                class="px-3 py-1 rounded-md border border-[#B9B1B6] text-[#91848C] ajax-patients-page">{{ $page }}</a>
                                        @endif
                                    @endforeach

                                    @if ($current < $last)
                                        <a href="{{ $patients->nextPageUrl() }}"
                                            class="px-3 py-1 rounded-md border border-[#B9B1B6] text-[#91848C] ajax-patients-page">&gt;</a>
                                    @else
                                        <span
                                            class="px-3 py-1 rounded-md border border-[#B9B1B6] text-[#91848C] opacity-60 cursor-not-allowed">&gt;</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- Toasts Container --}}
    <div id="toastContainer"
        style="position:fixed; top:30px; right:30px; z-index:99999; display:flex; flex-direction:column; gap:12px;"></div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        /* =========================
                   Globals
           ========================= */
        const CSRF_TOKEN = '{{ csrf_token() }}';
        let currentPatientsAjax = null;

        /* =========================
                   Helpers
           ========================= */
        function debounce(fn, wait = 300) {
            let t;
            return (...args) => {
                clearTimeout(t);
                t = setTimeout(() => fn.apply(this, args), wait);
            };
        }

        function showToast(message, type = 'success') {
            const icon = type === 'success' ?
                `<svg width="22" height="22" fill="none"><circle cx="11" cy="11" r="11" fill="#20b354"/><path d="M7 12l3 3 5-6" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>` :
                `<svg width="22" height="22" fill="none"><circle cx="11" cy="11" r="11" fill="#d8000c"/><path d="M7 7l8 8M15 7l-8 8" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg>`;
            const $toast = $(
                    `<div class="toast-msg"><span>${icon}</span><div style="flex:1">${message}</div><button class="toast-close" aria-label="Close">&times;</button></div>`
                )
                .css({
                    background: '#fff',
                    border: '1px solid #e2d9df',
                    padding: '10px 12px',
                    borderRadius: '10px',
                    boxShadow: '0 8px 20px rgba(0,0,0,.06)',
                    minWidth: '260px',
                    display: 'flex',
                    gap: '10px',
                    alignItems: 'center'
                });
            $('#toastContainer').append($toast);
            $toast.find('.toast-close').on('click', () => $toast.fadeOut(300, () => $toast.remove()));
            setTimeout(() => $toast.fadeOut(400, () => $toast.remove()), 3500);
        }

        function patientsQueryFromInputs() {
            return new URLSearchParams($('#patientsFiltersForm').serialize()).toString();
        }

        function patientsUpdateUrl(query) {
            const newUrl = query ? (`?${query}`) : location.pathname;
            window.history.replaceState({}, '', newUrl);
        }

        function patientsExtractAndSwap(html) {
            const $html = $('<div>').html(html);
            const $wrap = $html.find('#patientsTableWrap');
            if ($wrap.length) $('#patientsTableWrap').replaceWith($wrap);
            else $('#patientsContainer').html(html);
        }

        function patientsSetLoading(isLoading) {
            $('#patientsLoading').toggleClass('hidden', !isLoading);
        }

        /* =========================
                Core Loader
           ========================= */
        function loadPatients(url = null) {
            patientsSetLoading(true);

            if (currentPatientsAjax && currentPatientsAjax.readyState !== 4) {
                try {
                    currentPatientsAjax.abort();
                } catch (_) {}
            }

            const baseUrl = "{{ route('admin.patients') }}"; // adjust if needed
            const finalUrl = url ? url : (function() {
                const q = patientsQueryFromInputs();
                return q ? `${baseUrl}?${q}` : baseUrl;
            })();

            currentPatientsAjax = $.ajax({
                url: finalUrl,
                type: 'GET',
                dataType: 'html',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            return currentPatientsAjax
                .done((html) => {
                    patientsExtractAndSwap(html);
                    const query = (new URL(finalUrl, window.location.href)).searchParams.toString();
                    patientsUpdateUrl(query);
                })
                .fail((xhr, status) => {
                    if (status === 'abort') return;
                    const msg = xhr?.responseJSON?.message || 'Failed to load patients.';
                    $('#patientsContainer').append(`<div class="p-4 text-center text-[#91848C]">${msg}</div>`);
                })
                .always(() => patientsSetLoading(false));
        }

        /* =========================
             Initialize + Events
           ========================= */
        $(function() {
            // Intercept form submit
            $('#patientsFiltersForm').on('submit', function(e) {
                e.preventDefault();
                loadPatients();
            });

            // Range change
            $('#patientsRangeSelect').on('change', function() {
                loadPatients();
            });

            // Live search
            $('#patientsSearchInput').on('input', debounce(function() {
                loadPatients();
            }, 350));

            // Reset
            $(document).on('click', '#patientsResetFilters', function(e) {
                e.preventDefault();
                $('#patientsRangeSelect').val('');
                $('#patientsSearchInput').val('');
                loadPatients($(this).attr('href'));
            });

            // Pagination
            $(document).on('click', '#patientsContainer a.ajax-patients-page', function(e) {
                e.preventDefault();
                const href = $(this).attr('href');
                if (href) loadPatients(href);
            });

            // Select-all toggle
            $(document).on('change', '#patientsSelectAll', function() {
                const checked = $(this).is(':checked');
                $('.patientsRowCheck').prop('checked', checked);
            });
        });

        /* =========================
                  Dropdowns
           ========================= */
        function togglePatientsDropdown(btn) {
            const dropdown = btn.nextElementSibling;
            dropdown.classList.toggle('hidden');
            // Hide other open menus
            document.querySelectorAll('#patientsTableWrap td .absolute').forEach((el) => {
                if (el !== dropdown) el.classList.add('hidden');
            });
        }
        window.togglePatientsDropdown = togglePatientsDropdown;

        document.addEventListener('click', (e) => {
            const isTrigger = e.target.closest('button[onclick^="togglePatientsDropdown"]');
            const isMenu = e.target.closest('td .absolute');
            if (!isTrigger && !isMenu) {
                document.querySelectorAll('#patientsTableWrap td .absolute').forEach((el) => el.classList.add(
                    'hidden'));
            }
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('#patientsTableWrap td .absolute').forEach((el) => el.classList.add(
                    'hidden'));
            }
        });
    </script>
@endsection
