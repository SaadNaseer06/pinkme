<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Event;
use App\Models\EventSponsorship;
use App\Models\SponsorReview;
use App\Models\Sponsorship;
use App\Models\SponsorshipProgram;
use App\Models\Program;
use App\Models\User;
use App\Models\Webinar;
use App\Models\WebinarRegistration;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Mail\WebinarRegistrationConfirmation;

class SponsorController extends Controller
{

    public function dashboard()

    {

        $user = Auth::user();

        $programs = Program::withSum('sponsorships as raised_amount', 'amount')

            ->orderByDesc('created_at')

            ->get();

        $sponsorships = Sponsorship::where('sponsor_id', $user->id)

            ->with(['program', 'sponsorshipProgram'])

            ->orderByDesc('date')

            ->get();

        $totalPrograms = $programs->count();

        $totalSponsored = $sponsorships->sum('amount');

        $activePrograms = $programs->filter(function (Program $program) {

            if ($program->event_date) {

                return Carbon::parse($program->event_date)->isFuture() || Carbon::parse($program->event_date)->isToday();
            }

            return $program->status === 'ongoing';
        })->count();

        $myContributions = $sponsorships->count();

        $baseSponsorQuery = User::query()

            ->whereHas('role', fn($query) => $query->where('name', 'sponsor'))

            ->with([

                'profile',

                'sponsorDetail',

                'sponsorships' => fn($query) => $query->latest('date')->limit(1)->with(['program', 'sponsorshipProgram']),

            ])

            ->withCount('sponsorships')

            ->withSum('sponsorships as total_contribution', 'amount')

            ->withMin('sponsorships as first_contribution_date', 'date')

            ->withMax('sponsorships as last_contribution_date', 'date');

        $individualSponsors = (clone $baseSponsorQuery)

            ->whereDoesntHave('sponsorDetail')

            ->orderByDesc('total_contribution')

            ->get();

        $companySponsors = (clone $baseSponsorQuery)

            ->whereHas('sponsorDetail')

            ->orderByDesc('total_contribution')

            ->get();

        $recentApplications = Application::with(['patient.user.profile', 'program'])

            ->orderByDesc('submission_date')

            ->limit(5)

            ->get();

        $monthlyData = Sponsorship::where('sponsor_id', $user->id)

            ->select(

                DB::raw('MONTH(date) as month'),

                DB::raw('YEAR(date) as year'),

                DB::raw('SUM(amount) as total')

            )

            ->groupBy('year', 'month')

            ->orderByDesc('year')

            ->orderByDesc('month')

            ->limit(12)

            ->get();

        return view('sponsor.dashboard', compact(

            'programs',

            'sponsorships',

            'totalPrograms',

            'totalSponsored',

            'activePrograms',

            'myContributions',

            'recentApplications',

            'monthlyData',

            'individualSponsors',

            'companySponsors'

        ));
    }

    public function events()

    {
        $user = Auth::user();

        $events = Event::with([
            'sponsors' => fn($query) => $query->with(['profile', 'sponsorDetail'])
                ->withPivot(['amount', 'registration_status', 'registered_at', 'confirmed_at']),
            'sponsorships' => function($query) {
                // Explicitly ensure we're using the event_sponsorships table
                return $query;
            }
        ])

            ->orderBy('date')

            ->get()

            ->map(function (Event $event) use ($user) {

                $eventDate = Carbon::parse($event->date);

                $today = Carbon::today();

                $eventDay = $eventDate->copy()->startOfDay();

                if ($eventDay->gt($today)) {

                    $status = 'upcoming';
                } elseif ($eventDay->eq($today)) {

                    $status = 'inProgress';
                } else {

                    $status = 'past';
                }

                $event->status = $status;

                $event->month_label = $eventDate->format('M');

                $event->day_label = $eventDate->format('d');

                $event->date_label = $eventDate->format('l, F j, Y');

                $event->time_label = $eventDate->format('g:i A');

                $event->total_sponsorship_amount = $event->sponsorships->sum('amount');

                $event->confirmed_sponsorship_amount = $event->confirmedSponsorships->sum('amount');

                $event->primary_sponsor = $event->sponsors->first();

                // Check if current user is registered
                $event->user_registration_status = $event->getSponsorRegistrationStatus($user->id);
                $event->is_user_registered = $event->isSponsorRegistered($user->id);
                $event->can_register = $event->isRegistrationOpen();

                return $event;
            });

        $ongoingEvents = $events->reject(fn($event) => $event->status === 'upcoming')->values();

        $upcomingEvents = $events->filter(fn($event) => $event->status === 'upcoming')->values();

        $pastEvents = $events->filter(fn($event) => $event->status === 'past')->values();

        return view('sponsor.events', compact('events', 'ongoingEvents', 'upcomingEvents', 'pastEvents'));
    }

