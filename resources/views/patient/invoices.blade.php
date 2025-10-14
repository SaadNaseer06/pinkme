@extends('patient.layouts.app')

@section('title', 'Invoices')

@section('content')

    <!-- Dashboard Content -->
    <main class="flex-1">

        <div class="mt-6 bg-[#F3E8EF] rounded-lg p-6">
            <div class="flex justify-between items-center mb-4 ml-3">
                <h2 class="text-xl font-semibold text-[#213430] app-main">
                    Invoices
                </h2>

            </div>
            <div class="table-container">
                <table class="min-w-full text-sm text-left mt-6">
                    <thead>
                        <tr class="border-t border-[#e0cfd8]">
                            <th class="p-2"><input type="checkbox" …></th>
                            <th class="p-2">Invoice #</th>
                            <th class="p-2">Application</th>
                            <th class="p-2">Issue Date</th>
                            <th class="p-2">Purpose</th>
                            <th class="p-2 text-center">Amount</th>
                            <th class="p-2">Method</th>
                            <th class="p-2">Status</th>
                            <th class="p-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @forelse($invoices as $invoice)
                            <tr class="border-t border-[#e0cfd8]">
                                <td class="p-2"><input type="checkbox" …></td>
                                <td class="p-2">{{ $invoice->invoice_number }}</td>
                                <td class="p-2">{{ $invoice->application->title }}</td>
                                <td class="p-2">{{ $invoice->issue_date->format('M d, Y') }}</td>
                                <td class="p-2">{{ $invoice->payment_purpose }}</td>
                                <td class="p-2 text-center">${{ number_format($invoice->amount, 2) }}</td>
                                <td class="p-2">{{ $invoice->payment_method }}</td>
                                <td class="p-2">{{ $invoice->status }}</td>
                                <td class="p-2">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="underline">View</a>
                                    @if ($invoice->file_path)
                                        | <a href="{{ route('invoices.download', $invoice) }}"
                                            class="underline">Download</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-4 text-center text-[#91848C]">No invoices found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script src="{{ asset('js/patient/dashboard.js') }}"></script>
@endsection
