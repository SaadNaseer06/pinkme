<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Program extends Model
{
    protected $fillable = [
        'title',
        'description',
        'event_date',
        'event_time',
        'banner',
        'status',
        'program_fund',
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime:H:i:s',
        'program_fund' => 'decimal:2',
    ];

    public function sponsorships(): HasMany
    {
        return $this->hasMany(Sponsorship::class);
    }

    public function sponsorshipProgram(): HasOne
    {
        return $this->hasOne(SponsorshipProgram::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(ProgramRegistration::class);
    }
}