    public function sponsorships()

    {

        $user = Auth::user();

        $sponsorships = Sponsorship::where('sponsor_id', $user->id)

            ->with(['program', 'sponsorshipProgram'])

            ->orderByDesc('date')

            ->paginate(15);

        $programs = Program::query()

            ->where('payment_type', 'flexible')

            ->withSum('sponsorships as raised_amount', 'amount')

            ->orderBy('event_date')

            ->orderBy('event_time')

            ->get()

            ->map(function (Program $program) {

                $goal = (float) ($program->program_fund ?? 0);

                $raised = (float) ($program->raised_amount ?? 0);

                $program->goal_amount = $goal;

                $program->remaining_amount = max($goal - $raised, 0);

                $program->progress_percent = $goal > 0 ? round(($raised / $goal) * 100) : 0;

                $program->start_date = $program->event_date;

                $program->end_date = $program->event_date;

                return $program;
            });

        $today = Carbon::today();

        $upcomingPrograms = $programs->filter(function (Program $program) use ($today) {

            if ($program->status === 'upcoming') {

                return true;
            }

            if ($program->event_date instanceof Carbon) {

                return $program->event_date->isAfter($today);
            }

            if ($program->event_date) {

                return Carbon::parse($program->event_date)->isAfter($today);
            }

            return false;
        })->values();

        $ongoingPrograms = $programs->filter(function (Program $program) use ($today) {

            if ($program->status === 'completed') {

                return false;
            }

            if ($program->status === 'ongoing') {

                return true;
            }

            if ($program->event_date instanceof Carbon) {

                if ($program->event_date->isAfter($today)) {

                    return false;
                }

                if ($program->event_date->isSameDay($today)) {

                    return true;
                }

                return $program->status !== 'completed';
            }

            if ($program->event_date) {

                $eventDate = Carbon::parse($program->event_date);

                if ($eventDate->isAfter($today)) {

                    return false;
                }

                if ($eventDate->isSameDay($today)) {

                    return true;
                }

                return $program->status !== 'completed';
            }

            return $program->status === 'ongoing';
        })->values();

        return view('sponsor.sponsorships', compact(

            'sponsorships',

            'ongoingPrograms',

            'upcomingPrograms'

        ));
    }

    public function becomeASponsor()

