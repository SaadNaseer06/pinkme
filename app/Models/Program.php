<?php

namespace App\Models;

use App\Support\ProgramType;
use Illuminate\Database\Eloquent\Builder;
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
        'program_type',
        'sponsor_name',
        'sponsor_logo',
        'custom_fields',
        'application_form_schema',
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime:H:i:s',
        'application_start_date' => 'date',
        'application_end_date' => 'date',
        'max_applications' => 'integer',
        'custom_fields' => 'array',
        'application_form_schema' => 'array',
    ];

    protected $appends = ['effective_status', 'effective_status_label'];

    /**
     * Status derived from application window: when application_start_date and/or
     * application_end_date are set, status becomes "live" on the opening date
     * and "completed" after the closing date.
     */
    public function getEffectiveStatusAttribute(): string
    {
        $today = now()->startOfDay();
        $start = $this->application_start_date ? $this->application_start_date->startOfDay() : null;
        $end = $this->application_end_date ? $this->application_end_date->startOfDay() : null;

        if ($start !== null && $end !== null) {
            if ($today->lt($start)) {
                return 'upcoming';
            }
            if ($today->lte($end)) {
                return 'ongoing';
            }
            return 'completed';
        }
        if ($start !== null) {
            return $today->lt($start) ? 'upcoming' : 'ongoing';
        }
        if ($end !== null) {
            return $today->lte($end) ? 'ongoing' : 'completed';
        }

        return $this->status ?? 'upcoming';
    }

    /**
     * Human-readable label for effective status ("Live now" when applications are open).
     */
    public function getEffectiveStatusLabelAttribute(): string
    {
        return match ($this->effective_status) {
            'ongoing' => 'Live now',
            'completed' => 'Closed',
            default => 'Upcoming',
        };
    }

    /**
     * Programs that are effectively upcoming (application not yet open).
     */
    public function scopeEffectiveUpcoming(Builder $query): Builder
    {
        $today = now()->toDateString();
        return $query->where(function (Builder $q) use ($today) {
            $q->where('application_start_date', '>', $today)
                ->orWhere(function (Builder $q2) use ($today) {
                    $q2->whereNull('application_start_date')
                        ->whereNull('application_end_date')
                        ->where('status', 'upcoming');
                });
        });
    }

    /**
     * Programs that are effectively live (application window open).
     */
    public function scopeEffectiveOngoing(Builder $query): Builder
    {
        $today = now()->toDateString();
        return $query->where(function (Builder $q) use ($today) {
            $q->where(function (Builder $q2) use ($today) {
                $q2->where('application_start_date', '<=', $today)
                    ->where(function (Builder $q3) use ($today) {
                        $q3->whereNull('application_end_date')
                            ->orWhere('application_end_date', '>=', $today);
                    });
            })->orWhere(function (Builder $q2) use ($today) {
                $q2->whereNull('application_start_date')
                    ->whereNull('application_end_date')
                    ->where('status', 'ongoing');
            });
        });
    }

    /**
     * Whether applications are currently open (effective status is ongoing).
     */
    public function isApplicationOpen(): bool
    {
        return $this->effective_status === 'ongoing';
    }

    public function registrationCount(): int
    {
        return $this->registrations()->count();
    }

    public function hasReachedMaxApplications(): bool
    {
        if (! $this->max_applications) {
            return false;
        }

        return $this->registrationCount() >= (int) $this->max_applications;
    }

    public function isAcceptingApplications(): bool
    {
        return $this->isApplicationOpen() && ! $this->hasReachedMaxApplications();
    }

    public function isMomentsThatMatter(): bool
    {
        return ProgramType::isMomentsThatMatter($this->program_type);
    }

    public function isFinancialAssistance(): bool
    {
        return ($this->program_type ?? ProgramType::FINANCIAL_ASSISTANCE) === ProgramType::FINANCIAL_ASSISTANCE;
    }

    public function hasDynamicApplicationForm(): bool
    {
        return ! empty($this->application_form_schema);
    }

    /**
     * Application form schema with missing select/radio options restored from the type template.
     *
     * @return list<array<string, mixed>>
     */
    public function resolvedApplicationFormSchema(): array
    {
        return \App\Support\ApplicationFormSchema::hydrateMissingOptions(
            $this->application_form_schema ?? [],
            $this->program_type
        );
    }

    public function programTypeLabel(): string
    {
        return ProgramType::label($this->program_type);
    }

    public function sponsorLogoUrl(): ?string
    {
        if (! $this->sponsor_logo) {
            return null;
        }

        return storage_url(ltrim($this->sponsor_logo, '/'));
    }

    public function sponsorships(): HasMany
    {
        return $this->hasMany(Sponsorship::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(ProgramRegistration::class);
    }

}
