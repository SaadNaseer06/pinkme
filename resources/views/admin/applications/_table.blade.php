@php
    use Illuminate\Contracts\Pagination\LengthAwarePaginator;
    use Carbon\Carbon;

    $fmtDate = fn($dt) => $dt ? Carbon::parse($dt)->format('Y-m-d') : '—';
    $appCode = fn($app) => $app->code ?: 'APP-' . str_pad((string) $app->id, 6, '0', STR_PAD_LEFT);

    $statusBadge = function ($status) {
        $map = [
            'approved' => ['bg' => '#C5E8D1', 'text' => '#20B354', 'label' => 'Approved'],
            'rejected' => ['bg' => '#E8C5C5', 'text' => '#B32020', 'label' => 'Rejected'],
            'under_review' => ['bg' => '#E4D7DF', 'text' => '#91848C', 'label' => 'Under Review'],
            'pending' => ['bg' => '#E4D7DF', 'text' => '#91848C', 'label' => 'Pending'],
        ];
        $key = str_replace(' ', '_', strtolower((string) $status));
        $cfg = $map[$key] ?? $map['pending'];
        return sprintf(
            '<span class="px-4 py-2 rounded-sm text-xs font-medium app-text" style="background:%s;color:%s">%s</span>',
            e($cfg['bg']),
            e($cfg['text']),
            e($cfg['label']),
        );
    };
@endphp

<div class="table-container">
    <table class="min-w-full text-sm text-left mt-6 relative overflow-visible">
        <thead>
            <tr class="border-t border-[#e0cfd8]">
                <th class="p-2 text-lg text-[#91848C] font-normal app-h">Patient Name</th>
                <th class="p-2 text-lg text-[#91848C] font-normal app-h pad-left">Applications ID</th>
                <th class="p-2 text-lg text-[#91848C] font-normal app-h">Sub. Date</th>
                <th class="p-2 text-lg text-[#91848C] font-normal app-h">Email</th>
                <th class="p-2 text-lg text-[#91848C] font-normal app-h">Contact</th>
                <th class="p-2 text-lg text-[#91848C] font-normal app-h">Assigned Case Manager</th>
                <th class="p-2 text-lg text-[#91848C] font-normal app-h">Status</th>
                <th class="p-2 text-lg text-[#91848C] font-normal app-h">Action</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            @forelse ($apps as $app)
                @php
                    $patientProfile = $app->patient?->user?->profile;
                    $reviewerProfile = $app->reviewer?->profile;
                    $patientName = $patientProfile->full_name ?? 'Unknown';
                    $email = $app->patient?->user?->email ?? '—';
                    $contact = $patientProfile->phone ?? '—';
                    $reviewerName = $reviewerProfile->full_name ?? '—';
                @endphp
                <tr class="border-t border-[#e0cfd8]">
                    <td class="p-2">{{ $patientName }}</td>
                    <td class="p-2 pad-left">{{ $appCode($app) }}</td>
                    <td class="p-2">{{ $fmtDate($app->created_at) }}</td>
                    <td class="p-2">{{ $email }}</td>
                    <td class="p-2">{{ $contact }}</td>
                    <td class="p-2">{{ $reviewerName }}</td>
                    <td class="p-2">
                        @if ($app->missingRequests->isNotEmpty())
                            <span class="px-4 py-2 rounded-sm text-xs font-medium app-text" style="background:#FFF2CC; color:#D9980D;">Missing Docs Requested</span>
                        @else
                            {!! $statusBadge($app->status) !!}
                        @endif
                    </td>
                    <td class="p-2 relative">
                        <button onclick="toggleDropdown(this)" class="text-[#213430] p-2 rounded-md focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                            </svg>
                        </button>
                        <div class="absolute right-[62px] top-10 w-[200px] max-w-none bg-[#F6EDF5] rounded-lg shadow-lg py-2 z-20 hidden">
                            <a href="{{ route('admin.viewApplication', $app->id) }}" class="flex items-center px-4 py-2 text-[#91848C] hover:bg-pink-100 text-sm">
                                <i class="fas fa-eye mr-2"></i> View Details
                            </a>
                            @if ($app->reviewer_id)
                                <div class="px-4 py-2 text-sm text-[#6C5F67] border-b border-[#E5D6E0]">
                                    <span class="font-medium text-[#213430]">Case Manager:</span> {{ $reviewerName }}
                                </div>
                                <a href="#" onclick="openAssignModal({{ $app->id }}, {{ $app->reviewer_id }}, @json($reviewerName))" class="flex items-center px-4 py-2 text-[#91848C] hover:bg-pink-100 text-sm">
                                    <i class="fas fa-user-edit mr-2"></i> Change Case Manager
                                </a>
                            @else
                                <a href="#" onclick="openAssignModal({{ $app->id }}, null, null)" class="flex items-center px-4 py-2 text-[#91848C] hover:bg-pink-100 text-sm">
                                    <i class="fas fa-user-plus mr-2"></i> Assign Case Manager
                                </a>
                            @endif
                            <a href="#" onclick="openRejectModal({{ $app->id }})" class="flex items-center px-4 py-2 gap-2 text-[#91848C] text-sm">
                                <i class="fa-solid fa-trash"></i> Delete Application
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr class="border-t border-[#e0cfd8]">
                    <td colspan="8" class="p-6 text-center text-[#91848C] app-text">
                        No applications found{{ $range ? ' in the selected range' : '' }}.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if ($apps instanceof LengthAwarePaginator && $apps->hasPages())
    @php
        $current = $apps->currentPage();
        $last = $apps->lastPage();
        $start = max(1, $current - 1);
        $end = min($last, $start + 2);
        $start = max(1, $end - 2);
        $pages = range($start, $end);
    @endphp
    <div class="mt-6 flex justify-end space-x-1">
        @if ($apps->onFirstPage())
            <span class="px-3 py-1 rounded-md border border-[#B9B1B6] text-[#91848C] opacity-60 cursor-not-allowed">&lt;</span>
        @else
            <a href="{{ $apps->previousPageUrl() }}" class="px-3 py-1 rounded-md border border-[#B9B1B6] text-[#91848C]">&lt;</a>
        @endif

        @foreach ($pages as $page)
            @if ($page == $current)
                <span class="px-4 py-1 rounded-md bg-[#9E2469] text-white">{{ $page }}</span>
            @else
                <a href="{{ $apps->url($page) }}" class="px-3 py-1 rounded-md border border-[#B9B1B6] text-[#91848C]">{{ $page }}</a>
            @endif
        @endforeach

        @if ($current < $last)
            <a href="{{ $apps->nextPageUrl() }}" class="px-3 py-1 rounded-md border border-[#B9B1B6] text-[#91848C]">&gt;</a>
        @else
            <span class="px-3 py-1 rounded-md border border-[#B9B1B6] text-[#91848C] opacity-60 cursor-not-allowed">&gt;</span>
        @endif
    </div>
@endif
