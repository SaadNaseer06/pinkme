<div id="pastGrantCyclesModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-xl max-h-[85vh] flex flex-col" role="dialog" aria-labelledby="pastGrantCyclesModalTitle" aria-modal="true">
        <div class="flex items-start justify-between gap-4 p-6 border-b border-gray-100">
            <div>
                <h3 id="pastGrantCyclesModalTitle" class="text-lg font-semibold text-gray-900">Past grant cycles</h3>
                <p class="text-sm text-gray-500 mt-1">Download a saved file for any closed May–June or Nov–Dec period.</p>
            </div>
            <button type="button" id="pastGrantCyclesModalClose"
                class="h-8 w-8 inline-flex items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-gray-50 shrink-0"
                aria-label="Close">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-6 overflow-y-auto flex-1 space-y-3">
            @forelse ($closedGrantCycles as $cycle)
                @php
                    $filename = \App\Support\FinancialAssistanceApplicationPeriod::archiveFilename($cycle['key']);
                @endphp
                <div class="rounded-xl border border-[#E6D8E1] bg-[#FDF7FB] p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <p class="font-semibold text-[#213430]">{{ $cycle['label'] }}</p>
                        <p class="text-sm text-[#6C5F67] mt-0.5">{{ number_format($cycle['count']) }} applications</p>
                    </div>
                    <a href="{{ route('admin.registrations.archives.export', $cycle['key']) }}"
                        class="admin-btn-primary text-sm justify-center shrink-0">
                        Download
                    </a>
                </div>
            @empty
                <p class="text-sm text-[#6C5F67] text-center py-6">No closed cycles yet. Periods appear here after May–June or Nov–Dec ends.</p>
            @endforelse
        </div>

        <div class="p-6 pt-0 flex justify-end">
            <button type="button" id="pastGrantCyclesModalDone"
                class="px-4 py-2 rounded-md border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50">
                Close
            </button>
        </div>
    </div>
</div>
