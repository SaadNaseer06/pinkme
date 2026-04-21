@extends('case_manager.layouts.app')

@section('title', 'Dashboard')

@section('content')

    <!---Main -->
    <main class="flex-1">

        <div class="max-w-8xl mx-auto">
            <!-- Status Cards -->
            @include('case_manager.partials.cards', ['cm' => $cm])

            <!-- Charts Section -->
            <div class="grid grid-cols-1 md:grid-cols-[60rem_36rem] gap-6 charts-section">
                <!-- Weekly Statistics -->
                <div class="bg-[#F3E8EF] rounded-xl p-6 ">
                    <!-- Header -->
                    <div class="mb-4 border-b border-[#DCCFD8] pb-4 ">
                        <h2 class="text-lg font-semibold text-[#213430] app-main">Application &amp; program request stats</h2>
                        <p class="text-xs text-[#91848C] app-text mt-1">Last 7 days · legacy applications + program registrations you can access</p>
                        <!--<div class="flex items-center gap-2 text-sm text-[#91848C]">-->
                        <!--    <div class="bg-[#FDD7EC] p-1 rounded-md">-->
                        <!--        <img src="{{ asset('public/images/Calender.svg') }}" alt="Calendar" class="w-5 h-5" />-->
                        <!--    </div>-->
                        <!--    <div class="flex items-center gap-1">-->
                        <!--        <span>Week</span>-->
                        <!--        <img src="{{ asset('public/images/down-arrow.svg') }}" alt="Down" class="w-3 h-3" />-->
                        <!--    </div>-->
                        <!--</div>-->
                    </div>

                    <!-- Chart -->
                    <div class="relative h-72 flex items-end">
                        <!-- Y-axis -->
                        <div class="flex flex-col justify-between h-full text-xs text-gray-400 pr-3 leading-none">
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

                        <!-- Bars (dynamic + toggle-able) -->
                        <div class="flex items-end space-x-[120px] h-full z-10 pl-4 chart-bars">
                            @foreach ($cm['weeklyBars'] as $bar)
                                <div class="flex flex-col items-center w-2 h-full bars-width">
                                    <div class="flex flex-col justify-end h-full w-full">
                                        <div class="segment segment-apps w-full bg-[#9E2469] rounded-t-full"
                                            style="height: {{ $bar['apps'] }}%; transition: height .25s ease"
                                            data-height="{{ $bar['apps'] }}"></div>
                                        <div class="segment segment-approved w-full bg-[#20B354]"
                                            style="height: {{ $bar['approved'] }}%; transition: height .25s ease"
                                            data-height="{{ $bar['approved'] }}"></div>
                                        <div class="segment segment-rejected w-full bg-[#B32020] rounded-b-full"
                                            style="height: {{ $bar['rejected'] }}%; transition: height .25s ease"
                                            data-height="{{ $bar['rejected'] }}"></div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-2 ">{{ $bar['label'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="flex justify-around mt-6 gap-6 flex-wrap text-sm text-[#213430] laptop-slider">
                        <!-- Applications -->
                        <div class="flex items-center gap-2 text-[14px] laptop-slider-gap" style="color: #9E2469">
                            <label class="switch mini" style="color: #9E2469">
                                <input id="toggle-apps" type="checkbox" checked />
                                <span class="slider"><span class="circle"></span></span>
                            </label>
                            <label for="toggle-apps">Applications</label>
                        </div>

                        <!-- Approved -->
                        <div class="flex items-center gap-2 text-sm laptop-slider-gap" style="color: #20b354">
                            <label class="switch mini">
                                <input id="toggle-approved" type="checkbox" checked />
                                <span class="slider"><span class="circle"></span></span>
                            </label>
                            <label for="toggle-approved">Approved</label>
                        </div>

                        <!-- Rejected -->
                        <div class="flex items-center gap-2 text-sm laptop-slider-gap" style="color: #b32020">
                            <label class="switch mini">
                                <input id="toggle-rejected" type="checkbox" checked />
                                <span class="slider"><span class="circle"></span></span>
                            </label>
                            <label for="toggle-rejected">Rejected</label>
                        </div>
                    </div>
                </div>

                <!-- Acquisitions -->
                <div class="bg-[#F3E8EF] rounded-lg p-6">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-4 pb-4 border-b border-[#DCCFD8]">
                        <h2 class="text-lg font-semibold text-[#213430] app-main">Acquisitions</h2>
                        <!--<div class="flex items-center gap-2 text-sm text-[#91848C]">-->
                        <!--    <div class="bg-[#FDD7EC] p-1 rounded-md">-->
                        <!--        <img src="{{ asset('public/images/Calender.svg') }}" alt="Calendar" class="w-5 h-5" />-->
                        <!--    </div>-->
                        <!--    <div class="flex items-center gap-1">-->
                        <!--        <span>Month</span>-->
                        <!--        <img src="{{ asset('public/images/down-arrow.svg') }}" alt="Down" class="w-3 h-3" />-->
                        <!--    </div>-->
                        <!--</div>-->
                    </div>

                    <!-- Progress Items -->
                    <div class="space-y-4">
                        <div class="divide-y divide-dashed divide-[#DCCFD8]">
                            <!-- Applications -->
                            <div class="flex items-center justify-between py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-[#9E2469]"></div>
                                    <span class="text-md text-[#213430] font-medium app-text ">Applications</span>
                                </div>
                                <div class="flex items-center gap-2 w-2/3">
                                    <div class="w-full h-2 bg-[#DCCFD8] rounded-full overflow-hidden bars-height">
                                        <div class="h-full bg-[#9E2469] rounded-full"
                                            style="width: {{ $cm['acqPct']['applications'] }}%"></div>
                                    </div>
                                    <span
                                        class="text-sm text-[#213430] font-semibold app-text">{{ $cm['acqPct']['applications'] }}%</span>
                                </div>
                            </div>

                            <!-- Shortlisted (Under Review) -->
                            <div class="flex items-center justify-between py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-[#F7BF14]"></div>
                                    <span class="text-md text-[#213430] font-medium app-text">Shortlisted</span>
                                </div>
                                <div class="flex items-center gap-2 w-2/3">
                                    <div class="w-full h-2 bg-[#DCCFD8] rounded-full overflow-hidden bars-height">
                                        <div class="h-full bg-[#F7BF14] rounded-full"
                                            style="width: {{ $cm['acqPct']['shortlisted'] }}%"></div>
                                    </div>
                                    <span
                                        class="text-sm text-[#213430] font-semibold app-text">{{ $cm['acqPct']['shortlisted'] }}%</span>
                                </div>
                            </div>

                            <!-- Rejected -->
                            <div class="flex items-center justify-between py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-[#B32020]"></div>
                                    <span class="text-md text-[#213430] font-medium app-text">Rejected</span>
                                </div>
                                <div class="flex items-center gap-2 w-2/3">
                                    <div class="w-full h-2 bg-[#DCCFD8] rounded-full overflow-hidden bars-height">
                                        <div class="h-full bg-[#B32020] rounded-full"
                                            style="width: {{ $cm['acqPct']['rejected'] }}%"></div>
                                    </div>
                                    <span
                                        class="text-sm text-[#213430] font-semibold app-text">{{ $cm['acqPct']['rejected'] }}%</span>
                                </div>
                            </div>

                            <!-- Pending -->
                            <div class="flex items-center justify-between py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-[#91848C]"></div>
                                    <span class="text-md text-[#213430] font-medium app-text">Pending</span>
                                </div>
                                <div class="flex items-center gap-2 w-2/3">
                                    <div class="w-full h-2 bg-[#DCCFD8] rounded-full overflow-hidden bars-height">
                                        <div class="h-full bg-[#91848C] rounded-full"
                                            style="width: {{ $cm['acqPct']['pending'] }}%"></div>
                                    </div>
                                    <span
                                        class="text-sm text-[#213430] font-semibold app-text">{{ $cm['acqPct']['pending'] }}%</span>
                                </div>
                            </div>

                            <!-- Approved -->
                            <div class="flex items-center justify-between py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-[#20B354]"></div>
                                    <span class="text-md text-[#213430] font-medium app-text">Approved</span>
                                </div>
                                <div class="flex items-center gap-2 w-2/3">
                                    <div class="w-full h-2 bg-[#DCCFD8] rounded-full overflow-hidden bars-height">
                                        <div class="h-full bg-[#20B354] rounded-full"
                                            style="width: {{ $cm['acqPct']['approved'] }}%"></div>
                                    </div>
                                    <span
                                        class="text-sm text-[#213430] font-semibold app-text">{{ $cm['acqPct']['approved'] }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Daily Statistics Area Chart -->
            <div class="max-w-8xl mt-5">
                <div class="bg-[#F3E8EF] p-4 rounded-xl ">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-4 border-b border-[#DCCFD8] pb-4">
                        <h2 class="text-lg font-semibold text-[#213430] app-main">Statistics Of Applications</h2>
                        <!--<div class="flex items-center gap-2 text-sm text-[#91848C]">-->
                        <!--    <div class="bg-[#FDD7EC] p-1 rounded-md">-->
                        <!--        <img src="{{ asset('public/images/Calender.svg') }}" alt="Calendar" class="w-5 h-5" />-->
                        <!--    </div>-->
                        <!--    <div class="flex items-center gap-1">-->
                        <!--        <span>Week</span>-->
                        <!--        <img src="{{ asset('public/images/down-arrow.svg') }}" alt="Down" class="w-3 h-3" />-->
                        <!--    </div>-->
                        <!--</div>-->
                    </div>

                    <!-- Chart -->
                    <div class="relative h-[240px] w-full">
                        <canvas id="statsChart"></canvas>
                    </div>
                </div>
            </div>
    </main>

    {{-- Legend toggle script for the stacked bars --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>

   
@endsection
