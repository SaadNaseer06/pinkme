@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;
    use Carbon\Carbon;
    use App\Models\Application;
    use Illuminate\Contracts\Pagination\LengthAwarePaginator;

    $user = Auth::user();

    // --- Range filter (optional) ---
    $range = request('range'); // 'week' | 'month' | null
    $startDate = null;
    if ($range === 'week') {
        $startDate = Carbon::now()->subWeek();
    }
    if ($range === 'month') {
        $startDate = Carbon::now()->subMonth();
    }

    // --- Base query: applications assigned to this case manager ---
    try {
        $apps = Application::query()
            ->with(['program:id,title', 'patient:id,user_id', 'patient.user:id,email'])
            ->where('reviewer_id', $user->id)
            ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
            ->latest('created_at')
            ->paginate(10)
            ->appends(request()->query());
    } catch (\Exception $e) {
        // Fallback to empty paginator / collection
        $apps = collect();
        session()->flash('error', 'Unable to load applications. Please try again later.');
    }

    // --- Helpers ---
    $fmtDate = fn($dt) => $dt ? Carbon::parse($dt)->format('Y-m-d h:i A') : '—';

    $appCode = function ($app) {
        if (!empty($app->code)) {
            return $app->code;
        }
        return 'APP-' . str_pad((string) ($app->id ?? 0), 6, '0', STR_PAD_LEFT);
    };

    $statusBadge = function ($status) {
        $normalized = str_replace(' ', '_', strtolower((string) $status));
        $map = [
            'approved' => ['bg' => '#C5E8D1', 'text' => '#20B354', 'label' => 'Approved'],
            'rejected' => ['bg' => '#E8C5C5', 'text' => '#B32020', 'label' => 'Rejected'],
            'under_review' => ['bg' => '#E4D7DF', 'text' => '#91848C', 'label' => 'Under Review'],
            'pending' => ['bg' => '#E4D7DF', 'text' => '#91848C', 'label' => 'Pending'],
        ];
        $cfg = $map[$normalized] ?? $map['pending'];
        return sprintf(
            '<span class="px-4 py-2 rounded-sm text-xs font-medium app-text" style="background:%s;color:%s">%s</span>',
            e($cfg['bg']),
            e($cfg['text']),
            e($cfg['label']),
        );
    };
@endphp

@extends('case_manager.layouts.app')

@section('title', 'My Application')

