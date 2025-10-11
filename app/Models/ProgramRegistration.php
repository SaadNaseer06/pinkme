<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramRegistration extends Model
{
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
    ];

    protected $casts = [
        'document_paths' => 'array',
        'dob' => 'date',
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
}
