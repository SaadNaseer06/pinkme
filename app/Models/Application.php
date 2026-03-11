<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Invoice;
use App\Models\Program;

class Application extends Model
{
    use Notifiable, HasFactory;
    // In your Application model
    protected $casts = [
        'submission_date' => 'date',
    ];

    protected $fillable = [
        'patient_id',
        'reviewer_id',
        'finance_user_id',
        'program_id',
        'title',
        'age',
        'blood_group',
        'assistance_type',
        'description',
        'status',
        'submission_date',
        'decision_date',
        'rejection_reason',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function financeUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finance_user_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }
    public function missingRequests()
    {
        return $this->hasMany(ApplicationMissingRequest::class);
    }

    public function clearMissingRequestsForCaseManager($caseManagerId)
    {
        return $this->missingRequests()
            ->where('case_manager_id', $caseManagerId)
            ->delete();
    }
}
