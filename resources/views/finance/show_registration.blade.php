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

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
            <div class="bg-[#F3E8EF] p-6 rounded-xl">
                <h3 class="text-lg font-semibold text-[#213430] mb-3">Details</h3>
                <hr class="border-[#DCCFD8] mb-4" />
                <div class="space-y-5">
                    <div><span class="font-medium">Story</span><p class="text-[#91848C] mt-1">{{ Str::limit($registration->story ?? 'N/A', 200) }}</p></div>
                </div>
            </div>
        </div>

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
                        <li class="flex justify-between items-center">
                            <span>{{ $inv->invoice_number }} - ${{ number_format($inv->amount, 2) }} ({{ $inv->payment_purpose }})</span>
                            <span class="text-green-600">{{ $inv->status }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</main>
@endsection
