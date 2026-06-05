@php
    use App\Support\ProgramType;
    $programModel = $program ?? null;
    $selectedType = old('program_type', $programModel?->program_type ?? ProgramType::FINANCIAL_ASSISTANCE);
    $sponsorLogoUrl = $programModel?->sponsor_logo
        ? storage_url(ltrim($program->sponsor_logo, '/'))
        : null;
@endphp

<section class="rounded-2xl border border-[#E9DCE7] bg-white shadow-sm">
    <div class="border-b border-[#F1E5EF] px-6 py-5">
        <h2 class="text-lg font-semibold text-[#213430]">Program type &amp; sponsor</h2>
        <p class="mt-1 text-sm text-[#6C5B68]">
            Choose <strong>Moments That Matter</strong> for care-package applications, or <strong>Financial Assistance</strong> for grant applications.
            Sponsor details appear on the patient program page when the program is live.
        </p>
    </div>
    <div class="px-6 py-6 space-y-5">
        <div>
            <label for="program_type" class="block text-sm font-medium text-[#213430] mb-1.5">Program type *</label>
            <select name="program_type" id="program_type" required
                class="w-full max-w-md rounded-xl border border-[#DCCFD8] px-4 py-2.5 text-sm text-[#213430] focus:border-[#9E2469] focus:ring-2 focus:ring-[#9E2469]/20">
                @foreach (ProgramType::options() as $value => $label)
                    <option value="{{ $value }}" {{ $selectedType === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('program_type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="sponsor_name" class="block text-sm font-medium text-[#213430] mb-1.5">Sponsor name (optional)</label>
                <input type="text" name="sponsor_name" id="sponsor_name"
                    value="{{ old('sponsor_name', $programModel?->sponsor_name ?? '') }}"
                    placeholder="e.g. Community Partner Foundation"
                    class="w-full rounded-xl border border-[#DCCFD8] px-4 py-2.5 text-sm text-[#213430] focus:border-[#9E2469] focus:ring-2 focus:ring-[#9E2469]/20">
                @error('sponsor_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="sponsor_logo" class="block text-sm font-medium text-[#213430] mb-1.5">Sponsor logo (optional)</label>
                <input type="file" name="sponsor_logo" id="sponsor_logo" accept="image/*"
                    class="w-full text-sm text-[#213430] file:mr-4 file:rounded-lg file:border-0 file:bg-[#F3E8EF] file:px-4 file:py-2 file:text-sm file:font-medium file:text-[#9E2469]">
                @error('sponsor_logo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @if ($sponsorLogoUrl)
                    <div class="mt-3 flex items-center gap-3">
                        <img src="{{ $sponsorLogoUrl }}" alt="Current sponsor logo" class="h-12 w-auto max-w-[140px] object-contain rounded border border-[#DCCFD8] bg-white p-1">
                        <span class="text-xs text-[#91848C]">Current logo — upload a new file to replace.</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
