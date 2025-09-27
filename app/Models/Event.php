<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'date',
        'location',
    ];

    public function sponsorships(): HasMany
    {
        return $this->hasMany(EventSponsorship::class);
    }
    
    public function sponsors()
    {
        // users pivot with "amount"
        return $this->belongsToMany(User::class, 'event_sponsorships', 'event_id', 'sponsor_id')
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function getSponsorshipTotalAttribute(): float
    {
        return (float) $this->sponsorships()->sum('amount');
    }
}
