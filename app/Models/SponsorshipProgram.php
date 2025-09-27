<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SponsorshipProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'goal_amount',
        'raised_amount',
        'program_id',
        'start_date',
        'end_date',
    ];

    public function sponsorships(): HasMany
    {
        return $this->hasMany(Sponsorship::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'program_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
