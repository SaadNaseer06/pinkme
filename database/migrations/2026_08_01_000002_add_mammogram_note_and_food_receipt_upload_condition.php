<?php

use App\Support\ApplicationFormFieldTypes;
use App\Support\ProgramType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $schedulingNote = [
            'id' => (string) Str::uuid(),
            'name' => 'scheduling_note',
            'label' => "Note:\nOnce your application has been approved by PINK \"ME\", X-Ray Associates of New Mexico will\ncoordinate the scheduling of the appointment and provide the patient with the imaging location.",
            'type' => ApplicationFormFieldTypes::GUIDELINES,
            'section' => '',
            'required' => false,
            'help_text' => '',
            'options' => [],
            'maps_to_column' => null,
            'conditional' => null,
        ];

        $this->updateMammogramPrograms($schedulingNote);
        $this->updateFoodAssistancePrograms();
    }

    /**
     * @param  array<string, mixed>  $schedulingNote
     */
    private function updateMammogramPrograms(array $schedulingNote): void
    {
        $programs = DB::table('programs')
            ->where('program_type', ProgramType::MAMMOGRAM_IMAGING)
            ->whereNotNull('application_form_schema')
            ->get(['id', 'application_form_schema']);

        foreach ($programs as $program) {
            $schema = json_decode((string) $program->application_form_schema, true);
            if (! is_array($schema) || $schema === []) {
                continue;
            }

            $changed = false;
            $hasNote = collect($schema)->contains(
                fn ($field) => is_array($field) && ($field['name'] ?? '') === 'scheduling_note'
            );

            if (! $hasNote) {
                $insertAt = null;
                foreach ($schema as $index => $field) {
                    if (! is_array($field)) {
                        continue;
                    }
                    if (($field['name'] ?? '') === 'ack_accurate_info') {
                        $insertAt = $index + 1;
                        break;
                    }
                }

                if ($insertAt !== null) {
                    array_splice($schema, $insertAt, 0, [$schedulingNote]);
                } else {
                    $schema[] = $schedulingNote;
                }
                $changed = true;
            }

            if ($changed) {
                DB::table('programs')
                    ->where('id', $program->id)
                    ->update([
                        'application_form_schema' => json_encode(array_values($schema)),
                        'updated_at' => now(),
                    ]);
            }
        }
    }

    private function updateFoodAssistancePrograms(): void
    {
        $programs = DB::table('programs')
            ->where('program_type', ProgramType::FOOD_ASSISTANCE)
            ->whereNotNull('application_form_schema')
            ->get(['id', 'application_form_schema']);

        foreach ($programs as $program) {
            $schema = json_decode((string) $program->application_form_schema, true);
            if (! is_array($schema) || $schema === []) {
                continue;
            }

            $ackIndex = null;
            $receiptIndex = null;

            foreach ($schema as $index => $field) {
                if (! is_array($field)) {
                    continue;
                }
                $name = (string) ($field['name'] ?? '');
                if ($name === 'receipt_acknowledgment') {
                    $ackIndex = $index;
                }
                if ($name === 'previous_receipt') {
                    $receiptIndex = $index;
                    $schema[$index]['required'] = true;
                    $schema[$index]['conditional'] = [
                        'field' => 'receipt_acknowledgment',
                        'value' => '1',
                    ];
                    $schema[$index]['conditional_field'] = 'receipt_acknowledgment';
                    $schema[$index]['conditional_value'] = '1';
                }
            }

            if ($ackIndex === null || $receiptIndex === null) {
                continue;
            }

            // Ensure checkbox appears before the upload field.
            if ($ackIndex > $receiptIndex) {
                $ackField = $schema[$ackIndex];
                $receiptField = $schema[$receiptIndex];
                $schema[$receiptIndex] = $ackField;
                $schema[$ackIndex] = $receiptField;
            } elseif ($ackIndex + 1 !== $receiptIndex) {
                $receiptField = $schema[$receiptIndex];
                array_splice($schema, $receiptIndex, 1);
                $ackIndex = null;
                foreach ($schema as $index => $field) {
                    if (is_array($field) && ($field['name'] ?? '') === 'receipt_acknowledgment') {
                        $ackIndex = $index;
                        break;
                    }
                }
                if ($ackIndex !== null) {
                    array_splice($schema, $ackIndex + 1, 0, [$receiptField]);
                }
            }

            DB::table('programs')
                ->where('id', $program->id)
                ->update([
                    'application_form_schema' => json_encode(array_values($schema)),
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Irreversible content update for live program forms.
    }
};
