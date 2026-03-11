@extends('admin.layouts.admin')

@section('title', 'Create Program')

@section('content')
    <div class="max-w-8xl mx-auto">
        <div class="space-y-8">
            <div
                class="rounded-2xl bg-gradient-to-r from-[#B52D75] via-[#9E2469] to-[#E8A8C8] text-white p-8 shadow-lg relative">
                <div class="absolute inset-0 bg-black/10 rounded-2xl"></div>
                <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-xl" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.6);">
                        <p class="uppercase tracking-widest text-xs font-semibold opacity-90">Support Programs</p>
                        <h1 class="mt-2 text-3xl font-semibold">Create New Program</h1>
                        <p class="mt-3 text-sm lg:text-base max-w-xl opacity-95">Build every field you need - titles, descriptions, dates, payments, and more - using the flexible field builder below, then add an optional banner image.</p>
                    </div>
                    <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                        <a href="{{ route('admin.programs-events') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-white/25 px-5 py-3 text-sm font-medium text-white backdrop-blur transition hover:bg-white/30">Back
                            to programs</a>
                        <button type="submit" form="create-program-form"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-[#9E2469] shadow-md shadow-white/40 transition hover:bg-[#FFF1F7]">Save
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
                        <h2 class="text-lg font-semibold text-[#213430]">All fields are custom</h2>
                        <p class="mt-1 text-sm text-[#6C5B68]">Add the fields you need for this program. Use the quick-add buttons for Title, Date, Time, Status, Payment, and Fund goal in one click.</p>
                    </div>
                        <div class="px-6 py-6 text-sm text-[#6C5B68] space-y-2">
                            <p>Popular fields to add:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Title / Summary</li>
                                <li>Description</li>
                                <li>Application window (start &amp; end dates)</li>
                                <li>Time</li>
                                <li>Maximum applications (e.g. 10)</li>
                                <li>Status (upcoming, ongoing, completed)</li>
                                <li>Maximum applications and status</li>
                                <li>Location / Meeting link / Facilitator</li>
                            </ul>
                        </div>
                    </section>

                    @include('admin.programs.partials.custom_field_builder', [
                        'builderId' => 'program-field-builder',
                        'initialFields' => old('custom_fields', []),
                        'defaultFields' => $defaultFields ?? [],
                        'defaultProgramTitle' => optional($defaultProgram)->title,
                    ])

                    @include('admin.programs.partials.banner_upload', [
                        'inputId' => 'create-program-banner',
                        'bannerUrl' => null,
                    ])
                </div>

                <aside class="space-y-6">
                    <div class="rounded-2xl border border-[#E9DCE7] bg-white p-6 shadow-sm">
                        <h3 class="text-base font-semibold text-[#213430]">Before you publish</h3>
                        <ul class="mt-4 space-y-3 text-sm text-[#6C5B68]">
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#F8D4E6] text-xs font-semibold text-[#9E2469]">1</span>
                                Add a Title field so patients see a clear program name.
                            </li>
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#F8D4E6] text-xs font-semibold text-[#9E2469]">2</span>
                                Include a Time field if reminders need a schedule.
                            </li>
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#F8D4E6] text-xs font-semibold text-[#9E2469]">3</span>
                                Add application start and end dates to define the intake window.
                            </li>
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#F8D4E6] text-xs font-semibold text-[#9E2469]">4</span>
                                Add a maximum applications field to cap submissions.
                            </li>
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#F8D4E6] text-xs font-semibold text-[#9E2469]">5</span>
                                Add maximum applications and status to control availability.
                            </li>
                            <li class="flex items-start gap-3">
                                <span
                                    class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#F8D4E6] text-xs font-semibold text-[#9E2469]">6</span>
                                Upload a banner image so the program is instantly recognizable.
                            </li>
                        </ul>
                    </div>

                    <div class="rounded-2xl border border-[#E9DCE7] bg-[#FDF7FB] p-6 shadow-inner">
                        <h3 class="text-base font-semibold text-[#213430]">Quick tips</h3>
                        <p class="mt-3 text-sm text-[#6C5B68]">Everything is custom now - only add what your program needs. Backend-required values are derived from your fields. Banner upload stays separate below.</p>
                        <p class="mt-4 text-xs uppercase tracking-wider text-[#91848C]">Recommended minimum</p>
                        <p class="mt-1 text-sm text-[#6C5B68]">Title, Description, Time, Application start/end, Maximum applications, Status + optional banner.</p>
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
