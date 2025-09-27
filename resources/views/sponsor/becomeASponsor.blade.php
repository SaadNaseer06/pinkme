@extends('sponsor.layouts.app')

@section('title', 'Become A Sponsor')

@section('content')
    <div class="flex-1 flex flex-col">
        <main class="flex-1">
            <div class="max-w-8xl mx-auto">
                @include('sponsor.partials.cards')
            </div>

            
            @if (session('success') || $errors->any())
                <div class="max-w-4xl mx-auto px-4 mt-4 space-y-3">
                    @if (session('success'))
                        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="rounded-xl border border-[#F8D7DA] bg-[#FFF5F6] px-4 py-3">
                            <p class="text-sm font-semibold text-[#B32020]">We couldn't process your sponsorship.</p>
                            <ul class="mt-2 list-disc list-inside text-sm text-[#7B5268] space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            @php
    use Carbon\Carbon;
    use Illuminate\Support\Str;

    $imagePool = ['program-1.png', 'program-2.png', 'program-3.png', 'program-4.png', 'program-5.png'];
    $fundImagePool = ['S-1.png', 'S-2.png', 'S-3.png', 'S-4.png'];

    $defaultCommitment = $commitmentMessage ?? 'This program requires one sponsor to fund 100% of the costs.';

    $featuredProgram = $upcomingPrograms->first() ?? $ongoingPrograms->first();
    $featuredMonth = $featuredProgram?->month_label ?? ($featuredProgram?->event_date ? Carbon::parse($featuredProgram->event_date)->format('M') : 'TBD');
    $featuredDay = $featuredProgram?->day_label ?? ($featuredProgram?->event_date ? Carbon::parse($featuredProgram->event_date)->format('d') : '--');
    $featuredDateLabel = $featuredProgram?->date_label ?? ($featuredProgram?->event_date ? Carbon::parse($featuredProgram->event_date)->format('F d, Y') : 'To be announced');
    $featuredTime = $featuredProgram?->time_label ?? ($featuredProgram?->event_time ? Carbon::parse($featuredProgram->event_time)->format('h:i A') : 'To be announced');
    $featuredSummary = $featuredProgram ? Str::limit(strip_tags($featuredProgram->description), 160) : 'Select a program above to view its details here.';
    $featuredDetail = $featuredProgram ? Str::limit(strip_tags($featuredProgram->description), 220) : 'Choose a program from the list to learn more about its impact.';
    $featuredCommitment = $featuredProgram?->commitment_message ?? $defaultCommitment;
    $featuredFundLabel = $featuredProgram?->fund_label ?? 'To be determined';
    $featuredProgramId = $featuredProgram?->id;
    $featuredFundAmount = $featuredProgram?->program_fund;

    $investmentHighlights = [
        'Underwrite 100% of the program costs so participants receive support at no charge.',
        'Unlock bespoke recognition across on-site materials and digital storytelling.',
        'Receive a comprehensive impact report once the initiative concludes.',
    ];
