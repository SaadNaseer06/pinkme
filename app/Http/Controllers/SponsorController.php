<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Event;
use App\Models\EventSponsorship;
use App\Models\SponsorReview;
use App\Models\User;
use App\Models\Webinar;
use App\Models\WebinarRegistration;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Mail\WebinarRegistrationConfirmation;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class SponsorController extends Controller
{

    public function dashboard()

    {

        $user = Auth::user();

        $events = Event::withSum('confirmedSponsorships as raised_amount', 'amount')
            ->orderByDesc('date')
            ->get();

        $sponsorships = EventSponsorship::where('sponsor_id', $user->id)
            ->with('event')
            ->orderByDesc('registered_at')
            ->orderByDesc('created_at')
            ->get();

        $totalEvents = $events->count();
        $totalSponsored = $sponsorships->sum('amount');

        $today = Carbon::today();
        $activeEvents = $events->filter(function (Event $event) use ($today) {
            if (in_array($event->status, ['completed', 'cancelled'], true)) {
                return false;
            }

            if ($event->date) {
                $eventDate = $event->date instanceof Carbon ? $event->date : Carbon::parse($event->date);
                if ($eventDate->isFuture()) {
                    return false;
                }
            }

            if ($event->registration_deadline) {
                $deadline = $event->registration_deadline instanceof Carbon
                    ? $event->registration_deadline
                    : Carbon::parse($event->registration_deadline);
                return $deadline->isSameDay($today) || $deadline->isFuture();
            }

            return true;
        })->count();

        $myContributions = $sponsorships->count();

        $baseSponsorQuery = User::query()

            ->whereHas('role', fn($query) => $query->where('name', 'sponsor'))

            ->with([

                'profile',

                'sponsorDetail',

                'eventSponsorships' => fn($query) => $query->latest('registered_at')->latest('created_at')->limit(1)->with('event'),

            ])

            ->withCount('eventSponsorships')
            ->withSum('eventSponsorships as total_contribution', 'amount')
            ->withMin('eventSponsorships as first_contribution_date', 'created_at')
            ->withMax('eventSponsorships as last_contribution_date', 'created_at');

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

        $monthlyData = EventSponsorship::where('sponsor_id', $user->id)

            ->select(

                DB::raw('MONTH(COALESCE(registered_at, created_at)) as month'),

                DB::raw('YEAR(COALESCE(registered_at, created_at)) as year'),

                DB::raw('SUM(amount) as total')

            )

            ->groupBy('year', 'month')

            ->orderByDesc('year')

            ->orderByDesc('month')

            ->limit(12)

            ->get();

        $stats = $this->buildSponsorStats($user);

        return view('sponsor.dashboard', compact(
            'events',
            'sponsorships',
            'totalEvents',
            'totalSponsored',
            'activeEvents',
            'myContributions',
            'recentApplications',
            'monthlyData',
            'individualSponsors',
            'companySponsors',
            'stats'
        ));
    }

    public function events(Request $request)

    {
        $user = Auth::user();
        $selectedType = $request->query('type');
        if (!in_array($selectedType, ['full', 'flexible'], true)) {
            $selectedType = null;
        }

        $eventsQuery = Event::with([
            'sponsors' => fn($query) => $query->with(['profile', 'sponsorDetail'])
                ->withPivot(['amount', 'registration_status', 'registered_at', 'confirmed_at']),
            'sponsorships' => function($query) {
                // Explicitly ensure we're using the event_sponsorships table
                return $query;
            }
        ])
            ->orderBy('date');

        if ($selectedType) {
            $eventsQuery->where('payment_type', $selectedType);
        }

        $events = $eventsQuery
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

        $stats = $this->buildSponsorStats($user);

        return view('sponsor.events', compact('events', 'ongoingEvents', 'upcomingEvents', 'pastEvents', 'selectedType', 'stats'));
    }

    public function sponsorships()

    {
        return redirect()->route('sponsor.events', ['type' => 'flexible']);
    }

    public function becomeASponsor()
    {
        return redirect()->route('sponsor.events', ['type' => 'full']);
    }

    public function storeSponsorship(Request $request)
    {
        return redirect()
            ->route('sponsor.events')
            ->with('error', 'Sponsorships are handled through events now.');
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
        $baseQuery = EventSponsorship::where('sponsor_id', $user->id);
        $payments = (clone $baseQuery)
            ->with('event')
            ->orderByDesc('registered_at')
            ->orderByDesc('created_at')
            ->paginate(15);
        $totalAmount = (clone $baseQuery)->sum('amount');
        $eventCount = (clone $baseQuery)
            ->get(['event_id'])
            ->pluck('event_id')
            ->unique()
            ->count();

        $latestPayment = (clone $baseQuery)
            ->orderByDesc('registered_at')
            ->orderByDesc('created_at')
            ->first();

        $totals = [
            'total_amount' => (float) $totalAmount,
            'programs_supported' => $eventCount,
            'latest_contribution' => $latestPayment?->amount,
            'latest_date' => $latestPayment?->registered_at
                ? Carbon::parse($latestPayment->registered_at)
                : ($latestPayment?->created_at ? Carbon::parse($latestPayment->created_at) : null),
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
     * Register sponsor for an event — create Stripe Checkout Session and redirect to payment
     */
    public function registerForEvent(Request $request, Event $event)
    {
        $user = Auth::user();
        

        if (!$event->isRegistrationOpen()) {
            return back()->with('error', 'Registration for this event is no longer open.');
        }

        if ($event->isSponsorRegistered($user->id)) {
            return back()->with('error', 'You are already registered for this event.');
        }

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        if ($event->payment_type === 'full' && $event->sponsorships()
            ->whereIn('registration_status', ['pending', 'confirmed'])
            ->exists()) {
            return back()->with('error', 'This event already has a sponsor registration.');
        }

        // Full sponsorship: use remaining funding amount (no user amount choice)
        if ($event->payment_type === 'full') {
            if (!$event->funding_goal || $event->remaining_funding <= 0) {
                return back()->with('error', 'This event is already fully funded.');
            }
            $data['amount'] = (float) $event->remaining_funding;
        } else {
            // Flexible: validate amount against remaining if there is a goal
            if ($event->funding_goal && $data['amount'] > $event->remaining_funding) {
                return back()->with('error', 'Sponsorship amount exceeds remaining funding needed ($' . number_format($event->remaining_funding, 2) . ').');
            }
        }

        $amountCents = (int) round($data['amount'] * 100);
        if ($amountCents < 50) {
            return back()->with('error', 'Minimum payment amount is $0.50.');
        }

        $stripeSecret = config('services.stripe.secret');
        if (empty($stripeSecret)) {
            Log::warning('Stripe secret not configured; cannot process event sponsorship payment.');
            return back()->withInput()->with('error', 'Payment is not configured. Please contact the administrator.');
        }

        // Do NOT create event_sponsorships record here — only after payment succeeds (in success callback)
        Stripe::setApiKey($stripeSecret);
        $successUrl = rtrim(route('sponsor.events.registration.success'), '/') . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = route('sponsor.events.registration.cancel', ['event_id' => $event->id]);
        $messageForMetadata = $data['message'] ?? '';
        if (strlen($messageForMetadata) > 500) {
            $messageForMetadata = substr($messageForMetadata, 0, 500);
        }

        try {
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => config('services.stripe.currency', 'usd'),
                        'product_data' => [
                            'name' => 'Event sponsorship: ' . $event->title,
                            'description' => 'Sponsorship amount for ' . $event->title,
                        ],
                        'unit_amount' => $amountCents,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => [
                    'event_id' => (string) $event->id,
                    'sponsor_id' => (string) $user->id,
                    'amount' => (string) $data['amount'],
                    'message' => $messageForMetadata,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Stripe Checkout Session create failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Unable to start payment. Please try again or contact support.');
        }

        return redirect($session->url);
    }

    /**
     * Success URL after Stripe Checkout — create event_sponsorship only after payment is confirmed
     */
    public function eventRegistrationSuccess(Request $request)
    {
        $sessionId = trim((string) $request->query('session_id'));
        if ($sessionId === '') {
            return redirect()->route('sponsor.events')->with('error', 'Invalid session.');
        }

        $stripeSecret = config('services.stripe.secret');
        if (empty($stripeSecret)) {
            return redirect()->route('sponsor.events')->with('error', 'Unable to verify payment.');
        }

        Stripe::setApiKey($stripeSecret);
        try {
            $stripeSession = StripeSession::retrieve($sessionId);
        } catch (\Throwable $e) {
            Log::error('Stripe session retrieve failed on success callback', [
                'message' => $e->getMessage(),
                'session_id_prefix' => substr($sessionId, 0, 12),
            ]);
            return redirect()->route('sponsor.events')->with('error', 'Unable to verify payment.');
        }

        if ($stripeSession->payment_status !== 'paid') {
            return redirect()->route('sponsor.events')->with('error', 'Payment was not completed. No registration was created.');
        }

        $metadata = $stripeSession->metadata ?? (object) [];
        $eventId = isset($metadata->event_id) ? (int) $metadata->event_id : 0;
        $sponsorId = isset($metadata->sponsor_id) ? (int) $metadata->sponsor_id : 0;
        $amount = isset($metadata->amount) ? (float) $metadata->amount : 0;
        $message = isset($metadata->message) ? (string) $metadata->message : null;

        if (!$eventId || !$sponsorId || $amount < 0.5) {
            return redirect()->route('sponsor.events')->with('error', 'Invalid payment session.');
        }

        // Idempotent: if we already created a sponsorship for this session, show success
        $existing = EventSponsorship::where('stripe_checkout_session_id', $sessionId)->first();
        if ($existing) {
            return redirect()->route('sponsor.events.show', $existing->event_id)->with('success', 'Your payment was already confirmed.');
        }

        $event = Event::find($eventId);
        if (!$event) {
            return redirect()->route('sponsor.events')->with('error', 'Event not found.');
        }

        $sponsorship = $event->sponsorships()->create([
            'sponsor_id' => $sponsorId,
            'amount' => $amount,
            'registration_status' => 'confirmed',
            'payment_status' => 'paid',
            'message' => $message ?: null,
            'registered_at' => now(),
            'confirmed_at' => now(),
            'stripe_checkout_session_id' => $sessionId,
        ]);

        $sponsor = $sponsorship->sponsor;
        if ($sponsor && $sponsor->profile) {
            $sponsor->profile->update(['status' => 1]);
        }

        return redirect()->route('sponsor.events.show', $event->id)->with('success', 'Thank you! Your payment was successful and your sponsorship is confirmed.');
    }

    /**
     * Cancel URL when user abandons Stripe Checkout
     */
    public function eventRegistrationCancel(Request $request)
    {
        $eventId = $request->query('event_id');
        if ($eventId) {
            return redirect()->route('sponsor.events.show', $eventId)->with('info', 'Payment was cancelled. You can try again when you are ready.');
        }
        return redirect()->route('sponsor.events')->with('info', 'Payment was cancelled.');
    }

    /**
     * Confirm sponsorship and set sponsor profile status to approved (1) after successful payment
     */
    private function confirmSponsorshipAfterPayment(EventSponsorship $sponsorship): void
    {
        $sponsorship->update([
            'registration_status' => 'confirmed',
            'payment_status' => 'paid',
            'confirmed_at' => now(),
        ]);

        $sponsor = $sponsorship->sponsor;
        if ($sponsor && $sponsor->profile) {
            $sponsor->profile->update(['status' => 1]);
        }
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
        
        // Do not allow cancellation of paid sponsorships
        if ($sponsorship->payment_status === 'paid') {
            return back()->with('error', 'Cannot cancel a paid sponsorship. Please contact support if you need assistance.');
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
        
        $stats = $this->buildSponsorStats($user);

        return view('sponsor.my-event-registrations', compact(
            'registrations',
            'confirmed',
            'pending', 
            'cancelled',
            'totalAmount',
            'confirmedAmount',
            'pendingAmount',
            'stats'
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

    private function buildSponsorStats(User $user): array
    {
        $today = Carbon::today();

        $activeSponsorships = EventSponsorship::where('sponsor_id', $user->id)
            ->whereIn('registration_status', ['pending', 'confirmed'])
            ->count();

        $ongoingEvents = Event::query()
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->whereDate('date', '<=', $today)
            ->where(function ($query) use ($today) {
                $query->whereNull('registration_deadline')
                    ->orWhereDate('registration_deadline', '>=', $today);
            })
            ->count();

        $upcomingEvents = Event::query()
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->whereDate('date', '>', $today)
            ->count();

        return [
            'active_sponsorships' => $activeSponsorships,
            'ongoing_events' => $ongoingEvents,
            'support_programs' => Event::count(),
            'upcoming_events' => $upcomingEvents,
        ];
    }
}
