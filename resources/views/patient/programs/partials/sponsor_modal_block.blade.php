@php
    $prefix = $prefix ?? 'modal';
    $blockId = $prefix . '-sponsor-block';
    $logoId = $prefix . '-sponsor-logo';
    $nameId = $prefix . '-sponsor-name';
@endphp

<div id="{{ $blockId }}" class="program-sponsor-block hidden rounded-lg border border-[#EADFF0] bg-gradient-to-r from-[#FDF7FB] to-white px-4 py-4 shadow-sm">
    <p class="text-xs font-semibold uppercase tracking-wide text-[#9E2469] mb-3">Program sponsor</p>
    <div class="flex items-center gap-4 min-w-0">
        <div id="{{ $prefix }}-sponsor-logo-wrap" class="shrink-0 hidden">
            <img id="{{ $logoId }}" src="" alt="" class="h-14 w-auto max-w-[160px] object-contain rounded-md border border-[#DCCFD8] bg-white p-2">
        </div>
        <p id="{{ $nameId }}" class="text-base font-semibold text-[#213430] leading-snug break-words min-w-0"></p>
    </div>
</div>
