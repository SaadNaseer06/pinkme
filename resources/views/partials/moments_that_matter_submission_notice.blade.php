@php
    $paragraphs = \App\Support\MomentsThatMatterNotice::paragraphs();
@endphp

<div class="rounded-xl border border-[#F4BBD5] bg-[#FDF7FB] px-5 py-5 md:px-6 md:py-6 shadow-sm w-full min-w-0">
    <div class="flex items-start gap-3">
        <span class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#FDE8F3] text-[#9E2469]">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
        </span>
        <div class="min-w-0 space-y-3 text-base text-[#213430] app-text leading-relaxed">
            @foreach ($paragraphs as $paragraph)
                <p class="break-words {{ $loop->last ? 'font-semibold text-[#9E2469]' : '' }}">{{ $paragraph }}</p>
            @endforeach
        </div>
    </div>
</div>
