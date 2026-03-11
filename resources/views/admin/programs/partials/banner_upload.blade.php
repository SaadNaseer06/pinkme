@php
    $inputId = $inputId ?? 'program-banner';
    $bannerUrl = $bannerUrl ?? null;
@endphp

<section class="rounded-2xl border border-[#E9DCE7] bg-white shadow-sm">
    <div class="border-b border-[#F1E5EF] px-6 py-5">
        <h2 class="text-lg font-semibold text-[#213430]">Program banner</h2>
        <p class="mt-1 text-sm text-[#6C5B68]">Upload an optional cover image. This remains a separate upload and is not part of the custom fields.</p>
    </div>

    <div class="px-6 py-6 space-y-4">
        <p class="text-sm text-[#6C5B68]">Use a horizontal image for best results. Max size 2MB.</p>

        <div class="flex flex-col gap-4 lg:flex-row lg:items-center">
            <label for="{{ $inputId }}"
                class="flex-1 cursor-pointer rounded-2xl border border-dashed border-[#9E2469] bg-[#FDF7FB] px-4 py-6 text-center transition hover:border-[#B52D75] hover:bg-[#FBEAF3]">
                <input id="{{ $inputId }}" type="file" name="banner" accept="image/*" class="sr-only">
                <div class="flex flex-col items-center justify-center gap-2 text-[#213430]">
                    <svg class="h-8 w-8 text-[#9E2469]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 17.5V6.5C4 5.67157 4.67157 5 5.5 5H18.5C19.3284 5 20 5.67157 20 6.5V17.5C20 18.3284 19.3284 19 18.5 19H5.5C4.67157 19 4 18.3284 4 17.5Z" stroke="currentColor" stroke-width="1.5" />
                        <path d="M9 11.5C9 12.8807 7.88071 14 6.5 14C5.11929 14 4 12.8807 4 11.5C4 10.1193 5.11929 9 6.5 9C7.88071 9 9 10.1193 9 11.5Z" stroke="currentColor" stroke-width="1.5" />
                        <path d="M9.5 18L13.5 12L17.5 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="text-sm font-semibold text-[#213430]">Drop image or click to browse</span>
                    <span class="text-xs text-[#91848C]">Accepted formats: JPG, PNG. Up to 2MB.</span>
                </div>
            </label>

            <div class="w-full max-w-[180px]" data-banner-preview="{{ $inputId }}" style="display: {{ $bannerUrl ? 'block' : 'none' }};">
                <p class="text-xs font-semibold uppercase tracking-wide text-[#91848C]">Preview</p>
                <div class="mt-2 h-28 overflow-hidden rounded-xl border border-[#E9DCE7] bg-[#FDF7FB] flex items-center justify-center">
                    <img src="{{ $bannerUrl ?? '' }}" alt="Program banner" class="h-full w-full object-cover" data-preview-img>
                    <span class="text-[11px] text-[#6C5B68]" data-preview-empty {{ $bannerUrl ? 'style=display:none;' : '' }}>No image selected</span>
                </div>
            </div>
        </div>

        @error('banner')
            <p class="text-xs text-[#9E2469]">{{ $message }}</p>
        @enderror
    </div>
</section>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('{{ $inputId }}');
            const previewWrapper = document.querySelector('[data-banner-preview="{{ $inputId }}"]');
            if (!input || !previewWrapper) return;

            const imgEl = previewWrapper.querySelector('[data-preview-img]');
            const emptyEl = previewWrapper.querySelector('[data-preview-empty]');

            input.addEventListener('change', function (event) {
                const file = event.target.files && event.target.files[0];
                if (!file) return;
                const url = URL.createObjectURL(file);
                if (imgEl) {
                    imgEl.src = url;
                    imgEl.style.display = 'block';
                }
                if (emptyEl) {
                    emptyEl.style.display = 'none';
                }
                previewWrapper.style.display = 'block';
            });
        });
    </script>
@endpush
