<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramRegistration;
use App\Support\ApplicationFormFieldTypes;
use App\Support\ApplicationFormSchema;
use App\Support\ApplicationFormTemplates;
use App\Support\PatientApplicationNotifications;
use App\Support\ProgramDefaultTemplate;
use App\Support\ProgramType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProgramController extends Controller
{
    /**
     * Admin resource index — list lives on Programs & Events, not a separate patient view.
     */
    public function index()
    {
        return redirect()->route('admin.programs-events');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'username' => 'required|string|max:150',
            'phone' => 'required|string|max:20',
            'gender' => 'nullable|string|in:male,female,other',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
        ]);

        $validated['user_id'] = Auth::id();

        $program = Program::findOrFail($validated['program_id']);

        if (! $program->isApplicationOpen()) {
            return back()->with('error', 'Applications for this program are not open yet or have closed. Please check the application start and end dates.');
        }

        if ($program->hasReachedMaxApplications()) {
            return back()->with('error', \App\Support\ProgramApplicationCapacity::CLOSED_MESSAGE);
        }

        ProgramRegistration::create($validated);

        if ($program->max_applications) {
            $currentCount = ProgramRegistration::where('program_id', $program->id)->count();
            if ($currentCount >= $program->max_applications && $program->status !== 'completed') {
                $program->update(['status' => 'completed']);
            }
        }

        return back()->with('success', 'You have successfully registered for the program.');
    }

    public function show(Request $request, $id)
    {
        // Fetch the program; do not eager-load a nonexistent 'sponsor' relation
        $program = Program::findOrFail($id);

        $registration = null;
        if (Auth::check()) {
            $registration = ProgramRegistration::where('program_id', $program->id)
                ->where('user_id', Auth::id())
                ->select(['id', 'program_id', 'status', 'created_at', 'review_note'])
                ->first();
        }

        // Sponsor block removed from modal; keep payload empty
        $sponsorPayload = null;

        $registrationPayload = $registration ? [
            'id' => $registration->id,
            'status' => $registration->status,
            'status_label' => $registration->status_label,
            'submitted_at' => optional($registration->created_at)->format('d M Y, h:i A'),
            'view_url' => route('patient.programRegistrations.show', $registration),
            'review_note' => $registration->review_note,
        ] : null;

        if (! $request->expectsJson()) {
            if ($registration) {
                return redirect()->route('patient.programRegistrations.show', $registration);
            }

            return redirect()->route('patient.programsAndAids')
                ->with('info', 'You have not registered for this program yet.');
        }

        return response()->json([
            'title' => $program->title,
            'description' => $program->description,
            'event_date' => $program->event_date
                ? $program->event_date->format('l, F d, Y')
                : null,
            'event_time' => $program->event_time ? Carbon::parse($program->event_time)->format('H:i') : null,
            'banner' => $program->banner
                ? storage_url($program->banner)
                : asset('public/images/program-details.png'),
            'sponsor' => $sponsorPayload,
            'registration' => $registrationPayload,
            'custom_fields' => $program->custom_fields ?? [],
            'effective_status' => $program->effective_status,
            'effective_status_label' => $program->effective_status_label,
            'application_start_date' => $program->application_start_date?->format('d M Y'),
            'application_end_date' => $program->application_end_date?->format('d M Y'),
            'is_application_open' => $program->isApplicationOpen(),
            'is_at_capacity' => $program->hasReachedMaxApplications(),
            'is_accepting_applications' => $program->isAcceptingApplications(),
            'program_type' => $program->program_type ?? ProgramType::FINANCIAL_ASSISTANCE,
            'program_type_label' => $program->programTypeLabel(),
            'sponsor_name' => $program->sponsor_name,
            'sponsor_logo' => $program->sponsorLogoUrl(),
            'application_form_schema' => $program->resolvedApplicationFormSchema(),
            'has_dynamic_application_form' => $program->hasDynamicApplicationForm(),
        ]);
    }

    public function create()
    {
        $defaultProgram = Program::query()->orderByDesc('id')->first();

        if ($defaultProgram) {
            $defaultFields = $defaultProgram->custom_fields ?? [];
            $defaultProgramTitle = $defaultProgram->title;
        } else {
            $defaultFields = ProgramDefaultTemplate::customFields();
            $defaultProgramTitle = 'Recommended starter';
        }

        $usesStarterTemplate = $defaultProgram === null;
        $applicationFormTemplates = ApplicationFormTemplates::all();

        return view('admin.programs.create', compact(
            'defaultProgram',
            'defaultFields',
            'defaultProgramTitle',
            'usesStarterTemplate',
            'applicationFormTemplates',
        ));
    }

    public function store(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_date' => ['nullable', 'date'],
            'event_time' => ['nullable', 'date_format:H:i'],
            'application_start_date' => ['nullable', 'date'],
            'application_end_date' => ['nullable', 'date'],
            'max_applications' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'in:upcoming,ongoing,completed'],
            'program_type' => ['nullable', 'string', Rule::in(array_keys(ProgramType::options()))],
            'sponsor_name' => ['nullable', 'string', 'max:255'],
            'sponsor_logo' => ['nullable', 'image', 'max:'.(25 * 1024)],
            'banner' => ['nullable', 'image', 'max:'.(25 * 1024)],
            'custom_fields' => ['array'],
            'custom_fields.*.name' => ['required', 'string', 'max:60', Rule::in($this->allowedFieldNames())],
            'custom_fields.*.id' => ['nullable', 'string', 'max:60'],
            'custom_fields.*.label' => ['nullable', 'string', 'max:120'],
            'custom_fields.*.type' => ['required_with:custom_fields.*.name', Rule::in($this->customFieldTypes())],
            'custom_fields.*.value' => ['nullable', 'string', 'max:1000'],
            ...$this->applicationFormSchemaValidationRules(),
        ]);

        $validator->after(function ($validator) use ($r) {
            $fields = collect($r->input('custom_fields', []));

            $hasTitleField = $fields->contains(fn ($field) => isset($field['name']) && $field['name'] === 'title');
            $titleValue = $fields->first(function ($field) {
                return isset($field['name']) && $field['name'] === 'title'
                    && strlen(trim((string) ($field['value'] ?? ''))) > 0;
            });

            $inlineTitle = $this->stringValue($r->input('title'));
            $hasTitleValue = ($inlineTitle !== '') || (bool) $titleValue;

            $fieldValue = function (string $name) use ($fields, $r) {
                $direct = $r->input($name);
                if (! empty($direct)) {
                    return $direct;
                }
                $match = $fields->first(fn ($field) => ($field['name'] ?? null) === $name);

                return $match['value'] ?? null;
            };

            // Prevent duplicate field names
            $names = $fields->pluck('name')->filter()->map(fn ($n) => strtolower(trim($n)));
            $duplicateNames = $names->count() !== $names->unique()->count()
                ? $names->duplicates()->unique()->values()->all()
                : [];

            if (! $hasTitleField) {
                $validator->errors()->add('custom_fields', 'Please add a Title field.');
            }

            if (! $hasTitleValue) {
                $validator->errors()->add('title', 'Title is required. Please fill in the Title field.');
            }

            if (! empty($duplicateNames)) {
                $validator->errors()->add('custom_fields', 'Do not repeat the same field: '.implode(', ', $duplicateNames).'.');
            }

            $startDate = $fieldValue('application_start_date');
            $endDate = $fieldValue('application_end_date');
            if ($startDate && $endDate) {
                try {
                    $start = Carbon::parse($startDate);
                    $end = Carbon::parse($endDate);
                    if ($end->lt($start)) {
                        $validator->errors()->add('custom_fields', 'Application end date must be on or after the start date.');
                    }
                } catch (\Throwable $e) {
                    $validator->errors()->add('custom_fields', 'Application dates must be valid.');
                }
            }
        });

        $data = $validator->validate();

        if ($r->hasFile('banner')) {
            $data['banner'] = $r->file('banner')->store('programs', 'public');
        }

        if ($r->hasFile('sponsor_logo')) {
            $data['sponsor_logo'] = $r->file('sponsor_logo')->store('programs/sponsors', 'public');
        }

        $data['program_type'] = $r->input('program_type', ProgramType::FINANCIAL_ASSISTANCE);
        $data['sponsor_name'] = $r->input('sponsor_name');

        $data['custom_fields'] = $this->normalizeCustomFields($r->input('custom_fields', []));
        $data['application_form_schema'] = $this->normalizeApplicationFormSchema($r->input('application_form_schema', []));
        $data = $this->mergeDerivedDefaults($data);

        $program = Program::create($data);
        PatientApplicationNotifications::programCreatedForPatients($program);

        return redirect()->route('admin.programs-events')->with('success', 'Program created.');
    }

    public function edit(Program $program)
    {
        $program->loadCount('registrations');
        $applicationFormTemplates = ApplicationFormTemplates::all();

        return view('admin.programs.edit', compact('program', 'applicationFormTemplates'));
    }

    public function update(Request $r, Program $program)
    {
        $validator = Validator::make($r->all(), [
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_date' => ['nullable', 'date'],
            'event_time' => ['nullable', 'date_format:H:i'],
            'application_start_date' => ['nullable', 'date'],
            'application_end_date' => ['nullable', 'date'],
            'max_applications' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'in:upcoming,ongoing,completed'],
            'program_type' => ['nullable', 'string', Rule::in(array_keys(ProgramType::options()))],
            'sponsor_name' => ['nullable', 'string', 'max:255'],
            'sponsor_logo' => ['nullable', 'image', 'max:'.(25 * 1024)],
            'banner' => ['nullable', 'image', 'max:'.(25 * 1024)],
            'custom_fields' => ['array'],
            'custom_fields.*.name' => ['required', 'string', 'max:60', Rule::in($this->allowedFieldNames())],
            'custom_fields.*.id' => ['nullable', 'string', 'max:60'],
            'custom_fields.*.label' => ['nullable', 'string', 'max:120'],
            'custom_fields.*.type' => ['required_with:custom_fields.*.name', Rule::in($this->customFieldTypes())],
            'custom_fields.*.value' => ['nullable', 'string', 'max:1000'],
            ...$this->applicationFormSchemaValidationRules(),
        ]);

        $validator->after(function ($validator) use ($r) {
            $fields = collect($r->input('custom_fields', []));

            $hasTitleField = $fields->contains(fn ($field) => isset($field['name']) && $field['name'] === 'title');
            $titleValue = $fields->first(function ($field) {
                return isset($field['name']) && $field['name'] === 'title'
                    && strlen(trim((string) ($field['value'] ?? ''))) > 0;
            });

            $inlineTitle = $this->stringValue($r->input('title'));
            $hasTitleValue = ($inlineTitle !== '') || (bool) $titleValue;

            $fieldValue = function (string $name) use ($fields, $r) {
                $direct = $r->input($name);
                if (! empty($direct)) {
                    return $direct;
                }
                $match = $fields->first(fn ($field) => ($field['name'] ?? null) === $name);

                return $match['value'] ?? null;
            };

            // Prevent duplicate field names
            $names = $fields->pluck('name')->filter()->map(fn ($n) => strtolower(trim($n)));
            $duplicateNames = $names->count() !== $names->unique()->count()
                ? $names->duplicates()->unique()->values()->all()
                : [];

            if (! $hasTitleField) {
                $validator->errors()->add('custom_fields', 'Please add a Title field.');
            }

            if (! $hasTitleValue) {
                $validator->errors()->add('title', 'Title is required. Please fill in the Title field.');
            }

            if (! empty($duplicateNames)) {
                $validator->errors()->add('custom_fields', 'Do not repeat the same field: '.implode(', ', $duplicateNames).'.');
            }

            $startDate = $fieldValue('application_start_date');
            $endDate = $fieldValue('application_end_date');
            if ($startDate && $endDate) {
                try {
                    $start = Carbon::parse($startDate);
                    $end = Carbon::parse($endDate);
                    if ($end->lt($start)) {
                        $validator->errors()->add('custom_fields', 'Application end date must be on or after the start date.');
                    }
                } catch (\Throwable $e) {
                    $validator->errors()->add('custom_fields', 'Application dates must be valid.');
                }
            }
        });

        $data = $validator->validate();

        if ($r->hasFile('banner')) {
            $data['banner'] = $r->file('banner')->store('programs', 'public');
        } else {
            unset($data['banner']);
        }

        if ($r->hasFile('sponsor_logo')) {
            $data['sponsor_logo'] = $r->file('sponsor_logo')->store('programs/sponsors', 'public');
        } else {
            unset($data['sponsor_logo']);
        }

        $data['program_type'] = $r->input('program_type', $program->program_type ?? ProgramType::FINANCIAL_ASSISTANCE);
        $data['sponsor_name'] = $r->input('sponsor_name');

        $data['custom_fields'] = $this->normalizeCustomFields($r->input('custom_fields', []));
        $data['application_form_schema'] = $this->normalizeApplicationFormSchema($r->input('application_form_schema', []));
        $data = $this->mergeDerivedDefaults($data, $program);

        $program->update($data);

        return redirect()->route('admin.programs-events')->with('success', 'Program updated.');
    }

    public function destroy(Program $program)
    {
        $title = $program->title ?? 'Program';
        $registrationCount = $program->registrations()->count();

        foreach (['banner', 'sponsor_logo'] as $pathColumn) {
            $path = $program->{$pathColumn};
            if (filled($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $program->delete();

        $message = 'Program "'.$title.'" was deleted.';
        if ($registrationCount > 0) {
            $message .= ' '.$registrationCount.' linked application(s) were removed as well.';
        }

        return redirect()->route('admin.programs-events')->with('success', $message);
    }

    /**
     * Duplicate a program with a new application / event schedule.
     * Copies listing fields, application form, type, sponsor, and media from the source.
     */
    public function duplicate(Request $request, Program $program)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'event_date' => ['nullable', 'date'],
            'event_time' => ['nullable', 'date_format:H:i'],
            'application_start_date' => ['nullable', 'date'],
            'application_end_date' => ['nullable', 'date', 'after_or_equal:application_start_date'],
            'status' => ['nullable', 'in:upcoming,ongoing,completed'],
        ]);

        $eventDate = $this->normalizeDate($validated['event_date'] ?? optional($program->event_date)->format('Y-m-d') ?? now()->toDateString());
        $eventTime = $this->normalizeTime(
            $validated['event_time']
                ?? ($program->event_time ? Carbon::parse($program->event_time)->format('H:i') : '09:00')
        );
        $applicationStart = $this->normalizeNullableDate(
            $validated['application_start_date'] ?? optional($program->application_start_date)->format('Y-m-d')
        );
        $applicationEnd = $this->normalizeNullableDate(
            $validated['application_end_date'] ?? optional($program->application_end_date)->format('Y-m-d')
        );
        $status = $validated['status'] ?? ($program->status ?: 'upcoming');
        $title = trim($validated['title']);
        $eventTimeDisplay = Carbon::parse($eventTime)->format('H:i');

        $scheduleOverrides = [
            'title' => $title,
            'event_date' => $eventDate,
            'event_time' => $eventTimeDisplay,
            'application_start_date' => $applicationStart,
            'application_end_date' => $applicationEnd,
            'status' => $status,
            'description' => $program->description,
            'max_applications' => $program->max_applications !== null ? (string) $program->max_applications : '',
        ];

        $customFields = $this->duplicateListingFields($program, $scheduleOverrides);
        $applicationSchema = $this->duplicateApplicationFormSchema($program);

        $duplicate = $program->replicate([
            'banner',
            'sponsor_logo',
        ]);

        $duplicate->fill([
            'title' => $title,
            'description' => $program->description,
            'event_date' => $eventDate,
            'event_time' => $eventTime,
            'application_start_date' => $applicationStart,
            'application_end_date' => $applicationEnd,
            'status' => $status,
            'program_type' => $program->program_type,
            'sponsor_name' => $program->sponsor_name,
            'max_applications' => $program->max_applications,
            'custom_fields' => $customFields,
            'application_form_schema' => $applicationSchema,
            'banner' => $this->copyPublicStorageFile($program->banner, 'programs'),
            'sponsor_logo' => $this->copyPublicStorageFile($program->sponsor_logo, 'programs/sponsors'),
        ]);
        $duplicate->save();

        PatientApplicationNotifications::programCreatedForPatients($duplicate);

        return redirect()
            ->route('admin.programs-events');
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return list<array<string, mixed>>
     */
    private function duplicateListingFields(Program $program, array $overrides): array
    {
        $fields = is_array($program->custom_fields) ? $program->custom_fields : [];

        if ($fields === []) {
            $fields = ProgramDefaultTemplate::customFields();
        }

        $fields = array_map(function ($field) {
            if (! is_array($field)) {
                return $field;
            }
            $field['id'] = (string) Str::uuid();

            return $field;
        }, $fields);

        return $this->syncCustomFieldValues($fields, $overrides);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function duplicateApplicationFormSchema(Program $program): array
    {
        $schema = is_array($program->application_form_schema) ? $program->application_form_schema : [];

        if ($schema === [] && ProgramType::usesDynamicApplicationForm($program->program_type)) {
            $schema = ApplicationFormTemplates::forType($program->program_type);
        }

        return array_values(array_map(function ($field) {
            if (! is_array($field)) {
                return $field;
            }
            $field['id'] = (string) Str::uuid();

            return $field;
        }, $schema));
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     * @param  array<string, mixed>  $values
     * @return array<int, array<string, mixed>>
     */
    private function syncCustomFieldValues(array $fields, array $values): array
    {
        $present = [];
        foreach ($fields as &$field) {
            $name = $field['name'] ?? null;
            if ($name !== null && array_key_exists($name, $values)) {
                $field['value'] = $values[$name];
                $present[$name] = true;
            }
        }
        unset($field);

        // Ensure core listing keys exist so the edit form shows the same fields as the source program.
        $labels = [
            'title' => 'Title',
            'description' => 'Description',
            'event_date' => 'Program date',
            'application_start_date' => 'Application Start Date',
            'application_end_date' => 'Application End Date',
            'event_time' => 'Time',
            'status' => 'Status',
            'max_applications' => 'Maximum Applications',
        ];
        $types = [
            'title' => 'short_text',
            'description' => 'long_text',
            'event_date' => 'date',
            'application_start_date' => 'date',
            'application_end_date' => 'date',
            'event_time' => 'time',
            'status' => 'short_text',
            'max_applications' => 'number',
        ];

        foreach ($labels as $name => $label) {
            if (isset($present[$name]) || ! array_key_exists($name, $values)) {
                continue;
            }
            $fields[] = [
                'id' => (string) Str::uuid(),
                'name' => $name,
                'label' => $label,
                'type' => $types[$name] ?? 'short_text',
                'value' => $values[$name],
                'required' => false,
            ];
        }

        return array_values($fields);
    }

    private function copyPublicStorageFile(?string $path, string $directory): ?string
    {
        if (! filled($path) || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $destination = trim($directory, '/').'/'.Str::uuid().($extension !== '' ? '.'.$extension : '');

        Storage::disk('public')->copy($path, $destination);

        return $destination;
    }

    /**
     * @return array<string, mixed>
     */
    private function applicationFormSchemaValidationRules(): array
    {
        return [
            'application_form_schema' => ['nullable', 'array'],
            'application_form_schema.*.id' => ['nullable', 'string', 'max:60'],
            'application_form_schema.*.name' => ['required_with:application_form_schema', 'string', 'max:80'],
            'application_form_schema.*.label' => ['nullable', 'string', 'max:255'],
            'application_form_schema.*.type' => ['required_with:application_form_schema.*.name', Rule::in(array_keys(ApplicationFormFieldTypes::options()))],
            'application_form_schema.*.section' => ['nullable', 'string', 'max:120'],
            'application_form_schema.*.required' => ['nullable', 'boolean'],
            'application_form_schema.*.help_text' => ['nullable', 'string', 'max:500'],
            'application_form_schema.*.options' => ['nullable'],
            'application_form_schema.*.maps_to_column' => ['nullable', 'string', Rule::in(ApplicationFormSchema::MAPPABLE_COLUMNS)],
            'application_form_schema.*.conditional_field' => ['nullable', 'string', 'max:80'],
            'application_form_schema.*.conditional_value' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @param  array<int, mixed>  $rawFields
     * @return list<array<string, mixed>>
     */
    private function normalizeApplicationFormSchema(array $rawFields): array
    {
        return ApplicationFormSchema::normalize($rawFields);
    }

    /**
     * Allowed custom field types for programs.
     */
    private function customFieldTypes(): array
    {
        return ['short_text', 'long_text', 'number', 'money', 'date', 'time', 'link', 'boolean'];
    }

    /**
     * Predefined field names mapped from the previous static form.
     */
    private function allowedFieldNames(): array
    {
        return [
            'title',
            'description',
            'event_date',
            'event_time',
            'application_start_date',
            'application_end_date',
            'max_applications',
            'status',
            'custom_note',
            'link',
        ];
    }

    /**
     * Normalize incoming custom fields to a consistent, safe structure.
     */
    private function normalizeCustomFields(array $rawFields): array
    {
        $allowedTypes = $this->customFieldTypes();
        $allowedNames = $this->allowedFieldNames();

        return collect($rawFields)
            ->map(function ($field) use ($allowedTypes, $allowedNames) {
                $name = $field['name'] ?? null;
                if (! $name || ! in_array($name, $allowedNames, true)) {
                    return null;
                }

                $label = trim($field['label'] ?? '');

                $type = $field['type'] ?? 'short_text';
                if (! in_array($type, $allowedTypes, true)) {
                    $type = 'short_text';
                }

                $value = $field['value'] ?? '';
                if ($type === 'boolean') {
                    $value = filter_var($value, FILTER_VALIDATE_BOOL);
                } else {
                    $value = is_scalar($value) ? trim((string) $value) : '';
                }

                return [
                    'id' => $field['id'] ?? 'cf_'.Str::random(8),
                    'name' => $name,
                    'label' => $label,
                    'type' => $type,
                    'value' => $value,
                    'required' => (bool) ($field['required'] ?? false),
                ];
            })
            ->filter() // remove null or disallowed names
            ->values()
            ->all();
    }

    /**
     * Derive required base columns from custom fields so admins can rely on a fully dynamic form.
     */
    private function mergeDerivedDefaults(array $data, ?Program $existing = null): array
    {
        $fields = $data['custom_fields'] ?? [];

        $title = $data['title'] ?? $existing?->title;
        $description = $data['description'] ?? $existing?->description;
        $eventDate = $data['event_date'] ?? optional($existing?->event_date)->format('Y-m-d');
        $eventTime = $data['event_time'] ?? ($existing?->event_time ? \Carbon\Carbon::parse($existing->event_time)->format('H:i') : null);
        $applicationStartDate = $data['application_start_date'] ?? optional($existing?->application_start_date)->format('Y-m-d');
        $applicationEndDate = $data['application_end_date'] ?? optional($existing?->application_end_date)->format('Y-m-d');
        $maxApplications = $data['max_applications'] ?? $existing?->max_applications;
        $status = $data['status'] ?? $existing?->status ?? 'upcoming';

        foreach ($fields as &$field) {
            $name = $field['name'] ?? null;
            $type = $field['type'] ?? 'short_text';
            $value = $field['value'] ?? null;

            // Ensure a readable label even if not provided
            if (empty($field['label'])) {
                $field['label'] = $this->defaultLabelForName($name);
            }

            switch ($name) {
                case 'title':
                    $title = $this->stringValue($value) ?: $title;
                    break;
                case 'description':
                    $description = $this->stringValue($value) ?: $description;
                    break;
                case 'event_date':
                    $eventDate = $value ?: $eventDate;
                    break;
                case 'event_time':
                    $eventTime = $value ?: $eventTime;
                    break;
                case 'application_start_date':
                    $applicationStartDate = $value ?: $applicationStartDate;
                    break;
                case 'application_end_date':
                    $applicationEndDate = $value ?: $applicationEndDate;
                    break;
                case 'max_applications':
                    if ($value !== null && $value !== '') {
                        $maxApplications = is_numeric($value) ? (int) $value : $maxApplications;
                    }
                    break;
                case 'status':
                    $candidate = strtolower($this->stringValue($value));
                    if (in_array($candidate, ['upcoming', 'ongoing', 'completed'], true)) {
                        $status = $candidate;
                    }
                    break;
                default:
                    // leave as-is for custom display
                    break;
            }
        }
        unset($field);

        $data['title'] = $title ?: 'Untitled Program';
        $data['description'] = $description ?: 'Details will be shared soon.';
        $data['event_date'] = $this->normalizeDate($eventDate);
        $data['event_time'] = $this->normalizeTime($eventTime);
        $data['application_start_date'] = $this->normalizeNullableDate($applicationStartDate);
        $data['application_end_date'] = $this->normalizeNullableDate($applicationEndDate);
        $data['max_applications'] = $maxApplications !== null ? (int) $maxApplications : null;
        $data['status'] = $status ?: 'upcoming';
        $data['custom_fields'] = array_values($fields);

        return $data;
    }

    private function stringValue($value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }

    private function normalizeDate($value): string
    {
        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable $e) {
            return Carbon::now()->toDateString();
        }
    }

    private function normalizeNullableDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function normalizeTime($value): string
    {
        try {
            return Carbon::parse($value)->format('H:i:s');
        } catch (\Throwable $e) {
            return '09:00:00';
        }
    }

    private function defaultLabelForName(?string $name): string
    {
        return match ($name) {
            'title' => 'Title',
            'description' => 'Description',
            'event_date' => 'Program date',
            'event_time' => 'Time',
            'application_start_date' => 'Application Start Date',
            'application_end_date' => 'Application End Date',
            'max_applications' => 'Maximum Applications',
            'status' => 'Status',
            'custom_note' => 'Note',
            'link' => 'Link',
            default => 'Detail',
        };
    }
}
