<?php

use App\Support\ProgramType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
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
            foreach ($schema as &$field) {
                if (! is_array($field)) {
                    continue;
                }

                $name = (string) ($field['name'] ?? '');

                if ($name === 'preferred_center') {
                    $field['options'] = [
                        [
                            'value' => 'X-Ray Associates of New Mexico',
                            'label' => 'X-Ray Associates of New Mexico',
                        ],
                    ];
                    $changed = true;
                }

                if ($name === 'permission_to_share') {
                    $field['label'] = 'Permission to share information with X-Ray Associates of New Mexico';
                    $field['help_text'] = 'I authorize PINK "ME" to submit my information to X-Ray Associates of New Mexico.';
                    $changed = true;
                }
            }
            unset($field);

            if ($changed) {
                DB::table('programs')
                    ->where('id', $program->id)
                    ->update([
                        'application_form_schema' => json_encode($schema),
                        'updated_at' => now(),
                    ]);
            }
        }
    }

    public function down(): void
    {
        // Irreversible content update for live program forms.
    }
};
