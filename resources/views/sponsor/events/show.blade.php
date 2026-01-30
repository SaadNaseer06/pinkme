@extends('sponsor.layouts.app')

@section('title', $event->title)

@php
    use App\Support\EventHighlightFormatter;

    $formatAmount = function ($n) {
        $n = (float) $n;
        return $n == floor($n) ? (string) (int) $n : number_format($n, 2);
    };

    $descriptionHtml = $event->description
        ? strip_tags($event->description, '<p><br><strong><em><u><ol><ul><li><a><span><div><blockquote>')
        : null;
    $eventHighlightsHtml = EventHighlightFormatter::format($event->event_highlights);
@endphp

@section('content')
    <main class="flex-1">
        <div class="max-w-8xl mx-auto">

            <!-- Back to Events -->
            <a href="{{ route('sponsor.events') }}"
                class="inline-flex items-center gap-2 text-[#6C5B68] hover:text-[#C63A85] font-medium mb-6 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Events
            </a>

            <!-- Event Header: gradient and text tuned for white text readability -->
            @php
                $headerTextShadow = '0 1px 2px rgba(0,0,0,0.4), 0 2px 6px rgba(0,0,0,0.35)';
            @endphp
            <div
                class="rounded-2xl p-8 shadow-lg mb-8 overflow-hidden"
                style="background: linear-gradient(135deg, #9B2768 0%, #B82E75 25%, #C63A85 50%, #D04A8E 75%, #DB69A2 100%);">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between relative z-10">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span
                                class="bg-white/25 backdrop-blur text-white text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wide"
                                style="text-shadow: {{ $headerTextShadow }};">
                                {{ ucfirst($event->status) }}
                            </span>
                            @if ($event->registration_deadline && $event->registration_deadline->isFuture())
                                <span
                                    class="bg-white/25 backdrop-blur text-white text-xs font-medium px-3 py-1 rounded-full"
                                    style="text-shadow: {{ $headerTextShadow }};">
                                    Registration Deadline: {{ $event->registration_deadline->format('M d, Y') }}
                                </span>
                            @endif
                        </div>
                        <h1 class="text-3xl font-bold mb-2 text-white" style="text-shadow: {{ $headerTextShadow }};">
                            {{ $event->title }}</h1>
                        <div class="flex flex-wrap gap-4 text-sm text-white" style="text-shadow: {{ $headerTextShadow }};">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span>{{ $event->date->format('F j, Y \a\t g:i A') }}</span>
                            </div>
                            @if ($event->location)
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span>{{ $event->location }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Event Description -->
                    @if ($descriptionHtml)
                        <div class="bg-white rounded-2xl border border-[#E9DCE7] p-6 shadow-sm">
                            <h2 class="text-xl font-semibold text-[#213430] mb-4">About This Event</h2>
                            <div class="text-[#6C5B68] leading-relaxed">
                                {!! $descriptionHtml !!}
                            </div>
                        </div>
                    @else
                        <div class="bg-white rounded-2xl border border-[#E9DCE7] p-6 shadow-sm">
                            <h2 class="text-xl font-semibold text-[#213430] mb-4">About This Event</h2>
                            <p class="text-[#6C5B68]">No description has been added for this event yet.</p>
                        </div>
                    @endif

                    <!-- Event Highlights -->
                    @if ($eventHighlightsHtml)
                        <div class="bg-white rounded-2xl border border-[#E9DCE7] p-6 shadow-sm">
                            <h2 class="text-xl font-semibold text-[#213430] mb-4">Event Highlights</h2>
                            <div class="text-[#6C5B68]">
                                {!! $eventHighlightsHtml !!}
                            </div>
                        </div>
                    @endif

                    <!-- Current Sponsors -->
                    @if ($event->confirmedSponsors->isNotEmpty())
                        <div class="bg-white rounded-2xl border border-[#E9DCE7] p-6 shadow-sm">
                            <h2 class="text-xl font-semibold text-[#213430] mb-4">Current Sponsors</h2>
                            <div class="grid gap-4">
                                @foreach ($event->confirmedSponsors as $sponsor)
                                    <div class="flex items-center gap-4 p-4 bg-[#FDF7FB] rounded-xl">
                                        <div class="w-12 h-12 bg-[#DB69A2] rounded-full flex items-center justify-center">
                                            <span class="text-white font-semibold">
                                                {{ strtoupper(substr($sponsor->profile->first_name ?? $sponsor->email, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-[#213430]">
                                                {{ $sponsor->sponsorDetail->company_name ?? ($sponsor->profile->full_name ?? $sponsor->email) }}
                                            </h3>
                                            <p class="text-sm text-[#6C5B68]">
                                                Sponsored ${{ $formatAmount($sponsor->pivot->amount) }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Funding Progress -->
                    @if ($event->funding_goal)
                        @php
                            $progressPct = min(100, max(0, (int) $fundingProgress));
                        @endphp
                        <div class="bg-white rounded-2xl border border-[#E9DCE7] p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-[#213430] mb-4">Funding Progress</h3>

                            <div class="mb-4">
                                <div class="flex justify-between text-sm text-[#6C5B68] mb-2">
                                    <span>Progress</span>
                                    <span class="font-medium text-[#213430]">{{ $progressPct }}%</span>
                                </div>
                                <div class="w-full bg-[#F1E5EF] rounded-full h-3 overflow-hidden block" role="progressbar" aria-valuenow="{{ $progressPct }}" aria-valuemin="0" aria-valuemax="100">
                                    <div class="bg-gradient-to-r from-[#DB69A2] to-[#C63A85] rounded-full transition-all duration-300 block"
                                        style="height: 12px; width: {{ $progressPct }}%; min-width: {{ $progressPct > 0 ? '4px' : '0' }};"></div>
                                </div>
                            </div>

                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-[#6C5B68]">Goal:</span>
                                    <span
                                        class="font-semibold text-[#213430]">${{ $formatAmount($event->funding_goal) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-[#6C5B68]">Raised:</span>
                                    <span
                                        class="font-semibold text-[#213430]">${{ $formatAmount($event->confirmed_sponsorship_total) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-[#6C5B68]">Remaining:</span>
                                    <span
                                        class="font-semibold text-[#213430]">${{ $formatAmount($remainingFunding) }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Registration Status -->
                    <div class="bg-white rounded-2xl border border-[#E9DCE7] p-6 shadow-sm">
                        @if ($currentRegistration)
                            <!-- Already Registered: card with readable contrast -->
                            @php
                                $canCancel = $currentRegistration->registration_status !== 'cancelled'
                                    && $event->date
                                    && now()->isBefore($event->date)
                                    && ($currentRegistration->payment_status ?? 'pending') !== 'paid';
                            @endphp
                            <div class="text-center p-5 rounded-xl bg-[#FDF7FB] border border-[#F1E5EF]">
                                <div
                                    class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-[#213430] mb-2">You're Registered!</h3>
                                <p class="text-[#213430] mb-2">
                                    Status: <span class="font-semibold capitalize text-[#213430]">{{ $currentRegistration->registration_status }}</span>
                                </p>
                                <p class="text-[#213430] mb-4">
                                    Sponsorship Amount: <span class="font-semibold text-[#213430]">${{ $formatAmount($currentRegistration->amount) }}</span>
                                </p>
                                @if ($canCancel)
                                    <form method="POST" action="{{ route('sponsor.events.cancel', $event) }}"
                                        onsubmit="return confirm('Are you sure you want to cancel your registration?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full px-4 py-2.5 bg-red-600 text-white font-medium rounded-xl hover:bg-red-700 transition-colors border border-red-700">
                                            Cancel Registration
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @elseif($event->isRegistrationOpen())
                            @if ($event->payment_type === 'full')
                                <!-- Full Sponsorship: one fixed amount, one button -->
                                <h3 class="text-lg font-semibold text-[#213430] mb-2">Full Sponsorship</h3>
                                <p class="text-sm text-[#6C5B68] mb-4">Pay the remaining amount to fully fund this event.</p>
                                <div class="mb-4 p-4 bg-[#FDF7FB] rounded-xl border border-[#E9DCE7]">
                                    <span class="text-2xl font-bold text-[#213430]">${{ $formatAmount($remainingFunding) }}</span>
                                    <span class="text-sm text-[#6C5B68] ml-1">remaining</span>
                                </div>
                                <form method="POST" action="{{ route('sponsor.events.register', $event) }}">
                                    @csrf
                                    <input type="hidden" name="amount" value="{{ $remainingFunding }}">
                                    <div class="mb-4">
                                        <details class="text-sm text-[#6C5B68]">
                                            <summary class="cursor-pointer hover:text-[#213430]">Add a message (optional)</summary>
                                            <textarea name="message" rows="2" class="mt-2 w-full px-4 py-2 border border-[#DCCFD8] rounded-xl focus:ring-2 focus:ring-[#DB69A2] focus:border-transparent" placeholder="Why you're supporting this event..." maxlength="500"></textarea>
                                        </details>
                                        @error('message')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-[#DB69A2] to-[#C63A85] text-white font-semibold rounded-xl hover:opacity-95 transition-opacity">
                                        Pay ${{ $formatAmount($remainingFunding) }} &amp; Register
                                    </button>
                                </form>
                            @else
                                <!-- Flexible Sponsorship: choose amount, then pay -->
                                <h3 class="text-lg font-semibold text-[#213430] mb-2">Flexible Sponsorship</h3>
                                <p class="text-sm text-[#6C5B68] mb-4">Choose your amount. You’ll complete payment on the next step.</p>
                                @if ($event->funding_goal)
                                    <p class="text-xs text-[#6C5B68] mb-3">Remaining to fund: <span class="font-semibold text-[#213430]">${{ $formatAmount($remainingFunding) }}</span></p>
                                @endif
                                <form method="POST" action="{{ route('sponsor.events.register', $event) }}" id="flexible-register-form">
                                    @csrf
                                    <input type="hidden" name="amount" id="flexible-amount" value="">
                                    <div class="mb-4">
                                        <p class="text-sm font-medium text-[#213430] mb-2">Amount</p>
                                        <div class="flex flex-wrap gap-2 mb-2">
                                            @php
                                                $presets = [50, 100, 250, 500];
                                                $maxAmount = $event->funding_goal ? (float) $remainingFunding : 99999;
                                            @endphp
                                            @foreach ($presets as $p)
                                                @if ($p <= $maxAmount)
                                                    <button type="button" class="amount-btn px-4 py-2 rounded-xl border-2 border-[#DCCFD8] text-[#213430] font-medium hover:border-[#DB69A2] hover:bg-[#FDF7FB] transition-colors" data-amount="{{ $p }}">${{ number_format($p) }}</button>
                                                @endif
                                            @endforeach
                                            <button type="button" class="amount-btn px-4 py-2 rounded-xl border-2 border-dashed border-[#DCCFD8] text-[#6C5B68] font-medium hover:border-[#DB69A2] transition-colors" data-amount="other">Other</button>
                                        </div>
                                        <div id="other-amount-wrap" class="hidden mt-2">
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-3 flex items-center text-[#6C5B68]">$</span>
                                                <input type="number" id="other-amount-input" min="0.50" step="0.01" placeholder="Enter amount" class="w-full pl-8 pr-4 py-2 border border-[#DCCFD8] rounded-xl focus:ring-2 focus:ring-[#DB69A2] focus:border-transparent" @if ($event->funding_goal) max="{{ $remainingFunding }}" @endif>
                                            </div>
                                            @if ($event->funding_goal)
                                                <p class="text-xs text-[#6C5B68] mt-1">Max: ${{ $formatAmount($remainingFunding) }}</p>
                                            @endif
                                        </div>
                                        @error('amount')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="mb-4">
                                        <details class="text-sm text-[#6C5B68]">
                                            <summary class="cursor-pointer hover:text-[#213430]">Add a message (optional)</summary>
                                            <textarea name="message" rows="2" class="mt-2 w-full px-4 py-2 border border-[#DCCFD8] rounded-xl focus:ring-2 focus:ring-[#DB69A2] focus:border-transparent" placeholder="Why you're supporting this event..." maxlength="500"></textarea>
                                        </details>
                                        @error('message')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <button type="submit" id="flexible-submit-btn" disabled class="w-full py-3 px-4 bg-gray-300 text-gray-500 font-semibold rounded-xl cursor-not-allowed">
                                        Pay &amp; Register
                                    </button>
                                </form>
                                <script>
                                    (function() {
                                        var form = document.getElementById('flexible-register-form');
                                        var amountInput = document.getElementById('flexible-amount');
                                        var otherWrap = document.getElementById('other-amount-wrap');
                                        var otherInput = document.getElementById('other-amount-input');
                                        var submitBtn = document.getElementById('flexible-submit-btn');
                                        var maxAmount = @json($event->funding_goal ? (float) $remainingFunding : null);
                                        var selectedPreset = null;
                                        function formatAmountDisplay(num) {
                                            return num % 1 === 0 ? String(num) : parseFloat(num).toFixed(2);
                                        }
                                        function setAmount(value) {
                                            var num = parseFloat(value);
                                            if (isNaN(num) || num < 0.5) { amountInput.value = ''; selectedPreset = null; submitBtn.disabled = true; submitBtn.className = 'w-full py-3 px-4 bg-gray-300 text-gray-500 font-semibold rounded-xl cursor-not-allowed'; return; }
                                            if (maxAmount != null && num > maxAmount) return;
                                            amountInput.value = num; selectedPreset = value; otherInput.value = value === 'other' ? '' : value;
                                            submitBtn.disabled = false;
                                            submitBtn.className = 'w-full py-3 px-4 bg-gradient-to-r from-[#DB69A2] to-[#C63A85] text-white font-semibold rounded-xl hover:opacity-95 transition-opacity';
                                            submitBtn.textContent = 'Pay $' + (value === 'other' ? (otherInput.value || '0') : formatAmountDisplay(num)) + ' & Register';
                                        }
                                        form.querySelectorAll('.amount-btn').forEach(function(btn) {
                                            btn.addEventListener('click', function() {
                                                var amt = btn.getAttribute('data-amount');
                                                form.querySelectorAll('.amount-btn').forEach(function(b) { b.classList.remove('border-[#DB69A2]', 'bg-[#FDF7FB]'); b.classList.add('border-[#DCCFD8]'); });
                                                btn.classList.add('border-[#DB69A2]', 'bg-[#FDF7FB]'); btn.classList.remove('border-[#DCCFD8]');
                                                if (amt === 'other') { otherWrap.classList.remove('hidden'); setAmount('other'); otherInput.focus(); return; }
                                                otherWrap.classList.add('hidden'); otherInput.value = ''; setAmount(amt);
                                            });
                                        });
                                        otherInput.addEventListener('input', function() {
                                            var v = this.value.replace(/[^0-9.]/g, '');
                                            this.value = v;
                                            if (v === '') { amountInput.value = ''; submitBtn.disabled = true; submitBtn.className = 'w-full py-3 px-4 bg-gray-300 text-gray-500 font-semibold rounded-xl cursor-not-allowed'; submitBtn.textContent = 'Pay & Register'; return; }
                                            var num = parseFloat(v);
                                            if (num < 0.5) return;
                                            if (maxAmount != null && num > maxAmount) {
                                                num = maxAmount;
                                                this.value = formatAmountDisplay(num);
                                            }
                                            amountInput.value = num; submitBtn.disabled = false; submitBtn.className = 'w-full py-3 px-4 bg-gradient-to-r from-[#DB69A2] to-[#C63A85] text-white font-semibold rounded-xl hover:opacity-95 transition-opacity'; submitBtn.textContent = 'Pay $' + formatAmountDisplay(num) + ' & Register';
                                        });
                                        form.addEventListener('submit', function(e) {
                                            if (!otherWrap.classList.contains('hidden') && otherInput.value) amountInput.value = parseFloat(otherInput.value) || '';
                                            if (!amountInput.value || parseFloat(amountInput.value) < 0.5) { e.preventDefault(); alert('Please choose or enter an amount (min $0.50).'); return false; }
                                        });
                                    })();
                                </script>
                            @endif
                        @else
                            <!-- Registration Closed -->
                            <div class="text-center">
                                <div
                                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m0 0v3m0-3h3m-3 0h-3m-3-6l3-3M6 12l3 3m6-6l3 3M18 12l-3 3"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-[#213430] mb-2">Registration Closed</h3>
                                <p class="text-[#6C5B68]">
                                    @if ($event->registration_deadline && now()->isAfter($event->registration_deadline))
                                        The registration deadline has passed.
                                    @elseif($event->isFullyFunded())
                                        This event is fully funded.
                                    @elseif($event->max_sponsors && $event->confirmedSponsorships->count() >= $event->max_sponsors)
                                        Maximum sponsors reached.
                                    @else
                                        Registration is currently closed.
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Event Stats -->
                    <div class="bg-[#FDF7FB] rounded-2xl border border-[#F1E5EF] p-6">
                        <h3 class="text-lg font-semibold text-[#213430] mb-4">Event Statistics</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-[#6C5B68]">Total Sponsors:</span>
                                <span
                                    class="font-semibold text-[#213430]">{{ $event->confirmedSponsorships->count() }}</span>
                            </div>
                            @if ($event->max_sponsors)
                                <div class="flex justify-between">
                                    <span class="text-[#6C5B68]">Max Sponsors:</span>
                                    <span class="font-semibold text-[#213430]">{{ $event->max_sponsors }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-[#6C5B68]">Event Date:</span>
                                <span class="font-semibold text-[#213430]">{{ $event->date->format('M j, Y') }}</span>
                            </div>
                            @if ($event->registration_deadline)
                                <div class="flex justify-between">
                                    <span class="text-[#6C5B68]">Registration Ends:</span>
                                    <span
                                        class="font-semibold text-[#213430]">{{ $event->registration_deadline->format('M j, Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
