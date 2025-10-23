@extends('sponsor.layouts.app')

@section('title', 'Sponsorship')

@section('content')
    <!---Main -->
    <main class="flex-1">
        <div class="max-w-8xl mx-auto">
            @include('sponsor.partials.cards')
        </div>

        {{-- @if (session('success') || (old('program_source') === 'program' && $errors->any()))
            <div class="max-w-4xl mx-auto px-4 mt-4 space-y-3">
                @if (session('success'))
                    <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if (old('program_source') === 'program' && $errors->any())
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
        @endif --}}

        @php
            use Illuminate\Support\Str;
            use Carbon\Carbon;

            $sponsorshipImages = ['S-1.png', 'S-2.png', 'S-3.png', 'S-4.png'];
            $upcomingImages = ['program-1.png', 'program-2.png', 'program-3.png', 'program-4.png', 'program-5.png'];

            $formatProgramPayload = function ($program) {
                if (!$program) {
                    return null;
                }

                $startDate = $program->start_date ? Carbon::parse($program->start_date) : null;
                $endDate = $program->end_date ? Carbon::parse($program->end_date) : null;

                return [
                    'id' => $program->id,
                    'title' => $program->title,
                    'summary' => Str::limit(strip_tags($program->description), 140),
                    'description' => Str::limit(strip_tags($program->description), 240),
                    'goal' => (float) ($program->goal_amount ?? 0),
                    'raised' => (float) ($program->raised_amount ?? 0),
                    'remaining' => max(
                        (float) ($program->goal_amount ?? 0) - (float) ($program->raised_amount ?? 0),
                        0,
                    ),
                    'month_label' => $startDate ? $startDate->format('M') : 'TBD',
                    'day_label' => $startDate ? $startDate->format('d') : '--',
                    'start_label' => $startDate ? $startDate->format('F d, Y') : 'To be announced',
                    'end_label' => $endDate ? $endDate->format('F d, Y') : 'To be announced',
                    'date_label' =>
                        $startDate && $endDate
                            ? $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y')
                            : ($startDate
                                ? $startDate->format('M d, Y')
                                : 'Schedule coming soon'),
                ];
            };

            $allPrograms = $ongoingPrograms->concat($upcomingPrograms);
            $defaultProgram = $allPrograms->first();

            $selectedProgramId =
                old('program_source') === 'program' ? old('program_id') : optional($defaultProgram)->id;

            $selectedProgram = $selectedProgramId ? $allPrograms->firstWhere('id', (int) $selectedProgramId) : null;

            $selectedProgram ??= $defaultProgram;

            $defaultPayload = $selectedProgram ? $formatProgramPayload($selectedProgram) : null;
            $defaultAmount = $defaultPayload
                ? ($defaultPayload['remaining'] > 0
                    ? $defaultPayload['remaining']
                    : $defaultPayload['goal'])
                : null;

            if (old('program_source') === 'program') {
                $oldAmount = old('amount');
                if ($oldAmount !== null && $oldAmount !== '') {
                    $defaultAmount = (float) $oldAmount;
                }
            }

            $defaultAmountFormatted = $defaultAmount !== null ? number_format((float) $defaultAmount, 2, '.', '') : '';

            $defaultMonthLabel = $defaultPayload['month_label'] ?? 'TBD';
            $defaultDayLabel = $defaultPayload['day_label'] ?? '--';
            $defaultTitle = $selectedProgram->title ?? 'Select a program';
            $defaultSummary = $defaultPayload['summary'] ?? 'Choose a program to see its impact.';
            $defaultDescription =
                $defaultPayload['description'] ?? 'Choose a program to learn more about the work it funds.';
            $defaultStartLabel = $defaultPayload['start_label'] ?? 'To be announced';
            $defaultEndLabel = $defaultPayload['end_label'] ?? 'To be announced';
            $defaultGoalLabel = $defaultPayload ? '$' . number_format($defaultPayload['goal'], 2) : '$0.00';
            $defaultRaisedLabel = $defaultPayload ? '$' . number_format($defaultPayload['raised'], 2) : '$0.00';
            $defaultRemainingLabel = $defaultPayload ? '$' . number_format($defaultPayload['remaining'], 2) : '$0.00';

            $defaultRemainingValue = $defaultPayload
                ? number_format((float) ($defaultPayload['remaining'] ?? 0), 2, '.', '')
                : '';

            $selectedProgramIdValue =
                old('program_source') === 'program' ? old('program_id') : optional($selectedProgram)->id;

            $overlayAmountValue = $defaultAmountFormatted;
            if (old('program_source') === 'program') {
                $overlayAmountValue = old('amount', $defaultAmountFormatted);
            }

            $shouldOpenSponsorshipModal = old('program_source') === 'program' && $errors->any();
        @endphp

        <div class="max-w-[97rem] mx-auto crousel-width">
            <div class="px-4 py-6 bg-[#F3E8EF] rounded-lg">
                <div class="flex justify-between items-center pb-6">
                    <h1 class="text-2xl font-medium text-[#213430] program-h">Ongoing Flexible Programs</h1>
                    @if ($ongoingPrograms->count() > 4)
                        <div class="flex space-x-2">
                            <button id="prevBtn"
                                class="bg-transparent border border-[#213430] text-[#213430] hover:bg-[#DB69A2] hover:border-none hover:text-white p-2 rounded-lg">
                                <svg class="md:h-6 h-4 md:w-6 w-4 arrow-icon" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 18l-6-6 6-6" />
                                </svg>
                            </button>
                            <button id="nextBtn"
                                class="bg-transparent border border-[#213430] text-[#213430] hover:bg-[#DB69A2] hover:border-none hover:text-white p-2 rounded-lg">
                                <svg class="md:h-6 h-4 md:w-6 w-4 arrow-icon" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 18l6-6-6-6" />
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>

                <div class="relative overflow-hidden">
                    @if ($ongoingPrograms->isEmpty())
                        <div class="bg-white/40 border border-[#DCCFD8] rounded-lg p-8 text-center text-[#91848C]">
                            There are no ongoing flexible payment programs at the moment.
                        </div>
                    @else
                        <div id="carousel" class="carousel-container flex overflow-x-auto scrollbar-hide">
                            @foreach ($ongoingPrograms as $program)
                                @php
                                    $image = asset(
                                        'images/' . $sponsorshipImages[$loop->index % count($sponsorshipImages)],
                                    );
                                    $goal = (float) ($program->goal_amount ?? 0);
                                    $raised = (float) ($program->raised_amount ?? 0);
                                    $left = max($goal - $raised, 0);
                                    $payload = $formatProgramPayload($program);
                                    $payloadJson = $payload
                                        ? htmlspecialchars(
                                            json_encode(
                                                $payload,
                                                JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP,
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8',
                                        )
                                        : '';
                                @endphp
                                <div class="carousel-item p-2 min-w-[300px] md:min-w-[330px] lg:min-w-[350px]">
                                    <div
                                        class="bg-[#F3E8EF] border border-[#DCCFD8] rounded-lg overflow-hidden hover:ring-2 ring-[#db69a2]">
                                        <div class="p-2">
                                            <img src="{{ $image }}" alt="{{ $program->title }}"
                                                class="w-full h-48 object-cover rounded" />
                                        </div>
                                        <div class="p-4 space-y-4">
                                            <div class="space-y-2">
                                                <div class="flex items-center gap-2">
                                                    <h2 class="text-lg font-medium text-[#213430] app-main">
                                                        {{ $program->title }}</h2>
                                                    {{-- <span class="inline-flex items-center rounded-full bg-[#DB69A2] px-3 py-1 text-xs font-semibold text-white uppercase tracking-wide">Flexible Payment</span> --}}
                                                </div>
                                                <p class="text-sm text-[#91848C] app-text">
                                                    {{ Str::limit($program->description, 110) }}</p>
                                            </div>
                                            <button type="button"
                                                class="w-full py-3 text-[#91848C] font-medium border border-gray-300 rounded hover:bg-[#db69a2] hover:text-white hover:border-transparent rounded-lg app-main"
                                                data-sp-program='@json($payload)'>
                                                Donate Now
                                            </button>
                                            <div class="flex justify-between items-center text-sm">
                                                <div>
                                                    <p class="text-[#91848C] app-text">Raised</p>
                                                    <p class="font-medium text-[#213430] app-text">
                                                        ${{ number_format($raised, 0) }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[#91848C] app-text">Goal</p>
                                                    <p class="font-medium text-[#213430] app-text">
                                                        ${{ number_format($goal, 0) }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[#91848C] app-text">Left</p>
                                                    <p class="font-medium text-[#213430] app-text">
                                                        ${{ number_format($left, 0) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="max-w-[97rem] mx-auto crousel-width">
            <div class="px-4 py-6 bg-[#F3E8EF] rounded-lg mt-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-semibold text-[#213430] program-main">Upcoming Flexible Programs</h2>
                </div>

                <div class="space-y-4">
                    @forelse ($upcomingPrograms as $program)
                        @php
                            $image = asset('public/images/' . $upcomingImages[$loop->index % count($upcomingImages)]);
                            $startDate = $program->start_date ? Carbon::parse($program->start_date) : null;
                            $month = $startDate ? $startDate->format('M') : 'TBD';
                            $day = $startDate ? $startDate->format('d') : '--';
                            $goal = (float) ($program->goal_amount ?? 0);
                            $raised = (float) ($program->raised_amount ?? 0);
                            $left = max($goal - $raised, 0);
                            $payload = $formatProgramPayload($program);
                            $payloadJson = $payload
                                ? htmlspecialchars(
                                    json_encode($payload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP),
                                    ENT_QUOTES,
                                    'UTF-8',
                                )
                                : '';
                        @endphp
                        <div class="bg-[#F3E8EF] rounded-lg p-4 mb-4 flex items-center justify-between md:flex hidden">
                            <div class="flex items-center">
                                <div
                                    class="flex flex-col items-center justify-center w-20 h-20 border-2 border-pink rounded-lg mr-4 bg-[#FFF7FC]">
                                    <span class="text-sm text-pink">{{ $month }}</span>
                                    <span class="text-4xl font-bold text-pink">{{ $day }}</span>
                                </div>
                                <div class="w-20 h-20 rounded-lg overflow-hidden mr-4">
                                    <img src="{{ $image }}" alt="{{ $program->title }}"
                                        class="w-full h-full object-cover" />
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="text-xl font-semibold text-[#213430] program-h">{{ $program->title }}
                                        </h3>
                                        <span
                                            class="inline-flex items-center rounded-full bg-[#DB69A2] px-3 py-1 text-xs font-semibold text-white uppercase tracking-wide">Flexible
                                            Payment</span>
                                    </div>
                                    <p class="text-sm text-[#91848C] program-p">
                                        {{ Str::limit($program->description, 140) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-6 text-sm">
                                {{-- <div>
                                    <p class="text-[#91848C] app-text">Raised</p>
                                    <p class="font-medium text-[#213430] app-text">${{ number_format($raised, 0) }}</p>
                                </div>
                                <div>
                                    <p class="text-[#91848C] app-text">Goal</p>
                                    <p class="font-medium text-[#213430] app-text">${{ number_format($goal, 0) }}</p>
                                </div>
                                <div>
                                    <p class="text-[#91848C] app-text">Left</p>
                                    <p class="font-medium text-[#213430] app-text">${{ number_format($left, 0) }}</p>
                                </div> --}}
                                <button type="button"
                                    class="bg-transparent border border-[#213430] text-[#213430] py-3 px-8 rounded-lg program-btn hover:bg-[#db69a2] hover:text-white hover:border-transparent transition"
                                    data-sp-program='@json($payload)'>
                                    Sponsor Program
                                </button>
                            </div>
                        </div>

                        <div class="bg-[#F3E8EF] rounded-lg p-3 mb-4 flex items-center justify-between md:hidden flex">
                            <div class="flex flex-col gap-2">
                                <div class="w-[80px] h-[80px] rounded-lg overflow-hidden mr-4">
                                    <img src="{{ $image }}" alt="{{ $program->title }}"
                                        class="w-full h-full object-cover" />
                                </div>
                                <div
                                    class="flex flex-col items-center justify-center w-[80px] h-[60px] border-2 border-pink rounded-lg mr-4 bg-[#FFF7FC]">
                                    <span class="text-sm text-pink">{{ $month }}</span>
                                    <span class="text-4xl font-bold text-pink">{{ $day }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-[15px] font-semibold text-[#213430]">{{ $program->title }}</h3>
                                    <span
                                        class="inline-flex items-center rounded-full bg-[#DB69A2] px-2 py-0.5 text-[10px] font-semibold text-white uppercase tracking-wide">Flexible</span>
                                </div>
                                <p class="text-[13px] font-light text-[#91848C]">
                                    {{ Str::limit($program->description, 110) }}</p>
                                <div class="flex items-center justify-between text-xs text-[#91848C]">
                                    <span>Goal: ${{ number_format($goal, 0) }}</span>
                                    <span>Raised: ${{ number_format($raised, 0) }}</span>
                                </div>
                                <button type="button"
                                    class="bg-transparent border border-[#213430] text-[#213430] hover:bg-[#db69a2] hover:text-white hover:border-none py-2 px-6 rounded-lg"
                                    data-sp-program='@json($payload)'>
                                    Sponsor Program
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white/40 border border-[#DCCFD8] rounded-lg p-8 text-center text-[#91848C]">
                            No upcoming flexible payment programs are scheduled right now.
                        </div>
                    @endforelse
                </div>
            </div>
            @if ($allPrograms->isNotEmpty())
                <div id="sponsorshipProgramOverlay"
                    class="fixed inset-0 z-40 hidden items-center justify-center bg-black/60 px-4 py-6"
                    data-default-program='@json($defaultPayload)'
                    data-open-on-load="{{ $shouldOpenSponsorshipModal ? 'true' : 'false' }}">
                    <div class="relative w-full max-w-5xl bg-white rounded-3xl shadow-xl overflow-hidden">
                        <button type="button"
                            class="absolute right-4 top-4 rounded-full bg-white/80 p-2 text-[#213430] shadow-sm hover:bg-white"
                            data-sp-close>
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <div class="grid grid-cols-1 lg:grid-cols-[55%_45%]">
                            <div class="bg-[#FCEAF4] p-6 lg:p-8 space-y-6">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex flex-col items-center justify-center w-20 h-20 border-2 border-[#DB69A2] rounded-2xl bg-white">
                                        <span class="text-sm text-[#DB69A2]" data-sp-month>{{ $defaultMonthLabel }}</span>
                                        <span class="text-4xl font-bold text-[#DB69A2]"
                                            data-sp-day>{{ $defaultDayLabel }}</span>
                                    </div>
                                    <div class="space-y-2">
                                        <h2 class="text-2xl font-semibold text-[#213430]" data-sp-title>
                                            {{ $defaultTitle }}</h2>
                                        <p class="text-sm text-[#7B5268]" data-sp-summary>{{ $defaultSummary }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div class="bg-white border border-[#E7CEDA] rounded-xl p-4">
                                        <p class="text-xs uppercase tracking-wide text-[#B08BA2]">Goal</p>
                                        <p class="mt-1 text-lg font-semibold text-[#213430]" data-sp-goal>
                                            {{ $defaultGoalLabel }}</p>
                                    </div>
                                    <div class="bg-white border border-[#E7CEDA] rounded-xl p-4">
                                        <p class="text-xs uppercase tracking-wide text-[#B08BA2]">Raised</p>
                                        <p class="mt-1 text-lg font-semibold text-[#213430]" data-sp-raised>
                                            {{ $defaultRaisedLabel }}</p>
                                    </div>
                                    <div class="bg-white border border-[#E7CEDA] rounded-xl p-4">
                                        <p class="text-xs uppercase tracking-wide text-[#B08BA2]">Remaining</p>
                                        <p class="mt-1 text-lg font-semibold text-[#213430]" data-sp-remaining>
                                            {{ $defaultRemainingLabel }}</p>
                                    </div>
                                </div>

                                <div class="bg-white border border-[#E7CEDA] rounded-xl p-5 text-sm text-[#7B5268] leading-relaxed"
                                    data-sp-description>{{ $defaultDescription }}</div>

                                <div
                                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-sm text-[#7B5268]">
                                    <div>
                                        <span class="font-semibold text-[#213430]">Program start:</span>
                                        <span data-sp-start>{{ $defaultStartLabel }}</span>
                                    </div>
                                    <div>
                                        <span class="font-semibold text-[#213430]">Program end:</span>
                                        <span data-sp-end>{{ $defaultEndLabel }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 lg:p-8 space-y-6">
                                <form method="POST" action="{{ route('sponsor.sponsorships.store') }}" data-sp-form
                                    class="space-y-6">
                                    @csrf
                                    <input type="hidden" name="program_source" value="program">
                                    <input type="hidden" name="program_id" value="{{ $selectedProgramIdValue }}"
                                        data-sp-input>
                                    @error('program_id')
                                        <p class="text-xs text-[#B32020]">{{ $message }}</p>
                                    @enderror

                                    <div>
                                        <label for="sponsorship-program-amount"
                                            class="text-sm font-semibold text-[#213430]">Contribution amount</label>
                                        <div class="mt-2">
                                            <input id="sponsorship-program-amount" name="amount" type="number"
                                                min="1" step="0.01"
                                                class="w-full rounded-xl border border-[#E7CEDA] bg-white px-4 py-3 text-[#213430] focus:border-[#DB69A2] focus:outline-none focus:ring-2 focus:ring-[#DB69A2]/40"
                                                value="{{ $overlayAmountValue }}"
                                                @if ($defaultRemainingValue !== '') max="{{ $defaultRemainingValue }}" @endif
                                                data-sp-amount>
                                        </div>
                                        @error('amount')
                                            <p class="mt-1 text-xs text-[#B32020]">{{ $message }}</p>
                                        @enderror
                                        <p class="mt-2 text-xs text-[#B32020] hidden" data-sp-error role="alert"></p>
                                    </div>

                                    <button type="submit"
                                        class="w-full rounded-xl bg-[#DB69A2] py-3 text-white font-semibold shadow-sm hover:bg-[#c5588f] transition"
                                        data-sp-submit>
                                        Confirm Sponsorship
                                    </button>

                                    <p class="text-xs text-[#91848C]">
                                        Your support fuels life-saving care and resources for women fighting breast cancer.
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.getElementById('sponsorshipProgramOverlay');
            if (!overlay) {
                return;
            }

            const body = document.body;
            const defaultPayloadRaw = overlay.getAttribute('data-default-program');
            const openOnLoad = overlay.getAttribute('data-open-on-load') === 'true';

            const safeParse = function(value) {
                if (!value) {
                    return null;
                }

                try {
                    return JSON.parse(value);
                } catch (error) {
                    console.error('Unable to parse program payload', error);
                    return null;
                }
            };

            const defaultPayload = safeParse(defaultPayloadRaw);

            const triggers = document.querySelectorAll('[data-sp-program]');
            const monthEl = overlay.querySelector('[data-sp-month]');
            const dayEl = overlay.querySelector('[data-sp-day]');
            const titleEl = overlay.querySelector('[data-sp-title]');
            const summaryEl = overlay.querySelector('[data-sp-summary]');
            const goalEl = overlay.querySelector('[data-sp-goal]');
            const raisedEl = overlay.querySelector('[data-sp-raised]');
            const remainingEl = overlay.querySelector('[data-sp-remaining]');
            const descriptionEl = overlay.querySelector('[data-sp-description]');
            const startEl = overlay.querySelector('[data-sp-start]');
            const endEl = overlay.querySelector('[data-sp-end]');
            const programInput = overlay.querySelector('[data-sp-input]');
            const amountInput = overlay.querySelector('[data-sp-amount]');
            const form = overlay.querySelector('[data-sp-form]');
            const closeButtons = overlay.querySelectorAll('[data-sp-close]');
            const submitButton = overlay.querySelector('[data-sp-submit]');
            const errorEl = overlay.querySelector('[data-sp-error]');
            const payloadById = {};

            const formatCurrency = function(value) {
                const numeric = Number(value || 0);
                return new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(numeric);
            };

            const normalizeAmount = function(value) {
                if (value === null || value === undefined || value === '') {
                    return '';
                }

                const numeric = Number(value);
                if (Number.isNaN(numeric)) {
                    return '';
                }

                return numeric.toFixed(2);
            };

            const setAmountLimit = function(limit) {
                if (!amountInput) {
                    return;
                }

                if (typeof limit === 'number' && !Number.isNaN(limit)) {
                    const normalizedLimit = normalizeAmount(limit);
                    amountInput.setAttribute('max', normalizedLimit);
                    amountInput.dataset.remaining = normalizedLimit;
                } else {
                    amountInput.removeAttribute('max');
                    delete amountInput.dataset.remaining;
                }
            };

            const showError = function(message) {
                if (!errorEl) {
                    return;
                }

                errorEl.textContent = message;
                errorEl.classList.remove('hidden');
            };

            const hideError = function() {
                if (!errorEl) {
                    return;
                }

                errorEl.textContent = '';
                if (!errorEl.classList.contains('hidden')) {
                    errorEl.classList.add('hidden');
                }
            };

            const updateSubmitState = function(disabled) {
                if (!submitButton) {
                    return;
                }

                submitButton.disabled = disabled;
                submitButton.classList.toggle('opacity-60', disabled);
                submitButton.classList.toggle('cursor-not-allowed', disabled);
            };

            const validateAmount = function(options = {}) {
                if (!amountInput) {
                    return true;
                }

                const showMessage = options.showMessage !== false;
                const report = options.report === true;

                const rawValue = amountInput.value;
                const numericValue = Number(rawValue);
                const hasValue = rawValue !== '' && !Number.isNaN(numericValue);

                const maxAttr = amountInput.getAttribute('max');
                const maxValue = maxAttr !== null && maxAttr !== '' ? Number(maxAttr) : null;

                amountInput.setCustomValidity('');

                const limitExceeded = hasValue && maxValue !== null && maxValue >= 0 && numericValue >
                    maxValue + 1e-6;

                if (limitExceeded) {
                    const message =
                        `The maximum you can contribute to this program is ${formatCurrency(maxValue)}.`;

                    if (showMessage) {
                        showError(message);
                    }

                    amountInput.setCustomValidity(message);

                    if (report) {
                        amountInput.reportValidity();
                    }

                    updateSubmitState(true);
                    return false;
                }

                hideError();
                updateSubmitState(false);
                return true;
            };

            const applyPayload = function(payload, overrides = {}) {
                const data = payload || defaultPayload || null;
                if (!data) {
                    return;
                }

                if (data.id) {
                    payloadById[data.id] = data;
                }

                if (programInput && data.id) {
                    programInput.value = data.id;
                }

                if (monthEl) monthEl.textContent = data.month_label || 'TBD';
                if (dayEl) dayEl.textContent = data.day_label || '--';
                if (titleEl) titleEl.textContent = data.title || 'Select a program';
                if (summaryEl) summaryEl.textContent = data.summary ||
                    'Choose a program to learn more about the work it funds.';
                if (goalEl) goalEl.textContent = formatCurrency(data.goal);
                if (raisedEl) raisedEl.textContent = formatCurrency(data.raised);
                if (remainingEl) remainingEl.textContent = formatCurrency(data.remaining);
                if (descriptionEl) descriptionEl.textContent = data.description ||
                    'Choose a program to learn more about the work it funds.';
                if (startEl) startEl.textContent = data.start_label || 'To be announced';
                if (endEl) endEl.textContent = data.end_label || 'To be announced';

                const amountOverride = overrides.amount !== undefined ? overrides.amount : (data.remaining > 0 ?
                    data.remaining : data.goal);
                if (amountInput) {
                    hideError();
                    const limitValue = Number(data.remaining);
                    setAmountLimit(!Number.isNaN(limitValue) ? limitValue : null);

                    const normalizedValue = overrides.amountValue !== undefined ? overrides.amountValue :
                        normalizeAmount(amountOverride);
                    amountInput.value = normalizedValue;
                    validateAmount({
                        showMessage: false
                    });
                }
            };

            const openModal = function(payload, options = {}) {
                applyPayload(payload, options);

                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
                body.classList.add('overflow-hidden');
            };

            const closeModal = function() {
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
                body.classList.remove('overflow-hidden');

                hideError();
                updateSubmitState(false);

                if (amountInput) {
                    amountInput.setCustomValidity('');
                }

                applyPayload(defaultPayload, {
                    amountValue: normalizeAmount(amountInput ? amountInput.value : undefined)
                });
            };

            triggers.forEach(function(trigger) {
                trigger.addEventListener('click', function(event) {
                    event.preventDefault();
                    const raw = trigger.getAttribute('data-sp-program');
                    const parsed = safeParse(raw);
                    if (!parsed) {
                        return;
                    }

                    payloadById[parsed.id] = parsed;
                    openModal(parsed, {
                        amount: parsed.remaining > 0 ? parsed.remaining : parsed.goal
                    });
                });
            });

            closeButtons.forEach(function(button) {
                button.addEventListener('click', closeModal);
            });

            overlay.addEventListener('click', function(event) {
                if (event.target === overlay) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && !overlay.classList.contains('hidden')) {
                    closeModal();
                }
            });

            if (amountInput) {
                amountInput.addEventListener('input', function() {
                    hideError();
                    validateAmount({
                        showMessage: false
                    });
                });

                amountInput.addEventListener('blur', function() {
                    amountInput.value = normalizeAmount(amountInput.value);
                    validateAmount({
                        report: true
                    });
                });
            }

            if (form) {
                form.addEventListener('submit', function(event) {
                    if (!validateAmount({
                            report: true
                        })) {
                        event.preventDefault();
                        if (amountInput) {
                            amountInput.focus();
                        }
                    }
                });
            }

            if (programInput && !programInput.value && defaultPayload && defaultPayload.id) {
                programInput.value = defaultPayload.id;
            }

            if (amountInput && amountInput.value === '' && defaultPayload) {
                amountInput.value = normalizeAmount(defaultPayload.remaining > 0 ? defaultPayload.remaining :
                    defaultPayload.goal);
            }

            if (amountInput) {
                validateAmount({
                    showMessage: false
                });
            }

            if (openOnLoad && defaultPayload) {
                openModal(defaultPayload, {
                    amountValue: amountInput ? amountInput.value : undefined
                });
            }
        });
    </script>
@endpush
