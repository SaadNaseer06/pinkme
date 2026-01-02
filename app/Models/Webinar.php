<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Webinar extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'scheduled_at',
        'duration_minutes',
        'presenter',
        'join_url',
        'status',
        'audience',
        'max_attendees',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'duration_minutes' => 'integer',
        'max_attendees' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(WebinarRegistration::class);
    }

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'webinar_registrations')
            ->withPivot(['status', 'role_name', 'joined_at'])
            ->withTimestamps();
    }

    public function isJoinable(): bool
    {
        if ($this->status === 'cancelled' || $this->status === 'completed') {
            return false;
        }

        if ($this->scheduled_at && $this->scheduled_at->isPast()) {
            return false;
        }

        if ($this->max_attendees && $this->registeredCount() >= $this->max_attendees) {
            return false;
        }

        return true;
    }

    public function registeredCount(): int
    {
        if (!is_null($this->getAttribute('attendee_count'))) {
            return (int) $this->getAttribute('attendee_count');
        }

        return (int) $this->registrations()
            ->where('status', 'registered')
            ->count();
    }

    public function getRemainingSlotsAttribute(): ?int
    {
        if (!$this->max_attendees) {
            return null;
        }

        return max($this->max_attendees - $this->registeredCount(), 0);
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->status === 'cancelled') {
            return 'Cancelled';
        }

        if ($this->status === 'completed') {
            return 'Completed';
        }

        if ($this->scheduled_at && $this->scheduled_at->isPast()) {
            return 'Completed';
        }

        return ucfirst($this->status ?? 'upcoming');
    }

    public function getDateLabelAttribute(): ?string
    {
        return $this->scheduled_at ? $this->scheduled_at->format('M d, Y') : null;
    }

    public function getTimeLabelAttribute(): ?string
    {
        return $this->scheduled_at ? $this->scheduled_at->format('g:i A') : null;
    }

    public function getAudienceLabelAttribute(): string
    {
        return match ($this->audience) {
            'patient' => 'Patients only',
            'sponsor' => 'Sponsors only',
            default => 'Patients & Sponsors',
        };
    }
}
