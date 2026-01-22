@php
    $builderId = $builderId ?? 'program-field-builder';
    $initialFields = $initialFields ?? [];
    $defaultFields = $defaultFields ?? [];
    $defaultProgramTitle = $defaultProgramTitle ?? null;
@endphp

<section class="rounded-2xl border border-[#E9DCE7] bg-white shadow-sm">
    <div class="flex flex-col gap-1 border-b border-[#F1E5EF] px-6 py-5">
        <h2 class="text-lg font-semibold text-[#213430]">Additional Program Fields</h2>
        <p class="mt-1 text-sm text-[#6C5B68]">Pick a field name from the list (title, description, dates, fund, etc.) so it maps correctly everywhere.</p>
    </div>

    <div class="px-6 py-6">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start">
            <div class="rounded-xl border border-[#E9DCE7] bg-[#FDF7FB] p-4 shadow-inner space-y-4 lg:w-[280px] lg:flex-none lg:sticky lg:top-4">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-sm font-semibold text-[#213430]">Field controls</p>
                    <span class="text-xs font-semibold uppercase tracking-wide text-[#91848C]">Count: <span data-field-count>0</span></span>
                </div>
                <p class="text-xs text-[#B32020]" data-duplicate-warning style="display: none;"></p>
                @if (!empty($defaultFields))
                    <div class="rounded-lg border border-[#DCCFD8] bg-white px-3 py-3">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs font-semibold text-[#213430]">Default form</p>
                            <button type="button" data-load-default
                                class="text-xs font-semibold text-[#DB69A2] hover:underline">Use it</button>
                        </div>
                        <p class="mt-1 text-[11px] text-[#6C5B68]">Load the previous program fields{{ $defaultProgramTitle ? ' from "' . $defaultProgramTitle . '"' : '' }}.</p>
                        <p class="mt-2 hidden text-[11px] rounded-lg border border-[#D1E7DD] bg-[#F0FFF4] px-3 py-2 text-[#0F5132]"
                            data-default-hint>Default form applied. You can still edit or remove fields.</p>
                    </div>
                @endif
                <p class="text-xs text-[#6C5B68]">Add common fields from the chips below, then fine-tune on the right.</p>
                <button type="button" id="{{ $builderId }}-add"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-dashed border-[#DB69A2] bg-white px-4 py-2 text-sm font-semibold text-[#DB69A2] transition hover:bg-[#FDF0F7]">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" />
                    </svg>
                    Add a blank field
                </button>
                <div class="flex flex-wrap gap-2">
                    <button type="button" data-quick-field="title" class="rounded-full border border-[#DCCFD8] bg-white px-3 py-1 text-xs font-semibold text-[#213430] transition hover:border-[#DB69A2] hover:text-[#DB69A2]">+ Title</button>
                    <button type="button" data-quick-field="description" class="rounded-full border border-[#DCCFD8] bg-white px-3 py-1 text-xs font-semibold text-[#213430] transition hover:border-[#DB69A2] hover:text-[#DB69A2]">+ Description</button>
                    <button type="button" data-quick-field="application_start_date" class="rounded-full border border-[#DCCFD8] bg-white px-3 py-1 text-xs font-semibold text-[#213430] transition hover:border-[#DB69A2] hover:text-[#DB69A2]">+ Application Start</button>
                    <button type="button" data-quick-field="application_end_date" class="rounded-full border border-[#DCCFD8] bg-white px-3 py-1 text-xs font-semibold text-[#213430] transition hover:border-[#DB69A2] hover:text-[#DB69A2]">+ Application End</button>
                    <button type="button" data-quick-field="event_time" class="rounded-full border border-[#DCCFD8] bg-white px-3 py-1 text-xs font-semibold text-[#213430] transition hover:border-[#DB69A2] hover:text-[#DB69A2]">+ Time</button>
                    <button type="button" data-quick-field="status" class="rounded-full border border-[#DCCFD8] bg-white px-3 py-1 text-xs font-semibold text-[#213430] transition hover:border-[#DB69A2] hover:text-[#DB69A2]">+ Status</button>
                    <button type="button" data-quick-field="max_applications" class="rounded-full border border-[#DCCFD8] bg-white px-3 py-1 text-xs font-semibold text-[#213430] transition hover:border-[#DB69A2] hover:text-[#DB69A2]">+ Max Applications</button>
                </div>
                <p class="text-xs text-[#91848C]">Quick-add drops in the right label and type. Edit values on the right.</p>
            </div>

            <div class="space-y-4 flex-1">
                <p class="text-sm text-[#6C5B68]">Fields you add appear here. They are shown to patients and also drive required backend values.</p>
                <div id="{{ $builderId }}-list" class="space-y-4" data-field-builder-list></div>
                <p class="text-xs text-[#91848C]">Tip: popular fields include "Location", "Program lead", "Capacity", "Registration deadline", "Meeting link", and "Notes for participants".</p>
                @if ($errors->has('custom_fields') || $errors->has('title'))
                    <p class="text-xs font-semibold text-[#B32020]">
                        {{ $errors->first('title') ?? $errors->first('custom_fields') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</section>

<template id="{{ $builderId }}-template">
    <div class="rounded-xl border border-[#E9DCE7] bg-[#FDF7FB] px-4 py-4 shadow-sm" data-custom-field>
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-sm font-semibold text-[#213430]">Field</p>
                <p class="text-xs text-[#6C5B68]">Pick the field and enter a value. Advanced settings are optional.</p>
            </div>
            <button type="button" data-action="remove"
                class="text-[#B32020] text-xs font-semibold hover:underline">Remove</button>
        </div>

        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-[#213430]">Field name</label>
                <select data-role="name" data-name="name"
                    class="w-full rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70">
                    <option value="title">Title</option>
                    <option value="description">Description</option>
                    <option value="event_time">Time</option>
                    <option value="application_start_date">Application Start Date</option>
                    <option value="application_end_date">Application End Date</option>
                    <option value="status">Status</option>
                    <option value="max_applications">Maximum Applications</option>
                    <option value="link">Link</option>
                    <option value="custom_note">Additional Note</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-[#213430]">Value</label>
                <div data-role="value-slot"></div>
            </div>
        </div>

        <div class="mt-3">
            <button type="button" data-action="toggle-advanced"
                class="text-xs font-semibold text-[#213430] hover:underline">More fields</button>
            <div class="mt-3 hidden space-y-3" data-role="advanced">
                <div>
                    <label class="mb-1 block text-sm font-medium text-[#213430]">Display label</label>
                    <input type="text" data-role="label" data-name="label" placeholder="e.g. Program Title"
                        class="w-full rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-[#213430]">Field type</label>
                    <select data-role="type" data-name="type"
                        class="w-full rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70">
                        <option value="short_text">Short text</option>
                        <option value="long_text">Long text</option>
                        <option value="number">Number</option>
                        <option value="money">Currency</option>
                        <option value="date">Date</option>
                        <option value="time">Time</option>
                        <option value="link">Link</option>
                        <option value="boolean">Yes / No</option>
                    </select>
                </div>
            </div>
        </div>

        <input type="hidden" data-role="id" data-name="id">
    </div>
</template>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const listEl = document.getElementById('{{ $builderId }}-list');
            const addBtn = document.getElementById('{{ $builderId }}-add');
            const template = document.getElementById('{{ $builderId }}-template');
            const initialFields = @json($initialFields);
            const fieldCountEl = document.querySelector('[data-field-count]');
            const quickButtons = document.querySelectorAll('[data-quick-field]');
            const duplicateWarning = document.querySelector('[data-duplicate-warning]');
            const defaultFields = @json($defaultFields);
            const defaultButton = document.querySelector('[data-load-default]');
            const defaultHint = document.querySelector('[data-default-hint]');
            const HIGHLIGHT_CLASS = 'outline outline-2 outline-[#DB69A2]';

            if (!listEl || !addBtn || !template) {
                return;
            }

            const fieldPresets = {
                title: { label: 'Title', type: 'short_text' },
                description: { label: 'Description', type: 'long_text' },
                event_time: { label: 'Time', type: 'time' },
                application_start_date: { label: 'Application Start Date', type: 'date' },
                application_end_date: { label: 'Application End Date', type: 'date' },
                status: { label: 'Status', type: 'short_text' },
                max_applications: { label: 'Maximum Applications', type: 'number' },
                link: { label: 'Link', type: 'link' },
                custom_note: { label: 'Additional Note', type: 'long_text' },
            };

            const typeCopy = {
                short_text: 'Short text',
                long_text: 'Long text',
                number: 'Number',
                money: 'Currency',
                date: 'Date',
                time: 'Time',
                link: 'Link',
                boolean: 'Yes / No'
            };

            const formatBoolean = (value) => {
                return value === true || value === '1' || value === 1 || value === 'true';
            };

            const randomId = () => (window.crypto && window.crypto.randomUUID) ? window.crypto.randomUUID() : ('cf_' + Math.random().toString(36).slice(2, 10));

            const updateCount = () => {
                if (!fieldCountEl) return;
                fieldCountEl.textContent = listEl.querySelectorAll('[data-custom-field]').length;
            };

            const setDuplicateWarning = (message = '') => {
                if (!duplicateWarning) return;
                duplicateWarning.textContent = message;
                duplicateWarning.style.display = message === '' ? 'none' : 'block';
            };

            const selectedNames = (omitEl = null) => Array.from(listEl.querySelectorAll('[data-role="name"]'))
                .filter((el) => !omitEl || el !== omitEl)
                .map((el) => el.value);

            const highlightExisting = (name) => {
                if (!name) return;
                const select = Array.from(listEl.querySelectorAll('[data-role="name"]')).find((el) => el.value === name);
                const row = select ? select.closest('[data-custom-field]') : null;
                if (!row) return;
                row.classList.add(HIGHLIGHT_CLASS);
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => row.classList.remove(HIGHLIGHT_CLASS), 1200);
            };

            const syncIndexes = () => {
                listEl.querySelectorAll('[data-custom-field]').forEach((row, index) => {
                    row.querySelectorAll('[data-name]').forEach((input) => {
                        const base = input.getAttribute('data-name');
                        input.name = `custom_fields[${index}][${base}]`;
                    });
                });
                updateCount();
            };

            const buildValueControl = (type, value, name) => {
                const baseClass = 'w-full rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#DB69A2] focus:ring focus:ring-[#F8D4E6] focus:ring-opacity-70';

                if (name === 'status') {
                    const select = document.createElement('select');
                    select.className = baseClass;
                    select.setAttribute('data-role', 'value');
                    select.setAttribute('data-name', 'value');
                    select.innerHTML = `
                        <option value="upcoming">Upcoming</option>
                        <option value="ongoing">Ongoing</option>
                    `;
                    select.value = (value && ['upcoming', 'ongoing'].includes(String(value).toLowerCase()))
                        ? String(value).toLowerCase()
                        : 'upcoming';
                    return select;
                }


                if (type === 'long_text') {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'space-y-2';

                    const editor = document.createElement('div');
                    editor.className = `${baseClass} min-h-[120px] whitespace-pre-wrap`;
                    editor.setAttribute('contenteditable', 'true');
                    editor.setAttribute('data-role', 'rich-text');
                    editor.textContent = value || '';

                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.setAttribute('data-role', 'value');
                    hidden.setAttribute('data-name', 'value');
                    hidden.value = value || '';

                    editor.addEventListener('input', () => {
                        hidden.value = editor.textContent.trim();
                    });

                    wrapper.appendChild(editor);
                    wrapper.appendChild(hidden);
                    return wrapper;
                }

                let control;

                switch (type) {
                    case 'number':
                        control = document.createElement('input');
                        control.type = 'number';
                        control.step = '1';
                        control.value = value || '';
                        break;
                    case 'money':
                        control = document.createElement('input');
                        control.type = 'number';
                        control.step = '0.01';
                        control.placeholder = 'e.g. 2500';
                        control.value = value || '';
                        break;
                    case 'date':
                        control = document.createElement('input');
                        control.type = 'date';
                        control.value = value || '';
                        break;
                    case 'time':
                        control = document.createElement('input');
                        control.type = 'time';
                        control.value = value || '';
                        break;
                    case 'link':
                        control = document.createElement('input');
                        control.type = 'url';
                        control.placeholder = 'https://example.com';
                        control.value = value || '';
                        break;
                    case 'boolean':
                        control = document.createElement('select');
                        control.innerHTML = `
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        `;
                        control.value = formatBoolean(value) ? '1' : '0';
                        break;
                    default:
                        control = document.createElement('input');
                        control.type = 'text';
                        control.placeholder = 'Enter value';
                        control.value = value || '';
                        break;
                }

                control.className = baseClass;
                control.setAttribute('data-role', 'value');
                control.setAttribute('data-name', 'value');

                return control;
            };

            const addField = (field = {}) => {
                const node = template.content.firstElementChild.cloneNode(true);
                const nameSelect = node.querySelector('[data-role="name"]');
                const typeSelect = node.querySelector('[data-role="type"]');
                const labelInput = node.querySelector('[data-role="label"]');
                const idInput = node.querySelector('[data-role="id"]');
                const valueSlot = node.querySelector('[data-role="value-slot"]');
                const removeBtn = node.querySelector('[data-action="remove"]');
                const previousName = { value: null };

                const pickDefaultName = () => {
                    const used = new Set(selectedNames());
                    const order = ['title', 'description', 'application_start_date', 'application_end_date', 'event_time', 'max_applications', 'status', 'link', 'custom_note'];
                    const next = order.find((n) => !used.has(n));
                    return next || null;
                };

                const nameValue = field.name && fieldPresets[field.name] ? field.name : pickDefaultName();
                if (!nameValue) {
                    setDuplicateWarning('All available fields are already added.');
                    highlightExisting(selectedNames()[0] || 'title');
                    return;
                }
                nameSelect.value = nameValue;
                const preset = fieldPresets[nameValue] || { label: 'Detail', type: 'short_text' };
                previousName.value = nameValue;

                labelInput.value = field.label || preset.label;
                const safeType = typeCopy[field.type] ? field.type : preset.type;
                typeSelect.value = safeType;
                idInput.value = field.id || randomId();

                valueSlot.innerHTML = '';
                valueSlot.appendChild(buildValueControl(safeType, field.value, nameValue));

                nameSelect.addEventListener('change', function(e) {
                    const selected = e.target.value;
                    // prevent duplicates
                    if (selected !== previousName.value && selectedNames(nameSelect).includes(selected)) {
                        setDuplicateWarning(`Field "${fieldPresets[selected]?.label || selected}" is already added. Edit the existing one instead.`);
                        highlightExisting(selected);
                        nameSelect.value = previousName.value;
                        return;
                    }
                    setDuplicateWarning('');
                    previousName.value = selected;
                    const preset = fieldPresets[selected] || { label: 'Detail', type: 'short_text' };
                    labelInput.value = preset.label;
                    typeSelect.value = preset.type;
                    valueSlot.innerHTML = '';
                    valueSlot.appendChild(buildValueControl(preset.type, '', selected));
                    syncIndexes();
                });

                typeSelect.addEventListener('change', function(e) {
                    valueSlot.innerHTML = '';
                    valueSlot.appendChild(buildValueControl(e.target.value, '', nameSelect.value));
                    syncIndexes();
                });

                const advanced = node.querySelector('[data-role="advanced"]');
                const toggleAdvanced = node.querySelector('[data-action="toggle-advanced"]');

                if (toggleAdvanced && advanced) {
                    toggleAdvanced.addEventListener('click', function() {
                        const isHidden = advanced.classList.contains('hidden');
                        advanced.classList.toggle('hidden');
                        toggleAdvanced.textContent = isHidden ? 'Hide advanced' : 'More fields';
                    });
                }

                removeBtn.addEventListener('click', function() {
                    node.remove();
                    syncIndexes();
                    setDuplicateWarning('');
                });

                listEl.appendChild(node);
                syncIndexes();
            };

            const applyDefaultFields = () => {
                if (!Array.isArray(defaultFields) || defaultFields.length === 0) {
                    return;
                }
                listEl.innerHTML = '';
                defaultFields.forEach((field) => addField(field));
                setDuplicateWarning('');
                if (defaultHint) {
                    defaultHint.classList.remove('hidden');
                    defaultHint.textContent = 'Default form applied. You can still edit or remove fields.';
                }
                listEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
            };

            // Quick add common fields
            if (quickButtons.length) {
                quickButtons.forEach((btn) => {
                    btn.addEventListener('click', function() {
                        const name = btn.getAttribute('data-quick-field');
                        if (selectedNames().includes(name)) {
                            setDuplicateWarning(`Field "${fieldPresets[name]?.label || name}" is already added. Scroll to edit.`);
                            highlightExisting(name);
                            return;
                        }
                        const preset = fieldPresets[name] || { label: 'Detail', type: 'short_text' };
                        addField({ name, label: preset.label, type: preset.type });
                        setDuplicateWarning('');
                    });
                });
            }

            if (defaultButton) {
                defaultButton.addEventListener('click', applyDefaultFields);
            }

            // Seed builder
            if (Array.isArray(initialFields) && initialFields.length > 0) {
                initialFields.forEach((field) => addField(field));
            } else {
                addField({ name: 'title', label: '', type: 'short_text' });
            }

            addBtn.addEventListener('click', function() {
                addField({ name: 'title', label: '', type: 'short_text' });
            });
        });
    </script>
@endpush
