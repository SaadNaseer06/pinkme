@extends('admin.layouts.admin')

@php
    $isEdit = isset($event);
    $pageTitle = $isEdit ? 'Edit Event' : 'Create Event';
    $heroTitle = $isEdit ? 'Update Event Details' : 'Plan a New Event';
    $heroSubtitle = $isEdit
        ? 'Adjust the agenda, confirm logistics, and keep sponsors informed before publishing.'
        : 'Outline the experience, lock in logistics, and optionally recognise a launch sponsor before publishing.';
    $submitLabel = $isEdit ? 'Update Event' : 'Save Event';
    $formAction = $isEdit ? route('events.update', $event) : route('events.store');
    $titleValue = old('title', $isEdit ? $event->title : '');
    $descriptionValue = old('description', $isEdit ? $event->description : '');
    $dateValue = old('date', $isEdit && $event->date ? $event->date->format('Y-m-d\TH:i') : '');
    $locationValue = old('location', $isEdit ? $event->location : '');
    $selectedSponsor = old('sponsor_id', '');
    $hasSponsor = $selectedSponsor !== '' && $selectedSponsor !== null;
    $amountValue = old('amount', '');
    $sponsorshipBadgeClasses = $hasSponsor ? 'bg-[#F3B9DA] text-[#B74F86]' : 'bg-[#F8D4E6] text-[#C25E95]';
    $sponsorshipBadgeLabel = $hasSponsor ? 'Enabled' : 'Skipped';
    $amountWrapperClasses = $hasSponsor ? '' : 'opacity-50';
@endphp

@section('title', $pageTitle)

