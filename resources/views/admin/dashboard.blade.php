@php
    use Illuminate\Support\Facades\Auth;
    use App\Models\Application;
    use Carbon\Carbon;

    // Get the time period from request, default to 'week'
    $timePeriod = request()->get('period', 'week');

    // For admin, get all applications (no reviewer_id filter)
    $q = Application::query();

    $totalCount = (clone $q)->count();
    $approvedCount = (clone $q)->where('status', 'approved')->count();
    $rejectedCount = (clone $q)->where('status', 'rejected')->count();
    $pendingCount = (clone $q)->where('status', 'pending')->count();
    $underReviewCount = (clone $q)->where('status', 'under_review')->count();
    $latestPatients = App\Models\Patient::with('user')
        ->select('patients.*')
        ->join(DB::raw('(SELECT MAX(id) as id FROM patients GROUP BY user_id) as latest'), function ($join) {
            $join->on('patients.id', '=', 'latest.id');
        })
        ->orderBy('patients.created_at', 'desc')
        ->take(5)
        ->get();

    $acqCounts = [
        'applications' => $totalCount,
        'shortlisted' => $underReviewCount,
        'rejected' => $rejectedCount,
        'pending' => $pendingCount,
        'approved' => $approvedCount,
    ];

    $maxAcq = max(1, max($acqCounts));
    $acqPct = array_map(fn($c) => (int) round(($c / $maxAcq) * 100), $acqCounts);

    // Function to get chart data based on time period
    function getChartData($query, $period)
    {
        $chartData = [];

        switch ($period) {
            case 'day':
                // Last 24 hours by 4-hour intervals
                for ($i = 5; $i >= 0; $i--) {
                    $start = Carbon::now()->subHours($i * 4);
                    $end = $start->copy()->addHours(4);
                    $label = $start->format('H:i');

                    $intervalTotal = (clone $query)->whereBetween('created_at', [$start, $end])->count();
                    $intervalApproved = (clone $query)
                        ->where('status', 'approved')
                        ->whereBetween('created_at', [$start, $end])
                        ->count();
                    $intervalRejected = (clone $query)
                        ->where('status', 'rejected')
                        ->whereBetween('created_at', [$start, $end])
                        ->count();
                    $intervalRemain = max(0, $intervalTotal - $intervalApproved - $intervalRejected);

                    if ($intervalTotal > 0) {
                        $appsPct = (int) round(($intervalRemain / $intervalTotal) * 100);
                        $approvedPct = (int) round(($intervalApproved / $intervalTotal) * 100);
                        $rejectedPct = max(0, 100 - $appsPct - $approvedPct);
                    } else {
                        $appsPct = $approvedPct = $rejectedPct = 0;
                    }

                    $chartData[$label] = [
                        'apps' => $appsPct,
                        'approved' => $approvedPct,
                        'rejected' => $rejectedPct,
                    ];
                }
                break;

            case 'week':
                // Last 7 days
                $weekdayToLabel = [1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun'];
                for ($i = 6; $i >= 0; $i--) {
                    $day = Carbon::today()->subDays($i);
                    $label = $weekdayToLabel[(int) $day->isoWeekday()];

                    $dayTotal = (clone $query)->whereDate('created_at', $day->toDateString())->count();
                    $dayApproved = (clone $query)
                        ->where('status', 'approved')
                        ->whereDate('created_at', $day->toDateString())
                        ->count();
                    $dayRejected = (clone $query)
                        ->where('status', 'rejected')
                        ->whereDate('created_at', $day->toDateString())
                        ->count();
                    $dayRemain = max(0, $dayTotal - $dayApproved - $dayRejected);

                    if ($dayTotal > 0) {
                        $appsPct = (int) round(($dayRemain / $dayTotal) * 100);
                        $approvedPct = (int) round(($dayApproved / $dayTotal) * 100);
                        $rejectedPct = max(0, 100 - $appsPct - $approvedPct);
                    } else {
                        $appsPct = $approvedPct = $rejectedPct = 0;
                    }

                    $chartData[$label] = [
                        'apps' => $appsPct,
                        'approved' => $approvedPct,
                        'rejected' => $rejectedPct,
                    ];
                }
                break;

            case 'month':
                // Last 4 weeks
                for ($i = 3; $i >= 0; $i--) {
                    $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
                    $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();
                    $label = 'W' . (4 - $i);

                    $weekTotal = (clone $query)->whereBetween('created_at', [$weekStart, $weekEnd])->count();
                    $weekApproved = (clone $query)
                        ->where('status', 'approved')
                        ->whereBetween('created_at', [$weekStart, $weekEnd])
                        ->count();
                    $weekRejected = (clone $query)
                        ->where('status', 'rejected')
                        ->whereBetween('created_at', [$weekStart, $weekEnd])
                        ->count();
                    $weekRemain = max(0, $weekTotal - $weekApproved - $weekRejected);

                    if ($weekTotal > 0) {
                        $appsPct = (int) round(($weekRemain / $weekTotal) * 100);
                        $approvedPct = (int) round(($weekApproved / $weekTotal) * 100);
                        $rejectedPct = max(0, 100 - $appsPct - $approvedPct);
                    } else {
                        $appsPct = $approvedPct = $rejectedPct = 0;
                    }

                    $chartData[$label] = [
                        'apps' => $appsPct,
                        'approved' => $approvedPct,
                        'rejected' => $rejectedPct,
                    ];
                }
                break;

            case 'all':
                // Last 6 months
                for ($i = 5; $i >= 0; $i--) {
                    $month = Carbon::now()->subMonths($i);
                    $label = $month->format('M');

                    $monthTotal = (clone $query)
                        ->whereYear('created_at', $month->year)
                        ->whereMonth('created_at', $month->month)
                        ->count();
                    $monthApproved = (clone $query)
                        ->where('status', 'approved')
                        ->whereYear('created_at', $month->year)
                        ->whereMonth('created_at', $month->month)
                        ->count();
                    $monthRejected = (clone $query)
                        ->where('status', 'rejected')
                        ->whereYear('created_at', $month->year)
                        ->whereMonth('created_at', $month->month)
                        ->count();
                    $monthRemain = max(0, $monthTotal - $monthApproved - $monthRejected);

                    if ($monthTotal > 0) {
                        $appsPct = (int) round(($monthRemain / $monthTotal) * 100);
                        $approvedPct = (int) round(($monthApproved / $monthTotal) * 100);
                        $rejectedPct = max(0, 100 - $appsPct - $approvedPct);
                    } else {
                        $appsPct = $approvedPct = $rejectedPct = 0;
                    }

                    $chartData[$label] = [
                        'apps' => $appsPct,
                        'approved' => $approvedPct,
                        'rejected' => $rejectedPct,
                    ];
                }
                break;
        }

        return $chartData;
    }

    $chartData = getChartData($q, $timePeriod);

    // Time period labels
    $periodLabels = [
        'day' => 'Day',
        'week' => 'Week',
        'month' => 'Month',
        'all' => 'All Time',
    ];

    $short = function ($n) {
        return $n >= 1000 ? number_format($n / 1000, 2) . 'K' : number_format($n);
    };
@endphp

@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <!---Main -->
    <main class="flex-1">
        <div class="max-w-8xl mx-auto">
            {{-- Cards Section --}}
            @include('admin.partials.cards')

            <!-- Charts Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 charts-section">
                <div class="bg-[#F3E8EF] rounded-lg p-3">
                    <div class="flex justify-between items-center mb-4 mt-3">
                        <h2 class="text-lg font-semibold text-[#213430] px-3 pt-3 app-main">
                            Recent Applications
                        </h2>
                    </div>
                    <div class="table-container">
                        <table class="min-w-full text-sm text-left">
                            <thead>
                                <tr class="border-t border-[#e0cfd8]">
                                    <th class="p-3 text-lg text-[#91848C] font-medium app-h">Name</th>
                                    <th class="p-3 text-lg text-[#91848C] font-medium app-h pad-left">App ID</th>
                                    <th class="p-3 text-lg text-[#91848C] font-medium app-h">Sub. Date</th>
                                    <th class="p-3 text-lg text-[#91848C] font-medium app-h">Status</th>
                                    <th class="p-3 text-lg text-[#91848C] font-medium app-h">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @forelse($recentApplications ?? [] as $application)
                                    <tr class="border-t border-[#e0cfd8]">
                                        <td class="p-2">
                                            <div class="flex items-center gap-2">
                                                {{-- <img src="{{ $application->patient->user->profile->avatar ?? '' }}"
                                                    alt="" class="w-8 h-8 rounded-full" /> --}}
                                                <span
                                                    class="text-[#91848C] text-[16px] font-light app-text">{{ $application->patient->user->profile->full_name ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td
                                            class="p-3 align-middle text-[#91848C] text-[16px] font-light app-text pad-left">
                                            APP-{{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td class="p-3 align-middle text-[#91848C] text-[16px] font-light app-text">
                                            {{ $application->submission_date ? $application->submission_date->format('Y-m-d') : 'N/A' }}
                                        </td>
                                        <td class="p-3 align-middle">
                                            <span
                                                class="inline-flex items-center gap-1 text-sm font-light app-text
                                            @if ($application->status === 'pending') text-[#8E7C93]
                                            @elseif($application->status === 'approved') text-[#20B354]
                                            @elseif($application->status === 'rejected') text-[#B32020]
                                            @else text-[#91848C] @endif">
                                                <span
                                                    class="w-2 h-2 rounded-full
                                                @if ($application->status === 'pending') bg-[#8E7C93]
                                                @elseif($application->status === 'approved') bg-[#20B354]
                                                @elseif($application->status === 'rejected') bg-[#B32020]
                                                @else bg-[#91848C] @endif"></span>
                                                {{ ucfirst($application->status) }}
                                            </span>
                                        </td>
                                        @php
                                            $appViewUrl = route('admin.viewApplication', $application->id);
                                            $appFilterId = $application->code ?? $application->id;
                                            $appListUrl = route('admin.applications', ['q' => $appFilterId]);
                                            $patientEmail = optional($application->patient?->user)->email;
                                        @endphp
                                        <td class="p-3">
                                            <div class="action-menu-wrapper relative inline-block" data-action-wrapper>
                                                <button type="button" data-action-toggle
                                                    class="text-[#213430] p-2 rounded-md focus:outline-none hover:text-[#db69a2] transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                    </svg>
                                                </button>
                                                <div data-action-menu class="patient-action-menu absolute right-0 top-10 z-20 hidden">
                                                    <a href="{{ $appViewUrl }}" class="patient-action-item">
                                                        <i class="fas fa-eye patient-action-icon"></i>
                                                        <span>View Application</span>
                                                    </a>
                                                    <a href="{{ $appListUrl }}" class="patient-action-item">
                                                        <img src="{{ asset('public/images/assign.svg') }}" alt="" class="patient-action-icon">
                                                        <span>Open Applications List</span>
                                                    </a>
                                                    {{-- @if (!empty($patientEmail))
                                                        <a href="mailto:{{ $patientEmail }}" class="patient-action-item">
                                                            <i class="fas fa-envelope patient-action-icon"></i>
                                                            <span>Email Patient</span>
                                                        </a>
                                                    @endif --}}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="border-t border-[#e0cfd8]">
                                        <td colspan="5"
                                            class="p-3 text-center text-[#91848C] text-[16px] font-light app-text">
                                            No recent applications found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="flex justify-between items-center">
                        <div class="mt-3 flex justify-start">
                            <h1 class="text-md text-[#91848C] font-light app-text px-3">
                                {{-- Showing {{ $recentApplications->count() }} of {{ $stats['total_applications'] ?? 0 }} Applications --}}
                            </h1>
                        </div>
                    </div>
                </div>

                <!-- Weekly Statistics Chart -->
                <div class="bg-[#F3E8EF] rounded-xl p-3 relative">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-4 border-b border-[#DCCFD8] pb-4 relative z-30">
                        <h2 class="text-lg font-semibold text-[#213430] px-3 pt-6 app-main">
                            Application Stats
                        </h2>
                        <div class="flex items-center gap-2 text-sm text-[#91848C] pt-6 px-3 relative">
                            <div class="bg-[#FDD7EC] p-1 rounded-md">
                                <img src="{{ asset('public/images/Calender.svg') }}" alt="Calendar" class="w-5 h-5" />
                            </div>
                            <div class="relative z-50">
                                <button id="periodDropdown"
                                    class="flex items-center gap-1 hover:text-[#213430] transition-colors bg-white px-3 py-2 rounded-lg border border-[#DCCFD8] shadow-sm">
                                    <span id="currentPeriod">{{ $periodLabels[$timePeriod] }}</span>
                                    <img src="{{ asset('public/images/down-arrow.svg') }}" alt="Down"
                                        class="w-3 h-3 transition-transform duration-200" id="dropdownIcon" />
                                </button>
                                <div id="periodOptions"
                                    class="absolute right-0 top-full mt-2 bg-white rounded-lg shadow-xl border border-[#DCCFD8] hidden min-w-[140px] z-50">
                                    @foreach ($periodLabels as $key => $label)
                                        <a href="{{ request()->url() }}?period={{ $key }}"
                                            class="block px-4 py-3 text-sm text-[#213430] hover:bg-[#F3E8EF] transition-colors first:rounded-t-lg last:rounded-b-lg {{ $timePeriod === $key ? 'bg-[#F3E8EF] font-medium text-[#db69a2]' : '' }}">
                                            {{ $label }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart -->
                    <div class="relative h-72 flex items-end mt-10 z-10">
                        <!-- Y-axis -->
                        <div class="flex flex-col justify-between h-full text-xs text-gray-400 pr-3 leading-none z-20">
                            <span>100%</span>
                            <span>80%</span>
                            <span>60%</span>
                            <span>40%</span>
                            <span>20%</span>
                            <span>0%</span>
                        </div>

                        <!-- Grid lines -->
                        <div class="absolute left-10 right-4 top-0 bottom-6 flex flex-col justify-between z-0">
                            <div class="w-full border-t border-dashed border-gray-300"></div>
                            <div class="w-full border-t border-dashed border-gray-300"></div>
                            <div class="w-full border-t border-dashed border-gray-300"></div>
                            <div class="w-full border-t border-dashed border-gray-300"></div>
                            <div class="w-full border-t border-dashed border-gray-300"></div>
                            <div class="w-full border-t border-dashed border-gray-300"></div>
                        </div>

                        <!-- Bars (Dynamic + Toggle-able) -->
                        <div class="flex items-end justify-between h-full z-20 pl-4 laptop-bar w-full pr-4">
                            @foreach ($chartData as $label => $bar)
                                <div class="flex flex-col items-center w-2 h-full mx-1">
                                    <div class="flex flex-col justify-end h-full w-full">
                                        <!-- Applications (pink, top) -->
                                        <div class="segment segment-apps w-full bg-[#db69a2] rounded-t-full"
                                            style="height: {{ $bar['apps'] }}%; transition: height .25s ease"
                                            data-height="{{ $bar['apps'] }}"></div>

                                        <!-- Approved (green, middle) -->
                                        <div class="segment segment-approved w-full bg-[#20B354]"
                                            style="height: {{ $bar['approved'] }}%; transition: height .25s ease"
                                            data-height="{{ $bar['approved'] }}"></div>

                                        <!-- Rejected (red, bottom) -->
                                        <div class="segment segment-rejected w-full bg-[#B32020] rounded-b-full"
                                            style="height: {{ $bar['rejected'] }}%; transition: height .25s ease"
                                            data-height="{{ $bar['rejected'] }}"></div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-2">{{ $label }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Legend -->
                    <div
                        class="flex justify-around mt-6 gap-6 flex-wrap text-sm text-[#213430] laptop-slider z-20 relative">
                        <!-- Applications -->
                        <div class="flex items-center gap-2 text-[14px] laptop-slider-gap" style="color: #db69a2">
                            <label class="switch mini" style="color: #db69a2">
                                <input id="toggle-apps" type="checkbox" checked />
                                <span class="slider">
                                    <span class="circle"></span>
                                </span>
                            </label>
                            <label for="toggle-apps">Applications</label>
                        </div>

                        <!-- Approved -->
                        <div class="flex items-center gap-2 text-sm laptop-slider-gap" style="color: #20b354">
                            <label class="switch mini">
                                <input id="toggle-approved" type="checkbox" checked />
                                <span class="slider">
                                    <span class="circle"></span>
                                </span>
                            </label>
                            <label for="toggle-approved">Approved</label>
                        </div>

                        <!-- Rejected -->
                        <div class="flex items-center gap-2 text-sm laptop-slider-gap" style="color: #b32020">
                            <label class="switch mini">
                                <input id="toggle-rejected" type="checkbox" checked />
                                <span class="slider">
                                    <span class="circle"></span>
                                </span>
                            </label>
                            <label for="toggle-rejected">Rejected</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-[#F3E8EF] rounded-lg p-6 mt-10">
                <div class="flex justify-between items-center mb-2 pb-4">
                    <h2 class="text-xl font-medium text-[#213430] app-main">
                        Patients Lists
                    </h2>
                    {{-- <a href="{{ route('patients.all') }}" --}}
                    {{-- <a href="#" class="text-lg font-medium text-decoration underline text-[#db69a2] app-main">
                        View All
                    </a> --}}
                </div>

                <div class="table-container">
                    <table class="min-w-full text-sm text-left mt-2">
                        <thead>
                            <tr class="border-t border-[#e0cfd8]">
                                <th class="p-2 text-lg text-[#91848C] font-medium app-h">Patient</th>
                                <th class="p-2 text-lg text-[#91848C] font-medium app-h px-10">Email</th>
                                <th class="p-2 text-lg text-[#91848C] font-medium app-h">Contact</th>
                                <th class="p-2 text-lg text-[#91848C] font-medium app-h">Age</th>
                                <th class="p-2 text-lg text-[#91848C] font-medium app-h">Disease</th>
                                <th class="p-2 text-lg text-[#91848C] font-medium app-h">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            @forelse($latestPatients as $patient)
                                <tr class="border-t border-[#e0cfd8]">
                                    <td class="p-3">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $patient->user ? $patient->user->avatar_url : asset('public/images/profile.png') }}" alt=""
                                                class="w-8 h-8 rounded-full" />
                                            <span class="text-[#91848C] text-[16px] font-light app-text">
                                                {{ $patient->user->name ?? 'Unknown' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text px-10">
                                        {{ $patient->user->email ?? 'N/A' }}
                                    </td>
                                    <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                        {{ $patient->user->profile->phone ?? 'N/A' }}
                                    </td>
                                    <td class="p-2 align-middle">
                                        <span class="inline-flex items-center gap-1 text-[#8E7C93] text-sm app-text">
                                            @php
                                                $dob = $patient->user->profile->date_of_birth ?? null;
                                                $age = $dob ? \Carbon\Carbon::parse($dob)->age : 'N/A';
                                            @endphp
                                            {{ $age }}
                                        </span>
                                    </td>
                                    <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                        {{ $patient->diagnosis ?? 'N/A' }}
                                    </td>
                                    @php
                                        $patientQuery = $patient->user->email
                                            ?? ($patient->user->name ?? $patient->id);
                                        $applicationsUrl = route('admin.applications', ['q' => $patientQuery]);
                                        $patientsUrl = route('admin.patients', ['q' => $patientQuery]);
                                    @endphp
                                    <td class="p-2 align-middle">
                                        <div class="action-menu-wrapper relative inline-block" data-action-wrapper>
                                            <button type="button" data-action-toggle
                                                class="text-[#213430] p-2 rounded-md focus:outline-none hover:text-[#db69a2] transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                </svg>
                                            </button>
                                            <div data-action-menu
                                                class="patient-action-menu absolute right-0 top-10 z-20 hidden">
                                                <a href="{{ $patientsUrl }}" class="patient-action-item">
                                                    <i class="fas fa-user-circle patient-action-icon"></i>
                                                    <span>View in Patients List</span>
                                                </a>
                                                <a href="{{ $applicationsUrl }}" class="patient-action-item">
                                                    <img src="{{ asset('public/images/assign.svg') }}" alt="" class="patient-action-icon">
                                                    <span>View Applications</span>
                                                </a>
                                                @if (!empty($patient->user->email))
                                                    <a href="mailto:{{ $patient->user->email }}"
                                                        class="patient-action-item">
                                                        <i class="fas fa-envelope patient-action-icon"></i>
                                                        <span>Email Patient</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-[#91848C] py-4">No patients found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    {{-- JavaScript for dropdown and chart toggles --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dropdown functionality
            const dropdownBtn = document.getElementById('periodDropdown');
            const dropdownOptions = document.getElementById('periodOptions');
            const dropdownIcon = document.getElementById('dropdownIcon');
            const closeActionMenus = () => {
                document.querySelectorAll('[data-action-menu]').forEach(menu => {
                    menu.classList.add('hidden');
                });
            };

            if (dropdownBtn && dropdownOptions) {
                dropdownBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropdownOptions.classList.toggle('hidden');
                    dropdownIcon.classList.toggle('rotate-180');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!dropdownBtn.contains(e.target) && !dropdownOptions.contains(e.target)) {
                        dropdownOptions.classList.add('hidden');
                        dropdownIcon.classList.remove('rotate-180');
                    }
                });

                // Prevent dropdown from closing when clicking inside options
                dropdownOptions.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }

            // Action dropdowns
            const actionButtons = document.querySelectorAll('[data-action-toggle]');
            const actionMenus = document.querySelectorAll('[data-action-menu]');

            actionButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const menu = button.nextElementSibling;
                    if (!menu) {
                        return;
                    }

                    const shouldOpen = menu.classList.contains('hidden');
                    closeActionMenus();
                    if (shouldOpen) {
                        menu.classList.remove('hidden');
                    }
                });
            });

            actionMenus.forEach(menu => {
                menu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('[data-action-wrapper]')) {
                    closeActionMenus();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeActionMenus();
                }
            });

            // Chart toggle functionality
            const toggles = {
                apps: document.getElementById('toggle-apps'),
                approved: document.getElementById('toggle-approved'),
                rejected: document.getElementById('toggle-rejected'),
            };

            function setSegmentVisibility(segmentClass, show) {
                document.querySelectorAll('.' + segmentClass).forEach(el => {
                    const h = el.getAttribute('data-height') || '0';
                    el.style.height = show ? (h + '%') : '0%';
                });
            }

            if (toggles.apps && toggles.approved && toggles.rejected) {
                // Initialize
                setSegmentVisibility('segment-apps', toggles.apps.checked);
                setSegmentVisibility('segment-approved', toggles.approved.checked);
                setSegmentVisibility('segment-rejected', toggles.rejected.checked);

                // Events
                toggles.apps.addEventListener('change', e => setSegmentVisibility('segment-apps', e.target
                    .checked));
                toggles.approved.addEventListener('change', e => setSegmentVisibility('segment-approved', e.target
                    .checked));
                toggles.rejected.addEventListener('change', e => setSegmentVisibility('segment-rejected', e.target
                    .checked));
            }

            // Add loading animation when period is changed
            const periodLinks = document.querySelectorAll('#periodOptions a');
            periodLinks.forEach(link => {
                link.addEventListener('click', function() {
                    // Show loading state
                    const chartContainer = document.querySelector('.laptop-bar');
                    if (chartContainer) {
                        chartContainer.style.opacity = '0.5';
                        chartContainer.style.pointerEvents = 'none';
                    }
                });
            });
        });
    </script>

    <style>
        .rotate-180 {
            transform: rotate(180deg);
        }

        /* Ensure dropdown appears above chart elements */
        #periodOptions {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
        }

        /* Improve dropdown hover effects */
        #periodOptions a:hover {
            background-color: #F3E8EF;
            transform: translateX(2px);
        }

        /* Loading state for chart */
        .chart-loading {
            opacity: 0.5;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .patient-action-menu {
            min-width: 12rem;
            background-color: #ffffff;
            border: 1px solid #DCCFD8;
            border-radius: 0.75rem;
            box-shadow: 0 12px 30px rgba(33, 52, 48, 0.15);
            padding: 0.35rem 0;
        }

        .patient-action-item {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.55rem 1rem;
            font-size: 0.875rem;
            color: #213430;
            text-decoration: none;
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        .patient-action-item + .patient-action-item {
            border-top: 1px solid rgba(220, 207, 216, 0.6);
        }

        .patient-action-item:hover {
            background-color: #F3E8EF;
            color: #213430;
        }

        .patient-action-icon {
            width: 1.1rem;
            height: 1.1rem;
            color: #DB69A2;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
        }

        img.patient-action-icon {
            display: block;
            width: 1.1rem;
            height: 1.1rem;
            object-fit: contain;
        }
    </style>
@endsection
