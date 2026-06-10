@extends('admin.layouts.admin')

@section('title', 'Create Program')

@section('content')
    <div class="max-w-5xl mx-auto">
        <div class="space-y-6">
            <div class="rounded-2xl bg-gradient-to-r from-[#B52D75] via-[#9E2469] to-[#E8A8C8] text-white p-6 sm:p-8 shadow-lg relative">
                <div class="absolute inset-0 bg-black/10 rounded-2xl"></div>
                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="uppercase tracking-widest text-xs font-semibold opacity-90">Support Programs</p>
                        <h1 class="mt-1 text-2xl sm:text-3xl font-semibold">Create New Program</h1>
                        <p class="mt-2 text-sm opacity-95 max-w-lg">Follow the four steps below. Most programs only need listing details; Mammogram and Food programs also need an application form.</p>
                    </div>
                    <div class="flex gap-3 shrink-0">
                        <a href="{{ route('admin.programs-events') }}"
                            class="inline-flex items-center justify-center rounded-xl bg-white/25 px-4 py-2.5 text-sm font-medium text-white hover:bg-white/30">Cancel</a>
                        <button type="submit" form="create-program-form"
                            class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-[#9E2469] shadow-md hover:bg-[#FFF1F7]">Save program</button>
                    </div>
                </div>
            </div>

            <form id="create-program-form" method="POST" action="{{ route('programs.store') }}" enctype="multipart/form-data">
                @csrf

                @include('admin.programs.partials.program_form_steps')

                <div data-program-step="1">
                    @include('admin.programs.partials.program_type_and_sponsor', ['program' => null])
                </div>

                <div data-program-step="2" class="hidden">
                    <section class="rounded-2xl border border-[#E9DCE7] bg-white shadow-sm">
                        <div class="border-b border-[#F1E5EF] px-6 py-5">
                            <h2 class="text-lg font-semibold text-[#213430]">Program listing</h2>
                            <p class="mt-1 text-sm text-[#6C5B68]">What patients see on the program card — title, description, application window, and capacity.</p>
                            @if (!empty($usesStarterTemplate))
                                <p class="mt-3 text-sm rounded-lg border border-[#D1E7DD] bg-[#F0FFF4] px-3 py-2 text-[#0F5132]">
                                    First program? Click <strong>Use starter template</strong> in the field panel, then edit the values.
                                </p>
                            @endif
                        </div>
                        @include('admin.programs.partials.custom_field_builder', [
                            'builderId' => 'program-field-builder',
                            'initialFields' => old('custom_fields', []),
                            'defaultFields' => $defaultFields ?? [],
                            'defaultProgramTitle' => $defaultProgramTitle ?? null,
                            'usesStarterTemplate' => $usesStarterTemplate ?? false,
                            'embedded' => true,
                        ])
                    </section>
                </div>

                <div data-program-step="3" class="hidden">
                    <div class="mb-4 rounded-xl border border-[#EADFF0] bg-[#FDF7FB] px-4 py-3 text-sm text-[#6C5B68]" data-legacy-form-notice>
                        <p class="font-medium text-[#213430]">This program type uses the built-in application form.</p>
                        <p class="mt-1">Financial Assistance and Moments That Matter already include a standard patient form. You only need to configure a custom form if you want to override that behavior.</p>
                        <label class="mt-3 flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" data-enable-custom-app class="rounded border-[#DCCFD8] text-[#9E2469]">
                            <span>Configure a custom application form anyway</span>
                        </label>
                    </div>
                    <div data-application-form-panel>
                        @include('admin.programs.partials.application_form_builder', [
                            'builderId' => 'application-form-builder',
                            'initialFields' => old('application_form_schema', []),
                            'applicationFormTemplates' => $applicationFormTemplates ?? [],
                        ])
                    </div>
                </div>

                <div data-program-step="4" class="hidden">
                    @include('admin.programs.partials.banner_upload', [
                        'inputId' => 'create-program-banner',
                        'bannerUrl' => null,
                    ])
                </div>
            </form>
        </div>
    </div>
@endsection
