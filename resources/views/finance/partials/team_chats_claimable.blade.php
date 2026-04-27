@if (($financeClaimableRegistrations ?? collect())->isNotEmpty())
    <div class="max-w-8xl mx-auto mb-6 rounded-xl border border-amber-200 bg-amber-50/80 p-4 md:p-5">
        <h2 class="text-sm font-semibold text-[#213430] mb-2">Finance queue — claim to work on an application</h2>
        <p class="text-xs text-[#6C5F67] mb-3">When you claim, other finance users no longer see this item as open. You can still open it from Patient Requests.</p>
        <ul class="space-y-2">
            @foreach ($financeClaimableRegistrations as $reg)
                @if ($reg->finance_user_id === null)
                    <li class="flex flex-wrap items-center justify-between gap-2 rounded-lg bg-white/90 border border-[#E6D8E1] px-3 py-2 text-sm">
                        <span class="text-[#213430]">
                            <span class="font-medium">{{ $reg->full_name }}</span>
                            <span class="text-[#91848C]"> — {{ $reg->program?->title ?? 'Program' }}</span>
                        </span>
                        <form method="POST" action="{{ route('finance.registrations.claim_chat', $reg) }}" class="shrink-0">
                            @csrf
                            <button type="submit" class="rounded-md bg-[#9E2469] px-3 py-1.5 text-xs font-medium text-white hover:bg-[#B52D75]">
                                Claim &amp; work on this
                            </button>
                        </form>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
@endif
