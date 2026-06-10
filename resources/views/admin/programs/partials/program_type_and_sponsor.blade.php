@php
    use App\Support\ProgramType;
    $programModel = $program ?? null;
    $selectedType = old('program_type', $programModel?->program_type ?? ProgramType::FINANCIAL_ASSISTANCE);
    $sponsorLogoUrl = $programModel?->sponsor_logo
        ? storage_url(ltrim($program->sponsor_logo, '/'))
        : null;

    $typeDescriptions = [
        ProgramType::FINANCIAL_ASSISTANCE => 'Uses the standard grant application with billing, income docs, and case manager review.',
        ProgramType::MOMENTS_THAT_MATTER => 'Care package applications with a dedicated shipping workflow.',
        ProgramType::MAMMOGRAM_IMAGING => 'Custom imaging support form — load the Mammogram starter template on step 3.',
        ProgramType::FOOD_ASSISTANCE => 'Custom food card application — load the Food Assistance starter template on step 3.',
    ];
@endphp

<section class="rounded-2xl border border-[#E9DCE7] bg-white shadow-sm">
    <div class="border-b border-[#F1E5EF] px-6 py-5">
        <h2 class="text-lg font-semibold text-[#213430]">Program type</h2>
        <p class="mt-1 text-sm text-[#6C5B68]">Pick the workflow that matches this program. You can configure listing details and the application form in the next steps.</p>
    </div>

    <div class="px-6 py-6 space-y-6">
        <div class="grid gap-3 sm:grid-cols-2" role="radiogroup" aria-label="Program type">
            @foreach (ProgramType::options() as $value => $label)
                <label class="program-type-card relative cursor-pointer rounded-xl border-2 p-4 transition has-[:checked]:border-[#9E2469] has-[:checked]:bg-[#FDF0F7] border-[#E9DCE7] bg-white hover:border-[#DCCFD8]">
                    <input type="radio" name="program_type" value="{{ $value }}" class="sr-only" {{ $selectedType === $value ? 'checked' : '' }} required>
                    <div class="flex items-start gap-3">
                        <span class="program-type-radio mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2 border-[#DCCFD8]">
                            <span class="h-2 w-2 rounded-full bg-white"></span>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-[#213430]">{{ $label }}</p>
                            <p class="mt-1 text-xs text-[#6C5B68] leading-relaxed">{{ $typeDescriptions[$value] ?? '' }}</p>
                        </div>
                    </div>
                </label>
            @endforeach
        </div>
        @error('program_type')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror

        <div class="hidden rounded-lg border border-[#D1E7DD] bg-[#F0FFF4] px-4 py-3 text-sm text-[#0F5132]" data-template-suggest>
            <span data-template-suggest-text></span>
        </div>

        <div class="border-t border-[#F1E5EF] pt-6">
            <h3 class="text-base font-semibold text-[#213430]">Sponsor <span class="font-normal text-[#91848C]">(optional)</span></h3>
            <p class="mt-1 text-sm text-[#6C5B68]">Shown on the patient program page when live.</p>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label for="sponsor_name" class="block text-sm font-medium text-[#213430] mb-1.5">Sponsor name</label>
                    <input type="text" name="sponsor_name" id="sponsor_name"
                        value="{{ old('sponsor_name', $programModel?->sponsor_name ?? '') }}"
                        placeholder="e.g. Community Partner Foundation"
                        class="w-full rounded-xl border border-[#DCCFD8] px-4 py-2.5 text-sm text-[#213430] focus:border-[#9E2469] focus:ring-2 focus:ring-[#9E2469]/20">
                    @error('sponsor_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="sponsor_logo" class="block text-sm font-medium text-[#213430] mb-1.5">Sponsor logo</label>
                    <input type="file" name="sponsor_logo" id="sponsor_logo" accept="image/*"
                        class="w-full text-sm text-[#213430] file:mr-4 file:rounded-lg file:border-0 file:bg-[#F3E8EF] file:px-4 file:py-2 file:text-sm file:font-medium file:text-[#9E2469]">
                    @error('sponsor_logo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if ($sponsorLogoUrl)
                        <div class="mt-3 flex items-center gap-3">
                            <img src="{{ $sponsorLogoUrl }}" alt="Current sponsor logo" class="h-12 w-auto max-w-[140px] object-contain rounded border border-[#DCCFD8] bg-white p-1">
                            <span class="text-xs text-[#91848C]">Upload a new file to replace.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    .program-type-card:has(input:checked) .program-type-radio {
        border-color: #9E2469;
        background-color: #9E2469;
    }
</style>
@endpush
