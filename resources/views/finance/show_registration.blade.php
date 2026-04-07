@extends('finance.layouts.app')

@section('title', 'View Registration')

@section('content')
<main>
    <div class="max-w-8xl mx-auto mt-6 px-5">
        {{-- @if (session('success'))
            <div class="mb-4 rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-green-800">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-red-800">{{ session('error') }}</div>
        @endif --}}

        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('finance.registrations') }}" class="text-[#9E2469] hover:underline flex items-center gap-1">
                <i class="fas fa-arrow-left"></i> Back to Requests
            </a>
            @if (!$registration->registrationInvoices->isNotEmpty())
                <a href="{{ route('finance.invoice.create', $registration) }}" class="bg-[#9E2469] text-white px-4 py-2 rounded-md text-sm hover:bg-[#B52D75]">
                    <i class="fas fa-dollar-sign mr-1"></i> Allocate Budget (Generate Invoice)
                </a>
            @else
                <span class="px-4 py-2 rounded-md text-sm bg-green-100 text-green-700">Budget Already Allocated</span>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-[#F3E8EF] p-6 rounded-xl">
                <h3 class="text-lg font-semibold text-[#213430] mb-3">Applicant Info</h3>
                <hr class="border-[#DCCFD8] mb-4" />
                <div class="space-y-5">
                    <div class="flex justify-between"><span class="font-medium">Name</span><span>{{ $registration->full_name ?? 'N/A' }}</span></div>
                    <div class="flex justify-between"><span class="font-medium">Email</span><span>{{ $registration->email ?? 'N/A' }}</span></div>
                    <div class="flex justify-between"><span class="font-medium">Phone</span><span>{{ $registration->phone ?? 'N/A' }}</span></div>
                </div>
            </div>
            <div class="bg-[#F3E8EF] p-6 rounded-xl">
                <h3 class="text-lg font-semibold text-[#213430] mb-3">Application Info</h3>
                <hr class="border-[#DCCFD8] mb-4" />
                <div class="space-y-5">
                    <div class="flex justify-between"><span class="font-medium">Program</span><span>{{ optional($registration->program)->title ?? 'N/A' }}</span></div>
                    <div class="flex justify-between"><span class="font-medium">Status</span><span>{{ ucfirst($registration->status) }}</span></div>
                    <div class="flex justify-between"><span class="font-medium">Approved by</span><span>{{ optional($registration->assignedCaseManager?->profile)->full_name ?? 'N/A' }}</span></div>
                </div>
            </div>
        </div>

        @php
            $billingUrlRows = old('billing_urls');
            if (! is_array($billingUrlRows)) {
                $billingUrlRows = collect(preg_split('/\r\n|\r|\n/', (string) ($registration->payment_links ?? '')))
                    ->map(fn ($l) => trim((string) $l))
                    ->filter()
                    ->values()
                    ->all();
            }
            if (count($billingUrlRows) === 0) {
                $billingUrlRows = [''];
            }
        @endphp
        <div class="mt-6 bg-[#F3E8EF] rounded-lg p-6">
            <h3 class="text-lg font-semibold text-[#213430] mb-2">Billing payment links</h3>
            <p class="text-sm text-[#6C5F67] mb-4">Add one payment portal URL per row. Use <strong>Add link</strong> for more rows. Saved links are visible to administrators and case managers.</p>
            <hr class="border-[#DCCFD8] mb-4" />
            <form method="POST" action="{{ route('finance.registrations.billing_payment_links', $registration) }}" class="space-y-4" id="billing-links-form">
                @csrf
                <div id="billing-url-rows" class="space-y-2">
                    @foreach ($billingUrlRows as $rowUrl)
                        <div class="billing-url-row flex flex-wrap gap-2 items-center">
                            <input type="text"
                                name="billing_urls[]"
                                value="{{ $rowUrl }}"
                                placeholder="https://payment.example.org/patient"
                                autocomplete="off"
                                class="flex-1 min-w-[12rem] rounded-md border bg-white px-3 py-2 text-sm text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-[#9E2469]/30 {{ $errors->has('billing_urls') ? 'border-red-400 ring-1 ring-red-400' : 'border-[#DCCFD8]' }}">
                            <button type="button"
                                class="billing-url-remove shrink-0 px-3 py-2 rounded-md border border-[#91848C] text-[#213430] text-sm hover:bg-white disabled:opacity-40 disabled:cursor-not-allowed"
                                title="Remove this row">
                                Remove
                            </button>
                        </div>
                    @endforeach
                </div>
                @error('billing_urls')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div class="flex flex-wrap items-center gap-3 pt-1">
                    <button type="button" id="billing-url-add"
                        class="px-4 py-2 rounded-md border border-[#9E2469] text-[#9E2469] text-sm font-medium hover:bg-[#FDE8F3]">
                        Add link
                    </button>
                    <button type="submit" class="bg-[#9E2469] text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-[#B52D75]">
                        Save billing links
                    </button>
                </div>
            </form>
            @if (filled($registration->payment_links))
                <div class="mt-5 rounded-lg border border-[#DCCFD8] bg-white/90 p-4">
                    <p class="text-xs font-semibold text-[#213430] mb-2">Open links (preview)</p>
                    <div class="text-sm text-[#213430]">
                        {!! \App\Support\BillingPaymentLinks::toHtml($registration->payment_links) !!}
                    </div>
                </div>
            @endif
        </div>

        <template id="billing-url-row-template">
            <div class="billing-url-row flex flex-wrap gap-2 items-center">
                <input type="text"
                    name="billing_urls[]"
                    value=""
                    placeholder="https://payment.example.org/patient"
                    autocomplete="off"
                    class="flex-1 min-w-[12rem] rounded-md border border-[#DCCFD8] bg-white px-3 py-2 text-sm text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-[#9E2469]/30">
                <button type="button"
                    class="billing-url-remove shrink-0 px-3 py-2 rounded-md border border-[#91848C] text-[#213430] text-sm hover:bg-white disabled:opacity-40 disabled:cursor-not-allowed"
                    title="Remove this row">
                    Remove
                </button>
            </div>
        </template>

        <div class="mt-6 bg-[#F3E8EF] rounded-lg p-6">
            <h3 class="text-lg font-semibold text-[#213430] mb-3">Bill Statement Attachments</h3>
            <hr class="border-[#DCCFD8] mb-4" />
            @if (!empty($registration->bill_statements))
                <ul class="space-y-2">
                    @foreach ($registration->bill_statements as $index => $bill)
                        <li class="flex items-center justify-between gap-2 bg-white rounded-md px-4 py-3 border border-[#E5D2DE]">
                            <span class="text-[#213430] truncate">{{ $bill['filename'] }}</span>
                            <div class="flex items-center gap-3 shrink-0">
                                <a href="{{ route('finance.registrations.bill_statement.download', [$registration, $index]) }}?preview=1" target="_blank" class="text-[#9E2469] font-medium hover:underline">Preview</a>
                                <a href="{{ route('finance.registrations.bill_statement.download', [$registration, $index]) }}" class="text-[#213430] font-medium hover:underline">Download</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-[#6C5F67]">No bill statements uploaded.</p>
            @endif
        </div>

        @if ($registration->registrationInvoices->isNotEmpty())
            <div class="mt-6 bg-[#F3E8EF] rounded-lg p-6">
                <h3 class="text-lg font-semibold text-[#213430] mb-3">Generated Invoices</h3>
                <hr class="border-[#DCCFD8] mb-4" />
                <ul class="space-y-2">
                    @foreach ($registration->registrationInvoices as $inv)
                        <li class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                            <span>{{ $inv->invoice_number }} - ${{ number_format($inv->amount, 2) }} ({{ $inv->payment_purpose }})</span>
                            <div class="flex items-center gap-3">
                                <span class="text-green-600">{{ $inv->status }}</span>
                                <form method="POST" action="{{ route('finance.invoice.resend_emails', [$registration, $inv]) }}" class="inline" onsubmit="return confirm('Resend invoice emails to the patient and admins?');">
                                    @csrf
                                    <button type="submit" class="text-sm text-[#9E2469] hover:underline">Resend invoice email (PDF)</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</main>
@endsection

@push('scripts')
    <script>
        (function () {
            const container = document.getElementById('billing-url-rows');
            const template = document.getElementById('billing-url-row-template');
            const addBtn = document.getElementById('billing-url-add');
            if (!container || !template || !addBtn) return;

            function updateRemoveState() {
                const rows = container.querySelectorAll('.billing-url-row');
                const disable = rows.length <= 1;
                rows.forEach(function (row) {
                    const btn = row.querySelector('.billing-url-remove');
                    if (btn) btn.disabled = disable;
                });
            }

            addBtn.addEventListener('click', function () {
                const node = template.content.firstElementChild.cloneNode(true);
                const input = node.querySelector('input');
                if (input) input.value = '';
                container.appendChild(node);
                updateRemoveState();
                if (input) input.focus();
            });

            container.addEventListener('click', function (e) {
                const btn = e.target.closest('.billing-url-remove');
                if (!btn || btn.disabled) return;
                const row = btn.closest('.billing-url-row');
                if (!row || container.querySelectorAll('.billing-url-row').length <= 1) return;
                row.remove();
                updateRemoveState();
            });

            updateRemoveState();
        })();
    </script>
@endpush
