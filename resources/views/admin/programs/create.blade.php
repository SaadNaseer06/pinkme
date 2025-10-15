@extends('admin.layouts.admin')

@section('title', 'Create Program')

@section('content')
    <div class="max-w-8xl mx-auto">
        <div class="space-y-8">
            <div
                class="rounded-2xl bg-gradient-to-r from-[#C63A85] via-[#DB69A2] to-[#E8A8C8] text-white p-8 shadow-lg relative">
                <div class="absolute inset-0 bg-black/10 rounded-2xl"></div>
                <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-xl" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.6);">
                        <p class="uppercase tracking-widest text-xs font-semibold opacity-90">Support Programs</p>
                        <h1 class="mt-2 text-3xl font-semibold">Create New Program</h1>
                        <p class="mt-3 text-sm lg:text-base max-w-xl opacity-95">Launch a support program that sponsors and
                            patients can rally around. Fill in the details and publish when you are ready.</p>
                    </div>
                    <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                        <a href="{{ route('admin.sponsors') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-white/25 px-5 py-3 text-sm font-medium text-white backdrop-blur transition hover:bg-white/30">Back
                            to programs</a>
                        <button type="submit" form="create-program-form"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-[#DB69A2] shadow-md shadow-white/40 transition hover:bg-[#FFF1F7]">Save
                            Program</button>
                    </div>
                </div>
            </div>



            <form id="create-program-form" method="POST" action="{{ route('programs.store') }}"
                enctype="multipart/form-data" class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_22rem]">
                @csrf
                <div class="space-y-8">
                    <section class="rounded-2xl border border-[#E9DCE7] bg-white shadow-sm">
                        <div class="border-b border-[#F1E5EF] px-6 py-5">
                            <h2 class="text-lg font-semibold text-[#213430]">Program Overview</h2>
                            <p class="mt-1 text-sm text-[#6C5B68]">Introduce sponsors and patients to the program.</p>
                        </div>
                        <div class="space-y-6 px-6 py-6">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-[#213430]">Program Title <span
                                        class="text-[#DB69A2]">*</span></label>
                                <input name="title" type="text" value="{{ old('title') }}"
                                    placeholder="e.g. Community Wellness Initiative"
                                    class="w-full rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70"
                                    required>
                                @error('title')
                                    <p class="mt-1 text-xs text-[#DB69A2]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-[#213430]">Program Type <span
                                        class="text-[#DB69A2]">*</span></label>
                                <select name="payment_type"
                                    class="w-full rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70"
                                    required>
                                    <option value="full" @selected(old('payment_type') === 'full')>Full amount (fixed)</option>
                                    <option value="flexible" @selected(old('payment_type') === 'flexible')>Flexible (pay what you can)
                                    </option>
                                </select>
                                @error('payment_type')
                                    <p class="mt-1 text-xs text-[#DB69A2]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-[#213430]">Program Description <span
                                        class="text-[#DB69A2]">*</span></label>
                                <textarea name="description" rows="5"
                                    placeholder="Describe who the program supports, what is included, and how participants benefit."
                                    class="w-full rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70"
                                    required>{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-xs text-[#DB69A2]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-[#E9DCE7] bg-white shadow-sm">
                        <div class="flex flex-col gap-1 border-b border-[#F1E5EF] px-6 py-5">
                            <h2 class="text-lg font-semibold text-[#213430]">Schedule & Status</h2>
                            <p class="mt-1 text-sm text-[#6C5B68]">Control when the program runs and how it appears on
                                dashboards.</p>
                        </div>
                        <div class="space-y-6 px-6 py-6">
                            <div class="grid gap-6 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-[#213430]">Program Date <span
                                            class="text-[#DB69A2]">*</span></label>
                                    <input name="event_date" type="date" value="{{ old('event_date') }}"
                                        class="w-full rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70"
                                        required>
                                    @error('event_date')
                                        <p class="mt-1 text-xs text-[#DB69A2]">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-[#213430]">Program Time <span
                                            class="text-[#DB69A2]">*</span></label>
                                    <input name="event_time" type="time" value="{{ old('event_time') }}"
                                        class="w-full rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70"
                                        required>
                                    @error('event_time')
                                        <p class="mt-1 text-xs text-[#DB69A2]">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-[#213430]">Program Status <span
                                        class="text-[#DB69A2]">*</span></label>
                                <div class="relative">
                                    <select name="status"
                                        class="w-full appearance-none rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-sm text-[#213430] outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70"
                                        required>
                                        <option value="upcoming" @selected(old('status') === 'upcoming')>Upcoming</option>
                                        <option value="ongoing" @selected(old('status') === 'ongoing')>Ongoing</option>
                                        <option value="completed" @selected(old('status') === 'completed')>Completed</option>
                                    </select>
                                    <span
                                        class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-[#91848C]">
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </div>
                                @error('status')
                                    <p class="mt-1 text-xs text-[#DB69A2]">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-[#6C5B68]">Status controls badges across the sponsor and patient
                                    views.</p>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-[#E9DCE7] bg-white shadow-sm">
                        <div class="border-b border-[#F1E5EF] px-6 py-5">
                            <h2 class="text-lg font-semibold text-[#213430]">Funding Goal</h2>
                            <p class="mt-1 text-sm text-[#6C5B68]">Let sponsors know the contribution target for this
                                program.</p>
                        </div>
                        <div class="px-6 py-6">
                            <label class="mb-1 block text-sm font-medium text-[#213430]">Program Fund Goal <span
                                    class="text-[#DB69A2]">*</span></label>
                            <div class="relative mt-1">
                                <span
                                    class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#91848C]">$</span>
                                <input name="program_fund" type="number" min="0" step="0.01"
                                    value="{{ old('program_fund') }}" placeholder="5000"
                                    class="w-full rounded-xl border border-[#DCCFD8] bg-white pl-9 pr-4 py-3 text-sm outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70"
                                    required>
                            </div>
                            @error('program_fund')
                                <p class="mt-1 text-xs text-[#DB69A2]">{{ $message }}</p>
                            @enderror
                        </div>
                    </section>

                    <section class="rounded-2xl border border-[#E9DCE7] bg-white shadow-sm">
                        <div class="border-b border-[#F1E5EF] px-6 py-5">
                            <h2 class="text-lg font-semibold text-[#213430]">Program Banner</h2>
                            <p class="mt-1 text-sm text-[#6C5B68]">Upload a visual that represents the initiative
                                (optional).</p>
                        </div>
                        <div class="space-y-4 px-6 py-6">
                            <label for="banner-input"
                                class="block cursor-pointer rounded-2xl border-2 border-dashed border-[#DCCFD8] bg-[#FDF7FB] px-6 py-10 text-center transition hover:border-[#DB69A2] hover:bg-[#FDF0F7]">
                                <div class="flex flex-col items-center gap-3 text-[#6C5B68]">
                                    <span
                                        class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-white text-[#DB69A2] shadow-md shadow-[#E4CADC]">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="1.8" />
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-[#213430]">Drop image or click to browse</p>
                                        <p class="mt-1 text-xs text-[#91848C]">Accepted formats: JPG, PNG, up to 2MB.</p>
                                    </div>
                                    <p id="banner-file-name" class="text-xs font-medium text-[#DB69A2]">No file selected
                                        yet</p>
                                </div>
                                <input id="banner-input" type="file" name="banner" accept="image/*" class="hidden">
                            </label>
                            <div class="hidden rounded-xl border border-[#E9DCE7] bg-white p-4" id="banner-preview-card">
                                <p class="text-xs font-semibold uppercase tracking-wider text-[#91848C]">Preview</p>
                                <img id="banner-preview" alt="Banner preview"
                                    class="mt-3 h-40 w-full rounded-lg object-cover" />
                            </div>
                            @error('banner')
                                <p class="text-xs text-[#DB69A2]">{{ $message }}</p>
                            @enderror
                        </div>
                    </section>
                </div>

                <aside class="space-y-6">
                    <div class="rounded-2xl border border-[#E9DCE7] bg-white p-6 shadow-sm">
                        <h3 class="text-base font-semibold text-[#213430]">Before you publish</h3>
                        <ul class="mt-4 space-y-3 text-sm text-[#6C5B68]">
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#F8D4E6] text-xs font-semibold text-[#DB69A2]">1</span>
                                Confirm your date and time so patients receive accurate reminders.
                            </li>
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#F8D4E6] text-xs font-semibold text-[#DB69A2]">2</span>
                                Set a realistic fund goal that matches your sponsor commitments.
                            </li>
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#F8D4E6] text-xs font-semibold text-[#DB69A2]">3</span>
                                Use a banner that clearly represents the program for quicker recognition.
                            </li>
                        </ul>
                    </div>

                    <div class="rounded-2xl border border-[#E9DCE7] bg-[#FDF7FB] p-6 shadow-inner">
                        <h3 class="text-base font-semibold text-[#213430]">Quick tips</h3>
                        <p class="mt-3 text-sm text-[#6C5B68]">Programs marked as <span
                                class="font-semibold text-[#DB69A2]">Ongoing</span> appear at the top of patient
                            dashboards. Completed programs remain accessible for reporting.</p>
                        <p class="mt-4 text-xs uppercase tracking-wider text-[#91848C]">Need edits later?</p>
                        <p class="mt-1 text-sm text-[#6C5B68]">You can always update the schedule, fund goal, or banner
                            from the program details page.</p>
                    </div>
                </aside>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        }

        h1,
        h2,
        h3 {
            font-weight: 600;
        }

        p,
        label,
        input,
        textarea,
        select,
        button {
            font-weight: 400;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var input = document.getElementById('banner-input');
            var fileName = document.getElementById('banner-file-name');
            var previewCard = document.getElementById('banner-preview-card');
            var previewImage = document.getElementById('banner-preview');

            if (!input || !fileName || !previewCard || !previewImage) {
                return;
            }

            input.addEventListener('change', function(event) {
                if (!event.target.files || event.target.files.length === 0) {
                    fileName.textContent = 'No file selected yet';
                    previewCard.classList.add('hidden');
                    previewImage.removeAttribute('src');
                    return;
                }

                var file = event.target.files[0];
                fileName.textContent = file.name;

                if (!file.type.startsWith('image/')) {
                    previewCard.classList.add('hidden');
                    previewImage.removeAttribute('src');
                    return;
                }

                var reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewCard.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            });
        });
    </script>
@endpush
