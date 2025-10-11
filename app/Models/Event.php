<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'description',
        'date',
        'location',
        'funding_goal',
        'status',
        'event_highlights',
        'image',
        'max_sponsors',
        'registration_deadline',
    ];

    protected $casts = [
        'date' => 'datetime',
        'registration_deadline' => 'datetime',
        'funding_goal' => 'decimal:2',
    ];

    /**
     * Get all sponsorships for this event
     */
    public function sponsorships(): HasMany
    {
        return $this->hasMany(EventSponsorship::class);
    }

    /**
     * Get confirmed sponsorships only
     */
    public function confirmedSponsorships(): HasMany
    {
        return $this->hasMany(EventSponsorship::class)
            ->where('registration_status', 'confirmed');
    }

    /**
     * Get pending sponsorships
     */
    public function pendingSponsorships(): HasMany
    {
        return $this->hasMany(EventSponsorship::class)
            ->where('registration_status', 'pending');
    }
    
    /**
     * Get all sponsors (many-to-many through event_sponsorships)
     */
    public function sponsors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_sponsorships', 'event_id', 'sponsor_id')
            ->withPivot(['amount', 'registration_status', 'message', 'registered_at', 'confirmed_at'])
            ->withTimestamps();
    }

    /**
     * Get only confirmed sponsors
     */
    public function confirmedSponsors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_sponsorships', 'event_id', 'sponsor_id')
            ->wherePivot('registration_status', 'confirmed')
            ->withPivot(['amount', 'registration_status', 'message', 'registered_at', 'confirmed_at'])
            ->withTimestamps();
    }

    /**
     * Get total sponsorship amount (all statuses)
     */
    public function getSponsorshipTotalAttribute(): float
    {
        return (float) $this->sponsorships()->sum('amount');
    }

    /**
     * Get total confirmed sponsorship amount
     */
    public function getConfirmedSponsorshipTotalAttribute(): float
    {
        return (float) $this->confirmedSponsorships()->sum('amount');
    }

    /**
     * Get remaining funding needed
     */
    public function getRemainingFundingAttribute(): float
    {
        if (!$this->funding_goal) {
            return 0;
        }
        return max(0, $this->funding_goal - $this->confirmed_sponsorship_total);
    }

    /**
     * Get funding progress percentage
     */
    public function getFundingProgressAttribute(): int
    {
        if (!$this->funding_goal || $this->funding_goal <= 0) {
            return 0;
        }
        return min(100, (int) round(($this->confirmed_sponsorship_total / $this->funding_goal) * 100));
    }

    /**
     * Check if event is fully funded
     */
    public function isFullyFunded(): bool
    {
        if (!$this->funding_goal) {
            return false;
        }
        return $this->confirmed_sponsorship_total >= $this->funding_goal;
    }

    /**
     * Check if event registration is open
     */
    public function isRegistrationOpen(): bool
    {
        // Check if event is not cancelled or completed
        if (in_array($this->status, ['cancelled', 'completed'])) {
            return false;
        }

        // Check registration deadline
        if ($this->registration_deadline && now()->isAfter($this->registration_deadline)) {
            return false;
        }

        // Check if fully funded
        if ($this->isFullyFunded()) {
            return false;
        }

        return true;
    }

    /**
     * Check if a sponsor is already registered
     */
    public function isSponsorRegistered(int $sponsorId): bool
    {
        return $this->hasEventSponsor($sponsorId);
    }

    /**
     * Get sponsor's registration status
     */
    public function getSponsorRegistrationStatus(int $sponsorId): ?string
    {
        $sponsorship = $this->getEventSponsorship($sponsorId);
        return $sponsorship?->registration_status;
    }
    
    /**
     * Explicitly check if event has a specific sponsor to avoid relationship conflicts
     */
    public function hasEventSponsor(int $sponsorId): bool
    {
        return EventSponsorship::where('event_id', $this->id)
            ->where('sponsor_id', $sponsorId)
            ->whereIn('registration_status', ['pending', 'confirmed'])
            ->exists();
    }
    
    /**
     * Get event sponsorship record for a specific sponsor
     */
    public function getEventSponsorship(int $sponsorId): ?EventSponsorship
    {
        return EventSponsorship::where('event_id', $this->id)
            ->where('sponsor_id', $sponsorId)
            ->first();
    }
}