@section('content')
    <div class="max-w-8xl mx-auto">
        <div class="space-y-8">
            <div class="rounded-2xl bg-gradient-to-r from-[#C63A85] via-[#DB69A2] to-[#F9C6E2] p-8 text-white shadow-lg">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-2xl" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.6);">
                        <p class="uppercase tracking-widest text-xs font-semibold">Events & Activations</p>
                        <h1 class="mt-2 text-3xl font-semibold">{{ $heroTitle }}</h1>
                        <p class="mt-3 max-w-2xl text-sm lg:text-base">{{ $heroSubtitle }}</p>
                    </div>
                    <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                        <a href="{{ route('admin.sponsors') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-white/20 px-5 py-3 text-sm font-medium text-white backdrop-blur transition hover:bg-white/30">Back
                            to events</a>
                        <button type="submit" form="create-event-form"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-[#DB69A2] shadow-md shadow-white/40 transition hover:bg-[#FFF1F7]">{{ $submitLabel }}</button>
                    </div>
                </div>
            </div>



            <form id="create-event-form" method="POST" action="{{ $formAction }}" enctype="multipart/form-data"
                class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_22rem]">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif
                <div class="space-y-8">
                    <section class="rounded-2xl border border-[#E9DCE7] bg-white shadow-sm">
                        <div class="border-b border-[#F1E5EF] px-6 py-5">
                            <h2 class="text-lg font-semibold text-[#213430]">Event Snapshot</h2>
                            <p class="mt-1 text-sm text-[#6C5B68]">Give sponsors and attendees a clear idea of what to
                                expect.</p>
                        </div>
                        <div class="space-y-6 px-6 py-6">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-[#213430]">Event Title <span
                                        class="text-[#DB69A2]">*</span></label>
                                <input name="title" type="text" value="{{ $titleValue }}"
                                    placeholder="e.g. Pink Ribbon Charity Walk"
                                    class="w-full rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70"
                                    required>
                                @error('title')
                                    <p class="mt-1 text-xs text-[#DB69A2]">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-[#213430]">Event Description</label>
                                <textarea name="description" id="event-description" rows="5"
                                    placeholder="Share the agenda, audience, and key highlights participants should know."
                                    class="w-full rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70">{!! $descriptionValue !!}</textarea>
                                @error('description')
                                    <p class="mt-1 text-xs text-[#DB69A2]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-[#213430]">Event Image</label>
                                <div
                                    class="mt-1 flex justify-center rounded-xl border-2 border-dashed border-[#DCCFD8] px-6 pt-5 pb-6">
                                    <div class="space-y-1 text-center">
                                        @if ($isEdit && $event->image)
                                            <div class="mb-4" id="current-image">
                                                <img src="{{ asset('storage/app/public/' . ltrim($event->image, '/')) }}" alt="Current event image"
                                                    class="mx-auto h-32 w-auto rounded-lg shadow-sm">
                                                <p class="mt-2 text-xs text-[#6C5B68]">Current image</p>
                                            </div>
                                        @endif
                                        <div id="image-preview"></div>
                                        <svg class="mx-auto h-12 w-12 text-[#91848C]" stroke="currentColor" fill="none"
                                            viewBox="0 0 48 48">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-[#6C5B68]">
                                            <label for="image"
                                                class="relative cursor-pointer rounded-md font-medium text-[#DB69A2] focus-within:outline-none focus-within:ring-2 focus-within:ring-[#DB69A2] focus-within:ring-offset-2 hover:text-[#C63A85]">
                                                <span>Upload event image</span>
                                                <input id="image" name="image" type="file" class="sr-only"
                                                    accept="image/*">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-[#91848C]">PNG, JPG, GIF up to 2MB</p>
                                    </div>
                                </div>
                                @error('image')
                                    <p class="mt-1 text-xs text-[#DB69A2]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-[#E9DCE7] bg-white shadow-sm">
                        <div class="border-b border-[#F1E5EF] px-6 py-5">
                            <h2 class="text-lg font-semibold text-[#213430]">Event Details & Settings</h2>
                            <p class="mt-1 text-sm text-[#6C5B68]">Configure event logistics, funding goals, and
                                registration settings.</p>
                        </div>
                        <div class="space-y-6 px-6 py-6">
                            <div class="grid gap-6 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-[#213430]">Date & Time <span
                                            class="text-[#DB69A2]">*</span></label>
                                    <input id="event-date" name="date" type="datetime-local" value="{{ $dateValue }}"
                                        class="w-full rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70"
                                        required>
                                    @error('date')
                                        <p class="mt-1 text-xs text-[#DB69A2]">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-[#213430]">Event Location</label>
                                    <input name="location" type="text" value="{{ $locationValue }}"
                                        placeholder="Venue or virtual link"
                                        class="w-full rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70">
                                    @error('location')
                                        <p class="mt-1 text-xs text-[#DB69A2]">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid gap-6 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-[#213430]">Funding Goal ($)</label>
                                    <div class="relative">
                                        <span
                                            class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#91848C]">$</span>
                                        <input name="funding_goal" type="number" min="0" step="0.01"
                                            value="{{ old('funding_goal', $isEdit ? $event->funding_goal : '') }}"
                                            placeholder="25000"
                                            class="w-full rounded-xl border border-[#DCCFD8] bg-white pl-9 pr-4 py-3 text-sm outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70">
                                    </div>
                                    @error('funding_goal')
                                        <p class="mt-1 text-xs text-[#DB69A2]">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-[#213430]">Event Status</label>
                                    <div class="relative">
                                        <select name="status"
                                            class="w-full appearance-none rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-sm text-[#213430] outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70">
                                            <option value="upcoming" @selected(old('status', $isEdit ? $event->status : 'upcoming') == 'upcoming')>Upcoming</option>
                                            <option value="ongoing" @selected(old('status', $isEdit ? $event->status : '') == 'ongoing')>Ongoing</option>
                                            <option value="completed" @selected(old('status', $isEdit ? $event->status : '') == 'completed')>Completed</option>
                                            <option value="cancelled" @selected(old('status', $isEdit ? $event->status : '') == 'cancelled')>Cancelled</option>
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
                                </div>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-[#213430]">Registration Deadline</label>
                                <input id="registration-deadline" name="registration_deadline" type="datetime-local"
                                    value="{{ old('registration_deadline', $isEdit && $event->registration_deadline ? $event->registration_deadline->format('Y-m-d\TH:i') : '') }}"
                                    class="w-full rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70">
                                @error('registration_deadline')
                                    <p class="mt-1 text-xs text-[#DB69A2]">{{ $message }}</p>
                                @enderror
                                <p id="deadline-error" class="mt-1 text-xs text-[#DB69A2] hidden">Registration deadline
                                    must be before the event date</p>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-[#213430]">Event Highlights</label>
                                <textarea name="event_highlights" rows="3"
                                    placeholder="Key features, speakers, activities (use • to separate highlights)"
                                    class="w-full rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70">{{ old('event_highlights', $isEdit ? $event->event_highlights : '') }}</textarea>
                                @error('event_highlights')
                                    <p class="mt-1 text-xs text-[#DB69A2]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="rounded-xl bg-[#FDF7FB] px-4 py-3 text-xs text-[#6C5B68]">
                                Registration deadline should be before the event date to allow proper planning. Leave blank
                                for no deadline.
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-[#E9DCE7] bg-white shadow-sm">
                        <div class="border-b border-[#F1E5EF] px-6 py-5 flex flex-col gap-1">
                            <h2 class="text-lg font-semibold text-[#213430]">Launch Sponsorship <span
                                    class="text-xs font-medium uppercase tracking-wider text-[#DB69A2]">Optional</span>
                            </h2>
                            <p class="text-sm text-[#6C5B68]">Highlight a sponsor who has already pledged support.</p>
                        </div>
                        <div class="space-y-6 px-6 py-6">
                            <div class="grid gap-6 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-[#213430]">Select Sponsor</label>
                                    <div class="relative">
                                        <select id="sponsor-select" name="sponsor_id"
                                            class="w-full appearance-none rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-sm text-[#213430] outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70"
                                            {{ $sponsors->isEmpty() ? 'disabled' : '' }}>
                                            <option value="">Select sponsor</option>
                                            @forelse ($sponsors as $sponsor)
                                                <option value="{{ $sponsor->id }}" @selected((string) $selectedSponsor === (string) $sponsor->id)>
                                                    {{ $sponsor->{$displayCol} ?? $sponsor->email }}</option>
                                            @empty
                                                <option value="" disabled>No sponsors available</option>
                                            @endforelse
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
                                    @error('sponsor_id')
                                        <p class="mt-1 text-xs text-[#DB69A2]">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div id="sponsorship-amount-wrapper" class="transition {{ $amountWrapperClasses }}">
                                    <div class="flex items-center justify-between">
                                        <label class="mb-1 block text-sm font-medium text-[#213430]">Sponsorship Amount
                                            ($)</label>
                                        <span id="sponsorship-state"
                                            class="rounded-full px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide {{ $sponsorshipBadgeClasses }}">{{ $sponsorshipBadgeLabel }}</span>
                                    </div>
                                    <div class="relative mt-1">
                                        <span
                                            class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#91848C]">$</span>
                                        <input id="amount-input" name="amount" type="number" min="0.01"
                                            step="0.01" value="{{ $amountValue }}" placeholder="2500"
                                            class="w-full rounded-xl border border-[#DCCFD8] bg-white pl-9 pr-4 py-3 text-sm outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70"
                                            {{ $hasSponsor ? '' : 'disabled' }}>
                                    </div>
                                    @error('amount')
                                        <p class="mt-1 text-xs text-[#DB69A2]">{{ $message }}</p>
                                    @enderror
                                </div>

                            </div>

                            <div
                                class="rounded-xl border border-dashed border-[#F4C9DD] bg-[#FEF6FB] px-5 py-4 text-sm text-[#6C5B68]">
                                Adding a launch sponsor automatically creates the first sponsorship record and highlights
                                them on the event page.
                            </div>
                        </div>
                    </section>
                </div>

                <aside class="space-y-6">
                    <div class="rounded-2xl border border-[#E9DCE7] bg-white p-6 shadow-sm">
                        <h3 class="text-base font-semibold text-[#213430]">Event Checklist</h3>
                        <ul class="mt-4 space-y-3 text-sm text-[#6C5B68]">
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#F8D4E6] text-xs font-semibold text-[#DB69A2]">1</span>
                                Confirm your venue capacity and any accessibility details before publishing.
                            </li>
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#F8D4E6] text-xs font-semibold text-[#DB69A2]">2</span>
                                Lock in at least one sponsor to secure upfront funding for supplies.
                            </li>
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#F8D4E6] text-xs font-semibold text-[#DB69A2]">3</span>
                                Share the event link internally for final proofreading before it goes live.
                            </li>
                        </ul>
                    </div>

                    <div class="rounded-2xl border border-[#E9DCE7] bg-[#FDF7FB] p-6 shadow-inner">
                        <h3 class="text-base font-semibold text-[#213430]">Co-ordinate with Programs</h3>
                        <p class="mt-3 text-sm text-[#6C5B68]">Pair this event with an ongoing support program to maximise
                            sponsor visibility. Program banners appear beside event cards when they share a date range.</p>
                        <p class="mt-4 text-xs uppercase tracking-wider text-[#91848C]">Reminder</p>
                        <p class="mt-1 text-sm text-[#6C5B68]">Event highlights will sync to the sponsors hub immediately
                            after saving.</p>
                    </div>
                </aside>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const descriptionField = document.querySelector('#event-description');

            if (descriptionField && window.ClassicEditor) {
                ClassicEditor.create(descriptionField, {
                        toolbar: [
                            'heading', '|',
                            'bold', 'italic', 'underline', 'link', '|',
                            'bulletedList', 'numberedList', '|',
                            'blockQuote', 'undo', 'redo'
                        ]
                    })
                    .then(editor => {
                        window.__eventDescriptionEditor = editor;
                        editor.ui.view.editable.element.style.minHeight = '280px';
                    })
                    .catch(error => {
                        console.error('Failed to initialise the event description editor:', error);
                    });
            }

            // Sponsor select functionality
            var sponsorSelect = document.getElementById('sponsor-select');
            var amountWrapper = document.getElementById('sponsorship-amount-wrapper');
            var amountInput = document.getElementById('amount-input');
            var badge = document.getElementById('sponsorship-state');

            if (sponsorSelect && amountWrapper && amountInput && badge) {
                var updateState = function() {
                    if (sponsorSelect.value) {
                        amountWrapper.classList.remove('opacity-50');
                        amountInput.removeAttribute('disabled');
                        badge.textContent = 'Enabled';
                        badge.classList.remove('bg-[#F8D4E6]', 'text-[#C25E95]', 'bg-[#F3B9DA]',
                            'text-[#B74F86]');
                        badge.classList.add('bg-[#F3B9DA]', 'text-[#B74F86]');
                    } else {
                        amountWrapper.classList.add('opacity-50');
                        amountInput.value = '';
                        amountInput.setAttribute('disabled', 'disabled');
                        badge.textContent = 'Skipped';
                        badge.classList.remove('bg-[#F3B9DA]', 'text-[#B74F86]', 'bg-[#F8D4E6]',
                            'text-[#C25E95]');
                        badge.classList.add('bg-[#F8D4E6]', 'text-[#C25E95]');
                    }
                };

                sponsorSelect.addEventListener('change', updateState);
                updateState();
            }

            // Image preview functionality
            var imageInput = document.getElementById('image');
            var imagePreview = document.getElementById('image-preview');

            if (imageInput && imagePreview) {
                imageInput.addEventListener('change', function(e) {
                    var file = e.target.files[0];

                    if (file && file.type.startsWith('image/')) {
                        var reader = new FileReader();

                        reader.onload = function(event) {
                            imagePreview.innerHTML = `
                                <div class="mb-4">
                                    <img src="${event.target.result}" 
                                         alt="Preview" 
                                         class="mx-auto h-32 w-auto rounded-lg shadow-sm">
                                    <p class="mt-2 text-xs text-[#6C5B68]">New image selected</p>
                                </div>
                            `;
                        };

                        reader.readAsDataURL(file);
                    } else {
                        imagePreview.innerHTML = '';
                    }
                });
            }

            // Date validation: Registration deadline must be before event date
            var eventDateInput = document.getElementById('event-date');
            var registrationDeadlineInput = document.getElementById('registration-deadline');
            var deadlineError = document.getElementById('deadline-error');
            var form = document.getElementById('create-event-form');

            function validateDates() {
                if (!eventDateInput || !registrationDeadlineInput || !deadlineError) {
                    return true;
                }

                var eventDate = eventDateInput.value;
                var registrationDeadline = registrationDeadlineInput.value;

                // If registration deadline is empty, it's valid (optional field)
                if (!registrationDeadline) {
                    deadlineError.classList.add('hidden');
                    registrationDeadlineInput.classList.remove('border-[#DB69A2]');
                    return true;
                }

                // If event date is empty, we can't validate
                if (!eventDate) {
                    return true;
                }

                // Compare dates
                if (new Date(registrationDeadline) >= new Date(eventDate)) {
                    deadlineError.classList.remove('hidden');
                    registrationDeadlineInput.classList.add('border-[#DB69A2]');
                    return false;
                } else {
                    deadlineError.classList.add('hidden');
                    registrationDeadlineInput.classList.remove('border-[#DB69A2]');
                    return true;
                }
            }

            // Validate on input change
            if (eventDateInput) {
                eventDateInput.addEventListener('change', validateDates);
                eventDateInput.addEventListener('input', validateDates);
            }

            if (registrationDeadlineInput) {
                registrationDeadlineInput.addEventListener('change', validateDates);
                registrationDeadlineInput.addEventListener('input', validateDates);
            }

            // Validate on form submit
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (!validateDates()) {
                        e.preventDefault();
                        registrationDeadlineInput.focus();

                        // Scroll to the error
                        registrationDeadlineInput.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                });
            }

            // Set minimum date for event date (current date/time)
            if (eventDateInput) {
                var now = new Date();
                var year = now.getFullYear();
                var month = String(now.getMonth() + 1).padStart(2, '0');
                var day = String(now.getDate()).padStart(2, '0');
                var hours = String(now.getHours()).padStart(2, '0');
                var minutes = String(now.getMinutes()).padStart(2, '0');
                var minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
                eventDateInput.setAttribute('min', minDateTime);

                // Also set min for registration deadline (can't be in the past)
                if (registrationDeadlineInput) {
                    registrationDeadlineInput.setAttribute('min', minDateTime);
                }
            }

            // Set max date for registration deadline based on event date
            if (eventDateInput && registrationDeadlineInput) {
                var updateDeadlineLimits = function() {
                    var eventDateValue = eventDateInput.value;

                    if (eventDateValue) {
                        // Set max to event date
                        registrationDeadlineInput.setAttribute('max', eventDateValue);

                        // If registration deadline is already set and is after event date, clear it
                        var currentDeadline = registrationDeadlineInput.value;
                        if (currentDeadline && new Date(currentDeadline) >= new Date(eventDateValue)) {
                            registrationDeadlineInput.value = '';
                            deadlineError.classList.remove('hidden');
                            setTimeout(function() {
                                deadlineError.classList.add('hidden');
                            }, 3000);
                        }
                    } else {
                        registrationDeadlineInput.removeAttribute('max');
                    }
                };

                eventDateInput.addEventListener('change', updateDeadlineLimits);
                eventDateInput.addEventListener('input', updateDeadlineLimits);

                // Set initial max if event date already has value
                if (eventDateInput.value) {
                    updateDeadlineLimits();
                }
            }
        });
    </script>
@endpush
