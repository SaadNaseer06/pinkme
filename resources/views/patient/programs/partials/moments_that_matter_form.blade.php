@php
    use App\Support\MomentsThatMatterOptions;
    $user = auth()->user();
    $profile = $user?->profile;
@endphp

<div id="mtmPopupModal" class="fixed inset-0 z-50 hidden flex items-start sm:items-center justify-center bg-black/60 px-4 py-6 overflow-y-auto">
    <div class="bg-[#F3E8EF] p-6 rounded-lg w-full max-w-3xl min-w-0 relative overflow-y-auto max-h-[90vh] shadow-xl border border-[#DCCFD8]">
        <button type="button" onclick="document.getElementById('mtmPopupModal').classList.add('hidden')"
            class="absolute top-4 right-4 text-[#91848C] hover:text-black text-2xl font-bold">&times;</button>

        <div class="mb-6 pr-8">
            <p class="text-xs font-semibold uppercase tracking-wide text-[#9E2469]">Moments That Matter</p>
            <h2 id="mtm-modal-program-title" class="text-xl font-semibold text-[#213430] app-main mt-1">Care package application</h2>
            <p class="text-sm text-[#6C5F67] app-text mt-2">
                Bringing comfort, encouragement &amp; love through every step of the journey. Each submission is carefully reviewed by our team.
            </p>
        </div>

        <form action="{{ route('program.register') }}" method="POST" class="space-y-6 min-w-0">
            @csrf
            <input type="hidden" name="program_id" id="mtm_program_id" value="">

            <section class="border border-[#DCCFD8] bg-white/60 rounded-lg p-4 space-y-4">
                <h3 class="text-md font-semibold text-[#213430] app-main">1. Applicant information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">First name *</label>
                        <input type="text" name="first_name" required maxlength="255"
                            value="{{ old('first_name', $profile?->first_name) }}"
                            class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Last name *</label>
                        <input type="text" name="last_name" required maxlength="255"
                            value="{{ old('last_name', $profile?->last_name) }}"
                            class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Date of birth *</label>
                        <input type="date" name="dob" required
                            value="{{ old('dob', $profile?->date_of_birth ? \Carbon\Carbon::parse($profile->date_of_birth)->format('Y-m-d') : '') }}"
                            class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Phone number *</label>
                        <input type="tel" name="phone" required maxlength="20"
                            value="{{ old('phone', $user?->phone ?? $profile?->phone) }}"
                            class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Email address *</label>
                        <input type="email" name="email" required maxlength="255"
                            value="{{ old('email', $user?->email) }}"
                            class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-sm">
                    </div>
                </div>
            </section>

            <section class="border border-[#DCCFD8] bg-white/60 rounded-lg p-4 space-y-4">
                <h3 class="text-md font-semibold text-[#213430] app-main">2. Shipping information</h3>
                <p class="text-xs text-[#91848C]">Packages ship within the United States only.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Street address *</label>
                        <input type="text" name="street_address" required maxlength="255" value="{{ old('street_address') }}"
                            class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Apartment / Suite</label>
                        <input type="text" name="apartment_suite" maxlength="120" value="{{ old('apartment_suite') }}"
                            class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">City *</label>
                        <input type="text" name="city" required maxlength="120" value="{{ old('city') }}"
                            class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">State *</label>
                        <input type="text" name="state" required maxlength="120" value="{{ old('state') }}"
                            class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">ZIP code *</label>
                        <input type="text" name="postal_code" required maxlength="20" value="{{ old('postal_code') }}"
                            class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-sm">
                    </div>
                </div>
            </section>

            <section class="border border-[#DCCFD8] bg-white/60 rounded-lg p-4 space-y-4">
                <h3 class="text-md font-semibold text-[#213430] app-main">3. Package selection</h3>
                <p class="text-sm text-[#6C5F67]">Select the package that best reflects your journey or the journey of your loved one.</p>
                <div class="space-y-3">
                    @foreach (MomentsThatMatterOptions::PACKAGES as $value => $label)
                        <label class="flex items-start gap-3 rounded-lg border border-[#DCCFD8] bg-white p-3 cursor-pointer hover:border-[#9E2469]">
                            <input type="radio" name="mtm_package" value="{{ $value }}" required class="mt-1 text-[#9E2469]"
                                {{ old('mtm_package') === $value ? 'checked' : '' }}>
                            <span>
                                <span class="font-medium text-[#213430]">{{ $label }}</span>
                                <span class="block text-xs text-[#91848C] mt-0.5">{{ MomentsThatMatterOptions::PACKAGE_DESCRIPTIONS[$value] }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </section>

            <section class="border border-[#DCCFD8] bg-white/60 rounded-lg p-4 space-y-4">
                <h3 class="text-md font-semibold text-[#213430] app-main">4. Journey information</h3>
                <div>
                    <p class="text-sm font-medium mb-2">Are you applying for: *</p>
                    <div class="space-y-2">
                        @foreach (MomentsThatMatterOptions::APPLYING_FOR as $value => $label)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="radio" name="applying_for" value="{{ $value }}" required class="text-[#9E2469]"
                                    data-mtm-applying-for="{{ $value }}"
                                    {{ old('applying_for') === $value ? 'checked' : '' }}>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>
                <div id="mtm-loved-one-wrap" class="{{ in_array(old('applying_for'), ['family_member', 'friend_loved_one']) ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium mb-1">Name of patient / loved one (if different from applicant)</label>
                    <input type="text" name="patient_loved_one_name" maxlength="255" value="{{ old('patient_loved_one_name') }}"
                        class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-sm">
                </div>
                <div>
                    <p class="text-sm font-medium mb-2">Current treatment status *</p>
                    <div class="space-y-2">
                        @foreach (MomentsThatMatterOptions::TREATMENT_STATUS as $value => $label)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="radio" name="mtm_treatment_status" value="{{ $value }}" required class="text-[#9E2469]"
                                    data-mtm-treatment-status="{{ $value }}"
                                    {{ old('mtm_treatment_status') === $value ? 'checked' : '' }}>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    <div id="mtm-treatment-other-wrap" class="mt-2 {{ old('mtm_treatment_status') === 'other' ? '' : 'hidden' }}">
                        <input type="text" name="mtm_treatment_status_other" placeholder="Please specify"
                            value="{{ old('mtm_treatment_status_other') }}"
                            class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Type of breast cancer diagnosis (optional)</label>
                        <input type="text" name="mtm_diagnosis_type" maxlength="255" value="{{ old('mtm_diagnosis_type') }}"
                            class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Date of diagnosis (optional)</label>
                        <input type="date" name="mtm_diagnosis_date" value="{{ old('mtm_diagnosis_date') }}"
                            class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-sm">
                    </div>
                </div>
            </section>

            <section class="border border-[#DCCFD8] bg-white/60 rounded-lg p-4 space-y-3">
                <h3 class="text-md font-semibold text-[#213430] app-main">5. Your story</h3>
                <p class="text-sm text-[#6C5F67]">Please share your story, experience, or why this package would be meaningful to you or your loved one.</p>
                <textarea name="story" required rows="6" maxlength="10000"
                    class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-sm">{{ old('story') }}</textarea>
            </section>

            <section class="border border-[#DCCFD8] bg-white/60 rounded-lg p-4 space-y-3">
                <h3 class="text-md font-semibold text-[#213430] app-main">6. Photo &amp; story permission</h3>
                <p class="text-sm text-[#6C5F67]">May PINK “ME”® share parts of your story on social media, newsletters, or awareness campaigns?</p>
                <div class="space-y-2">
                    @foreach (MomentsThatMatterOptions::STORY_PERMISSION as $value => $label)
                        <label class="flex items-start gap-2 text-sm">
                            <input type="radio" name="mtm_story_permission" value="{{ $value }}" required class="mt-1 text-[#9E2469]"
                                {{ old('mtm_story_permission') === $value ? 'checked' : '' }}>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </section>

            <section class="border border-[#DCCFD8] bg-white/60 rounded-lg p-4 space-y-3">
                <h3 class="text-md font-semibold text-[#213430] app-main">7. Acknowledgment</h3>
                <p class="text-sm text-[#6C5F67]">Please confirm all of the following:</p>
                <div class="space-y-2">
                    @foreach (MomentsThatMatterOptions::ACKNOWLEDGMENT_LABELS as $key => $label)
                        <label class="flex items-start gap-2 text-sm">
                            <input type="checkbox" name="mtm_acknowledgments[]" value="{{ $key }}" required class="mt-1 text-[#9E2469]"
                                {{ is_array(old('mtm_acknowledgments')) && in_array($key, old('mtm_acknowledgments'), true) ? 'checked' : '' }}>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </section>

            <section class="border border-[#DCCFD8] bg-white/60 rounded-lg p-4 space-y-4">
                <h3 class="text-md font-semibold text-[#213430] app-main">8. Signature</h3>
                <div>
                    <label class="block text-sm font-medium mb-1">Applicant signature *</label>
                    <div class="rounded-md border border-dashed border-[#DCCFD8] bg-white">
                        <canvas id="mtm-signature-pad" class="w-full h-40 block" style="touch-action: none;"></canvas>
                    </div>
                    <div class="flex items-center justify-between mt-2 text-xs text-[#91848C]">
                        <span>Sign inside the box above.</span>
                        <button type="button" id="mtm-signature-clear" class="px-3 py-1 rounded-md border border-[#9E2469] text-[#9E2469] text-sm">Clear</button>
                    </div>
                    <input type="hidden" name="signature_data" id="mtm_signature_data" value="">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Date *</label>
                    <input type="date" name="signature_date" required value="{{ old('signature_date', now()->format('Y-m-d')) }}"
                        class="w-full max-w-xs px-4 py-2 rounded-md border border-[#DCCFD8] bg-[#FDF7FB] text-sm">
                </div>
            </section>

            <div class="flex flex-wrap gap-3 pt-2">
                <button type="button" onclick="document.getElementById('mtmPopupModal').classList.add('hidden')"
                    class="px-6 py-2 bg-[#FFF7FC] text-[#91848C] border border-gray-300 rounded-md text-sm">Cancel</button>
                <button type="submit"
                    class="px-6 py-2 bg-[#9E2469] text-white rounded-md hover:bg-[#B52D75] text-sm font-semibold">Submit application</button>
            </div>
        </form>
    </div>
</div>
