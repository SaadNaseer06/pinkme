@php
    use Illuminate\Support\Facades\Auth;
    use Carbon\Carbon;
    use App\Models\Application;
    use Illuminate\Contracts\Pagination\LengthAwarePaginator;
    use App\Models\UserProfile;

    $user = Auth::user();

    // --- Range filter ---
    $range = request('range');
    $startDate = match ($range) {
        'week' => Carbon::now()->subWeek(),
        'month' => Carbon::now()->subMonth(),
        default => null,
    };

    // --- Base query ---
    $apps = Application::query()
        ->with(['program:id,title', 'patient:id,user_id', 'patient.user:id,email', 'missingRequests'])
        ->whereNotNull('reviewer_id') // Only show applications with assigned reviewers
        ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
        ->latest('created_at')
        ->paginate(10)
        ->appends(request()->query());

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

    $caseManagers = App\Models\User::with('profile')->where('role_id', $caseManagerRoleId)->get();
@endphp

@extends('admin.layouts.admin')

@section('title', 'Applications')

@section('content')
    <div class="flex-1 flex flex-col">
        <main class="flex-1">
            <div class="max-w-8xl mx-auto">
                @include('admin.partials.cards')

                <div class="mt-6 bg-[#F3E8EF] rounded-lg p-6">
                    {{-- Header + Filter --}}
                    <div class="flex justify-between flex-col md:flex-row items-center mb-4 ml-3">
                        <h2 class="text-xl font-semibold text-[#213430] app-main mb-2 md:mb-0">All Applications List</h2>

                        <form method="GET" class="flex space-x-4">
                            <div class="relative w-[140px] md:w-[200px]">
                                <select name="range"
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
                            <button type="submit"
                                class="px-4 py-2 bg-[#DB69A2] text-white rounded-md app-text hover:bg-pink-600">
                                Apply
                            </button>
                            @if (request()->filled('range'))
                                <a href="{{ route('case_manager.myApplication') }}"
                                    class="px-3 py-2 border border-[#DCCFD8] text-[#91848C] rounded-md app-text">
                                    Reset
                                </a>
                            @endif
                        </form>
                    </div>

                    {{-- Table --}}
                    <div class="table-container">
                        <table class="min-w-full text-sm text-left mt-6 relative overflow-visible">
                            <thead>
                                <tr class="border-t border-[#e0cfd8]">
                                    <th class="p-2 text-lg text-[#91848C] font-normal app-h">Patient Name</th>
                                    <th class="p-2 text-lg text-[#91848C] font-normal app-h pad-left">Applications ID</th>
                                    <th class="p-2 text-lg text-[#91848C] font-normal app-h">Sub. Date</th>
                                    <th class="p-2 text-lg text-[#91848C] font-normal app-h">Email</th>
                                    <th class="p-2 text-lg text-[#91848C] font-normal app-h">Contact</th>
                                    <th class="p-2 text-lg text-[#91848C] font-normal app-h">Assigned Reviewer</th>
                                    <th class="p-2 text-lg text-[#91848C] font-normal app-h">Status</th>
                                    <th class="p-2 text-lg text-[#91848C] font-normal app-h">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @forelse ($apps as $app)
                                    @php
                                        $patientProfile = UserProfile::where(
                                            'user_id',
                                            $app->patient->user_id,
                                        )->first();
                                        $reviewerProfile = UserProfile::where('user_id', $app->reviewer_id)->first();

                                        $patientName = $patientProfile->full_name ?? 'Unknown';
                                        $email = $app->patient?->user?->email ?? '—';
                                        $contact = $patientProfile->phone ?? '—';
                                        $reviewerName = $reviewerProfile->full_name ?? '—';
                                    @endphp
                                    <tr class="border-t border-[#e0cfd8]">
                                        <td class="p-2">{{ $patientName }}</td>
                                        <td class="p-2 pad-left">{{ $appCode($app) }}</td>
                                        <td class="p-2">{{ $fmtDate($app->created_at) }}</td>
                                        <td class="p-2">{{ $email }}</td>
                                        <td class="p-2">{{ $contact }}</td>
                                        <td class="p-2">{{ $reviewerName }}</td>
                                        <td class="p-2">
                                            @if ($app->missingRequests->isNotEmpty())
                                                <span class="px-4 py-2 rounded-sm text-xs font-medium app-text"
                                                    style="background:#FFF2CC; color:#D9980D;">Missing Docs Requested</span>
                                            @else
                                                {!! $statusBadge($app->status) !!}
                                            @endif
                                        </td>
                                        <td class="p-2 relative">
                                            <button onclick="toggleDropdown(this)"
                                                class="text-[#213430] p-2 rounded-md focus:outline-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                </svg>
                                            </button>
                                            <div
                                                class="absolute right-[62px] top-10 w-[200px] max-w-none bg-[#F6EDF5] rounded-lg shadow-lg py-2 z-20 hidden">
                                                <a href="{{ route('admin.viewApplication', $app->id) }}"
                                                    class="flex items-center px-4 py-2 text-[#91848C] hover:bg-pink-100 text-sm">
                                                    <i class="fas fa-eye mr-2"></i> View Details
                                                </a>
                                                <a href="#"
                                                    onclick="openAssignModal({{ $app->id }}, {{ $app->reviewer_id ?? 'null' }})"
                                                    class="flex items-center px-4 py-2 text-[#91848C] text-sm">
                                                    <i class="fas fa-check mr-2"></i>
                                                    {{ $app->reviewer_id ? 'Change Reviewer' : 'Assign Reviewer' }}
                                                </a>
                                                <a href="#" onclick="openRejectModal({{ $app->id }})"
                                                    class="flex items-center px-4 py-2 gap-2 text-[#91848C] text-sm">
                                                    <i class="fa-solid fa-trash"></i> Delete Application
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="border-t border-[#e0cfd8]">
                                        <td colspan="8" class="p-6 text-center text-[#91848C] app-text">
                                            No applications found{{ $range ? ' in the selected range' : '' }}.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($apps instanceof LengthAwarePaginator && $apps->hasPages())
                        @php
                            $current = $apps->currentPage();
                            $last = $apps->lastPage();
                            $start = max(1, $current - 1);
                            $end = min($last, $start + 2);
                            $start = max(1, $end - 2);
                            $pages = range($start, $end);
                        @endphp
                        <div class="mt-6 flex justify-end space-x-1">
                            @if ($apps->onFirstPage())
                                <span
                                    class="px-3 py-1 rounded-md border border-[#B9B1B6] text-[#91848C] opacity-60 cursor-not-allowed">&lt;</span>
                            @else
                                <a href="{{ $apps->previousPageUrl() }}"
                                    class="px-3 py-1 rounded-md border border-[#B9B1B6] text-[#91848C]">&lt;</a>
                            @endif

                            @foreach ($pages as $page)
                                @if ($page == $current)
                                    <span class="px-4 py-1 rounded-md bg-[#DB69A2] text-white">{{ $page }}</span>
                                @else
                                    <a href="{{ $apps->url($page) }}"
                                        class="px-3 py-1 rounded-md border border-[#B9B1B6] text-[#91848C]">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if ($current < $last)
                                <a href="{{ $apps->nextPageUrl() }}"
                                    class="px-3 py-1 rounded-md border border-[#B9B1B6] text-[#91848C]">&gt;</a>
                            @else
                                <span
                                    class="px-3 py-1 rounded-md border border-[#B9B1B6] text-[#91848C] opacity-60 cursor-not-allowed">&gt;</span>
                            @endif
                        </div>
                    @endif
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
                                <img src="{{ $manager ? $manager->avatar_url : asset('public/images/profile.png') }}" alt="Reviewer"
                                    class="w-10 h-10 rounded-full" />
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
        function showToast(message, type = 'success') {
            const icon = type === 'success' ?
                `<svg width="22" height="22" fill="none"><circle cx="11" cy="11" r="11" fill="#20b354"/><path d="M7 12l3 3 5-6" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>` :
                `<svg width="22" height="22" fill="none"><circle cx="11" cy="11" r="11" fill="#d8000c"/><path d="M7 7l8 8M15 7l-8 8" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg>`;
            const toast = $(`
        <div class="toast-msg toast-${type}">
            <span>${icon}</span>
            <div style="flex:1">${message}</div>
            <button class="toast-close" aria-label="Close">&times;</button>
        </div>
    `);
            $('#toastContainer').append(toast);
            toast.find('.toast-close').on('click', function() {
                toast.fadeOut(300, function() {
                    toast.remove();
                });
            });
            setTimeout(function() {
                toast.fadeOut(400, function() {
                    toast.remove();
                });
            }, 3500);
        }

        let currentDeleteApplicationId = null;

        // Show delete modal for the given ID
        function openRejectModal(appId) {
            currentDeleteApplicationId = appId;
            $('#rejectModal').removeClass('hidden');
        }

        function closeRejectModal() {
            currentDeleteApplicationId = null;
            $('#rejectModal').addClass('hidden');
        }

        // AJAX delete on Yes button
        $('#deleteConfirmBtn').off('click').on('click', function() {
            if (!currentDeleteApplicationId) return;
            $('#deleteConfirmBtn').prop('disabled', true).text('Deleting...');
            $.ajax({
                url: `/admin/applications/${currentDeleteApplicationId}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Reload so toast appears via session flash
                    location.reload();
                },
                error: function(xhr) {
                    closeRejectModal();
                    showToast('Failed to delete application: ' + (xhr.responseJSON?.message ||
                        'Unknown error'), 'error');
                    $('#deleteConfirmBtn').prop('disabled', false).text('Yes');
                }
            });
        });



        $('#confirmAssignBtn').on('click', function() {
            if (!selectedReviewerId || !currentApplicationId) {
                showToast('Please select a reviewer.', 'error');
                return;
            }
            $('#confirmAssignBtn').prop('disabled', true).text('Assigning...');
            $.ajax({
                url: `/admin/applications/${currentApplicationId}/assign-reviewer`,
                type: 'POST',
                data: {
                    reviewer_id: selectedReviewerId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    closeAssignModal();
                    showToast('Reviewer assigned successfully!', 'success');
                    setTimeout(() => location.reload(), 2000);
                },
                error: function(xhr) {
                    showToast('Failed to assign reviewer: ' + (xhr.responseJSON?.message ||
                        'Unknown error'), 'error');
                    $('#confirmAssignBtn').prop('disabled', false).text('CONFIRM & ASSIGN');
                }
            });
        });
        let currentApplicationId = null;
        let assignedReviewerId = null;
        let selectedReviewerId = null;

        // Open modal, highlight assigned reviewer, disable their button
        window.openAssignModal = function(applicationId, reviewerId) {
            currentApplicationId = applicationId;
            assignedReviewerId = reviewerId;
            selectedReviewerId = null;

            $('#reviewerSearch').val('');
            $('.manager-row').show();

            $('.selectReviewer').each(function() {
                const userId = $(this).data('user-id');
                if (userId == assignedReviewerId) {
                    $(this).addClass('bg-[#db69a2] text-[#ffffff] font-bold cursor-not-allowed')
                        .text('Assigned').prop('disabled', true);
                } else {
                    $(this).removeClass('bg-[#db69a2] font-bold cursor-not-allowed')
                        .text('Select').prop('disabled', false);
                }
            });

            $('#confirmAssignBtn').prop('disabled', true);

            $('#assignModal').removeClass('hidden');
        }

        window.closeAssignModal = function() {
            $('#assignModal').addClass('hidden');
        }

        // Reviewer search/filter logic
        $('#reviewerSearch').on('keyup', function() {
            let value = $(this).val().toLowerCase();
            $('.manager-row').filter(function() {
                $(this).toggle(
                    $(this).find('span').text().toLowerCase().indexOf(value) > -1
                );
            });
        });

        // Selecting a reviewer
        $(document).on('click', '.selectReviewer', function() {
            if ($(this).prop('disabled')) return;

            $('.selectReviewer').removeClass('bg-[#db69a2] text-white font-bold cursor-not-allowed').text('Select');
            $(this).addClass('bg-[#db69a2] text-[#ffffff] font-bold cursor-not-allowed').text('Selected');
            selectedReviewerId = $(this).data('user-id');
            $('#confirmAssignBtn').prop('disabled', false);
        });

        // Assign on confirm
        $('#confirmAssignBtn').on('click', function() {
            if (!selectedReviewerId || !currentApplicationId) {
                alert('Please select a reviewer.');
                return;
            }
            $('#confirmAssignBtn').prop('disabled', true).text('Assigning...');
            $.ajax({
                url: `/admin/applications/${currentApplicationId}/assign-reviewer`,
                type: 'POST',
                data: {
                    reviewer_id: selectedReviewerId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    closeAssignModal();
                    location.reload();
                },
                error: function(xhr) {
                    alert('Failed to assign reviewer: ' + (xhr.responseJSON?.message ||
                        'Unknown error'));
                    $('#confirmAssignBtn').prop('disabled', false).text('CONFIRM & ASSIGN');
                }
            });
        });

        // Dropdown, reject modal, and close helpers (as before)
        function toggleDropdown(btn) {
            const dropdown = btn.nextElementSibling;
            dropdown.classList.toggle('hidden');
            document.querySelectorAll("td .absolute").forEach(el => {
                if (el !== dropdown) el.classList.add("hidden");
            });
        }

        function openRejectModal() {
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
    </script>

@endsection
