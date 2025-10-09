<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function applications()
    {
        return $this->hasMany(Application::class, 'reviewer_id');
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function sponsorDetail()
    {
        return $this->hasOne(SponsorDetail::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function sponsorships()
    {
        return $this->hasMany(Sponsorship::class, 'sponsor_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function reviewedApplications()
    {
        return $this->hasMany(Application::class, 'reviewer_id');
    }

    public function reviews()
    {
        return $this->hasMany(SponsorReview::class, 'sponsor_id');
    }

    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    public function reviewerApplications()
    {
        return $this->hasMany(Application::class, 'reviewer_id');
    }

    public function scopeReviewers($query)
    {
        return $query->whereHas('profile')
            ->withCount('reviewerApplications as assigned_applications_count');
    }

    public function getReviewerIdAttribute()
    {
        return 'RVW-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    public function getReviewerStatusAttribute()
    {
        // Check if reviewer has recent activity (within last 30 days)
        $hasRecentActivity = $this->reviewerApplications()
            ->where('updated_at', '>=', now()->subDays(30))
            ->exists();

        return $hasRecentActivity ? 'Active' : 'Inactive';
    }

    public function getFullNameAttribute()
    {
        return $this->profile ? $this->profile->full_name : 'N/A';
    }

    public function getPhoneAttribute()
    {
        return $this->profile ? $this->profile->phone : 'N/A';
    }

    public function getGenderAttribute()
    {
        return $this->profile ? ucfirst($this->profile->gender) : 'N/A';
    }
}
