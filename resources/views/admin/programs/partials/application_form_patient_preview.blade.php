{{-- Reuses the patient application modal in preview-only mode. --}}
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.2.0/dist/signature_pad.umd.min.js"></script>
@include('patient.programs.partials.dynamic_application_modal')

@push('scripts')
<script>
(function() {
    const slugify = (v) => String(v || '').toLowerCase().trim().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');

    window.collectApplicationFormSchema = function(builderId) {
        const id = builderId || 'application-form-builder';
        const listEl = document.getElementById(id + '-list');
        if (!listEl) return [];

        return Array.from(listEl.querySelectorAll('[data-app-field]')).map((node) => {
            const label = node.querySelector('[data-role="label"]')?.value?.trim() || '';
            const nameRaw = node.querySelector('[data-role="name"]')?.value?.trim() || '';
            const name = slugify(nameRaw || label) || 'field';
            const optionsText = node.querySelector('[data-role="options"]')?.value || '';
            const options = optionsText.split(/\r?\n/).map((s) => s.trim()).filter(Boolean);
            const condField = node.querySelector('[data-role="conditional_field"]')?.value?.trim() || '';
            const condValue = node.querySelector('[data-role="conditional_value"]')?.value?.trim() || '';

            return {
                id: node.querySelector('[data-role="id"]')?.value || null,
                name,
                label: label || name,
                type: node.querySelector('[data-role="type"]')?.value || 'short_text',
                section: node.querySelector('[data-role="section"]')?.value?.trim() || '',
                required: !!node.querySelector('[data-role="required"]')?.checked,
                help_text: node.querySelector('[data-role="help_text"]')?.value?.trim() || '',
                options,
                maps_to_column: node.querySelector('[data-role="maps_to_column"]')?.value || null,
                conditional: (condField && condValue !== '')
                    ? { field: slugify(condField), value: condValue }
                    : null,
            };
        });
    };

    window.getDraftProgramTitle = function(fallback) {
        const rows = document.querySelectorAll('#program-field-builder-list [data-custom-field]');
        for (const row of rows) {
            if (row.querySelector('[data-role="name"]')?.value === 'title') {
                const valueEl = row.querySelector('[data-role="value-slot"] input, [data-role="value-slot"] textarea, [data-role="value"]');
                const val = valueEl?.value?.trim();
                if (val) return val;
            }
        }
        return fallback || 'Program Application';
    };

    window.previewDraftApplicationFormAsPatient = function(options) {
        const opts = options && typeof options === 'object' ? options : {};
        const schema = Array.isArray(opts.schema)
            ? opts.schema
            : window.collectApplicationFormSchema(opts.builderId || 'application-form-builder');

        if (!schema.length) {
            alert('Add application questions first to preview the patient form.');
            return;
        }

        previewApplicationFormAsPatient({
            id: opts.programId || '',
            title: opts.title || getDraftProgramTitle(opts.fallbackTitle || 'Program Application'),
            schema,
        });
    };
})();
</script>
@endpush
