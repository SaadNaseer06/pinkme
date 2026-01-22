<?php

namespace App\Http\Controllers;

use App\Models\ProgramRegistration;
use App\Models\SponsorshipProgram;
use Illuminate\Support\Facades\Auth;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ProgramController extends Controller
{
    public function index()
    {
        $upcomingPrograms = Program::where('status', 'upcoming')->get();
        $ongoingPrograms = Program::where('status', 'ongoing')->get();

        return view('patient.programs.index', compact('upcomingPrograms', 'ongoingPrograms'));
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
        if ($program->max_applications) {
            $currentCount = ProgramRegistration::where('program_id', $program->id)->count();
            if ($currentCount >= $program->max_applications) {
                return back()->with('error', 'Applications for this program are closed.');
            }
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

        if (!$request->expectsJson()) {
            if ($registration) {
                return redirect()->route('patient.programRegistrations.show', $registration);
            }

            return redirect()->route('patient.programsAndAids')
                ->with('info', 'You have not registered for this program yet.');
        }

        return response()->json([
            'title' => $program->title,
            'description' => $program->description,
            'event_date' => Carbon::parse($program->event_date)->format('l, F d, Y'),
            'event_time' => $program->event_time ? Carbon::parse($program->event_time)->format('H:i') : null,
            'banner' => $program->banner
                ? asset('storage/' . ltrim($program->banner, '/'))
                : asset('public/images/program-details.png'),
            'sponsor' => $sponsorPayload,
            'registration' => $registrationPayload,
            'custom_fields' => $program->custom_fields ?? [],
        ]);
    }

    public function create()
    {
        $defaultProgram = Program::orderByDesc('id')->first();
        $defaultFields = $defaultProgram?->custom_fields ?? [];

        return view('admin.programs.create', compact('defaultProgram', 'defaultFields'));
    }

    public function store(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'title'       => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_date'  => ['nullable', 'date'],
            'event_time'  => ['nullable', 'date_format:H:i'],
            'application_start_date' => ['nullable', 'date'],
            'application_end_date' => ['nullable', 'date'],
            'max_applications' => ['nullable', 'integer', 'min:1'],
            'status'      => ['nullable', 'in:upcoming,ongoing,completed'],
            'banner'      => ['nullable', 'image', 'max:2048'],
            'custom_fields' => ['array'],
            'custom_fields.*.name' => ['required', 'string', 'max:60', Rule::in($this->allowedFieldNames())],
            'custom_fields.*.id' => ['nullable', 'string', 'max:60'],
            'custom_fields.*.label' => ['nullable', 'string', 'max:120'],
            'custom_fields.*.type' => ['required_with:custom_fields.*.name', Rule::in($this->customFieldTypes())],
            'custom_fields.*.value' => ['nullable', 'string', 'max:1000'],
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
                if (!empty($direct)) {
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

            if (!$hasTitleField) {
                $validator->errors()->add('custom_fields', 'Please add a Title field.');
            }

            if (!$hasTitleValue) {
                $validator->errors()->add('title', 'Title is required. Please fill in the Title field.');
            }

            if (!empty($duplicateNames)) {
                $validator->errors()->add('custom_fields', 'Do not repeat the same field: ' . implode(', ', $duplicateNames) . '.');
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

        $data['custom_fields'] = $this->normalizeCustomFields($r->input('custom_fields', []));
        $data = $this->mergeDerivedDefaults($data);

        Program::create($data);

        return redirect()->route('admin.programs-events')->with('success', 'Program created.');
    }

    public function edit(Program $program)
    {
        // Render the edit form
        return view('admin.programs.edit', compact('program'));
    }

    public function update(Request $r, Program $program)
    {
        $validator = Validator::make($r->all(), [
            'title'       => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_date'  => ['nullable', 'date'],
            'event_time'  => ['nullable', 'date_format:H:i'],
            'application_start_date' => ['nullable', 'date'],
            'application_end_date' => ['nullable', 'date'],
            'max_applications' => ['nullable', 'integer', 'min:1'],
            'status'      => ['nullable', 'in:upcoming,ongoing,completed'],
            'banner'      => ['nullable', 'image', 'max:2048'],
            'custom_fields' => ['array'],
            'custom_fields.*.name' => ['required', 'string', 'max:60', Rule::in($this->allowedFieldNames())],
            'custom_fields.*.id' => ['nullable', 'string', 'max:60'],
            'custom_fields.*.label' => ['nullable', 'string', 'max:120'],
            'custom_fields.*.type' => ['required_with:custom_fields.*.name', Rule::in($this->customFieldTypes())],
            'custom_fields.*.value' => ['nullable', 'string', 'max:1000'],
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
                if (!empty($direct)) {
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

            if (!$hasTitleField) {
                $validator->errors()->add('custom_fields', 'Please add a Title field.');
            }

            if (!$hasTitleValue) {
                $validator->errors()->add('title', 'Title is required. Please fill in the Title field.');
            }

            if (!empty($duplicateNames)) {
                $validator->errors()->add('custom_fields', 'Do not repeat the same field: ' . implode(', ', $duplicateNames) . '.');
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
            // Keep existing banner if not replaced
            unset($data['banner']);
        }

        $data['custom_fields'] = $this->normalizeCustomFields($r->input('custom_fields', []));
        $data = $this->mergeDerivedDefaults($data, $program);

        $program->update($data);

        return redirect()->route('admin.programs-events')->with('success', 'Program updated.');
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
                if (!$name || !in_array($name, $allowedNames, true)) {
                    return null;
                }

                $label = trim($field['label'] ?? '');

                $type = $field['type'] ?? 'short_text';
                if (!in_array($type, $allowedTypes, true)) {
                    $type = 'short_text';
                }

                $value = $field['value'] ?? '';
                if ($type === 'boolean') {
                    $value = filter_var($value, FILTER_VALIDATE_BOOL);
                } else {
                    $value = is_scalar($value) ? trim((string) $value) : '';
                }

                return [
                    'id' => $field['id'] ?? 'cf_' . Str::random(8),
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
            'event_date' => 'Date',
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
