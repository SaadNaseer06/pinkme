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
                            <th class="p-2 text-lg text-[#91848C] font-normal">ID</th>
                            <th class="p-2 text-lg text-[#91848C] font-normal">Patient</th>
                            <th class="p-2 text-lg text-[#91848C] font-normal">Title</th>
                            <th class="p-2 text-lg text-[#91848C] font-normal">Program</th>
                            <th class="p-2 text-lg text-[#91848C] font-normal">Sent</th>
                            <th class="p-2 text-lg text-[#91848C] font-normal">Status</th>
                            <th class="p-2 text-lg text-[#91848C] font-normal">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($applications as $app)
                            <tr class="border-t border-[#e0cfd8]">
                                <td class="p-2">APP-{{ str_pad((string) $app->id, 6, '0', STR_PAD_LEFT) }}</td>
                                <td class="p-2">{{ $app->patient?->user?->profile?->full_name ?? '—' }}</td>
                                <td class="p-2">{{ $app->title }}</td>
                                <td class="p-2">{{ optional($app->program)->title ?? '—' }}</td>
                                <td class="p-2">{{ $app->sent_to_finance_at ? $app->sent_to_finance_at->format('M d, Y') : '—' }}</td>
                                <td class="p-2">
                                    @if ($app->invoices->isNotEmpty())
                                        <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-700">Budget Allocated</span>
                                    @else
                                        <span class="px-2 py-1 rounded text-xs bg-amber-100 text-amber-700">Pending</span>
                                    @endif
                                </td>
                                <td class="p-2">
                                    <a href="{{ route('finance.applications.show', $app) }}" class="text-[#9E2469] hover:underline text-sm">View</a>
                                    @if (!$app->invoices->isNotEmpty())
                                        | <a href="{{ route('finance.invoice.create', $app) }}" class="text-[#9E2469] hover:underline text-sm">Allocate Budget</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr class="border-t border-[#e0cfd8]">
                                <td colspan="7" class="p-6 text-center text-[#91848C]">No applications assigned to you yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $applications->links() }}</div>
        </div>
    </div>
</main>
@endsection
