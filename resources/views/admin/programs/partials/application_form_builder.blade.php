@php
    $builderId = $builderId ?? 'application-form-builder';
    $initialFields = $initialFields ?? [];
    $applicationFormTemplates = $applicationFormTemplates ?? [];
    $fieldTypes = \App\Support\ApplicationFormFieldTypes::options();
    $mappableColumns = \App\Support\ApplicationFormSchema::MAPPABLE_COLUMNS;
    $typeLabels = array_flip($fieldTypes);
@endphp

<section class="rounded-2xl border border-[#E9DCE7] bg-white shadow-sm">
    <div class="border-b border-[#F1E5EF] px-6 py-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-[#213430]">Application questions</h2>
                <p class="mt-1 text-sm text-[#6C5B68]">Questions patients answer when they apply. Start with a template, then tweak as needed.</p>
            </div>
            <span class="rounded-full bg-[#F3E8EF] px-3 py-1 text-xs font-semibold text-[#9E2469]">
                <span data-app-field-count>0</span> fields
            </span>
        </div>
    </div>

    <div class="border-b border-[#F1E5EF] bg-[#FDF7FB] px-4 py-4 sm:px-6">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            @if (!empty($applicationFormTemplates))
                <div class="flex flex-wrap gap-2">
                    @foreach ($applicationFormTemplates as $typeKey => $templateFields)
                        @if (!empty($templateFields))
                            <button type="button" data-load-app-template="{{ $typeKey }}"
                                class="rounded-lg border border-[#DCCFD8] bg-white px-3 py-2 text-xs font-semibold text-[#213430] hover:border-[#9E2469] hover:text-[#9E2469]">
                                Load {{ \App\Support\ProgramType::label($typeKey) }} template
                            </button>
                        @endif
                    @endforeach
                </div>
            @endif
            <div class="flex flex-wrap gap-2">
                <button type="button" data-quick-app-field="section_header" class="rounded-full border border-[#DCCFD8] bg-white px-3 py-1.5 text-xs font-semibold text-[#213430] hover:border-[#9E2469]">+ Section</button>
                <button type="button" data-quick-app-field="short_text" class="rounded-full border border-[#DCCFD8] bg-white px-3 py-1.5 text-xs font-semibold text-[#213430] hover:border-[#9E2469]">+ Text</button>
                <button type="button" data-quick-app-field="select" class="rounded-full border border-[#DCCFD8] bg-white px-3 py-1.5 text-xs font-semibold text-[#213430] hover:border-[#9E2469]">+ Dropdown</button>
                <button type="button" data-quick-app-field="file" class="rounded-full border border-[#DCCFD8] bg-white px-3 py-1.5 text-xs font-semibold text-[#213430] hover:border-[#9E2469]">+ Upload</button>
                <button type="button" id="{{ $builderId }}-add"
                    class="rounded-full border border-dashed border-[#9E2469] bg-white px-3 py-1.5 text-xs font-semibold text-[#9E2469] hover:bg-[#FDF0F7]">+ Add field</button>
            </div>
        </div>
    </div>

    <div class="px-4 py-6 sm:px-6">
        <div id="{{ $builderId }}-empty" class="hidden rounded-xl border-2 border-dashed border-[#E9DCE7] bg-[#FDF7FB] px-6 py-10 text-center">
            <p class="text-sm font-semibold text-[#213430]">No application questions yet</p>
            <p class="mt-2 text-sm text-[#6C5B68] max-w-md mx-auto">Load a starter template above for Mammogram or Food programs, or add fields one at a time.</p>
        </div>
        <div id="{{ $builderId }}-list" class="space-y-2" data-app-field-builder-list></div>
        @if ($errors->has('application_form_schema'))
            <p class="mt-3 text-xs font-semibold text-[#B32020]">{{ $errors->first('application_form_schema') }}</p>
        @endif
    </div>
</section>

