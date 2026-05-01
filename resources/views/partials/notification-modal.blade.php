<div class="fixed inset-0 z-50 hidden items-center justify-center bg-[#1f1f1f]/45 backdrop-blur-sm" data-notification-modal>
    <div class="mx-4 w-full max-w-2xl overflow-hidden rounded-3xl border border-[#f2d9e6] bg-white shadow-2xl">
        <div class="bg-gradient-to-r from-[#fff5fb] via-white to-[#fff0f7] px-6 pb-4 pt-5">
            <div class="mb-3 flex items-center justify-between">
                <span class="inline-flex items-center rounded-full bg-[#fce6f1] px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-[#9E2469]">
                    Pink "ME" update
                </span>
                <button type="button" class="text-[#9f8a96] transition hover:text-[#6f5a65]" data-modal-close aria-label="Dismiss">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-xl font-semibold text-[#213430]" data-modal-title>Notification</h3>
                    <p class="mt-1 text-xs text-[#91848C]" data-modal-time></p>
                </div>
                <img src="{{ asset('public/images/logo.png') }}" alt="Pink Me logo" class="h-9 w-auto object-contain" />
            </div>
        </div>
        <div class="px-6 pb-6 pt-2">
            <div class="mb-4 hidden overflow-hidden rounded-2xl border border-[#f1dce7] bg-[#fff8fc]" data-modal-image-wrap>
                <img src="" alt="Program banner" class="h-[260px] w-full object-cover object-center" data-modal-image>
            </div>
            <p class="text-base leading-relaxed text-[#3f3340]" data-modal-message></p>
            <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:justify-end">
                <button type="button" class="inline-flex items-center justify-center rounded-lg border border-[#e8d4df] px-4 py-2 text-sm font-medium text-[#7d6973] transition hover:bg-[#fdf5fa]" data-modal-dismiss>
                    Dismiss
                </button>
                <a href="#" class="inline-flex items-center justify-center rounded-lg bg-[#9E2469] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#B52D75]" data-modal-view>
                    View details
                </a>
            </div>
        </div>
    </div>
</div>