@section('content')
    <main class="flex-1 py-4">

        {{-- Flash / status messages --}}
        {{-- @if (session('success'))
            <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md mb-4" role="alert">
                <h4 class="font-semibold mb-1">Action completed</h4>
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-md mb-4" role="alert">
                <h4 class="font-semibold mb-1">Error</h4>
                <p>{{ session('error') }}</p>
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-md mb-4" role="alert">
                <h4 class="font-semibold mb-1">Please review the form</h4>
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}

        {{-- Status cards --}}
        @include('case_manager.partials.cards')

        <div class="mt-6 bg-[#F3E8EF] rounded-lg p-6">
            <div class="flex justify-between flex-col md:flex-row items-center mb-4 ml-3">
                <h2 class="text-xl font-semibold text-[#213430] app-main mb-2 md:mb-0">
                    All Applications List
                </h2>

                {{-- Range Filter --}}
                <form method="GET" class="flex space-x-4">
                    <div class="relative w-[140px] md:w-[200px]">
                        <select name="range"
                            class="w-full appearance-none rounded-md px-3 py-2 pr-10 text-sm text-[#91848C] bg-transparent border border-[#91848C] focus:outline-none">
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
                    <button type="submit"
                        class="px-4 py-2 bg-[#9E2469] text-white rounded-md app-text hover:bg-pink-600">Apply</button>
                    @if (request()->filled('range'))
                        <a href="{{ route('case_manager.myApplication') }}"
                            class="px-3 py-2 border border-[#DCCFD8] text-[#91848C] rounded-md app-text">Reset</a>
                    @endif
                </form>
            </div>

            <div class="table-container">
                <table class="min-w-full text-sm text-left mt-6 relative overflow-visible">
                    <thead>
                        <tr class="border-t border-[#e0cfd8]">
                            <th class="p-2 text-lg text-[#91848C] font-normal app-h">Application Title</th>
                            <th class="p-2 text-lg text-[#91848C] font-normal app-h">Application ID</th>
                            <th class="p-2 text-lg text-[#91848C] font-normal app-h">Submission Date</th>
                            <th class="p-2 text-lg text-[#91848C] font-normal app-h">Email</th>
                            <th class="p-2 text-lg text-[#91848C] font-normal app-h">Status</th>
                            <th class="p-2 text-lg text-[#91848C] font-normal app-h">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @forelse ($apps as $app)
                            @php
                                $title = optional($app->program)->title ?? '—';
                                $code = $appCode($app);
                                $date = $fmtDate($app->created_at);
                                $email = optional(optional($app->patient)->user)->email ?? '—';
                            @endphp

                            <tr class="border-t border-[#e0cfd8]">
                                <td class="p-2">
                                    <span class="text-[#91848C] text-[16px] font-light app-text">{{ $title }}</span>
                                </td>

                                <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                    {{ $code }}
                                </td>

                                <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                    {{ $date }}
                                </td>

                                <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                    {{ $email }}
                                </td>

                                <td class="p-2 align-middle">
                                    @if (!empty($app->missingRequests) && $app->missingRequests->isNotEmpty())
                                        <span class="px-4 py-2 rounded-sm text-xs font-medium app-text"
                                            style="background:#FFF2CC; color:#D9980D;">Missing Docs Requested</span>
                                    @else
                                        {!! $statusBadge($app->status) !!}
                                    @endif
                                </td>

                                <td class="p-2 relative">
                                    <button onclick="cmToggleDropdown(this)"
                                        class="text-[#213430] p-2 rounded-md focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>

                                    <div
                                        class="dropdown absolute right-[28px] top-10 w-[250px] max-w-none bg-[#F6EDF5] rounded-lg shadow-lg py-2 z-20 hidden">
                                        <a href="{{ route('case_manager.viewAssignedApplication', $app->id) }}"
                                            class="flex items-center px-4 py-2 text-[#91848C] hover:bg-[#ddd5dc] text-sm">
                                            <i class="fas fa-eye mr-2"></i> View Details
                                        </a>

                                        <form method="POST"
                                            action="{{ route('case_manager.applications.approve', $app->id) }}">
                                            @csrf
                                            <button type="submit"
                                                class="w-full text-left flex items-center px-4 py-2 text-[#91848C] hover:bg-[#ddd5dc] text-sm">
                                                <i class="fas fa-check mr-2"></i> Approve
                                            </button>
                                        </form>

                                        <button type="button" onclick="cmOpenRejectModal({{ $app->id }})"
                                            class="w-full text-left flex items-center px-4 py-2 text-[#91848C] hover:bg-[#ddd5dc] text-sm">
                                            <i class="fas fa-times mr-2"></i> Reject
                                        </button>

                                        <button type="button" onclick="cmOpenMissingDocModal({{ $app->id }})"
                                            class="w-full text-left flex items-center px-4 py-2 text-[#91848C] hover:bg-[#ddd5dc] text-sm">
                                            <i class="fas fa-file-alt mr-2"></i> Request Missing Documents
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-t border-[#e0cfd8]">
                                <td colspan="6" class="p-8 text-center text-[#91848C] app-text">
                                    <div class="flex flex-col items-center space-y-2">
                                        <svg class="w-10 h-10 text-[#DCCFD8]" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                        <p class="text-lg">No applications
                                            found{{ $range ? ' in the selected range' : '' }}.</p>
                                        @if (request()->filled('range'))
                                            <a href="{{ route('case_manager.myApplication') }}"
                                                class="mt-2 inline-block text-[#9E2469] underline hover:text-[#FE6EB6]">
                                                Reset filter and show all
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @php
                $p = $apps;
                $isPager = $p instanceof LengthAwarePaginator;
            @endphp
            @if ($isPager && $p->hasPages())
                @php
                    $current = $p->currentPage();
                    $last = $p->lastPage();
                    $start = max(1, $current - 1);
                    $end = min($last, $start + 2);
                    $start = max(1, $end - 2);
                    $pages = range($start, $end);
                @endphp
                <div class="mt-6 flex justify-end space-x-1">
                    {{-- Prev --}}
                    @if ($p->onFirstPage())
                        <span
                            class="px-3 py-1 rounded-md bg-transparent text-[#91848C] border border-[#B9B1B6] app-btn opacity-60 cursor-not-allowed">&lt;</span>
                    @else
                        <a href="{{ $p->previousPageUrl() }}"
                            class="px-3 py-1 rounded-md bg-transparent text-[#91848C] border border-[#B9B1B6] app-btn">&lt;</a>
                    @endif

                    {{-- Page window --}}
                    @foreach ($pages as $page)
                        @if ($page == $current)
                            <span class="px-4 py-1 rounded-md bg-[#9E2469] text-white app-btn-1">{{ $page }}</span>
                        @else
                            <a href="{{ $p->url($page) }}"
                                class="px-3 py-1 rounded-md bg-transparent text-[#91848C] border border-[#B9B1B6] app-btn">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if ($current < $last)
                        <a href="{{ $p->nextPageUrl() }}"
                            class="px-3 py-1 rounded-md bg-transparent text-[#91848C] border border-[#B9B1B6] app-btn">&gt;</a>
                    @else
                        <span
                            class="px-3 py-1 rounded-md bg-transparent text-[#91848C] border border-[#B9B1B6] app-btn opacity-60 cursor-not-allowed">&gt;</span>
                    @endif
                </div>
            @endif
        </div>
    </main>

    {{-- Reject Modal --}}
    @if ($apps->isNotEmpty())
        <div id="rejectModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-70 z-50 hidden">
            <div class="bg-[#F3E8EF] rounded-lg shadow-lg p-6 md:p-12 w-full max-w-[22rem] md:max-w-2xl text-center">
                <div class="flex flex-col items-center space-y-2">
                    <div class="flex justify-center">
                        <img src="{{ asset('public/images/reject-illustration.png') }}" alt="Reject illustration"
                            class="w-36 h-auto" />
                    </div>
                    <h1 class="text-2xl md:text-3xl font-normal text-gray-800">Reject Application</h1>
                    <p class="text-black text-md md:text-lg leading-relaxed">
                        Are you sure you want to reject this application? Please provide a <span
                            class="text-[#9E2469]">reason</span>.
                    </p>

                    <form id="rejectForm" method="POST"
                        action="{{ route('case_manager.applications.reject', $apps->first()->id) }}">
                        @csrf
                        <div class="w-full max-w-md mx-auto text-left space-y-3">
                            <label class="text-sm md:text-md text-[#91848C] font-normal">
                                Enter Reason For Rejection <span class="text-red-500">*</span>
                            </label>
                            <textarea name="reason" placeholder="Message" rows="4" required
                                class="w-full px-4 py-3 rounded-md bg-transparent text-[#B1A4AD] border border-[#DCCFD8] placeholder:text-[#C4B8C0] focus:outline-none focus:ring-2 focus:ring-[#9E2469]"></textarea>
                        </div>

                        <div class="flex justify-center gap-3 pt-4">
                            <button type="submit"
                                class="bg-[#9E2469] text-white px-3 py-3 rounded-md text-sm font-semibold hover:bg-[#FE6EB6] transition app-text">
                                CONFIRM & REJECT
                            </button>
                            <button type="button" onclick="cmCloseRejectModal()"
                                class="bg-transparent border border-[#D6C6CE] text-[#8B7E88] px-6 py-3 rounded-md text-sm font-semibold hover:bg-[#DCCFD8] transition app-text">
                                CANCEL
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Missing Document Modal --}}
    <div id="missingDocModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-70 z-50 hidden">
        <div class="bg-[#F3E8EF] rounded-lg shadow-lg p-6 md:p-12 w-full max-w-[22rem] md:max-w-2xl text-center">
            <form method="POST" id="missingDocForm" action="">
                @csrf
                <div class="flex flex-col items-center space-y-2">
                    <h1 class="text-2xl md:text-3xl font-normal text-gray-800">Request Missing Documents</h1>

                    <p class="text-black text-md md:text-lg leading-relaxed">
                        Notify the patient to upload the missing documents
                        <br class="md:inline hidden">
                        required for application review.
                    </p>

                    <div class="w-full max-w-md mx-auto text-left space-y-3">
                        <label class="text-md text-[#91848C] font-normal">
                            Enter Details For Missing Document <span class="text-red-500">*</span>
                        </label>
                        <textarea name="message" placeholder="Message" rows="4" required
                            class="w-full px-4 py-3 rounded-md bg-transparent text-[#B1A4AD] border border-[#DCCFD8] placeholder:text-[#C4B8C0] focus:outline-none focus:ring-2 focus:ring-[#9E2469]"></textarea>
                    </div>

                    <div class="flex justify-center gap-4 pt-4">
                        <button type="submit"
                            class="bg-[#9E2469] text-white px-6 py-3 rounded-md text-sm font-semibold hover:bg-[#FE6EB6] transition">
                            SEND
                        </button>
                        <button type="button" onclick="cmCloseMissingDocument()"
                            class="bg-transparent border border-[#D6C6CE] text-[#8B7E88] px-6 py-3 rounded-md text-sm font-semibold hover:bg-[#DCCFD8] transition">
                            CANCEL
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Page JS (dropdowns + modals) --}}
    <script>
        function cmToggleDropdown(btn) {
            document.querySelectorAll('.dropdown').forEach(d => d.classList.add('hidden'));
            const menu = btn.parentElement.querySelector('.dropdown');
            if (!menu) {
                return;
            }

            menu.classList.toggle('hidden');

            const handleClickOutside = function(e) {
                if (!btn.contains(e.target) && !menu.contains(e.target)) {
                    menu.classList.add('hidden');
                    document.removeEventListener('click', handleClickOutside);
                }
            };

            document.addEventListener('click', handleClickOutside);
        }

        const baseCM = "{{ url('case_manager/applications') }}";

        function cmOpenMissingDocModal(appId) {
            const modal = document.getElementById('missingDocModal');
            const form = document.getElementById('missingDocForm');
            if (form) {
                form.action = `${baseCM}/${appId}/request-missing`;
            }
            modal.classList.remove('hidden');
        }

        function cmCloseMissingDocument() {
            const modal = document.getElementById('missingDocModal');
            modal.classList.add('hidden');
            const textarea = modal.querySelector('textarea[name="message"]');
            if (textarea) textarea.value = '';
        }

        function cmOpenRejectModal(appId) {
            const form = document.getElementById('rejectForm');
            if (form) {
                form.action = `${baseCM}/${appId}/reject`;
            }
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function cmCloseRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            const form = document.getElementById('rejectForm');
            if (form) form.reset();
        }
    </script>

    <script src="{{ asset('js/case_manager/dashboard.js') }}"></script>
@endsection