@endphp


            <div class="mb-8">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-2xl font-semibold text-[#213430] mb-4 program-h">Upcoming Programs</h2>
                </div>

                @forelse ($upcomingPrograms as $program)
                    @php
                        $programImage = asset('images/' . $imagePool[$loop->index % count($imagePool)]);
                        $monthLabel = $program->month_label ?? ($program->event_date ? Carbon::parse($program->event_date)->format('M') : 'TBD');
                        $dayLabel = $program->day_label ?? ($program->event_date ? Carbon::parse($program->event_date)->format('d') : '--');
                        $dateLabel = $program->date_label ?? ($program->event_date ? Carbon::parse($program->event_date)->format('F d, Y') : 'To be announced');
                        $timeLabel = $program->time_label ?? ($program->event_time ? Carbon::parse($program->event_time)->format('h:i A') : 'To be announced');
                        $commitmentText = $program->commitment_message ?? $defaultCommitment;
                        $shortDescription = Str::limit(strip_tags($program->description), 140);
                        $detailDescription = Str::limit(strip_tags($program->description), 220);
                        $detailPayload = [
                            'id' => $program->id,
                            'title' => $program->title,
                            'description_short' => $shortDescription,
                            'description_long' => $detailDescription,
                            'month_label' => $monthLabel,
                            'day_label' => $dayLabel,
                            'date_label' => $dateLabel,
                            'time_label' => $timeLabel,
                            'commitment_text' => $commitmentText,
                            'fund_amount' => $program->program_fund !== null ? (float) $program->program_fund : null,
                            'fund_label' => $program->fund_label ?? 'To be determined',
                        ];
                    @endphp
                    <div class="bg-[#F3E8EF] rounded-lg p-4 mb-4 flex items-center justify-between md:flex hidden">
                        <div class="flex items-center">
                            <div class="flex flex-col items-center justify-center w-20 h-20 border-2 border-pink rounded-lg mr-4 bg-[#FFF7FC]">
                                <span class="text-sm text-pink">{{ $monthLabel }}</span>
                                <span class="text-4xl font-bold text-pink">{{ $dayLabel }}</span>
                            </div>
                            <div class="w-20 h-20 rounded-lg overflow-hidden mr-4">
                                <img src="{{ $programImage }}" alt="{{ $program->title }}" class="w-full h-full object-cover" />
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-[#213430] mb-1 program-h">{{ $program->title }}</h3>
                                <p class="text-sm text-[#91848C] program-p">{{ $shortDescription }}</p>
                                <p class="text-xs text-[#DB69A2] font-medium uppercase tracking-wide">Full Funding Required</p>
                                <span class="inline-flex items-center gap-2 px-3 py-1 mt-2 text-xs font-semibold text-white bg-[#DB69A2] rounded-full uppercase">Sponsor Covers 100%</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-4 text-sm items-end">
                            <div class="flex flex-col text-right">
                                <span class="text-[#91848C] app-text">Program Fund</span>
                                <span class="text-[#213430] font-medium app-text">{{ $program->fund_label ?? 'To be determined' }}</span>
                            </div>
                            <div class="flex items-center gap-6 text-sm">
                                <p class="text-[#91848C] app-text max-w-xs">{{ $commitmentText }}</p>
                                <button type="button" class="bg-transparent border border-[#213430] text-[#213430] py-3 px-8 rounded-lg program-btn" data-program='@json($detailPayload)'>
                                    Become The Sponsor
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bg-[#F3E8EF] rounded-lg p-3 mb-4 flex items-center justify-between md:hidden flex">
                        <div class="flex flex-col gap-2">
                            <div class="w-[80px] h-[80px] rounded-lg overflow-hidden mr-4">
                                <img src="{{ $programImage }}" alt="{{ $program->title }}" class="w-full h-full object-cover" />
                            </div>
                            <div class="flex flex-col items-center justify-center w-[80px] h-[60px] border-2 border-pink rounded-lg mr-4 bg-[#FFF7FC]">
                                <span class="text-sm text-pink">{{ $monthLabel }}</span>
                                <span class="text-4xl font-bold text-pink">{{ $dayLabel }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 w-full pl-4">
                            <h3 class="text-[15px] font-semibold text-[#213430]">{{ $program->title }}</h3>
                            <p class="text-[13px] font-light text-[#91848C]">{{ Str::limit(strip_tags($program->description), 110) }}</p>
                            <div class="flex justify-between text-[12px]">
                                <span class="text-[#91848C]">Program Fund</span>
                                <span class="text-[#213430] font-medium">{{ $program->fund_label ?? 'To be determined' }}</span>
                            </div>
                            <p class="text-xs text-[#DB69A2] font-medium uppercase">Full Funding Required</p>
                            <span class="text-[11px] font-semibold text-[#213430]">Sponsor covers 100% of costs</span>
                            <button type="button" class="bg-transparent border border-[#213430] text-[#213430] hover:bg-[#db69a2] hover:text-white hover:border-none py-2 px-6 rounded-lg" data-program='@json($detailPayload)'>
                                Become The Sponsor
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="bg-white/40 border border-[#DCCFD8] rounded-lg p-8 text-center text-[#91848C]">
                        No upcoming programs available at the moment.
                    </div>
                @endforelse
            </div>

            <div class="max-w-[97rem] mx-auto crousel-width">
                <div class="px-4 py-6 bg-[#F3E8EF] rounded-lg">
                    <div class="flex justify-between items-center pb-6">
                        <h1 class="text-2xl font-medium text-[#213430] program-h">Ongoing Support Program</h1>
                        <div class="flex space-x-2">
                            <button id="fundPrevBtn" class="bg-transparent border border-[#213430] text-[#213430] hover:bg-[#DB69A2] hover:border-none hover:text-white p-2 rounded-lg">
                                <svg class="md:h-6 h-4 md:w-6 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 18l-6-6 6-6" />
                                </svg>
                            </button>
                            <button id="fundNextBtn" class="bg-transparent border border-[#213430] text-[#213430] hover:bg-[#DB69A2] hover:border-none hover:text-white p-2 rounded-lg">
                                <svg class="md:h-6 h-4 md:w-6 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 18l6-6-6-6" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="relative overflow-hidden">
                        @if ($ongoingPrograms->isEmpty())
                            <div class="bg-white/40 border border-[#DCCFD8] rounded-lg p-8 text-center text-[#91848C]">
                                No active sponsorship programs right now.
                            </div>
                        @else
                            <div id="fundCarousel" class="carousel-container flex overflow-x-auto scrollbar-hide">
                                @foreach ($ongoingPrograms as $program)
                                    @php
                                        $image = asset('images/' . $fundImagePool[$loop->index % count($fundImagePool)]);
                                        $monthLabel = $program->month_label ?? ($program->event_date ? Carbon::parse($program->event_date)->format('M') : 'TBD');
                                        $dayLabel = $program->day_label ?? ($program->event_date ? Carbon::parse($program->event_date)->format('d') : '--');
                                        $dateLabel = $program->date_label ?? ($program->event_date ? Carbon::parse($program->event_date)->format('F d, Y') : 'To be announced');
                                        $timeLabel = $program->time_label ?? ($program->event_time ? Carbon::parse($program->event_time)->format('h:i A') : 'To be announced');
                                        $shortDescription = Str::limit(strip_tags($program->description), 120);
                                        $detailDescription = Str::limit(strip_tags($program->description), 220);
                                        $detailPayload = [
                            'id' => $program->id,
                                            'title' => $program->title,
                                            'description_short' => $shortDescription,
                                            'description_long' => $detailDescription,
                                            'month_label' => $monthLabel,
                                            'day_label' => $dayLabel,
                                            'date_label' => $dateLabel,
                                            'time_label' => $timeLabel,
                                            'commitment_text' => $program->commitment_message ?? $defaultCommitment,
                                            'fund_amount' => $program->program_fund !== null ? (float) $program->program_fund : null,
                                            'fund_label' => $program->fund_label ?? 'To be determined',
                                        ];
                                    @endphp
                                    <div class="carousel-item p-2 min-w-[300px] md:min-w-[330px] lg:min-w-[350px]">
                                        <div class="bg-[#F3E8EF] border border-[#DCCFD8] rounded-lg overflow-hidden hover:ring-2 ring-[#db69a2]">
                                            <div class="p-2">
                                                <img src="{{ $image }}" alt="{{ $program->title }}" class="w-full h-48 object-cover rounded" />
                                            </div>
                                            <div class="p-4 space-y-4">
                                                <h2 class="text-lg font-medium text-[#213430] app-main">{{ $program->title }}</h2>
                                                <p class="text-sm text-[#91848C] app-text">{{ $shortDescription }}</p>
                                                <div class="flex justify-between items-center text-sm">
                                                    <span class="text-[#91848C] app-text">Funding Type</span>
                                                    <span class="text-[#213430] font-medium app-text">Fully Funded - Sponsor Pays 100%</span>
                                                </div>
                                                <div class="flex justify-between items-center text-sm">
                                                    <span class="text-[#91848C] app-text">Program Fund</span>
                                                    <span class="text-[#213430] font-medium app-text">{{ $program->fund_label ?? 'To be determined' }}</span>
                                                </div>
                                                <button type="button" class="w-full py-3 text-[#91848C] font-medium border border-gray-300 rounded hover:bg-[#db69a2] hover:text-white hover:border-none rounded-lg app-main" data-program='@json($detailPayload)'>
                                                    Sponsor Entire Program
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div id="programDetailOverlay"
                 data-default-commitment="{{ $featuredCommitment }}"
                 data-initial-program-id="{{ old('program_id', $featuredProgramId) }}"
                 data-initial-amount="{{ old('amount', $featuredFundAmount) }}"
                 data-open-on-load="{{ $errors->any() ? 'true' : 'false' }}"
                 class="fixed inset-0 z-50 hidden items-start justify-center bg-black/40 backdrop-blur-sm px-4 py-10 overflow-y-auto">
                <div class="relative max-w-7xl w-full mx-auto">
                    <div class="bg-white/95 border border-[#E5CADB] rounded-[28px] shadow-2xl relative overflow-hidden">
                        <button type="button" class="absolute top-5 right-5 text-[#B08BA2] hover:text-[#7B5268] transition" data-program-modal-close>
                            <span class="sr-only">Close</span>
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <div class="grid grid-cols-1 lg:grid-cols-[60%_35%] gap-6 p-6 lg:p-8">
                            <div class="bg-white border border-[#E7CEDA] rounded-2xl p-6 shadow-sm">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6 border-b border-[#E7CEDA] pb-6">
                                    <div class="flex items-center gap-4">
                                        <div class="flex flex-col items-center justify-center w-20 h-20 border-2 border-[#DB69A2] rounded-2xl bg-[#FFF7FB] shadow-sm">
                                            <span class="text-sm text-[#DB69A2]" data-program-month>{{ $featuredMonth }}</span>
                                            <span class="text-4xl font-bold text-[#DB69A2]" data-program-day>{{ $featuredDay }}</span>
                                        </div>
                                        <div class="space-y-2">
                                            <h2 class="text-2xl font-semibold text-[#213430] app-main" data-program-title>{{ $featuredProgram->title ?? 'Select a Program' }}</h2>
                                            <p class="text-[#91848C] text-sm app-text max-w-xl" data-program-summary>{{ $featuredSummary }}</p>
                                            <span class="inline-flex items-center gap-2 px-3 py-1 text-xs font-semibold text-white bg-[#DB69A2] rounded-full uppercase tracking-wide">Full Funding Required</span>
                                        </div>
                                    </div>
                                    <div class="bg-[#FDF1F7] border border-[#E7CEDA] rounded-xl p-4 text-sm text-[#7B5268] w-full sm:w-60">
                                        <p class="font-medium app-text" data-program-commitment>{{ $featuredCommitment }}</p>
                                        <p class="mt-3"><span class="font-semibold">Time:</span> <span data-program-time>{{ $featuredTime }}</span></p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                    <div>
                                        <h3 class="text-lg font-semibold text-[#213430] mb-4 app-main">Program Overview</h3>
                                        <div class="space-y-3">
                                            <div class="flex items-center gap-3 bg-[#FFF7FB] border border-[#E7CEDA] rounded-xl px-4 py-3 shadow-sm">
                                                <i class="far fa-calendar text-[#DB69A2]"></i>
                                                <div>
                                                    <p class="text-xs uppercase tracking-wide text-[#B08BA2]">Date</p>
                                                    <p class="text-sm text-[#213430]" data-program-date>{{ $featuredDateLabel }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3 bg-[#FFF7FB] border border-[#E7CEDA] rounded-xl px-4 py-3 shadow-sm">
                                                <i class="far fa-clock text-[#DB69A2]"></i>
                                                <div>
                                                    <p class="text-xs uppercase tracking-wide text-[#B08BA2]">Time</p>
                                                    <p class="text-sm text-[#213430]" data-program-time>{{ $featuredTime }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3 bg-[#FFF7FB] border border-[#E7CEDA] rounded-xl px-4 py-3 shadow-sm">
                                                <i class="fas fa-donate text-[#DB69A2]"></i>
                                                <div>
                                                    <p class="text-xs uppercase tracking-wide text-[#B08BA2]">Program Fund</p>
                                                    <p class="text-sm text-[#213430]" data-program-fund>{{ $featuredFundLabel }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-[#213430] mb-4 app-main">About This Program</h3>
                                        <div class="bg-[#FFF7FB] border border-[#E7CEDA] rounded-2xl p-5 leading-relaxed text-sm text-[#91848C] space-y-3 shadow-sm">
                                            <p data-program-description>{{ $featuredDetail }}</p>
                                            <ul class="space-y-2 text-[#213430]">
                                                <li class="flex items-center gap-2"><span class="text-[#DB69A2] text-xl">&#8226;</span> Single-sponsor initiative with high visibility</li>
                                                <li class="flex items-center gap-2"><span class="text-[#DB69A2] text-xl">&#8226;</span> Custom recognition across materials</li>
                                                <li class="flex items-center gap-2"><span class="text-[#DB69A2] text-xl">&#8226;</span> Comprehensive impact reporting</li>
                                                <li class="flex items-center gap-2"><span class="text-[#DB69A2] text-xl">&#8226;</span> Dedicated liaison to manage program delivery</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('sponsor.sponsorships.store') }}" class="bg-[#FCEAF4] border border-[#E7CEDA] rounded-2xl p-6 space-y-6 shadow-sm" data-sponsorship-form>
                                @csrf
                                <input type="hidden" name="program_id" value="{{ old('program_id', $featuredProgramId) }}" data-program-input>
        <input type="hidden" name="program_source" value="program">
                                @error('program_id')
                                    <p class="text-xs text-[#B32020]">{{ $message }}</p>
                                @enderror

                                <h3 class="text-lg font-semibold text-[#213430] app-main">Program Investment</h3>
                                <div class="bg-white/80 border border-[#E9D2DF] rounded-2xl p-5 space-y-4">
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-[#B08BA2]">Total Program Fund</p>
                                        <p class="text-3xl font-bold text-[#DB69A2]" data-program-fund>{{ $featuredFundLabel }}</p>
                                        <p class="text-sm text-[#91848C] mt-2 app-text">This amount represents the full commitment required to deliver the program at no charge to beneficiaries.</p>
                                    </div>
                                    <div class="bg-[#F6E0ED] border border-[#E1C4D3] rounded-xl p-4 text-sm text-[#7B5268] space-y-2">
                                        <h4 class="font-semibold text-[#213430] mb-1">When you fund the program</h4>
                                        <ul class="space-y-2">
                                            @foreach ($investmentHighlights as $point)
                                                <li class="flex items-start gap-2"><span class="text-[#DB69A2] mt-1">&#8226;</span><span>{{ $point }}</span></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <label for="sponsorshipAmount" class="text-sm font-semibold text-[#213430] app-main">Your Contribution</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#DB69A2] font-semibold mt-3">$</span>
                                        <input type="number"
                                               min="1"
                                               step="0.01"
                                               id="sponsorshipAmount"
                                               name="amount"
                                               value="{{ old('amount', $featuredFundAmount) }}"
                                               class="w-full rounded-xl border border-[#E7CEDA] bg-white py-3 pl-10 pr-4 text-sm text-[#213430] focus:border-[#DB69A2] focus:outline-none focus:ring-2 focus:ring-[#F0C5DE]"
                                               data-program-amount>
                                    </div>
                                    <p class="text-xs text-[#91848C]">Adjust this value if you plan to co-sponsor or make a partial contribution.</p>
                                    @error('amount')
                                        <p class="text-xs text-[#B32020]">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="bg-white/70 border border-[#E9D2DF] rounded-2xl p-4 space-y-2 text-sm text-[#7B5268]">
                                    <h4 class="text-sm font-semibold text-[#213430]">Billing Contact</h4>
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $sponsorContext['logo'] }}" alt="{{ $sponsorContext['name'] }}" class="w-10 h-10 rounded-full object-cover">
                                        <div>
                                            <p class="text-[#213430] font-medium">{{ $sponsorContext['name'] }}</p>
                                            <p>{{ $sponsorContext['email'] }}</p>
                                            <p>{{ $sponsorContext['phone'] }}</p>
                                        </div>
                                    </div>
                                    <p class="text-xs">{{ $sponsorContext['about'] }}</p>
                                </div>

                                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                                    <button type="button" class="w-full px-5 py-3 bg-white border border-[#E7CEDA] text-[#91848C] rounded-xl hover:bg-[#F7EDF4] transition" data-program-modal-close>
                                        Cancel
                                    </button>
                                    <button type="submit" class="w-full px-6 py-3 bg-[#DB69A2] text-white rounded-xl shadow-sm hover:bg-[#C4568E] transition">
                                        Confirm Sponsorship
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const overlay = document.getElementById('programDetailOverlay');
            if (!overlay) {
                return;
            }

            const body = document.body;
            const programForm = overlay.querySelector('[data-sponsorship-form]');
            const programInput = programForm ? programForm.querySelector('[data-program-input]') : null;
            const amountInput = programForm ? programForm.querySelector('[data-program-amount]') : null;
            const programTriggers = document.querySelectorAll('[data-program]');

            const monthEl = overlay.querySelector('[data-program-month]');
            const dayEl = overlay.querySelector('[data-program-day]');
            const titleEl = overlay.querySelector('[data-program-title]');
            const summaryEl = overlay.querySelector('[data-program-summary]');
            const commitmentEl = overlay.querySelector('[data-program-commitment]');
            const dateEl = overlay.querySelector('[data-program-date]');
            const timeEls = overlay.querySelectorAll('[data-program-time]');
            const fundEls = overlay.querySelectorAll('[data-program-fund]');
            const descriptionEl = overlay.querySelector('[data-program-description]');

            const defaultMonth = monthEl ? monthEl.textContent : 'TBD';
            const defaultDay = dayEl ? dayEl.textContent : '--';
            const defaultTitle = titleEl ? titleEl.textContent : 'Program Details';
            const defaultSummary = summaryEl ? summaryEl.textContent : '';
            const defaultDate = dateEl ? dateEl.textContent : 'To be announced';
            const defaultTime = timeEls.length ? timeEls[0].textContent : 'To be announced';
            const defaultFundLabel = fundEls.length ? fundEls[0].textContent : 'To be determined';
            const defaultDescription = descriptionEl ? descriptionEl.textContent : '';
            const defaultCommitment = overlay.dataset.defaultCommitment || (commitmentEl ? commitmentEl.textContent : '');

            const defaultProgramId = overlay.dataset.initialProgramId || '';
            const defaultAmountRaw = overlay.dataset.initialAmount || '';
            const defaultAmount = defaultAmountRaw && defaultAmountRaw !== 'null' ? defaultAmountRaw : '';
            const shouldOpenOnLoad = overlay.dataset.openOnLoad === 'true';

            const payloadById = {};
            const defaultPayload = {
                id: defaultProgramId || null,
                month_label: defaultMonth,
                day_label: defaultDay,
                title: defaultTitle,
                description_short: defaultSummary,
                description_long: defaultDescription,
                commitment_text: defaultCommitment,
                date_label: defaultDate,
                time_label: defaultTime,
                fund_label: defaultFundLabel,
                fund_amount: defaultAmount || null,
            };

            function normalizeAmount(value) {
                if (value === null || value === undefined || value === '' || value === 'null') {
                    return '';
                }

                const numeric = Number(value);
                if (!Number.isFinite(numeric)) {
                    return '';
                }

                return numeric.toString();
            }

            function applyProgramData(data, overrides = {}) {
                if (!data) {
                    return;
                }

                if (monthEl) monthEl.textContent = data.month_label || defaultMonth;
                if (dayEl) dayEl.textContent = data.day_label || defaultDay;
                if (titleEl) titleEl.textContent = data.title || defaultTitle;
                if (summaryEl) summaryEl.textContent = data.description_short || defaultSummary;
                if (commitmentEl) commitmentEl.textContent = data.commitment_text || defaultCommitment;
                if (dateEl) dateEl.textContent = data.date_label || defaultDate;

                if (timeEls.length) {
                    const timeValue = data.time_label || defaultTime;
                    timeEls.forEach(function (timeEl) {
                        timeEl.textContent = timeValue;
                    });
                }

                if (fundEls.length) {
                    const fundValue = data.fund_label || defaultFundLabel;
                    fundEls.forEach(function (fundEl) {
                        fundEl.textContent = fundValue;
                    });
                }

                if (descriptionEl) {
                    descriptionEl.textContent = data.description_long || defaultDescription;
                }

                if (programInput && data.id) {
                    programInput.value = data.id;
                }

                if (amountInput) {
                    const overrideAmount = overrides.amount !== undefined ? overrides.amount : data.fund_amount;
                    const normalized = normalizeAmount(overrideAmount);
                    amountInput.value = normalized !== '' ? normalized : normalizeAmount(defaultAmount);
                }
            }

            function openOverlayWithData(data, options = {}) {
                applyProgramData(data, options);
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
                body.classList.add('overflow-hidden');
            }

            function closeModal() {
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
                body.classList.remove('overflow-hidden');

                applyProgramData(defaultPayload, { amount: defaultAmount });
            }

            programTriggers.forEach(function (trigger) {
                const raw = trigger.getAttribute('data-program');
                if (raw) {
                    try {
                        const parsed = JSON.parse(raw);
                        if (parsed && parsed.id) {
                            payloadById[parsed.id] = parsed;
                        }
                    } catch (error) {
                        console.error('Unable to parse program payload', error);
                    }
                }

                trigger.addEventListener('click', function (event) {
                    event.preventDefault();
                    const payloadRaw = trigger.getAttribute('data-program');
                    if (!payloadRaw) {
                        return;
                    }

                    let data;
                    try {
                        data = JSON.parse(payloadRaw);
                    } catch (error) {
                        console.error('Unable to parse program payload', error);
                        return;
                    }

                    if (data && data.id) {
                        payloadById[data.id] = data;
                    }

                    openOverlayWithData(data, { amount: data ? data.fund_amount : undefined });
                });
            });

            overlay.querySelectorAll('[data-program-modal-close]').forEach(function (btn) {
                btn.addEventListener('click', closeModal);
            });

            overlay.addEventListener('click', function (event) {
                if (event.target === overlay) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !overlay.classList.contains('hidden')) {
                    closeModal();
                }
            });

            if (programInput && defaultProgramId) {
                programInput.value = defaultProgramId;
            }

            if (amountInput) {
                amountInput.value = normalizeAmount(defaultAmount);
            }

            if (shouldOpenOnLoad) {
                const initialPayload = (defaultProgramId && payloadById[defaultProgramId]) ? payloadById[defaultProgramId] : defaultPayload;
                openOverlayWithData(initialPayload, { amount: defaultAmount });
            }
        });
    </script>
@endpush





















