@php
    $icon = $icon ?? asset('public/images/notification.svg');
    $iconClass = $iconClass ?? 'h-4';
@endphp

<div class="relative inline-block" data-notification-center>
    <button type="button" class="relative flex items-center justify-center rounded-full focus:outline-none focus-visible:ring-2 focus-visible:ring-[#9E2469] focus-visible:ring-offset-2" data-notification-toggle aria-label="Notifications">
        <img src="{{ $icon }}" alt="Notifications" class="{{ $iconClass }}" />
        <span class="hidden absolute -top-2 -right-2 min-w-[20px] rounded-full bg-[#9E2469] px-1.5 py-0.5 text-center text-[11px] font-semibold leading-none text-white shadow-md" data-notification-count>0</span>
    </button>

    <div class="hidden absolute right-0 mt-3 w-80 max-w-none rounded-2xl border border-[#E5D6E0] bg-[#F3E8EF] shadow-xl z-50" data-notification-dropdown>
        <div class="flex items-center justify-between rounded-t-2xl bg-[#9E2469] px-4 py-3 text-white">
            <span class="text-sm font-semibold">Notifications</span>
            <button type="button" class="text-xs font-medium tracking-wide underline-offset-4 hover:underline" data-notification-mark-all>Mark all</button>
        </div>
        <div class="max-h-80 overflow-y-auto p-2 space-y-2" data-notification-list></div>
        <p class="hidden px-4 py-6 text-center text-xs text-[#91848C]" data-notification-empty>You're all caught up!</p>
    </div>
</div>
