@extends('sponsor.layouts.app')

@section('title', 'Payment')

@section('content')
    <main class="flex-1">
        <div class="max-w-8xl mx-auto px-4 py-6 space-y-8">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-[#213430]">Payment History</h1>
                    <p class="text-sm text-[#91848C]">Track and manage your sponsorship contributions here.</p>
                </div>
                <a href="{{ route('sponsor.events') }}" class="inline-flex items-center justify-center rounded-lg bg-[#DB69A2] px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-[#C4568E]">
                    Sponsor An Event
                </a>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-2xl border border-[#E5CADB] bg-white px-5 py-4 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-[#91848C]">Total Contributed</p>
                    <p class="mt-2 text-2xl font-semibold text-[#DB69A2]">${{ number_format($totals['total_amount'], 2) }}</p>
                </div>
                <div class="rounded-2xl border border-[#E5CADB] bg-white px-5 py-4 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-[#91848C]">Events Sponsored</p>
                    <p class="mt-2 text-2xl font-semibold text-[#213430]">{{ number_format($totals['programs_supported']) }}</p>
                </div>
                <div class="rounded-2xl border border-[#E5CADB] bg-white px-5 py-4 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-[#91848C]">Latest Contribution</p>
                    <p class="mt-2 text-2xl font-semibold text-[#213430]">
                        {{ $totals['latest_contribution'] ? '$' . number_format($totals['latest_contribution'], 2) : '--' }}
                    </p>
                    @if ($totals['latest_date'])
                        <p class="text-xs text-[#91848C] mt-1">on {{ $totals['latest_date']->format('M d, Y') }}</p>
                    @endif
                </div>
            </div>

            {{-- <div class="grid gap-6 lg:grid-cols-3">
                <button id="openFormBtn"
                    class="flex items-center justify-center h-full min-h-[220px] rounded-2xl border border-dashed border-[#C4B2BE] bg-[#F3E8EF] px-6 text-center text-[#213430] shadow-sm hover:border-[#DB69A2] hover:text-[#DB69A2]">
                    <div class="flex flex-col items-center space-y-2">
                        <img src="{{ asset('public/images/add-payment.svg') }}" alt="Add" class="w-8 h-8" />
                        <span class="font-semibold">Add Payment Method</span>
                        <span class="text-sm text-gray-500">Store a card for quick sponsorships</span>
                    </div>
                </button>

                <div id="cardPreview" class="lg:col-span-2 hidden lg:block">
                    <div
                        class="relative h-full rounded-2xl overflow-hidden bg-gradient-to-r from-[#4F1C3C] via-[#7D2858] to-[#E23F95] p-6 text-white shadow-xl">
                        <div class="absolute inset-0 rounded-2xl bg-black/30 pointer-events-none"></div>

                        <div class="relative z-10 flex justify-between items-start">
                            <img src="{{ asset('public/images/wifi.svg') }}" alt="Contactless" class="w-6 h-6" />
                            <span class="text-xl tracking-wider font-semibold">PINKME</span>
                        </div>
                        <div class="relative z-10 mt-10 text-2xl tracking-[0.35em]" id="previewCardNumber">**** **** **** ****</div>
                        <div class="relative z-10 mt-6 grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-xs uppercase text-white/70">Card Holder</p>
                                <p class="text-base font-semibold" id="previewName">CARDHOLDER NAME</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase text-white/70">Expires</p>
                                <p class="text-base font-semibold" id="previewExpiry">MM / YY</p>
                            </div>
                        </div>
                        <img id="previewLogo" src="{{ asset('public/images/payment-1.svg') }}"
                            class="absolute bottom-6 right-6 z-10 h-8" alt="Card Network" />
                    </div>
                </div>
            </div> --}}

            <div class="rounded-2xl border border-[#E5CADB] bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-[#E5CADB] px-5 py-4">
                    <h2 class="text-lg font-semibold text-[#213430]">Contribution Ledger</h2>
                    <span class="text-sm text-[#91848C]">
                        Showing {{ $payments->firstItem() ?? 0 }}-{{ $payments->lastItem() ?? 0 }} of {{ $payments->total() }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#E5CADB]">
                        <thead class="bg-[#F9EFF5] text-xs uppercase tracking-wide text-[#91848C]">
                            <tr>
                                <th class="px-5 py-3 text-left">Event</th>
                                <th class="px-5 py-3 text-left">Date</th>
                                <th class="px-5 py-3 text-left">Amount</th>
                                <th class="px-5 py-3 text-left">Funding Record</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1DCE8] text-sm text-[#213430]">
                            @forelse ($payments as $payment)
                                @php
                                    $eventTitle = optional($payment->event)->title ?? 'Event';
                                    $goalAmount = (float) ($payment->event?->funding_goal ?? 0);
                                    $raisedAmount = (float) ($payment->event?->confirmed_sponsorship_total ?? 0);
                                    $fundingSummary = $goalAmount > 0
                                        ? 'Goal: $' . number_format($goalAmount, 2) . ' | Raised: $' . number_format($raisedAmount, 2)
                                        : 'One-time contribution';
                                    $paymentDate = $payment->registered_at ?? $payment->created_at;
                                @endphp
                                <tr class="hover:bg-[#FDF4FA] transition">
                                    <td class="px-5 py-4 font-medium">{{ $eventTitle }}</td>
                                    <td class="px-5 py-4">{{ $paymentDate ? \Carbon\Carbon::parse($paymentDate)->format('M d, Y') : '--' }}</td>
                                    <td class="px-5 py-4 font-semibold text-[#DB69A2]">${{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-5 py-4 text-[#91848C]">{{ $fundingSummary }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-6 text-center text-[#91848C]">
                                        You have not recorded any sponsorship payments yet.
                                        <a href="{{ route('sponsor.events') }}" class="text-[#DB69A2] underline">Sponsor an event</a> to make your first contribution.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($payments instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="border-t border-[#E5CADB] px-5 py-4">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div id="formModal" class="hidden fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="relative w-full max-w-xl rounded-2xl border border-[#ECECEC] bg-white p-6 shadow-xl">
                <button id="closeFormBtn" type="button" class="absolute top-4 right-4 text-[#91848C] hover:text-[#213430]">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <h2 class="text-xl font-semibold text-[#213430] mb-4">Add Payment Method</h2>

                <div class="flex flex-wrap items-center gap-2 mb-6" id="paymentMethods">
                    @foreach (['payment-1.svg', 'payment-2.svg', 'payment-3.svg', 'payment-4.svg'] as $logo)
                        <div class="payment-option relative rounded-lg border border-[#DCCFD8] p-2 cursor-pointer" data-logo="{{ asset('public/images/' . $logo) }}">
                            <img src="{{ asset('public/images/' . $logo) }}" class="h-8" alt="Payment option">
                            <span class="check-icon absolute -top-2 -right-2 hidden h-5 w-5 rounded-full bg-[#DB69A2] text-white text-xs flex items-center justify-center">&#10003;</span>
                        </div>
                    @endforeach
                </div>

                <form id="paymentForm" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-[#213430]">Cardholder Name</label>
                        <input type="text" id="fullName" name="fullName" class="mt-1 w-full rounded-xl border border-[#E7CEDA] bg-white px-4 py-3 text-sm focus:border-[#DB69A2] focus:outline-none focus:ring-2 focus:ring-[#F0C5DE]" placeholder="Jane Cooper" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#213430]">Card Number</label>
                        <input type="text" id="cardInput" name="cardNumber" inputmode="numeric" maxlength="19" class="mt-1 w-full rounded-xl border border-[#E7CEDA] bg-white px-4 py-3 text-sm focus:border-[#DB69A2] focus:outline-none focus:ring-2 focus:ring-[#F0C5DE]" placeholder="0000 0000 0000 0000" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-[#213430]">Expiry</label>
                            <input type="text" id="expiry" name="expiry" maxlength="5" class="mt-1 w-full rounded-xl border border-[#E7CEDA] bg-white px-4 py-3 text-sm focus:border-[#DB69A2] focus:outline-none focus:ring-2 focus:ring-[#F0C5DE]" placeholder="MM/YY" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#213430]">CVV</label>
                            <input type="password" name="cvv" maxlength="4" class="mt-1 w-full rounded-xl border border-[#E7CEDA] bg-white px-4 py-3 text-sm focus:border-[#DB69A2] focus:outline-none focus:ring-2 focus:ring-[#F0C5DE]" placeholder="123" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-[#213430]">Country</label>
                            <input type="text" name="country" class="mt-1 w-full rounded-xl border border-[#E7CEDA] bg-white px-4 py-3 text-sm focus:border-[#DB69A2] focus:outline-none focus:ring-2 focus:ring-[#F0C5DE]" placeholder="Country" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#213430]">City</label>
                            <input type="text" name="city" class="mt-1 w-full rounded-xl border border-[#E7CEDA] bg-white px-4 py-3 text-sm focus:border-[#DB69A2] focus:outline-none focus:ring-2 focus:ring-[#F0C5DE]" placeholder="City" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-[#213430]">State</label>
                            <input type="text" name="state" class="mt-1 w-full rounded-xl border border-[#E7CEDA] bg-white px-4 py-3 text-sm focus:border-[#DB69A2] focus:outline-none focus:ring-2 focus:ring-[#F0C5DE]" placeholder="State" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#213430]">Postal Code</label>
                            <input type="text" name="postal" class="mt-1 w-full rounded-xl border border-[#E7CEDA] bg-white px-4 py-3 text-sm focus:border-[#DB69A2] focus:outline-none focus:ring-2 focus:ring-[#F0C5DE]" placeholder="Postal Code" />
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-[#91848C]">
                        <input type="checkbox" id="makeDefault" class="h-4 w-4 rounded border border-[#C4B2BE] text-[#DB69A2] focus:ring-[#DB69A2]" />
                        <label for="makeDefault">Make this my default payment method</label>
                    </div>
                    <div class="flex justify-end gap-3 pt-3">
                        <button type="button" class="rounded-xl border border-[#E7CEDA] px-5 py-2.5 text-sm text-[#91848C] hover:bg-[#F7EDF4]" data-form-cancel>Cancel</button>
                        <button type="submit" class="rounded-xl bg-[#DB69A2] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#C4568E]">Save Method</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var openFormBtn = document.getElementById('openFormBtn');
            var closeFormBtn = document.getElementById('closeFormBtn');
            var formModal = document.getElementById('formModal');
            var paymentForm = document.getElementById('paymentForm');
            var cardPreview = document.getElementById('cardPreview');
            var cardInput = document.getElementById('cardInput');
            var previewCardNumber = document.getElementById('previewCardNumber');
            var previewName = document.getElementById('previewName');
            var previewExpiry = document.getElementById('previewExpiry');
            var previewLogo = document.getElementById('previewLogo');
            var selectedLogo = previewLogo ? previewLogo.getAttribute('src') : null;

            function openModal() {
                if (formModal) {
                    formModal.classList.remove('hidden');
                }
            }

            function closeModal() {
                if (formModal) {
                    formModal.classList.add('hidden');
                }
            }

            if (openFormBtn) {
                openFormBtn.addEventListener('click', openModal);
            }
            if (closeFormBtn) {
                closeFormBtn.addEventListener('click', closeModal);
            }
            if (formModal) {
                formModal.addEventListener('click', function (event) {
                    if (event.target === formModal) {
                        closeModal();
                    }
                });
            }
            document.querySelectorAll('[data-form-cancel]').forEach(function (btn) {
                btn.addEventListener('click', closeModal);
            });

            var paymentOptions = document.querySelectorAll('.payment-option');
            paymentOptions.forEach(function (option) {
                option.addEventListener('click', function () {
                    paymentOptions.forEach(function (other) {
                        other.classList.remove('border-[#DB69A2]');
                        var icon = other.querySelector('.check-icon');
                        if (icon) {
                            icon.classList.add('hidden');
                        }
                    });

                    option.classList.add('border-[#DB69A2]');
                    var optionIcon = option.querySelector('.check-icon');
                    if (optionIcon) {
                        optionIcon.classList.remove('hidden');
                    }
                    selectedLogo = option.getAttribute('data-logo');
                });
            });

            if (cardInput) {
                cardInput.addEventListener('input', function () {
                    var digits = cardInput.value.replace(/\D/g, '').slice(0, 16);
                    var masked = digits.padStart(16, '*');
                    var formatted = masked.replace(/(.{4})/g, '$1 ').trim();
                    if (previewCardNumber) {
                        previewCardNumber.textContent = formatted || '**** **** **** ****';
                    }
                });
            }

            if (paymentForm) {
                paymentForm.addEventListener('submit', function (event) {
                    event.preventDefault();

                    var nameField = document.getElementById('fullName');
                    var expiryField = document.getElementById('expiry');
                    var name = nameField ? nameField.value.trim() : '';
                    var expiry = expiryField ? expiryField.value.trim() : '';
                    var digits = cardInput ? cardInput.value.replace(/\D/g, '').slice(0, 16) : '';
                    var masked = digits.padStart(16, '*');
                    var formatted = masked.replace(/(.{4})/g, '$1 ').trim();

                    if (previewCardNumber) {
                        previewCardNumber.textContent = formatted ? formatted : '**** **** **** ****';
                    }
                    if (previewName) {
                        previewName.textContent = name ? name.toUpperCase() : 'CARDHOLDER NAME';
                    }
                    if (previewExpiry) {
                        previewExpiry.textContent = expiry ? expiry : 'MM / YY';
                    }
                    if (previewLogo && selectedLogo) {
                        previewLogo.setAttribute('src', selectedLogo);
                    }

                    if (cardPreview) {
                        cardPreview.classList.remove('hidden');
                    }

                    closeModal();
                    paymentForm.reset();
                });
            }
        });
    </script>
@endpush
