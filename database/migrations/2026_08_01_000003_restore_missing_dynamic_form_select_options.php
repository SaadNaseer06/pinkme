<?php

use App\Support\ApplicationFormSchema;
use App\Support\ProgramType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $types = [
            ProgramType::FOOD_ASSISTANCE,
            ProgramType::MAMMOGRAM_IMAGING,
        ];

        $programs = DB::table('programs')
            ->whereIn('program_type', $types)
            ->whereNotNull('application_form_schema')
            ->get(['id', 'program_type', 'application_form_schema']);

        foreach ($programs as $program) {
            $schema = json_decode((string) $program->application_form_schema, true);
            if (! is_array($schema) || $schema === []) {
                continue;
            }

            $hydrated = ApplicationFormSchema::hydrateMissingOptions($schema, $program->program_type);
            if ($hydrated === $schema) {
                continue;
            }

            DB::table('programs')
                ->where('id', $program->id)
                ->update([
                    'application_form_schema' => json_encode($hydrated),
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Irreversible content repair.
    }
};
