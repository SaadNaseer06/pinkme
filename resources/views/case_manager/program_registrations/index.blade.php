@extends('case_manager.layouts.app')

@section('title', 'Program Registration Requests')

@section('content')
    <main class="flex-1">
        <div class="max-w-8xl mx-auto">
            <div class="mt-6 bg-[#F3E8EF] rounded-lg p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4 border-b border-[#DCCFD8] pb-4">
                    <div>
                        <h2 id="programRegHeading" class="text-2xl font-semibold text-[#213430] app-main">
                            {{ $selectedStatus === 'approved'
                                ? 'Approved Program Registrations'
                                : ($selectedStatus === 'rejected'
                                    ? 'Rejected Program Registrations'
                                    : ($selectedStatus === 'pending_finance'
                                        ? 'With Finance (your cases)'
                                        : ($selectedStatus === 'all'
                                            ? 'All Program Registrations'
                                            : 'Pending Program Registrations'))) }}
                        </h2>
                        <p class="text-sm text-[#91848C] app-text mt-1">
                            Review applications assigned to you and pending applications that are not yet assigned (claim by opening the record and approving, rejecting, or saving billing links).
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 text-sm text-[#91848C] app-text">
                        <span>Pending: <strong class="text-[#213430]">{{ $counts['pending'] }}</strong></span>
                        <span>With finance: <strong class="text-[#213430]">{{ $counts['pending_finance'] ?? 0 }}</strong></span>
                        <span>Approved: <strong class="text-[#213430]">{{ $counts['approved'] }}</strong></span>
                        <span>Rejected: <strong class="text-[#213430]">{{ $counts['rejected'] }}</strong></span>
                        <span>Total: <strong class="text-[#213430]">{{ $counts['all'] }}</strong></span>
                    </div>
                </div>

                <form id="cmProgramRegFiltersForm" method="GET" action="{{ route('case_manager.program_registrations.index') }}"
                    class="flex flex-col gap-3 mb-6 w-full min-w-0">
                    <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-stretch gap-3 w-full min-w-0">
                        <div class="relative w-full sm:w-48 sm:min-w-[12rem] shrink-0">
                            <select name="status" id="programRegStatus"
                                class="w-full min-w-0 appearance-none rounded-md px-3 py-2 pr-10 text-sm text-[#213430] bg-white border border-[#91848C] focus:outline-none">
                                <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="pending_finance" {{ $selectedStatus === 'pending_finance' ? 'selected' : '' }}>With finance</option>
                                <option value="approved" {{ $selectedStatus === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ $selectedStatus === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>All</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[#91848C]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        <div class="relative flex-1 min-w-0 w-full sm:min-w-[200px]">
                            <label for="programRegSearch" class="sr-only">Search applicants</label>
                            <input type="search" name="search" id="programRegSearch" value="{{ request('search') }}"
                                placeholder="Search by name, state, phone, or email"
                                autocomplete="off"
                                class="w-full min-w-0 rounded-md px-3 py-2 pl-10 text-sm text-[#213430] bg-white border border-[#91848C] placeholder:text-[#91848C] focus:outline-none focus:border-[#9E2469] focus:ring-1 focus:ring-[#9E2469]" />
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#91848C]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                                </svg>
                            </span>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto shrink-0">
                            <button type="submit"
                                class="w-full sm:w-auto px-4 py-2 bg-[#9E2469] text-white rounded-md text-sm font-medium hover:bg-[#B52D75] transition app-text">
                                Apply
                            </button>
                            <a id="cmProgramRegReset" href="{{ route('case_manager.program_registrations.index') }}"
                                class="w-full sm:w-auto text-center px-4 py-2 border border-[#DCCFD8] text-[#91848C] rounded-md text-sm app-text hover:bg-[#F9EFF5] transition {{ $selectedStatus === 'pending' && ! request()->filled('search') ? 'hidden' : '' }}">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>

                <div id="cmProgramRegTableWrap" class="transition-opacity min-h-[120px]">
                    @include('case_manager.program_registrations._list_fragment', ['registrations' => $registrations])
                </div>
            </div>
        </div>
    </main>

@endsection

@push('scripts')
    <script>
        (function () {
            const baseUrl = @json(route('case_manager.program_registrations.index'));
            const wrap = document.getElementById('cmProgramRegTableWrap');
            const form = document.getElementById('cmProgramRegFiltersForm');
            const statusSelect = document.getElementById('programRegStatus');
            const heading = document.getElementById('programRegHeading');
            const resetLink = document.getElementById('cmProgramRegReset');
            if (!wrap || !form || !statusSelect) return;

            const labels = {
                pending: 'Pending Program Registrations',
                pending_finance: 'With Finance (your cases)',
                approved: 'Approved Program Registrations',
                rejected: 'Rejected Program Registrations',
                all: 'All Program Registrations'
            };

            function updateChrome() {
                if (heading) {
                    heading.textContent = labels[statusSelect.value] || 'Program Registration Requests';
                }
                if (resetLink) {
                    const searchInput = document.getElementById('programRegSearch');
                    const hasSearch = searchInput && String(searchInput.value || '').trim() !== '';
                    resetLink.classList.toggle('hidden', statusSelect.value === 'pending' && !hasSearch);
                }
            }

            async function loadProgramRegistrations(url) {
                let targetUrl = url;
                if (!targetUrl) {
                    const params = new URLSearchParams(new FormData(form));
                    const qs = params.toString();
                    targetUrl = qs ? `${baseUrl}?${qs}` : baseUrl;
                }

                wrap.classList.add('opacity-50', 'pointer-events-none');
                try {
                    const res = await fetch(targetUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html',
                        },
                        credentials: 'same-origin',
                    });
                    if (!res.ok) throw new Error('Request failed');
                    wrap.innerHTML = await res.text();
                    const u = new URL(targetUrl, window.location.origin);
                    window.history.replaceState({}, '', u.pathname + u.search);
                    updateChrome();
                } catch (e) {
                    alert('Could not load registrations. Please try again.');
                } finally {
                    wrap.classList.remove('opacity-50', 'pointer-events-none');
                }
            }

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                loadProgramRegistrations();
            });

            statusSelect.addEventListener('change', function () {
                loadProgramRegistrations();
            });

            if (resetLink) {
                resetLink.addEventListener('click', function (e) {
                    e.preventDefault();
                    statusSelect.value = 'pending';
                    const searchInput = document.getElementById('programRegSearch');
                    if (searchInput) searchInput.value = '';
                    loadProgramRegistrations(baseUrl);
                });
            }

            document.addEventListener('click', function (e) {
                const pagLink = e.target.closest('.cm-prog-reg-pagination a[href]');
                if (!pagLink || !wrap.contains(pagLink)) return;
                e.preventDefault();
                loadProgramRegistrations(pagLink.href);
            });
        })();
    </script>
@endpush