<template id="{{ $builderId }}-template">
    <div class="rounded-xl border border-[#E9DCE7] bg-white overflow-hidden" data-app-field>
        <button type="button" data-action="toggle"
            class="flex w-full items-center gap-3 px-4 py-3 text-left hover:bg-[#FDF7FB] transition">
            <span class="text-[#91848C] text-xs" data-role="sort-handle">⋮⋮</span>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-[#213430] truncate" data-role="summary-label">New field</p>
                <p class="text-xs text-[#91848C] truncate" data-role="summary-meta">Short text</p>
            </div>
            <span class="shrink-0 rounded-full bg-[#F3E8EF] px-2 py-0.5 text-[10px] font-semibold text-[#9E2469] hidden" data-role="required-badge">Required</span>
            <span class="shrink-0 text-[#91848C] text-sm" data-role="chevron">▼</span>
        </button>

        <div class="hidden border-t border-[#F1E5EF] px-4 py-4 bg-[#FDF7FB]" data-role="body">
            <div class="flex justify-end mb-3">
                <button type="button" data-action="remove" class="text-xs font-semibold text-[#B32020] hover:underline">Remove field</button>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-[#213430]">Label *</label>
                    <input type="text" data-role="label" data-name="label" placeholder="e.g. First Name"
                        class="w-full rounded-xl border border-[#DCCFD8] bg-white px-3 py-2 text-sm focus:border-[#9E2469]">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-[#213430]">Field key</label>
                    <input type="text" data-role="name" data-name="name" placeholder="auto from label"
                        class="w-full rounded-xl border border-[#DCCFD8] bg-white px-3 py-2 text-sm focus:border-[#9E2469]">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-[#213430]">Type</label>
                    <select data-role="type" data-name="type"
                        class="w-full rounded-xl border border-[#DCCFD8] bg-white px-3 py-2 text-sm focus:border-[#9E2469]">
                        @foreach ($fieldTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-[#213430]">Section</label>
                    <input type="text" data-role="section" data-name="section" placeholder="Applicant Information"
                        class="w-full rounded-xl border border-[#DCCFD8] bg-white px-3 py-2 text-sm focus:border-[#9E2469]">
                </div>
            </div>

            <div data-role="options-wrap" class="hidden mt-4">
                <label class="mb-1 block text-sm font-medium text-[#213430]">Options (one per line)</label>
                <textarea data-role="options" data-name="options" rows="3" placeholder="Option 1&#10;Option 2"
                    class="w-full rounded-xl border border-[#DCCFD8] bg-white px-3 py-2 text-sm focus:border-[#9E2469]"></textarea>
            </div>

            <div class="mt-4 flex items-center gap-2">
                <input type="checkbox" data-role="required" data-name="required" value="1" class="rounded border-[#DCCFD8] text-[#9E2469]">
                <label class="text-sm text-[#213430]">Required</label>
            </div>

            <details class="mt-4 rounded-lg border border-[#E9DCE7] bg-white">
                <summary class="cursor-pointer px-3 py-2 text-xs font-semibold text-[#6C5B68] hover:text-[#9E2469]">Advanced options</summary>
                <div class="grid gap-4 p-3 md:grid-cols-2 border-t border-[#F1E5EF]">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-[#213430]">Help text</label>
                        <input type="text" data-role="help_text" data-name="help_text"
                            class="w-full rounded-lg border border-[#DCCFD8] px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-[#213430]">Map to column</label>
                        <select data-role="maps_to_column" data-name="maps_to_column"
                            class="w-full rounded-lg border border-[#DCCFD8] px-3 py-2 text-sm">
                            <option value="">— None —</option>
                            @foreach ($mappableColumns as $col)
                                <option value="{{ $col }}">{{ str_replace('_', ' ', $col) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-[#213430]">Show when field</label>
                        <input type="text" data-role="conditional_field" data-name="conditional_field" placeholder="field key"
                            class="w-full rounded-lg border border-[#DCCFD8] px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-[#213430]">Equals</label>
                        <input type="text" data-role="conditional_value" data-name="conditional_value"
                            class="w-full rounded-lg border border-[#DCCFD8] px-3 py-2 text-sm">
                    </div>
                </div>
            </details>
        </div>

        <input type="hidden" data-role="id" data-name="id">
    </div>
</template>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const listEl = document.getElementById('{{ $builderId }}-list');
    const emptyEl = document.getElementById('{{ $builderId }}-empty');
    const addBtn = document.getElementById('{{ $builderId }}-add');
    const template = document.getElementById('{{ $builderId }}-template');
    const initialFields = @json($initialFields);
    const appTemplates = @json($applicationFormTemplates);
    const fieldCountEl = document.querySelector('[data-app-field-count]');
    const typeLabels = @json($fieldTypes);
    const optionTypes = ['select', 'radio', 'checkbox_group'];
    const displayOnlyTypes = ['section_header', 'guidelines'];

    if (!listEl || !template) return;

    const slugify = (v) => String(v || '').toLowerCase().trim().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');
    const randomId = () => window.crypto?.randomUUID?.() || ('af_' + Math.random().toString(36).slice(2, 10));

    const optionsToText = (options) => {
        if (!Array.isArray(options)) return '';
        return options.map((o) => (typeof o === 'object' ? (o.label || o.value || '') : o)).filter(Boolean).join('\n');
    };

    const setProgramType = (typeKey) => {
        const radio = document.querySelector(`input[name="program_type"][value="${typeKey}"]`);
        if (radio) radio.checked = true;
        radio?.dispatchEvent(new Event('change', { bubbles: true }));
    };

    const updateEmpty = () => {
        const count = listEl.querySelectorAll('[data-app-field]').length;
        if (fieldCountEl) fieldCountEl.textContent = count;
        if (emptyEl) emptyEl.classList.toggle('hidden', count > 0);
    };

    const syncIndexes = () => {
        listEl.querySelectorAll('[data-app-field]').forEach((row, index) => {
            row.querySelectorAll('[data-name]').forEach((input) => {
                input.name = `application_form_schema[${index}][${input.getAttribute('data-name')}]`;
            });
        });
        updateEmpty();
    };

    const updateSummary = (node) => {
        const label = node.querySelector('[data-role="label"]')?.value?.trim() || 'Untitled field';
        const type = node.querySelector('[data-role="type"]')?.value || 'short_text';
        const section = node.querySelector('[data-role="section"]')?.value?.trim();
        const required = node.querySelector('[data-role="required"]')?.checked;
        node.querySelector('[data-role="summary-label"]').textContent = label;
        node.querySelector('[data-role="summary-meta"]').textContent =
            (typeLabels[type] || type) + (section ? ` · ${section}` : '');
        node.querySelector('[data-role="required-badge"]')?.classList.toggle('hidden', !required);
    };

    const toggleOptions = (node, type) => {
        node.querySelector('[data-role="options-wrap"]')?.classList.toggle('hidden', !optionTypes.includes(type));
    };

    const addField = (field = {}, expand = false) => {
        const node = template.content.firstElementChild.cloneNode(true);
        const refs = {
            label: node.querySelector('[data-role="label"]'),
            name: node.querySelector('[data-role="name"]'),
            type: node.querySelector('[data-role="type"]'),
            section: node.querySelector('[data-role="section"]'),
            options: node.querySelector('[data-role="options"]'),
            maps: node.querySelector('[data-role="maps_to_column"]'),
            help: node.querySelector('[data-role="help_text"]'),
            condField: node.querySelector('[data-role="conditional_field"]'),
            condValue: node.querySelector('[data-role="conditional_value"]'),
            required: node.querySelector('[data-role="required"]'),
            id: node.querySelector('[data-role="id"]'),
            body: node.querySelector('[data-role="body"]'),
            chevron: node.querySelector('[data-role="chevron"]'),
        };

        refs.label.value = field.label || '';
        refs.name.value = field.name || '';
        refs.type.value = field.type || 'short_text';
        refs.section.value = field.section || '';
        refs.options.value = optionsToText(field.options || []);
        refs.maps.value = field.maps_to_column || '';
        refs.help.value = field.help_text || '';
        refs.condField.value = field.conditional?.field || field.conditional_field || '';
        refs.condValue.value = field.conditional?.value || field.conditional_value || '';
        refs.required.checked = !!field.required;
        refs.id.value = field.id || randomId();

        const setExpanded = (open) => {
            refs.body.classList.toggle('hidden', !open);
            refs.chevron.textContent = open ? '▲' : '▼';
        };

        refs.label.addEventListener('input', () => {
            if (!refs.name.dataset.touched) refs.name.value = slugify(refs.label.value);
            updateSummary(node);
        });
        refs.name.addEventListener('input', () => { refs.name.dataset.touched = '1'; });
        refs.type.addEventListener('change', () => { toggleOptions(node, refs.type.value); updateSummary(node); });
        refs.section.addEventListener('input', () => updateSummary(node));
        refs.required.addEventListener('change', () => updateSummary(node));

        node.querySelector('[data-action="toggle"]').addEventListener('click', () => {
            setExpanded(refs.body.classList.contains('hidden'));
        });
        node.querySelector('[data-action="remove"]').addEventListener('click', () => {
            node.remove();
            syncIndexes();
        });

        toggleOptions(node, refs.type.value);
        updateSummary(node);
        setExpanded(expand || displayOnlyTypes.includes(refs.type.value));

        listEl.appendChild(node);
        syncIndexes();
    };

    const applyTemplate = (typeKey) => {
        const fields = appTemplates[typeKey];
        if (!Array.isArray(fields) || !fields.length) return;
        if (listEl.querySelectorAll('[data-app-field]').length && !confirm('Replace current fields with this template?')) return;
        listEl.innerHTML = '';
        fields.forEach((f) => addField(f, false));
        setProgramType(typeKey);
    };

    document.querySelectorAll('[data-load-app-template]').forEach((btn) => {
        btn.addEventListener('click', () => applyTemplate(btn.getAttribute('data-load-app-template')));
    });

    document.querySelectorAll('[data-quick-app-field]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const type = btn.getAttribute('data-quick-app-field');
            const presets = {
                section_header: { label: 'New Section', type: 'section_header' },
                short_text: { label: 'Short answer', type: 'short_text', required: true },
                select: { label: 'Dropdown question', type: 'select', options: ['Option 1', 'Option 2'], required: true },
                file: { label: 'Upload document', type: 'file', required: true },
                signature: { label: 'Signature', type: 'signature', required: true },
            };
            addField(presets[type] || { type }, true);
        });
    });

    if (Array.isArray(initialFields) && initialFields.length) {
        initialFields.forEach((f) => addField(f, false));
    } else {
        updateEmpty();
    }

    addBtn?.addEventListener('click', () => addField({ type: 'short_text' }, true));
});
</script>
@endpush
