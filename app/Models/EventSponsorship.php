<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventSponsorship extends Model
{
    protected $fillable = [
        'event_id',
        'sponsor_id',
        'amount',
        'registration_status',
        'message',
        'registered_at',
        'confirmed_at',
        'stripe_checkout_session_id',
        'payment_status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'registered_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    /**
     * Get the event this sponsorship belongs to
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the sponsor (user) for this sponsorship
     */
    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }

    /**
     * Check if sponsorship is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->registration_status === 'confirmed';
    }

    /**
     * Check if sponsorship is pending
     */
    public function isPending(): bool
    {
        return $this->registration_status === 'pending';
    }

    /**
     * Check if sponsorship is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->registration_status === 'cancelled';
    }

    /**
     * Confirm the sponsorship
     */
    public function confirm(): void
    {
        $this->update([
            'registration_status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Cancel the sponsorship
     */
    public function cancel(): void
    {
        $this->update([
            'registration_status' => 'cancelled',
        ]);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->registration_status) {
            'confirmed' => 'green',
            'pending' => 'yellow',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get status display text
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->registration_status) {
            'confirmed' => 'Confirmed',
            'pending' => 'Pending',
            'cancelled' => 'Cancelled',
            default => 'Unknown',
        };
    }
    
    /**
     * Approve the sponsorship registration
     */
    public function approve(): void
    {
        $this->update([
            'registration_status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }
    
    /**
     * Reject the sponsorship registration
     */
    public function reject(): void
    {
        $this->update([
            'registration_status' => 'cancelled',
        ]);
    }
    
    /**
     * Check if the sponsorship can be approved
     */
    public function canBeApproved(): bool
    {
        return $this->registration_status === 'pending';
    }
    
    /**
     * Check if the sponsorship can be rejected
     */
    public function canBeRejected(): bool
    {
        return in_array($this->registration_status, ['pending', 'confirmed']);
    }
    
    /**
     * Get formatted registration date
     */
    public function getFormattedRegisteredAtAttribute(): string
    {
        return $this->registered_at ? $this->registered_at->format('M d, Y g:i A') : 'N/A';
    }
    
    /**
     * Get formatted confirmed date
     */
    public function getFormattedConfirmedAtAttribute(): string
    {
        return $this->confirmed_at ? $this->confirmed_at->format('M d, Y g:i A') : 'N/A';
    }
}
