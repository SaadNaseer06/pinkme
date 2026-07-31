<div id="dynamicPopupModal" class="fixed inset-0 z-50 hidden flex items-start sm:items-center justify-center bg-black/60 px-4 py-6 overflow-y-auto">
    <div class="bg-[#F3E8EF] p-6 rounded-lg w-full max-w-4xl min-w-0 relative overflow-y-auto max-h-[90vh] shadow-xl border border-[#DCCFD8]">
        <button type="button" onclick="closeDynamicApplicationModal()"
            class="absolute top-4 right-4 text-[#91848C] hover:text-black text-2xl font-bold">&times;</button>

        <h2 id="dynamic-modal-title" class="text-lg font-medium text-black app-main mb-2">Program Application</h2>
        <p id="dynamic-modal-subtitle" class="text-sm text-[#91848C] mb-4"></p>

        <form id="dynamic-application-form" action="{{ route('program.register') }}" method="POST" enctype="multipart/form-data" class="space-y-6 min-w-0">
            @csrf
            <input type="hidden" name="program_id" id="dynamic_program_id" value="">
            <div id="dynamic-application-fields" class="space-y-6"></div>

            <div class="flex flex-wrap gap-3 justify-end border-t border-[#DCCFD8] pt-4">
                <button type="button" onclick="closeDynamicApplicationModal()"
                    class="rounded-lg border border-[#213430] px-5 py-2 text-sm font-medium text-[#213430] hover:bg-white">Cancel</button>
                <button type="submit"
                    class="rounded-lg bg-pink px-6 py-2 text-sm font-semibold text-white hover:bg-[#9E2469]">Submit Application</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const fieldsContainer = document.getElementById('dynamic-application-fields');
    const modal = document.getElementById('dynamicPopupModal');
    const form = document.getElementById('dynamic-application-form');
    let currentSchema = [];
    let signaturePads = {};

    const inputClass = 'w-full rounded-lg border border-[#DCCFD8] bg-white px-3 py-2 text-sm text-[#213430] focus:border-[#9E2469] focus:ring-1 focus:ring-[#9E2469]';
    const labelClass = 'block text-sm font-medium text-[#213430] mb-1';

    const slugMatch = (a, b) => String(a || '').toLowerCase().trim() === String(b || '').toLowerCase().trim();

    const getFieldValues = () => {
        const values = {};
        if (!form) return values;
        form.querySelectorAll('[data-dynamic-field]').forEach((el) => {
            const name = el.getAttribute('data-dynamic-field');
            if (!name) return;
            if (el.type === 'checkbox' && !el.name.endsWith('[]')) {
                values[name] = el.checked ? (el.value || 'Yes') : 'No';
            } else if (el.type === 'radio') {
                if (el.checked) values[name] = el.value;
            } else if (el.type === 'file') {
                // files handled separately
            } else if (!values[name]) {
                values[name] = el.value;
            }
        });
        form.querySelectorAll('input[type="checkbox"][name$="[]"]').forEach((el) => {
            const match = el.name.match(/app_field\[([^\]]+)\]/);
            if (!match) return;
            const name = match[1];
            if (!values[name]) values[name] = [];
            if (el.checked) values[name].push(el.value);
        });
        return values;
    };

    const isFieldVisible = (field, values) => {
        const cond = field.conditional;
        if (!cond || !cond.field) return true;
        const parent = values[cond.field];
        if (Array.isArray(parent)) return parent.map(String).some((v) => slugMatch(v, cond.value));
        return slugMatch(parent, cond.value);
    };

    const syncConditionalVisibility = () => {
        const values = getFieldValues();
        form.querySelectorAll('[data-conditional-field]').forEach((wrap) => {
            const fieldName = wrap.getAttribute('data-conditional-field');
            const field = currentSchema.find((f) => f.name === fieldName);
            if (!field) return;
            const visible = isFieldVisible(field, values);
            wrap.classList.toggle('hidden', !visible);
            wrap.querySelectorAll('input, select, textarea').forEach((el) => {
                if (visible) {
                    el.disabled = false;
                    if (field.required && el.type !== 'hidden') {
                        el.required = true;
                    }
                } else {
                    el.required = false;
                    el.disabled = true;
                    if (el.type === 'file') {
                        el.value = '';
                    } else if (el.type === 'checkbox' || el.type === 'radio') {
                        el.checked = false;
                    } else if (el.type !== 'hidden') {
                        el.value = '';
                    }
                }
            });
        });
    };

    const buildInput = (field) => {
        const type = field.type;
        const name = field.name;
        const required = !!field.required;
        const req = required ? 'required' : '';

        if (type === 'section_header') {
            const h = document.createElement('h3');
            h.className = 'text-md font-semibold text-[#213430] app-main border-b border-[#DCCFD8] pb-2';
            h.textContent = field.label || 'Section';
            return h;
        }

        if (type === 'guidelines') {
            const p = document.createElement('p');
            p.className = 'text-sm text-[#6C5B68] whitespace-pre-line rounded-lg border border-[#EADFF0] bg-[#FDF7FB] p-3';
            p.textContent = field.label || '';
            return p;
        }

        const wrap = document.createElement('div');
        wrap.className = 'space-y-1';
        if (field.conditional) {
            wrap.setAttribute('data-conditional-field', name);
        }

        const label = document.createElement('label');
        label.className = labelClass;
        label.textContent = (field.label || name) + (required ? ' *' : '');
        wrap.appendChild(label);

        if (field.help_text) {
            const help = document.createElement('p');
            help.className = 'text-xs text-[#91848C]';
            help.textContent = field.help_text;
            wrap.appendChild(help);
        }

        let control;
        const baseName = `app_field[${name}]`;

        switch (type) {
            case 'long_text':
                control = document.createElement('textarea');
                control.rows = 4;
                control.className = inputClass;
                control.name = baseName;
                if (required) control.required = true;
                control.setAttribute('data-dynamic-field', name);
                break;
            case 'select':
                control = document.createElement('select');
                control.className = inputClass;
                control.name = baseName;
                if (required) control.required = true;
                control.setAttribute('data-dynamic-field', name);
                (field.options || []).forEach((opt) => {
                    const o = document.createElement('option');
                    o.value = opt.value || opt.label || opt;
                    o.textContent = opt.label || opt.value || opt;
                    control.appendChild(o);
                });
                break;
            case 'radio':
                control = document.createElement('div');
                control.className = 'space-y-2';
                (field.options || []).forEach((opt, i) => {
                    const val = opt.value || opt.label || opt;
                    const id = `dyn_${name}_${i}`;
                    const row = document.createElement('label');
                    row.className = 'flex items-center gap-2 text-sm';
                    row.innerHTML = `<input type="radio" name="${baseName}" value="${val}" id="${id}" class="text-[#9E2469]" ${req} data-dynamic-field="${name}"><span>${opt.label || val}</span>`;
                    control.appendChild(row);
                });
                break;
            case 'checkbox':
                control = document.createElement('label');
                control.className = 'flex items-start gap-2 text-sm';
                control.innerHTML = `<input type="checkbox" name="${baseName}" value="1" class="mt-1 text-[#9E2469]" ${req} data-dynamic-field="${name}"><span>${field.label || ''}</span>`;
                wrap.innerHTML = '';
                wrap.appendChild(control);
                return wrap;
            case 'checkbox_group':
                control = document.createElement('div');
                control.className = 'space-y-2';
                (field.options || []).forEach((opt, i) => {
                    const val = opt.value || opt.label || opt;
                    const row = document.createElement('label');
                    row.className = 'flex items-center gap-2 text-sm';
                    row.innerHTML = `<input type="checkbox" name="app_field[${name}][]" value="${val}" class="text-[#9E2469]"><span>${opt.label || val}</span>`;
                    control.appendChild(row);
                });
                break;
            case 'file':
                control = document.createElement('input');
                control.type = 'file';
                control.accept = '.pdf,.jpg,.jpeg,.png';
                control.className = inputClass;
                control.name = baseName;
                control.setAttribute('data-dynamic-field', name);
                if (required) control.required = true;
                break;
            case 'date':
                control = document.createElement('input');
                control.type = 'date';
                control.className = inputClass;
                control.name = baseName;
                if (required) control.required = true;
                control.setAttribute('data-dynamic-field', name);
                break;
            case 'email':
                control = document.createElement('input');
                control.type = 'email';
                control.className = inputClass;
                control.name = baseName;
                if (required) control.required = true;
                control.setAttribute('data-dynamic-field', name);
                break;
            case 'phone':
            case 'number':
            case 'short_text':
            default:
                if (type === 'signature') {
                    const sigWrap = document.createElement('div');
                    sigWrap.className = 'space-y-2';
                    const canvas = document.createElement('canvas');
                    canvas.width = 500;
                    canvas.height = 120;
                    canvas.className = 'w-full max-w-full border border-[#DCCFD8] rounded-lg bg-white touch-none';
                    canvas.id = `sig_canvas_${name}`;
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = baseName;
                    hidden.id = `sig_input_${name}`;
                    if (required) hidden.required = true;
                    const clearBtn = document.createElement('button');
                    clearBtn.type = 'button';
                    clearBtn.className = 'text-xs text-[#9E2469] font-semibold';
                    clearBtn.textContent = 'Clear signature';
                    clearBtn.addEventListener('click', () => {
                        const pad = signaturePads[name];
                        if (pad) pad.clear();
                        hidden.value = '';
                    });
                    sigWrap.appendChild(canvas);
                    sigWrap.appendChild(clearBtn);
                    sigWrap.appendChild(hidden);
                    wrap.appendChild(sigWrap);
                    setTimeout(() => initDynamicSignature(name, canvas, hidden), 50);
                    return wrap;
                }
                control = document.createElement('input');
                control.type = type === 'number' ? 'number' : 'text';
                control.className = inputClass;
                control.name = baseName;
                if (required) control.required = true;
                control.setAttribute('data-dynamic-field', name);
                break;
        }

        wrap.appendChild(control);
        return wrap;
    };

    const initDynamicSignature = (name, canvas, hidden) => {
        if (typeof SignaturePad === 'undefined') return;
        const pad = new SignaturePad(canvas, { backgroundColor: 'rgb(255,255,255)' });
        signaturePads[name] = pad;
        const resize = () => {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = 120 * ratio;
            canvas.getContext('2d').scale(ratio, ratio);
            pad.clear();
        };
        resize();
        pad.addEventListener('endStroke', () => {
            if (!pad.isEmpty()) hidden.value = pad.toDataURL('image/png');
        });
    };

    window.renderDynamicApplicationForm = function(schema, programTitle) {
        currentSchema = Array.isArray(schema) ? schema : [];
        signaturePads = {};
        if (!fieldsContainer) return;

        fieldsContainer.innerHTML = '';
        let currentSection = '';

        currentSchema.forEach((field) => {
            if (field.section && field.section !== currentSection && field.type !== 'section_header') {
                currentSection = field.section;
                const sectionEl = document.createElement('h3');
                sectionEl.className = 'text-md font-semibold text-[#213430] app-main border-b border-[#DCCFD8] pb-2 mt-2';
                sectionEl.textContent = currentSection;
                fieldsContainer.appendChild(sectionEl);
            }
            fieldsContainer.appendChild(buildInput(field));
        });

        form.querySelectorAll('[data-dynamic-field], input[type="checkbox"], input[type="radio"]').forEach((el) => {
            el.addEventListener('change', syncConditionalVisibility);
            el.addEventListener('input', syncConditionalVisibility);
        });
        syncConditionalVisibility();

        const titleEl = document.getElementById('dynamic-modal-title');
        const subtitleEl = document.getElementById('dynamic-modal-subtitle');
        if (titleEl) titleEl.textContent = programTitle || 'Program Application';
        if (subtitleEl) subtitleEl.textContent = 'Please complete all required fields marked with *.';
    };

    window.openDynamicApplicationModal = function(programId, programTitle, schema) {
        document.getElementById('dynamic_program_id').value = programId;
        document.getElementById('registerModal')?.classList.add('hidden');
        document.getElementById('popupModal')?.classList.add('hidden');
        renderDynamicApplicationForm(schema, programTitle);
        modal?.classList.remove('hidden');
    };

    window.closeDynamicApplicationModal = function() {
        modal?.classList.add('hidden');
    };
})();
</script>
@endpush
