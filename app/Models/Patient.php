<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use Notifiable, HasFactory;
    protected $fillable = [
        'user_id',
        'marital_status',
        'blood_group',
        'diagnosis',
        'diagnosis_date',
        'disease_stage',
        'disease_type',
        'genetic_test',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Patient may use portal chat after submitting a program registration (including while pending
     * and unassigned) or when a legacy application has a reviewer.
     */
    public static function userHasAssignedCaseManager(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        $hasRegistration = ProgramRegistration::query()
            ->where('user_id', $user->id)
            ->exists();

        $patientId = static::query()->where('user_id', $user->id)->value('id');
        $hasApplicationReviewer = $patientId
            && Application::query()
                ->where('patient_id', $patientId)
                ->whereNotNull('reviewer_id')
                ->exists();

        return $hasRegistration || $hasApplicationReviewer;
    }
}
