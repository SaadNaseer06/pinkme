@if (($claimableProgramRegistrations ?? collect())->isNotEmpty())
    <div class="max-w-8xl mx-auto mb-6 rounded-xl border border-[#E6D8E1] bg-[#FDF7FB] p-4 md:p-5">
        <h2 class="text-sm font-semibold text-[#213430] mb-1">Open program applications</h2>
        <p class="text-xs text-[#6C5F67] mb-3">Claim an application to work on it exclusively. It disappears from other case managers’ open lists until admin reassigns it.</p>
        <ul class="space-y-2">
            @foreach ($claimableProgramRegistrations as $reg)
                <li class="flex flex-wrap items-center justify-between gap-2 rounded-lg bg-white border border-[#E6D8E1] px-3 py-2 text-sm">
                    <div class="min-w-0">
                        <span class="font-medium text-[#213430]">{{ $reg->full_name }}</span>
                        <span class="text-[#91848C]"> — {{ $reg->program?->title ?? 'Program' }}</span>
                        @if ($reg->user_id)
                            <span class="block text-xs text-[#91848C]">Patient account linked — you can open chat after claiming.</span>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('case_manager.program_registrations.claim_chat', $reg) }}" class="shrink-0">
                        @csrf
                        <button type="submit" class="rounded-md bg-[#9E2469] px-3 py-1.5 text-xs font-medium text-white hover:bg-[#B52D75]">
                            Work on this application
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>
@endif
