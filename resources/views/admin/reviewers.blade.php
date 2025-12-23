@extends('admin.layouts.admin')

@section('title', 'Case Managers')

@section('content')
    @php
        $statusFilter = $status ?? request('status', 'all');
        if (!in_array($statusFilter, ['active', 'inactive', 'assigned', 'all'], true)) {
            $statusFilter = 'all';
        }

        $searchFilter = $searchQuery ?? request('search', '');
        $assignedReviewerFilter = (int) ($assignedReviewer ?? request('assigned_reviewer', 0));
        $hasFilters = $statusFilter !== 'all' || $searchFilter !== '' || $assignedReviewerFilter > 0;
    @endphp

    <div class="flex-1 flex flex-col">
        <main class="flex-1">
            <div class="max-w-8xl mx-auto">
                {{-- @include('admin.partials.cards') --}}

                <div id="reviewerStatsWrap" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Active</p>
                                <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $reviewerCounts['active'] ?? 0 }}</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-amber-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Inactive</p>
                                <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $reviewerCounts['inactive'] ?? 0 }}
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l2.5 2.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Assigned</p>
                                <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $reviewerCounts['assigned'] ?? 0 }}
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2a4 4 0 014-4h0a4 4 0 014 4v2" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-pink-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total</p>
                                <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $reviewerCounts['all'] ?? 0 }}</p>
                            </div>
                            <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-3 mt-6 mb-6">
                    <div
                        class="md:col-span-2 bg-gradient-to-r from-[#FCE8F3] via-[#F7F0F7] to-white rounded-2xl p-6 shadow-sm">
                        <span class="text-xs font-semibold uppercase tracking-[0.2em] text-[#DB69A2]">Reviewer & Case
                            Manager workspace</span>
                        <h2 class="mt-2 text-2xl font-semibold text-[#213430]">Manage reviewers and case managers in one
                            view</h2>
                        <p class="mt-2 text-sm text-[#5F4E57]">One table, quick filters, and an assigned-only dropdown so
                            you can focus on everyone who owns applications.</p>
                    </div>
                    <div class="bg-white rounded-2xl p-6 border border-[#F0D9E6] shadow-sm flex flex-col justify-between">
                        <div>
                            <p class="text-sm font-semibold text-[#213430]">Assigned focus</p>
                            <p class="mt-1 text-xs text-[#91848C]">Use the dropdown to see only reviewers with active
                                assignments.</p>
                        </div>
                        <a href="{{ route('admin.assigned') }}"
                            class="inline-flex items-center justify-center px-4 py-2 mt-4 rounded-md bg-[#DB69A2] text-white text-sm font-semibold hover:bg-[#C4568C] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#DB69A2]">
                            View Assigned Applications
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.25 6.75H18V11.5M18 6.75l-6.75 6.75M6 17.25h12" />
                            </svg>
                        </a>
                    </div>
                </div>



                <div class="mt-6 bg-[#F3E8EF] rounded-lg p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-2">
                        <div>
                            <h2 class="text-xl font-semibold text-[#213430] app-main">Reviewer Directory</h2>
                            <p class="text-xs text-[#5F4E57] mt-1">Filter by ID, email, name, or limit to reviewers with
                                assigned applications.</p>
                        </div>
                        <a href="{{ route('admin.case-managers.create') }}"
                            class="inline-flex items-center bg-[#db69a2] text-white text-sm px-4 py-2 rounded-md app-h hover:bg-[#c95791]">
                            Add Case Manager
                        </a>
                    </div>

                    <div class="mt-4">
                        <form id="reviewerFilters" method="GET" action="{{ route('admin.reviewers') }}"
                            class="grid grid-cols-1 md:grid-cols-4 gap-4 max-w-8xl">
                            <div class="relative w-full">
                                <select name="status"
                                    class="bg-transparent border border-[#91848C] text-[#91848C] text-sm px-4 py-2 pr-8 rounded-md w-full appearance-none focus:outline-none">
                                    <option value="all" @selected($statusFilter === 'all')>All (active + inactive)</option>
                                    <option value="active" @selected($statusFilter === 'active')>Active</option>
                                    <option value="inactive" @selected($statusFilter === 'inactive')>Inactive</option>
                                    <option value="assigned" @selected($statusFilter === 'assigned')>Assigned (has applications)
                                    </option>
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
                                <input type="text" name="search" value="{{ $searchFilter }}"
                                    placeholder="Search by ID, name or email"
                                    class="bg-transparent border border-[#91848C] text-[#91848C] text-sm px-4 py-2 rounded-md w-full focus:outline-none placeholder-[#B1A4AD]" />
                            </div>

                            <div class="relative w-full">
                                <select name="assigned_reviewer"
                                    class="bg-transparent border border-[#91848C] text-[#91848C] text-sm px-4 py-2 pr-8 rounded-md w-full appearance-none focus:outline-none">
                                    <option value="">All Case Managers</option>
                                    @foreach ($assignedReviewers as $assigned)
                                        <option value="{{ $assigned->id }}" @selected($assignedReviewerFilter === $assigned->id)>
                                            {{ $assigned->profile->full_name ?? $assigned->email }}
                                            ({{ $assigned->applications_count }} apps)
                                        </option>
                                    @endforeach
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-[#91848C]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 md:col-span-4">
                                @if ($hasFilters)
                                    <a href="{{ route('admin.reviewers') }}"
                                        class="px-4 py-2 border border-[#DCCFD8] text-[#91848C] rounded-md text-sm app-text hover:bg-[#F9EFF5] transition">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <div id="reviewersTableWrap" class="table-container mt-6 bg-white rounded-lg shadow-md overflow-hidden">
                    <div
                        class="px-6 py-4 border-b border-[#e0cfd8] flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-[#213430]">Reviewers & Case Managers</h3>
                            <p class="text-xs text-[#91848C]">Use the controls to filter by status or assignment.</p>
                        </div>
                        <div class="flex flex-wrap gap-3 items-center"></div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left">
                            <thead>
                                <tr class="border-t border-[#e0cfd8] bg-gray-50">
                                    <th class="p-3 text-[#91848C] font-medium app-h">#</th>
                                    <th class="p-3 text-[#91848C] font-medium app-h">Reviewer ID</th>
                                    <th class="p-3 text-[#91848C] font-medium app-h">Email</th>
                                    <th class="p-3 text-[#91848C] font-medium app-h">Contact</th>
                                    <th class="p-3 text-[#91848C] font-medium app-h text-center">Assigned Apps</th>
                                    <th class="p-3 text-[#91848C] font-medium app-h">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @forelse ($teamMembers as $member)
                                    <tr class="border-t border-[#e0cfd8]">
                                        <td class="p-3">
                                            <span
                                                class="text-[#91848C] text-[16px] font-light app-text">{{ $loop->iteration + ($teamMembers->currentPage() - 1) * $teamMembers->perPage() }}</span>
                                        </td>
                                        <td class="p-3 align-middle text-[#213430] text-[16px] font-medium app-text">
                                            {{ $member->reviewer_id ?? '-' }}
                                            @if (optional($member->profile)->status == 1)
                                                <span
                                                    class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Active</span>
                                            @else
                                                <span
                                                    class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="p-3 align-middle text-[#91848C] text-[16px] font-light app-text">
                                            {{ $member->email }}
                                        </td>
                                        <td class="p-3 align-middle text-[#91848C] text-[16px] font-light app-text">
                                            {{ optional($member->profile)->phone ?? 'N/A' }}
                                        </td>
                                        <td
                                            class="p-3 align-middle text-center text-[#213430] text-[16px] font-semibold app-text">
                                            {{ $member->applications_count }}
                                        </td>
                                        <td class="p-3 relative">
                                            <button type="button" onclick="toggleRowMenu(this)"
                                                class="p-2 rounded-md text-[#213430] hover:bg-gray-100 focus:outline-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 12h.01M12 12h.01M19 12h.01" />
                                                </svg>
                                            </button>
                                            <div
                                                class="row-menu absolute right-2 top-10 w-40 bg-white border border-[#e5d7df] rounded-md shadow-lg hidden z-20">
                                                <a href="{{ route('admin.reviewers.show', $member->id) }}"
                                                    class="flex items-center px-3 py-2 text-sm text-[#213430] hover:bg-[#F9EFF5]">
                                                    View Profile
                                                </a>
                                                <a href="{{ route('admin.reviewers.edit', $member->id) }}"
                                                    class="flex items-center px-3 py-2 text-sm text-[#213430] hover:bg-[#F9EFF5]">
                                                    Edit
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6"
                                            class="p-6 text-center text-[#91848C] text-[16px] font-light app-text">
                                            No reviewers found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 flex justify-between items-center">
                        <div class="text-sm text-[#91848C]">
                            Showing {{ $teamMembers->firstItem() ?? 0 }} to {{ $teamMembers->lastItem() ?? 0 }} of
                            {{ $teamMembers->total() }} Team Members
                        </div>
                        <div class="ajax-pagination">
                            {{ $teamMembers->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <style>
        .table-container {
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
            const filtersForm = document.getElementById('reviewerFilters');
            const getTableWrap = () => document.getElementById('reviewersTableWrap');
            const getStatsWrap = () => document.getElementById('reviewerStatsWrap');

            window.toggleRowMenu = function(btn) {
                const menu = btn.nextElementSibling;
                const isOpen = menu?.classList.contains('hidden') === false;
                document.querySelectorAll('#reviewersTableWrap .row-menu').forEach(m => m.classList.add(
                    'hidden'));
                if (menu && !isOpen) {
                    menu.classList.remove('hidden');
                }
            };

            window.addEventListener('click', function(e) {
                if (!e.target.closest('.row-menu') && !e.target.closest(
                    'button[onclick^="toggleRowMenu"]')) {
                    document.querySelectorAll('#reviewersTableWrap .row-menu').forEach(m => m.classList.add(
                        'hidden'));
                }
            });

            async function fetchAndSwap(url) {
                const tableWrap = getTableWrap();
                if (tableWrap) tableWrap.classList.add('opacity-50', 'pointer-events-none');
                try {
                    const res = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const text = await res.text();
                    const doc = new DOMParser().parseFromString(text, 'text/html');
                    const newTable = doc.getElementById('reviewersTableWrap');
                    const newStats = doc.getElementById('reviewerStatsWrap');
                    if (newTable && tableWrap) tableWrap.replaceWith(newTable);
                    const statsWrap = getStatsWrap();
                    if (newStats && statsWrap) statsWrap.replaceWith(newStats);
                    window.history.pushState({}, '', url);
                    attachPagination(); // rebind for the new nodes
                } catch (e) {
                    console.error('Failed to load reviewers', e);
                } finally {
                    const freshTable = getTableWrap();
                    if (freshTable) {
                        freshTable.classList.remove('opacity-50', 'pointer-events-none');
                    }
                }
            }

            function buildUrlFromForm() {
                const data = new FormData(filtersForm);
                const params = new URLSearchParams(data);
                const url = new URL(filtersForm.action, window.location.origin);
                url.search = params.toString();
                return url.toString();
            }

            function debounce(fn, delay = 400) {
                let t;
                return (...args) => {
                    clearTimeout(t);
                    t = setTimeout(() => fn(...args), delay);
                };
            }

            const debouncedFetch = debounce(() => {
                fetchAndSwap(buildUrlFromForm());
            }, 350);

            filtersForm?.addEventListener('submit', function(e) {
                e.preventDefault();
            });

            const searchInput = filtersForm?.querySelector('input[name="search"]');
            const assignedSelect = filtersForm?.querySelector('select[name="assigned_reviewer"]');
            const statusSelect = filtersForm?.querySelector('select[name="status"]');

            searchInput?.addEventListener('input', debouncedFetch);
            assignedSelect?.addEventListener('change', () => fetchAndSwap(buildUrlFromForm()));
            statusSelect?.addEventListener('change', () => fetchAndSwap(buildUrlFromForm()));

            function attachPagination() {
                document.querySelectorAll('#reviewersTableWrap .ajax-pagination a').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        fetchAndSwap(this.href);
                    });
                });
            }

            attachPagination();
        });
    </script>
@endsection
