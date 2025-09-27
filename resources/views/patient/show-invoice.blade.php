@extends('patient.layouts.app')

@section('title', 'Invoice Details')

@section('content')

    <!-- Invoice Details -->
    <main class="flex-1">
        <div class="mt-6 bg-[#F6EAF0] rounded-lg p-6 max-w-xl mx-auto">
            <h2 class="text-xl font-semibold text-[#213430] mb-4">
                Invoice {{ $invoice->invoice_number ?? '' }}
            </h2>
            <div class="bg-white rounded-lg p-4 divide-y divide-[#E5D3E9]">
                <div class="py-3 flex justify-between">
                    <span class="font-medium text-[#213430]">Application</span>
                    <span class="text-[#213430]">{{ $invoice->application->title ?? '—' }}</span>
                </div>
                <div class="py-3 flex justify-between">
                    <span class="font-medium text-[#213430]">Issue Date</span>
                    <span class="text-[#213430]">{{ \Carbon\Carbon::parse($invoice->issue_date)->format('M d, Y') }}</span>
                </div>
                <div class="py-3 flex justify-between">
                    <span class="font-medium text-[#213430]">Purpose</span>
                    <span class="text-[#213430]">{{ $invoice->payment_purpose }}</span>
                </div>
                <div class="py-3 flex justify-between">
                    <span class="font-medium text-[#213430]">Amount</span>
                    <span class="text-[#213430]">${{ number_format($invoice->amount, 0) }}</span>
                </div>
                <div class="py-3 flex justify-between">
                    <span class="font-medium text-[#213430]">Method</span>
                    <span class="text-[#213430]">{{ $invoice->payment_method }}</span>
                </div>
                <div class="py-3 flex justify-between">
                    <span class="font-medium text-[#213430]">Status</span>
                    <span class="text-[#213430]">{{ $invoice->status }}</span>
                </div>
                @if (!empty($invoice->notes))
                    <div class="py-3">
                        <span class="font-medium text-[#213430] block">Notes</span>
                        <p class="text-[#213430] mt-1">{{ $invoice->notes }}</p>
                    </div>
                @endif
                @if ($invoice->file_path)
                    <div class="py-3">
                        <a href="{{ route('invoices.download', $invoice) }}"
                            class="text-[#DB69A2] underline flex items-center gap-1">
                            <img src="{{ asset('images/download.svg') }}" alt="" class="w-4 h-4" />
                            Download PDF
                        </a>
                    </div>
                @endif
            </div>
            <div class="mt-4">
                <a href="{{ route('patient.invoices') }}" class="text-[#DB69A2] underline flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 18l-6-6 6-6" />
                    </svg>
                    Back to Invoices
                </a>
            </div>
        </div>
    </main>

@endsection
@section('scripts')
    <script src="{{ asset('js/patient/dashboard.js') }}"></script>
@endsection