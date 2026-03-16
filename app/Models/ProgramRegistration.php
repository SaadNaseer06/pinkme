<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramRegistration extends Model
{
    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'program_id',
        'user_id',
        'first_name',
        'last_name',
        'username',
        'email',
        'phone',
        'dob',
        'gender',
        'blood_group',
        'medical_condition',
        'assistance_type',
        'quarter_applied',
        'programs_applied',
        'active_treatment',
        'pregnant',
        'family_history',
        'assistance_history',
        'heard_about',
        'referral_type',
        'treatment_facility_name',
        'street_address',
        'city',
        'state',
        'postal_code',
        'proof_of_income_status',
        'story',
        'authorization_allow',
        'authorization_permissions',
        'billing_details',
        'signature',
        'justification',
        'document_paths',
        'treatment_letter_path',
        'bill_statement_paths',
        'income_document_paths',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_note',
        'assigned_case_manager_id',
        'assigned_at',
        'finance_user_id',
        'sent_to_finance_at',
    ];

    protected $casts = [
        'document_paths' => 'array',
        'programs_applied' => 'array',
        'active_treatment' => 'boolean',
        'pregnant' => 'boolean',
        'proof_of_income_status' => 'array',
        'authorization_permissions' => 'array',
        'authorization_allow' => 'boolean',
        'bill_statement_paths' => 'array',
        'income_document_paths' => 'array',
        'dob' => 'date',
        'reviewed_at' => 'datetime',
        'assigned_at' => 'datetime',
        'sent_to_finance_at' => 'datetime',
    ];
    
    /**
     * Get the program this registration belongs to
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
    
    /**
     * Get the user this registration belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Admin reviewer who processed the registration.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Case manager assigned to handle this registration.
     */
    public function assignedCaseManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_case_manager_id');
    }

    /**
     * Finance user assigned for budget allocation.
     */
    public function financeUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finance_user_id');
    }

    /**
     * Invoices generated for this registration (budget allocation).
     */
    public function registrationInvoices()
    {
        return $this->hasMany(RegistrationInvoice::class);
    }
    
    /**
     * Get the full name of the registrant
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
    
    /**
     * Get formatted document paths for display
     */
    public function getDocumentsAttribute(): array
    {
        if (!$this->document_paths) {
            return [];
        }

        return $this->mapFileArray($this->document_paths);
    }

    public function getStatusLabelAttribute(): string
    {
        return match (strtolower((string) $this->status)) {
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            default               => 'Pending',
        };
    }

    /**
     * Calculate total grant amount from patient's selected programs (programs_applied).
     * Parses "(up to $XXX)" from each program name and sums the amounts.
     */
    public function getCalculatedGrantAmountAttribute(): ?float
    {
        $programs = $this->programs_applied;
        if (empty($programs) || !is_array($programs)) {
            return null;
        }

        $total = 0;
        foreach ($programs as $programName) {
            if (preg_match('/\(up to \$(\d+)\)/i', (string) $programName, $m)) {
                $total += (float) $m[1];
            }
        }

        return $total > 0 ? round($total, 2) : null;
    }

    public function getTreatmentLetterAttribute(): ?array
    {
        return $this->mapFile($this->treatment_letter_path);
    }

    public function getBillStatementsAttribute(): array
    {
        return $this->mapFileArray($this->bill_statement_paths);
    }

    public function getIncomeDocumentsAttribute(): array
    {
        return $this->mapFileArray($this->income_document_paths);
    }

    private function mapFile(?string $path): ?array
    {
        if (!$path) {
            return null;
        }

        return [
            'path' => $path,
            'url' => asset('storage/' . ltrim($path, '/')),
            'filename' => basename($path),
        ];
    }

    private function mapFileArray($paths): array
    {
        if (!$paths) {
            return [];
        }

        return collect(is_array($paths) ? $paths : [$paths])
            ->filter()
            ->map(fn ($path) => $this->mapFile($path))
            ->filter()
            ->values()
            ->toArray();
    }
}
