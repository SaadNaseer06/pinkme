<div class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm" data-notification-modal>
    <div class="mx-4 w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h3 class="text-xl font-semibold text-[#213430]" data-modal-title>Notification</h3>
                <p class="mt-1 text-xs text-[#91848C]" data-modal-time></p>
            </div>
            <button type="button" class="text-[#91848C] transition hover:text-[#213430]" data-modal-close aria-label="Dismiss">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <p class="mt-4 text-sm leading-relaxed text-[#213430]" data-modal-message></p>
        <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:justify-end">
            <button type="button" class="inline-flex items-center justify-center rounded-md border border-[#DCCFD8] px-4 py-2 text-sm text-[#91848C] transition hover:bg-[#F3E8EF]" data-modal-dismiss>Dismiss</button>
            <a href="#" class="inline-flex items-center justify-center rounded-md bg-[#DB69A2] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#c95791]" data-modal-view>View Details</a>
        </div>
    </div>
</div>
