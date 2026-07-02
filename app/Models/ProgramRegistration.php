<?php

namespace App\Models;

use App\Support\FinancialAssistanceApplicationPeriod;
use App\Support\ProgramType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramRegistration extends Model
{
    public const STATUS_PENDING = 'pending';

    /** Case manager completed review; awaiting finance (level 2) payment processing. */
    public const STATUS_PENDING_FINANCE = 'pending_finance';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    /** Moments That Matter: care package shipped to applicant. */
    public const STATUS_SHIPPED = 'shipped';

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
        'breast_cancer_stage',
        'ethnicity',
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
        'apartment_suite',
        'shipping_usa',
        'mtm_package',
        'applying_for',
        'patient_loved_one_name',
        'mtm_treatment_status',
        'mtm_treatment_status_other',
        'mtm_diagnosis_type',
        'mtm_diagnosis_date',
        'mtm_story_permission',
        'mtm_acknowledgments',
        'city',
        'state',
        'postal_code',
        'proof_of_income_status',
        'story',
        'story_media_paths',
        'story_notes',
        'authorization_allow',
        'authorization_permissions',
        'billing_details',
        'patient_bill_line_items',
        'payment_links',
        'finance_pre_payment_proof_paths',
        'signature',
        'signature_date',
        'justification',
        'document_paths',
        'application_responses',
        'treatment_letter_path',
        'bill_statement_paths',
        'income_document_paths',
        'status',
        'reviewed_by',
        'reviewed_at',
        'shipped_at',
        'shipped_by',
        'review_note',
        'internal_note_for_finance',
        'internal_note_for_admin',
        'assigned_case_manager_id',
        'assigned_at',
        'finance_user_id',
        'sent_to_finance_at',
    ];

    protected $casts = [
        'document_paths' => 'array',
        'application_responses' => 'array',
        'programs_applied' => 'array',
        'active_treatment' => 'boolean',
        'pregnant' => 'boolean',
        'proof_of_income_status' => 'array',
        'authorization_permissions' => 'array',
        'authorization_allow' => 'boolean',
        'finance_pre_payment_proof_paths' => 'array',
        'bill_statement_paths' => 'array',
        'income_document_paths' => 'array',
        'patient_bill_line_items' => 'array',
        'story_media_paths' => 'array',
        'mtm_acknowledgments' => 'array',
        'shipping_usa' => 'boolean',
        'mtm_diagnosis_date' => 'date',
        'signature_date' => 'date',
        'dob' => 'date',
        'reviewed_at' => 'datetime',
        'shipped_at' => 'datetime',
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

    public function shipper(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shipped_by');
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
     * Invoices generated for this registration (bills paid).
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
        return trim($this->first_name.' '.$this->last_name);
    }

    /**
     * Human-readable application id for finance emails (e.g. PRG-2026-000042).
     */
    public function getPublicReferenceAttribute(): string
    {
        $t = $this->created_at ?? now();

        return 'PRG-'.$t->format('Y').'-'.str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get formatted document paths for display
     */
    public function getDocumentsAttribute(): array
    {
        if (! $this->document_paths) {
            return [];
        }

        return $this->mapFileArray($this->document_paths);
    }

    public function isMomentsThatMatterApplication(): bool
    {
        if (filled($this->mtm_package)) {
            return true;
        }

        return $this->relationLoaded('program')
            ? $this->program?->isMomentsThatMatter() === true
            : $this->program()->where('program_type', ProgramType::MOMENTS_THAT_MATTER)->exists();
    }

    public function scopeForApplicationPeriod(Builder $query, ?string $periodKey): Builder
    {
        return FinancialAssistanceApplicationPeriod::applyToQuery($query, $periodKey);
    }

    /**
     * @param  'all'|'financial_assistance'|'moments_that_matter'  $type
     */
    public function scopeForApplicationType(Builder $query, string $type): Builder
    {
        if ($type === 'all') {
            return $query;
        }

        if ($type === ProgramType::MOMENTS_THAT_MATTER) {
            return $query->where(function (Builder $w): void {
                $w->whereNotNull('mtm_package')
                    ->where('mtm_package', '!=', '')
                    ->orWhereHas('program', fn (Builder $q) => $q->where('program_type', ProgramType::MOMENTS_THAT_MATTER));
            });
        }

        return $query->where(function (Builder $w): void {
            $w->where(function (Builder $w2): void {
                $w2->whereNull('mtm_package')->orWhere('mtm_package', '');
            })->whereDoesntHave('program', fn (Builder $q) => $q->where('program_type', ProgramType::MOMENTS_THAT_MATTER));
        });
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->isMomentsThatMatterApplication()) {
            return match (strtolower((string) $this->status)) {
                self::STATUS_SHIPPED => 'Shipped',
                self::STATUS_APPROVED => 'Awaiting shipment',
                default => 'Awaiting shipment',
            };
        }

        return match (strtolower((string) $this->status)) {
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_PENDING_FINANCE => 'Finance review',
            self::STATUS_SHIPPED => 'Shipped',
            default => 'Pending',
        };
    }

    /**
     * Calculate total grant amount from patient's selected programs (programs_applied).
     * Parses "(up to $XXX)" from each program name and sums the amounts.
     */
    public function getCalculatedGrantAmountAttribute(): ?float
    {
        $programs = $this->programs_applied;
        if (empty($programs) || ! is_array($programs)) {
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

    private function mapFile(mixed $path): ?array
    {
        if (! is_string($path) || $path === '') {
            return null;
        }

        return [
            'path' => $path,
            'url' => storage_url($path),
            'filename' => basename($path),
        ];
    }

    private function mapFileArray($paths): array
    {
        if (! $paths) {
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
