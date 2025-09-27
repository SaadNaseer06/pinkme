<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SponsorDetail extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'registration_number',
        'company_email',
        'company_phone',
        'company_type',
        'logo',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