    {

        $user = Auth::user();

        $profile = $user?->profile;

        $companyDetail = $user?->sponsorDetail;

        $commitmentMessage = 'This program requires one sponsor to fund 100% of the costs.';

        $sponsorName = $companyDetail?->company_name

            ?? $profile?->full_name

            ?? ($user?->email ?? 'Sponsor');

        $sponsorPhone = $companyDetail?->company_phone

            ?? $profile?->phone

            ?? 'Not provided';

        $sponsorEmail = $companyDetail?->company_email

            ?? ($user?->email ?? 'Not provided');

        $rawLogo = $companyDetail?->logo;

        if ($rawLogo) {

            $sponsorLogo = Str::startsWith($rawLogo, ['http://', 'https://', '/'])

                ? $rawLogo

                : asset('storage/' . ltrim($rawLogo, '/'));
        } else {

            $sponsorLogo = asset('images/brand.png');
        }

        $sponsorAbout = $companyDetail?->company_type

            ? 'Corporate sponsor focused on ' . $companyDetail->company_type . ' initiatives.'

            : "Dedicated individual sponsor supporting impactful women's health programs.";

        $sponsorContext = [

            'name' => $sponsorName,

            'phone' => $sponsorPhone ?: 'Not provided',

            'email' => $sponsorEmail ?: 'Not provided',

            'about' => $sponsorAbout,

            'logo' => $sponsorLogo,

        ];

        $programs = Program::query()

            ->where('payment_type', 'full')

            ->withSum('sponsorships as raised_amount', 'amount')

            ->orderBy('event_date')

            ->orderBy('event_time')

            ->get()

            ->map(function (Program $program) use ($commitmentMessage) {

                $eventDate = $program->event_date ? Carbon::parse($program->event_date) : null;

                $program->month_label = $eventDate ? $eventDate->format('M') : 'TBD';

                $program->day_label = $eventDate ? $eventDate->format('d') : '--';

                $program->date_label = $eventDate ? $eventDate->format('F d, Y') : 'To be announced';

                $program->time_label = $program->event_time ? Carbon::parse($program->event_time)->format('h:i A') : 'To be announced';

                $program->commitment_message = $commitmentMessage;

                $program->fund_label = $program->program_fund > 0

                    ? '$' . number_format((float) $program->program_fund, 2)

                    : 'To be determined';

                $program->remaining_amount = max(($program->program_fund ?? 0) - ($program->raised_amount ?? 0), 0);

                return $program;
            });

        $today = Carbon::today();

        $upcomingPrograms = $programs->filter(function (Program $program) use ($today) {

            if ($program->event_date) {

                return Carbon::parse($program->event_date)->isAfter($today);
            }

            return $program->status === 'upcoming';
        })->values();

        $ongoingPrograms = $programs->reject(function (Program $program) use ($today) {

            if ($program->event_date) {

                return Carbon::parse($program->event_date)->isAfter($today);
            }

            return $program->status === 'upcoming';
        })->values();

        return view('sponsor.becomeASponsor', compact(

            'ongoingPrograms',

            'upcomingPrograms',

            'sponsorContext',

            'commitmentMessage'

        ));
    }

