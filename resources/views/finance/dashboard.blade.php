@extends('finance.layouts.app')

@section('title', 'Finance Dashboard')

@section('content')
<main class="flex-1">
    <div class="max-w-8xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-[#F3E8EF] rounded-lg px-4 py-4 flex items-center justify-between">
                <div class="bg-[#91848C] p-3 rounded-full mr-4">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-[#213430] font-semibold">Pending Budget Allocation</h3>
                    <p class="text-md font-normal text-[#91848C] text-right">{{ $pendingRegistrations->total() }}</p>
                </div>
            </div>
            <div class="bg-[#C5E8D1] rounded-lg px-4 py-4 flex items-center justify-between">
                <div class="bg-[#20B354] p-3 rounded-full mr-4">
                    <i class="fas fa-check text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-[#213430] font-semibold">Budget Allocated</h3>
                    <p class="text-md font-normal text-[#20B354] text-right">{{ $allocatedCount }}</p>
                </div>
            </div>
        </div>

        <div class="bg-[#F3E8EF] rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-[#213430]">Patient Requests Awaiting Budget Allocation</h2>
                <a href="{{ route('finance.registrations') }}" class="text-[#9E2469] hover:underline text-sm">View All</a>
            </div>

            <div class="table-container">
                <table class="min-w-full text-sm text-left">
                    <thead>
                        <tr class="border-t border-[#e0cfd8]">
                            <th class="p-2 text-lg text-[#91848C] font-normal">Applicant</th>
                            <th class="p-2 text-lg text-[#91848C] font-normal">Program</th>
                            <th class="p-2 text-lg text-[#91848C] font-normal">Sent</th>
                            <th class="p-2 text-lg text-[#91848C] font-normal">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingRegistrations as $reg)
                            <tr class="border-t border-[#e0cfd8]">
                                <td class="p-2">{{ $reg->full_name ?? $reg->email ?? '—' }}</td>
                                <td class="p-2">{{ optional($reg->program)->title ?? '—' }}</td>
                                <td class="p-2">{{ $reg->sent_to_finance_at ? $reg->sent_to_finance_at->format('M d, Y') : '—' }}</td>
                                <td class="p-2">
                                    <a href="{{ route('finance.registrations.show', $reg) }}" class="text-[#9E2469] hover:underline text-sm">View</a>
                                    |
                                    <a href="{{ route('finance.invoice.create', $reg) }}" class="text-[#9E2469] hover:underline text-sm">Allocate Budget</a>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-t border-[#e0cfd8]">
                                <td colspan="4" class="p-6 text-center text-[#91848C]">No pending requests.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $pendingRegistrations->links() }}</div>
        </div>
    </div>
</main>
@endsection
