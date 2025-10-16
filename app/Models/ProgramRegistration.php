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
        'justification',
        'document_paths',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_note',
    ];

    protected $casts = [
        'document_paths' => 'array',
        'dob' => 'date',
        'reviewed_at' => 'datetime',
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
        
        return collect($this->document_paths)
            ->map(function ($path) {
                return [
                    'path' => $path,
                    'url' => asset('storage/' . $path),
                    'filename' => basename($path),
                ];
            })
            ->toArray();
    }

    public function getStatusLabelAttribute(): string
    {
        return match (strtolower((string) $this->status)) {
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            default               => 'Pending',
        };
    }
}
