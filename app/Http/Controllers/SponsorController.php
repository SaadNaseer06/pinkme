<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Event;
use App\Models\SponsorReview;
use App\Models\Sponsorship;
use App\Models\SponsorshipProgram;
use App\Models\Program;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

        $events = Event::with([

            'sponsors' => fn($query) => $query->with(['profile', 'sponsorDetail'])

                ->withPivot('amount'),

            'sponsorships',

        ])

            ->orderBy('date')

            ->get()

            ->map(function (Event $event) {

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

                $event->primary_sponsor = $event->sponsors->first();

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

        $programs = SponsorshipProgram::orderBy('start_date')

            ->orderByDesc('created_at')

            ->get()

            ->map(function (SponsorshipProgram $program) {

                $goal = (float) ($program->goal_amount ?? 0);

                $raised = (float) ($program->raised_amount ?? 0);

                $program->remaining_amount = max($goal - $raised, 0);

                $program->progress_percent = $goal > 0 ? round(($raised / $goal) * 100) : 0;

                return $program;
            });

        $today = Carbon::today();

        $upcomingPrograms = $programs->filter(function (SponsorshipProgram $program) use ($today) {

            if ($program->start_date) {

                return Carbon::parse($program->start_date)->isAfter($today);
            }

            return false;
        })->values();

        $ongoingPrograms = $programs->reject(function (SponsorshipProgram $program) use ($today) {

            $startDate = $program->start_date ? Carbon::parse($program->start_date) : null;

            $endDate = $program->end_date ? Carbon::parse($program->end_date) : null;

            if ($startDate && $startDate->isAfter($today)) {

                return true;
            }

            if ($endDate && $endDate->isBefore($today)) {

                return true;
            }

            return false;
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

        $programs = Program::withSum('sponsorships as raised_amount', 'amount')

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

                $redirectRoute = 'sponsor.becomeASponsor';
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
}
