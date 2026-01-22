<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $fillable = [
        'title',
        'description',
        'event_date',
        'event_time',
        'application_start_date',
        'application_end_date',
        'max_applications',
        'banner',
        'status',
        'custom_fields',
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime:H:i:s',
        'application_start_date' => 'date',
        'application_end_date' => 'date',
        'max_applications' => 'integer',
        'custom_fields' => 'array',
    ];

    public function sponsorships(): HasMany
    {
        return $this->hasMany(Sponsorship::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(ProgramRegistration::class);
    }

}
