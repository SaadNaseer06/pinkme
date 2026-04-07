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
                                    : ($selectedStatus === 'all'
                                        ? 'All Program Registrations'
                                        : 'Pending Program Registrations')) }}
                        </h2>
                        <p class="text-sm text-[#91848C] app-text mt-1">
                            Review and manage program registration requests assigned to you.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 text-sm text-[#91848C] app-text">
                        <span>Pending: <strong class="text-[#213430]">{{ $counts['pending'] }}</strong></span>
                        <span>Approved: <strong class="text-[#213430]">{{ $counts['approved'] }}</strong></span>
                        <span>Rejected: <strong class="text-[#213430]">{{ $counts['rejected'] }}</strong></span>
                        <span>Total: <strong class="text-[#213430]">{{ $counts['all'] }}</strong></span>
                    </div>
                </div>

                <form id="cmProgramRegFiltersForm" method="GET" action="{{ route('case_manager.program_registrations.index') }}"
                    class="flex flex-col md:flex-row md:items-center gap-3 mb-6">
                    <div class="relative w-full md:w-48">
                        <select name="status" id="programRegStatus"
                            class="w-full appearance-none rounded-md px-3 py-2 pr-10 text-sm text-[#213430] bg-white border border-[#91848C] focus:outline-none">
                            <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>Pending</option>
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
                    <button type="submit"
                        class="px-4 py-2 bg-[#9E2469] text-white rounded-md text-sm font-medium hover:bg-[#B52D75] transition app-text">
                        Apply Filter
                    </button>
                    <a id="cmProgramRegReset" href="{{ route('case_manager.program_registrations.index') }}"
                        class="px-4 py-2 border border-[#DCCFD8] text-[#91848C] rounded-md text-sm app-text hover:bg-[#F9EFF5] transition {{ $selectedStatus === 'pending' ? 'hidden' : '' }}">
                        Reset
                    </a>
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
                approved: 'Approved Program Registrations',
                rejected: 'Rejected Program Registrations',
                all: 'All Program Registrations'
            };

            function updateChrome() {
                if (heading) {
                    heading.textContent = labels[statusSelect.value] || 'Program Registration Requests';
                }
                if (resetLink) {
                    resetLink.classList.toggle('hidden', statusSelect.value === 'pending');
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
