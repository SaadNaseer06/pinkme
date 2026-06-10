@php
    $dynamicTypes = [
        \App\Support\ProgramType::MAMMOGRAM_IMAGING,
        \App\Support\ProgramType::FOOD_ASSISTANCE,
    ];
@endphp

<nav class="sticky top-0 z-20 -mx-1 mb-6 rounded-2xl border border-[#E9DCE7] bg-white/95 px-2 py-3 shadow-sm backdrop-blur" aria-label="Program setup steps">
    <ol class="flex flex-wrap items-center gap-1 sm:gap-2" data-program-step-nav>
        @foreach ([
            1 => ['label' => 'Type & sponsor', 'short' => 'Type'],
            2 => ['label' => 'Program listing', 'short' => 'Listing'],
            3 => ['label' => 'Application form', 'short' => 'Application'],
            4 => ['label' => 'Banner', 'short' => 'Banner'],
        ] as $num => $meta)
            <li class="flex items-center gap-1 sm:gap-2">
                @if ($num > 1)
                    <span class="hidden sm:inline text-[#DCCFD8]" aria-hidden="true">›</span>
                @endif
                <button type="button"
                    data-step-nav="{{ $num }}"
                    class="program-step-btn inline-flex items-center gap-2 rounded-xl px-3 py-2 text-left text-sm font-medium transition {{ $num === 1 ? 'bg-[#9E2469] text-white' : 'text-[#6C5B68] hover:bg-[#FDF0F7] hover:text-[#9E2469]' }}">
                    <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-semibold {{ $num === 1 ? 'bg-white/25' : 'bg-[#F3E8EF] text-[#9E2469]' }}">{{ $num }}</span>
                    <span class="hidden md:inline">{{ $meta['label'] }}</span>
                    <span class="md:hidden">{{ $meta['short'] }}</span>
                </button>
            </li>
        @endforeach
    </ol>
</nav>

<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between rounded-xl border border-[#E9DCE7] bg-[#FDF7FB] px-4 py-3 mb-6" data-step-footer>
    <p class="text-sm text-[#6C5B68]" data-step-hint>Step 1 of 4 — Choose the program type and optional sponsor.</p>
    <div class="flex gap-2">
        <button type="button" data-step-back
            class="hidden rounded-xl border border-[#DCCFD8] bg-white px-4 py-2 text-sm font-semibold text-[#213430] hover:bg-[#F3E8EF]">Back</button>
        <button type="button" data-step-next
            class="rounded-xl bg-[#9E2469] px-4 py-2 text-sm font-semibold text-white hover:bg-[#8A1F5C]">Continue</button>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const panels = document.querySelectorAll('[data-program-step]');
    const navBtns = document.querySelectorAll('[data-step-nav]');
    const backBtn = document.querySelector('[data-step-back]');
    const nextBtn = document.querySelector('[data-step-next]');
    const hintEl = document.querySelector('[data-step-hint]');
    const dynamicTypes = @json($dynamicTypes);
    const getProgramType = () => document.querySelector('input[name="program_type"]:checked')?.value || '';
    const stepHints = {
        1: 'Choose the program type and optional sponsor.',
        2: 'Set the title, description, dates, and capacity patients see on the program card.',
        3: 'Build the questions applicants answer — load a starter template to get started quickly.',
        4: 'Upload a cover image so the program is easy to recognize.',
    };

    let current = 1;
    const total = panels.length || 4;

    const usesDynamicForm = () => dynamicTypes.includes(getProgramType());

    const goTo = (step) => {
        current = Math.max(1, Math.min(total, step));
        panels.forEach((panel) => {
            const n = Number(panel.dataset.programStep);
            panel.classList.toggle('hidden', n !== current);
        });
        navBtns.forEach((btn) => {
            const n = Number(btn.dataset.stepNav);
            const active = n === current;
            btn.classList.toggle('bg-[#9E2469]', active);
            btn.classList.toggle('text-white', active);
            btn.classList.toggle('text-[#6C5B68]', !active);
            btn.classList.toggle('hover:bg-[#FDF0F7]', !active);
            btn.classList.toggle('hover:text-[#9E2469]', !active);
            const badge = btn.querySelector('span.inline-flex.h-6');
            if (badge) {
                badge.classList.toggle('bg-white/25', active);
                badge.classList.toggle('bg-[#F3E8EF]', !active);
                badge.classList.toggle('text-[#9E2469]', !active);
            }
        });
        if (backBtn) backBtn.classList.toggle('hidden', current === 1);
        if (nextBtn) {
            nextBtn.textContent = 'Continue';
            nextBtn.classList.toggle('hidden', current === total);
        }
        if (hintEl) {
            const base = stepHints[current] || '';
            hintEl.textContent = current === total
                ? `Step ${current} of ${total} — ${base} Click Save program when you are ready.`
                : `Step ${current} of ${total} — ${base}`;
        }

        if (usesDynamicForm()) {
            document.querySelector('[data-legacy-form-notice]')?.classList.add('hidden');
            document.querySelector('[data-application-form-panel]')?.classList.remove('opacity-50');
        }

        const step3Notice = document.querySelector('[data-legacy-form-notice]');
        const step3Builder = document.querySelector('[data-application-form-panel]');
        if (step3Notice && step3Builder) {
            const dynamic = usesDynamicForm();
            step3Notice.classList.toggle('hidden', dynamic);
            step3Builder.classList.toggle('opacity-50', !dynamic && !document.querySelector('[data-enable-custom-app]')?.checked);
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    navBtns.forEach((btn) => btn.addEventListener('click', () => goTo(Number(btn.dataset.stepNav))));
    backBtn?.addEventListener('click', () => goTo(current - 1));
    nextBtn?.addEventListener('click', () => goTo(current + 1));

    const syncTypeHint = () => {
        const banner = document.querySelector('[data-template-suggest]');
        if (!banner) return;
        if (usesDynamicForm()) {
            banner.classList.remove('hidden');
            const checked = document.querySelector('input[name="program_type"]:checked');
            const label = checked?.closest('label')?.querySelector('.text-sm.font-semibold')?.textContent?.trim() || 'this program';
            const textEl = banner.querySelector('[data-template-suggest-text]');
            if (textEl) {
                textEl.textContent = `On step 3, load the "${label}" starter template to pre-fill the application form.`;
            }
        } else {
            banner.classList.add('hidden');
        }
    };

    document.querySelectorAll('input[name="program_type"]').forEach((radio) => {
        radio.addEventListener('change', syncTypeHint);
    });

    document.querySelector('[data-enable-custom-app]')?.addEventListener('change', (e) => {
        const panel = document.querySelector('[data-application-form-panel]');
        if (panel) panel.classList.toggle('opacity-50', !e.target.checked);
    });

    goTo(1);
    syncTypeHint();
});
</script>
@endpush
