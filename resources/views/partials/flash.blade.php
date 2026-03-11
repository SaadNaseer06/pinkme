@php
    $successMessage = session('success');
    $errorMessage = session('error');
    $formErrors = ($errors ?? null) instanceof \Illuminate\Support\ViewErrorBag ? $errors->all() : [];
@endphp

@if ($successMessage || $errorMessage || !empty($formErrors))
    <div class="mb-6 space-y-3">
        @if ($successMessage)
            <div class="rounded-xl border border-[#F4C9DD] bg-[#FEF6FB] px-5 py-4 shadow-sm">
                <div class="flex items-start gap-3 text-[#6C5B68]">
                    <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#9E2469]/10 text-[#9E2469]">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-[#B52D75]">Success</p>
                        <p class="text-sm leading-relaxed">{{ $successMessage }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errorMessage)
            <div class="rounded-xl border border-[#F7BBCF] bg-[#FFF1F5] px-5 py-4 shadow-sm">
                <div class="flex items-start gap-3 text-[#7B2A54]">
                    <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#FCDCE5] text-[#B52D75]">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-[#B52D75]">Something needs attention</p>
                        <p class="text-sm leading-relaxed">{{ $errorMessage }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (!empty($formErrors))
            <div class="rounded-xl border border-[#F7BBCF] bg-[#FFF1F5] px-5 py-4 shadow-sm">
                <div class="flex items-start gap-3 text-[#7B2A54]">
                    <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#FCDCE5] text-[#B52D75]">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <div class="space-y-2">
                        <p class="text-sm font-semibold text-[#B52D75]">Please review and try again</p>
                        <ul class="list-disc space-y-1 pl-5 text-sm leading-relaxed">
                            @foreach ($formErrors as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif
