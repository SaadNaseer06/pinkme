@extends('finance.layouts.app')

@section('title', 'Patient Requests')

@section('content')
<main class="flex-1">
    <div class="max-w-8xl mx-auto">
        <div class="bg-[#F3E8EF] rounded-lg p-6">
            <h2 class="text-xl font-semibold text-[#213430] mb-4">Assignments Sent to You</h2>
            <div class="table-container">
                <table class="min-w-full text-sm text-left">
                    <thead>
                        <tr class="border-t border-[#e0cfd8]">
                            <th class="p-2 text-lg text-[#91848C] font-normal">Applicant</th>
                            <th class="p-2 text-lg text-[#91848C] font-normal">Program</th>
                            <th class="p-2 text-lg text-[#91848C] font-normal">Sent</th>
                            <th class="p-2 text-lg text-[#91848C] font-normal">Status</th>
                            <th class="p-2 text-lg text-[#91848C] font-normal">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($registrations as $reg)
                            <tr class="border-t border-[#e0cfd8]">
                                <td class="p-2">{{ $reg->full_name ?? $reg->email ?? '—' }}</td>
                                <td class="p-2">{{ optional($reg->program)->title ?? '—' }}</td>
                                <td class="p-2">{{ $reg->sent_to_finance_at ? $reg->sent_to_finance_at->format('M d, Y') : '—' }}</td>
                                <td class="p-2">
                                    @if ($reg->registrationInvoices->isNotEmpty())
                                        <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-700">Budget Allocated</span>
                                    @else
                                        <span class="px-2 py-1 rounded text-xs bg-amber-100 text-amber-700">Pending</span>
                                    @endif
                                </td>
                                <td class="p-2">
                                    <a href="{{ route('finance.registrations.show', $reg) }}" class="text-[#9E2469] hover:underline text-sm">View</a>
                                    @if (!$reg->registrationInvoices->isNotEmpty())
                                        | <a href="{{ route('finance.invoice.create', $reg) }}" class="text-[#9E2469] hover:underline text-sm">Allocate Budget</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr class="border-t border-[#e0cfd8]">
                                <td colspan="5" class="p-6 text-center text-[#91848C]">No registrations assigned to you yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $registrations->links() }}</div>
        </div>
    </div>
</main>
@endsection