    public function storeSponsorship(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'program_source' => ['required', 'in:program,sponsorship_program'],
            'program_id' => ['nullable', 'required_if:program_source,program', 'exists:programs,id'],
            'sponsorship_program_id' => ['nullable', 'required_if:program_source,sponsorship_program', 'exists:sponsorship_programs,id'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        [$redirectRoute, $programTitle, $amount] = DB::transaction(function () use ($validated, $user) {
            $program = null;
            $sponsorshipProgram = null;
            $redirectRoute = 'sponsor.sponsorships';

            if ($validated['program_source'] === 'sponsorship_program') {
                $sponsorshipProgram = SponsorshipProgram::query()
                    ->whereKey($validated['sponsorship_program_id'])
                    ->lockForUpdate()
                    ->firstOrFail();
            } else {
                $program = Program::query()
                    ->whereKey($validated['program_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $redirectRoute = $program->payment_type === 'flexible'
                    ? 'sponsor.sponsorships'
                    : 'sponsor.becomeASponsor';
            }

            $numericAmount = (float) $validated['amount'];

            $remaining = null;
            if ($sponsorshipProgram) {
                $remaining = max(
                    (float) ($sponsorshipProgram->goal_amount ?? 0) - (float) ($sponsorshipProgram->raised_amount ?? 0),
                    0
                );
            }

            if ($remaining !== null) {
                if ($remaining <= 0) {
                    throw ValidationException::withMessages([
                        'amount' => 'This program has already reached its funding goal.',
                    ]);
                }

                if ($numericAmount - $remaining > 1e-6) {
                    throw ValidationException::withMessages([
                        'amount' => 'The maximum you can contribute to this program is $' . number_format($remaining, 2) . '.',
                    ]);
                }
            }

            $sponsorship = [
                'sponsor_id' => $user->id,
                'program_id' => $program?->id,
                'sponsorship_program_id' => $sponsorshipProgram?->id,
                'amount' => $numericAmount,
                'date' => now()->toDateString(),
            ];

            if (!$sponsorship['program_id'] && !$sponsorship['sponsorship_program_id']) {
                $errorField = $validated['program_source'] === 'sponsorship_program'
                    ? 'sponsorship_program_id'
                    : 'program_id';

                throw ValidationException::withMessages([
                    $errorField => 'We could not determine which program you selected. Please try again.',
                ]);
            }

            Sponsorship::create($sponsorship);

            if ($sponsorshipProgram) {
                $sponsorshipProgram->raised_amount = (float) ($sponsorshipProgram->raised_amount ?? 0) + $numericAmount;
                $sponsorshipProgram->save();
            }

            $title = $sponsorshipProgram?->title ?? $program?->title ?? 'the selected program';

            return [$redirectRoute, $title, $numericAmount];
        });

        $formattedAmount = number_format($amount, 2);

        return redirect()
            ->route($redirectRoute)
            ->with('success', 'Thank you for sponsoring ' . $programTitle . '. We\'ve recorded your $' . $formattedAmount . ' contribution.');
    }

    public function reviews()
    {
        $user = Auth::user();
        $baseQuery = SponsorReview::query(); // Removed where('sponsor_id', $user->id)
        $reviews = (clone $baseQuery)
            ->with('reviewer.profile', 'reviewer.sponsorDetail', 'sponsor.profile')
            ->orderByDesc('created_at')
            ->paginate(10);

        $recentReviews = (clone $baseQuery)
            ->with('reviewer.profile', 'reviewer.sponsorDetail', 'sponsor.profile')
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $totalReviews = (clone $baseQuery)->count();
        $averageRating = $totalReviews > 0
            ? round((clone $baseQuery)->avg('rating'), 1)
            : null;

        $ratingCounts = (clone $baseQuery)
            ->select('rating', DB::raw('COUNT(*) as total'))
            ->groupBy('rating')
            ->pluck('total', 'rating')
            ->map(fn($count) => (int) $count);

        $ratingLabels = [
            1 => 'ONE',
            2 => 'TWO',
            3 => 'THREE',
            4 => 'FOUR',
            5 => 'FIVE',
        ];
        $distribution = collect(range(5, 1))->map(function (int $rating) use ($ratingCounts, $ratingLabels, $totalReviews) {
            $count = (int) ($ratingCounts[$rating] ?? 0);
            $percentage = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
            return [
                'rating' => $rating,
                'label' => $ratingLabels[$rating],
                'count' => $count,
                'percentage' => $percentage,
            ];
        });
        $summary = [
            'average' => $averageRating,
            'total' => $totalReviews,
            'distribution' => $distribution,
        ];
        return view('sponsor.reviews', compact('reviews', 'recentReviews', 'summary'));
    }


    public function storeReview(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);
        SponsorReview::create([
            'sponsor_id' => $user->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'],
        ]);
        return redirect()
            ->route('sponsor.reviews')
            ->with('success', 'Thanks for sharing your experience. Your review has been recorded.');
    }

    public function payment()
    {
        $user = Auth::user();
        $baseQuery = Sponsorship::where('sponsor_id', $user->id);
        $payments = (clone $baseQuery)
            ->with(['program', 'sponsorshipProgram'])
            ->orderByDesc('date')
            ->paginate(15);
        $totalAmount = (clone $baseQuery)->sum('amount');
        $programKeys = (clone $baseQuery)
            ->get(['program_id', 'sponsorship_program_id'])
            ->map(function (Sponsorship $payment) {
                if ($payment->program_id) {
                    return 'program-' . $payment->program_id;
                }
                if ($payment->sponsorship_program_id) {
                    return 'sponsorship-program-' . $payment->sponsorship_program_id;
                }
                return uniqid('manual-', true);
            })
            ->unique()
            ->count();

        $latestPayment = (clone $baseQuery)
            ->orderByDesc('date')
            ->first();

        $totals = [
            'total_amount' => (float) $totalAmount,
            'programs_supported' => $programKeys,
            'latest_contribution' => $latestPayment?->amount,
            'latest_date' => $latestPayment?->date ? Carbon::parse($latestPayment->date) : null,
        ];
        return view('sponsor.payment', compact('payments', 'totals'));
    }

    public function setting()
    {
        $user = Auth::user()->load(['profile', 'sponsorDetail']);
        return view('sponsor.setting', compact('user'));
    }

    /**
     * Update personal information for the sponsor.
     */
    public function updateSettings(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;
        if (!$profile) {
            $profile = new \App\Models\UserProfile(['user_id' => $user->id]);
            $profile->save();
            $user->load('profile');
            $profile = $user->profile;
        }

        $sponsorDetail = $user->sponsorDetail;
        // Only create SponsorDetail if we have company-related data to save
        $hasCompanyData = $request->filled(['company_name', 'company_email', 'company_phone', 'registration_number', 'company_type']);
        if (!$sponsorDetail && $hasCompanyData) {
            $sponsorDetail = new \App\Models\SponsorDetail(['user_id' => $user->id]);
        }

        $rules = [
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255|unique:user_profiles,username,' . ($profile->id ?? 'NULL'),
            'phone' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'gender' => 'nullable|string|max:10',
            'date_of_birth' => 'nullable|date',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            // Company/Sponsor specific fields
            'company_name' => 'nullable|string|max:255',
            'company_email' => 'nullable|email',
            'company_phone' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'company_type' => 'nullable|string|max:255',
        ];

        $data = $request->validate($rules);

        // Update user email
        $user->email = $data['email'];
        $user->save();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $profile->avatar = $path;
        }

        // Update or create profile fields
        $profile->first_name = $data['first_name'] ?? $profile->first_name;
        $profile->last_name = $data['last_name'] ?? $profile->last_name;
        $profile->username = $data['username'] ?? $profile->username;
        $profile->full_name = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
        $profile->phone = $data['phone'] ?? $profile->phone;
        $profile->gender = $data['gender'] ?? $profile->gender;
        $profile->date_of_birth = $data['date_of_birth'] ?? $profile->date_of_birth;
        $profile->country = $data['country'] ?? $profile->country;
        $profile->city = $data['city'] ?? $profile->city;
        $profile->state = $data['state'] ?? $profile->state;
        $profile->save();

        // Update sponsor details only if we have the record or company data
        if ($sponsorDetail || $hasCompanyData) {
            if (!$sponsorDetail) {
                $sponsorDetail = new \App\Models\SponsorDetail(['user_id' => $user->id]);
            }

            $sponsorDetail->company_name = $data['company_name'] ?? $sponsorDetail->company_name;
            $sponsorDetail->company_email = $data['company_email'] ?? $sponsorDetail->company_email;
            $sponsorDetail->company_phone = $data['company_phone'] ?? $sponsorDetail->company_phone;
            $sponsorDetail->registration_number = $data['registration_number'] ?? $sponsorDetail->registration_number;
            $sponsorDetail->company_type = $data['company_type'] ?? $sponsorDetail->company_type;
            $sponsorDetail->save();
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update password for the sponsor.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();
        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Update notification preferences for the sponsor.
     */
    public function updateNotifications(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;
        if (!$profile) {
            $profile = new \App\Models\UserProfile(['user_id' => $user->id]);
        }

        $profile->email_notification = $request->has('email_notification');
        $profile->sms_notification = $request->has('sms_notification');
        $profile->notify_on_new_notifications = $request->has('notify_on_new_notifications');
        $profile->notify_on_direct_message = $request->has('notify_on_direct_message');
        $profile->save();

        return back()->with('success', 'Notification preferences updated.');
    }

    /**
     * Update account settings for the sponsor.
     */
    public function updateAccount(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;
        if (!$profile) {
            $profile = new \App\Models\UserProfile(['user_id' => $user->id]);
        }

        $data = $request->validate([
            'username' => 'nullable|string|max:255|unique:user_profiles,username,' . ($profile->id ?? 'NULL'),
            'email' => 'required|email|unique:users,email,' . $user->id,
            'alternate_email' => 'nullable|email',
        ]);

        $user->email = $data['email'];
        $user->save();

        $profile->username = $data['username'] ?? $profile->username;
        $profile->alternate_email = $data['alternate_email'] ?? $profile->alternate_email;
        $profile->save();

        return back()->with('success', 'Account settings updated successfully.');
    }

    /**
     * Update social media links for the sponsor.
     */
    public function updateSocial(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;
        if (!$profile) {
            $profile = new \App\Models\UserProfile(['user_id' => $user->id]);
        }

        $data = $request->validate([
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'instagram' => 'nullable|url',
        ]);

        $profile->facebook = $data['facebook'] ?? $profile->facebook;
        $profile->twitter = $data['twitter'] ?? $profile->twitter;
        $profile->instagram = $data['instagram'] ?? $profile->instagram;
        $profile->save();

        return back()->with('success', 'Social media links updated.');
    }

    /**
     * Show detailed event information for sponsor registration
     */
    public function showEvent(\App\Models\Event $event)
    {
        $user = Auth::user();
        
        // Load event with sponsorships and sponsor details
        $event->load([
            'sponsors.profile',
            'sponsors.sponsorDetail',
            'confirmedSponsorships',
            'pendingSponsorships'
        ]);
        
        // Check if current sponsor is already registered
        $currentRegistration = $event->sponsorships()
            ->where('sponsor_id', $user->id)
            ->first();
        
        // Calculate funding progress
        $fundingProgress = $event->funding_progress;
        $remainingFunding = $event->remaining_funding;
        
        return view('sponsor.events.show', compact(
            'event',
            'currentRegistration',
            'fundingProgress',
            'remainingFunding'
        ));
    }
    
    /**
     * Register sponsor for an event
     */
    public function registerForEvent(Request $request, \App\Models\Event $event)
    {
        $user = Auth::user();
        
        // Check if registration is still open
        if (!$event->isRegistrationOpen()) {
            return back()->with('error', 'Registration for this event is no longer open.');
        }
        
        // Check if sponsor is already registered
        if ($event->isSponsorRegistered($user->id)) {
            return back()->with('error', 'You are already registered for this event.');
        }
        
        $data = $request->validate([
            'amount'             => ['required', 'numeric', 'min:0.01'],
            'message'            => ['nullable', 'string', 'max:500'],
        ]);
        
        // Check if amount doesn't exceed remaining funding needed
        if ($event->funding_goal && $data['amount'] > $event->remaining_funding) {
            return back()->with('error', 'Sponsorship amount exceeds remaining funding needed ($' . number_format($event->remaining_funding, 2) . ').');
        }
        
        // Create event sponsorship registration
        $event->sponsorships()->create([
            'sponsor_id' => $user->id,
            'amount' => $data['amount'],
            'registration_status' => 'pending',
            'message' => $data['message'] ?? null,
            'registered_at' => now(),
        ]);
        
        return back()->with('success', 'Your event registration has been submitted successfully! You will be notified once it\'s confirmed.');
    }
    
    /**
     * Cancel sponsor event registration
     */
    public function cancelEventRegistration(\App\Models\Event $event)
    {
        $user = Auth::user();
        
        $sponsorship = $event->sponsorships()
            ->where('sponsor_id', $user->id)
            ->whereIn('registration_status', ['pending', 'confirmed'])
            ->first();
        
        if (!$sponsorship) {
            return back()->with('error', 'No active registration found for this event.');
        }
        
        // Only allow cancellation if event hasn't started yet
        if ($event->date && now()->isAfter($event->date)) {
            return back()->with('error', 'Cannot cancel registration for events that have already started.');
        }
        
        $sponsorship->update([
            'registration_status' => 'cancelled'
        ]);
        
        return back()->with('success', 'Your event registration has been cancelled.');
    }
    
    /**
     * Show sponsor's event registrations
     */
    public function myEventRegistrations()
    {
        $user = Auth::user();
        
        // Get all events where the sponsor has registrations
        $registrations = EventSponsorship::where('sponsor_id', $user->id)
            ->with([
                'event' => function($query) {
                    $query->withCount(['confirmedSponsorships', 'pendingSponsorships']);
                }
            ])
            ->orderByDesc('registered_at')
            ->get()
            ->map(function ($sponsorship) {
                $event = $sponsorship->event;
                if ($event) {
                    $eventDate = Carbon::parse($event->date);
                    $event->formatted_date = $eventDate->format('M d, Y');
                    $event->formatted_time = $eventDate->format('g:i A');
                    $event->is_upcoming = $eventDate->isFuture();
                    $event->is_today = $eventDate->isToday();
                    $event->is_past = $eventDate->isPast() && !$eventDate->isToday();
                    
                    // Calculate funding progress
                    $event->funding_progress = $event->funding_progress;
                    $event->remaining_funding = $event->remaining_funding;
                }
                return $sponsorship;
            });
        
        // Group registrations by status
        $confirmed = $registrations->where('registration_status', 'confirmed');
        $pending = $registrations->where('registration_status', 'pending');
        $cancelled = $registrations->where('registration_status', 'cancelled');
        
        // Calculate totals
        $totalAmount = $registrations->sum('amount');
        $confirmedAmount = $confirmed->sum('amount');
        $pendingAmount = $pending->sum('amount');
        
        return view('sponsor.my-event-registrations', compact(
            'registrations',
            'confirmed',
            'pending', 
            'cancelled',
            'totalAmount',
            'confirmedAmount',
            'pendingAmount'
        ));
    }

    /**
     * List available webinars for sponsors.
     */
    public function webinars()
    {
        $user = Auth::user();

        $webinars = Webinar::query()
            ->whereIn('audience', ['both', 'sponsor'])
            ->withCount([
                'registrations as attendee_count' => fn($query) => $query->where('status', 'registered'),
            ])
            ->with(['registrations' => fn($query) => $query->where('user_id', $user->id)])
            ->orderBy('scheduled_at')
            ->get()
            ->map(function (Webinar $webinar) {
                $registration = $webinar->registrations->first();
                $webinar->current_registration = $registration;
                $webinar->is_registered = $registration?->isRegistered() ?? false;
                $webinar->can_join = $webinar->isJoinable();
                return $webinar;
            });

        return view('sponsor.webinars', compact('webinars'));
    }

    /**
     * Register sponsor for a webinar.
     */
    public function joinWebinar(Webinar $webinar)
    {
        $user = Auth::user();

        if (!$webinar->isJoinable()) {
            return back()->with('error', 'Registration for this webinar is closed.');
        }

        $existing = WebinarRegistration::where('webinar_id', $webinar->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing && $existing->isRegistered()) {
            return back()->with('error', 'You are already registered for this webinar.');
        }

        WebinarRegistration::updateOrCreate(
            [
                'webinar_id' => $webinar->id,
                'user_id' => $user->id,
            ],
            [
                'status' => 'registered',
                'role_name' => $user->role->name ?? null,
                'joined_at' => null,
            ]
        );

        $this->sendWebinarRegistrationEmail($webinar, $user);

        return back()->with('success', 'You have joined this webinar.');
    }

    /**
     * Cancel sponsor registration for a webinar.
     */
    public function cancelWebinar(Webinar $webinar)
    {
        $user = Auth::user();

        $registration = WebinarRegistration::where('webinar_id', $webinar->id)
            ->where('user_id', $user->id)
            ->where('status', 'registered')
            ->first();

        if (!$registration) {
            return back()->with('error', 'No active registration found for this webinar.');
        }

        if ($webinar->scheduled_at && $webinar->scheduled_at->isPast()) {
            return back()->with('error', 'Cannot cancel past webinars.');
        }

        $registration->update(['status' => 'cancelled']);

        return back()->with('success', 'Your webinar registration has been cancelled.');
    }

    private function sendWebinarRegistrationEmail(Webinar $webinar, $user): void
    {
        try {
            Mail::to($user->email)->send(new WebinarRegistrationConfirmation($webinar, $user));
        } catch (\Throwable $e) {
            Log::warning('Failed to send webinar registration email', [
                'user_id' => $user->id ?? null,
                'webinar_id' => $webinar->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
