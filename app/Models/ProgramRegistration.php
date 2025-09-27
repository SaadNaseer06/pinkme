<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
