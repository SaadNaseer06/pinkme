@extends('admin.layouts.admin')

@section('title', 'Edit Program')

@section('content')
    <div class="max-w-5xl mx-auto">
        <div class="space-y-6">
            <div class="rounded-2xl bg-gradient-to-r from-[#B52D75] via-[#9E2469] to-[#E8A8C8] text-white p-6 sm:p-8 shadow-lg relative">
                <div class="absolute inset-0 bg-black/10 rounded-2xl"></div>
                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="uppercase tracking-widest text-xs font-semibold opacity-90">Support Programs</p>
                        <h1 class="mt-1 text-2xl sm:text-3xl font-semibold">Edit Program</h1>
                        <p class="mt-2 text-sm opacity-95">Update listing details, application questions, or banner using the steps below.</p>
                    </div>
                    <div class="flex flex-wrap gap-3 shrink-0">
                        <a href="{{ route('admin.programs-events') }}"
                            class="inline-flex items-center justify-center rounded-xl bg-white/25 px-4 py-2.5 text-sm font-medium text-white hover:bg-white/30">Back</a>
                        <button type="button"
                            onclick="previewDraftApplicationFormAsPatient({ builderId: 'application-form-builder', programId: @js($program->id), fallbackTitle: @js($program->title) })"
                            class="inline-flex items-center justify-center rounded-xl bg-white/25 px-4 py-2.5 text-sm font-medium text-white hover:bg-white/30 border border-white/40"
                            title="See the application form as a patient would">Preview form</button>
                        <button type="submit" form="edit-program-form"
                            class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-[#9E2469] shadow-md hover:bg-[#FFF1F7]">Save changes</button>
                        <form action="{{ route('programs.destroy', $program) }}" method="POST" class="inline-flex"
                            onsubmit="return confirm(@js(
                                'Delete this program?'
                                .(($program->registrations_count ?? 0) > 0 ? ' '.$program->registrations_count.' application(s) will be removed too.' : '')
                                .' This cannot be undone.'
                            ));">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-white/90 px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-50">Delete</button>
                        </form>
                    </div>
                </div>
            </div>

            <form id="edit-program-form" method="POST" action="{{ route('programs.update', $program) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @include('admin.programs.partials.program_form_steps')

                <div data-program-step="1">
                    @include('admin.programs.partials.program_type_and_sponsor', ['program' => $program])
                </div>

                <div data-program-step="2" class="hidden">
                    <section class="rounded-2xl border border-[#E9DCE7] bg-white shadow-sm">
                        <div class="border-b border-[#F1E5EF] px-6 py-5">
                            <h2 class="text-lg font-semibold text-[#213430]">Program listing</h2>
                            <p class="mt-1 text-sm text-[#6C5B68]">Title, description, dates, and capacity shown on the patient program card.</p>
                        </div>
                        @include('admin.programs.partials.custom_field_builder', [
                            'builderId' => 'program-field-builder',
                            'initialFields' => old('custom_fields', $program->custom_fields ?? []),
                            'defaultFields' => [],
                            'defaultProgramTitle' => null,
                            'embedded' => true,
                        ])
                    </section>
                </div>

                <div data-program-step="3" class="hidden">
                    <div class="mb-4 rounded-xl border border-[#EADFF0] bg-[#FDF7FB] px-4 py-3 text-sm text-[#6C5B68]" data-legacy-form-notice>
                        <p class="font-medium text-[#213430]">This program type uses the built-in application form.</p>
                        <label class="mt-2 flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" data-enable-custom-app class="rounded border-[#DCCFD8] text-[#9E2469]"
                                {{ !empty($program->application_form_schema) ? 'checked' : '' }}>
                            <span>Configure a custom application form</span>
                        </label>
                    </div>
                    <div data-application-form-panel class="{{ empty($program->application_form_schema) && !\App\Support\ProgramType::usesDynamicApplicationForm($program->program_type) ? 'opacity-50' : '' }}">
                        @include('admin.programs.partials.application_form_builder', [
                            'builderId' => 'application-form-builder',
                            'initialFields' => old('application_form_schema', $program->application_form_schema ?? []),
                            'applicationFormTemplates' => $applicationFormTemplates ?? [],
                            'previewProgramId' => $program->id,
                            'previewProgramTitle' => $program->title,
                        ])
                    </div>
                </div>

                <div data-program-step="4" class="hidden">
                    @include('admin.programs.partials.banner_upload', [
                        'inputId' => 'edit-program-banner',
                        'bannerUrl' => $program->banner ? storage_url(ltrim($program->banner, '/')) : null,
                    ])
                </div>
            </form>
        </div>
    </div>

    @include('admin.programs.partials.application_form_patient_preview')
@endsection
