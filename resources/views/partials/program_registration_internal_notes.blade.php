@php
    $showFinanceNote = filled($registration->internal_note_for_finance ?? null);
    $showAdminNote = filled($registration->internal_note_for_admin ?? null);
@endphp

@if ($showFinanceNote || $showAdminNote)
    <div class="bg-[#FFF9E6] rounded-lg p-5 md:p-6 space-y-4 border border-[#E8D9A8]">
        <div>
            <h3 class="text-xl font-semibold text-[#213430] app-main">Internal Notes</h3>
            <p class="text-sm text-[#6C5F67] app-text mt-1">Staff only — not visible to the applicant.</p>
        </div>

        @if ($showFinanceNote)
            <div>
                <p class="text-sm font-semibold text-[#213430] app-main">Case manager → Finance</p>
                <div class="mt-2 rounded-md border border-[#E8D9A8] bg-white px-3 py-2">
                    <p class="text-[#4C4047] whitespace-pre-line app-text break-words">{{ $registration->internal_note_for_finance }}</p>
                </div>
            </div>
        @endif

        @if ($showAdminNote)
            <div>
                <p class="text-sm font-semibold text-[#213430] app-main">Finance → Admin</p>
                <div class="mt-2 rounded-md border border-[#E8D9A8] bg-white px-3 py-2">
                    <p class="text-[#4C4047] whitespace-pre-line app-text break-words">{{ $registration->internal_note_for_admin }}</p>
                </div>
            </div>
        @endif
    </div>
@endif
