<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\ProgramRegistration;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Program;
use App\Models\SponsorshipProgram;
use App\Models\Message;
use App\Models\Webinar;
use App\Models\WebinarRegistration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\WebinarRegistrationConfirmation;

class PatientController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $patient = Patient::where('user_id', $user->id)->first();

        if (!$patient) {
            // Create patient record if doesn't exist
            $patient = Patient::create(['user_id' => $user->id]);
        }

        // Get patient's applications
        $applications = Application::where('patient_id', $patient->id)
            ->with('program')
            ->orderByDesc('submission_date')
            ->orderByDesc('created_at')
            ->get();

        // Get application statistics
        $totalApplications = $applications->count();
        $pendingApplications = $applications->filter(
            fn($app) => strcasecmp($app->status ?? '', 'Pending') === 0
        )->count();
        $approvedApplications = $applications->filter(
            fn($app) => strcasecmp($app->status ?? '', 'Approved') === 0
        )->count();
        $rejectedApplications = $applications->filter(
            fn($app) => strcasecmp($app->status ?? '', 'Rejected') === 0
        )->count();

        // Get last application date
        $latestApplication = $applications->first();
        $latestSubmittedAt = $latestApplication
            ? ($latestApplication->submission_date ?? $latestApplication->created_at)
            : null;
        $lastApplicationDate = $latestSubmittedAt
            ? Carbon::parse($latestSubmittedAt)->format('d/m/Y')
            : 'N/A';
        $inReviewApplications = $applications->filter(function ($app) {
            $status = strtolower((string) $app->status);
            return in_array($status, ['pending', 'under review', 'under_review'], true);
        })->count();
        $latestApplicationStatus = $latestApplication ? ($latestApplication->status ?: 'N/A') : 'N/A';
        $latestApplicationCode = $latestApplication
            ? ($latestApplication->code ?: ('APP-' . str_pad((string) $latestApplication->id, 6, '0', STR_PAD_LEFT)))
            : null;
        $latestProgramTitle = optional(optional($latestApplication)->program)->title;

        // Prepare stats for the view
        $stats = [
            'total_applications' => $totalApplications,
            'pending_applications' => $pendingApplications,
            'approved_applications' => $approvedApplications,
            'rejected_applications' => $rejectedApplications,
            'last_application_date' => $lastApplicationDate,
            'in_review_applications' => $inReviewApplications,
            'latest_application_status' => $latestApplicationStatus,
            'latest_application_id' => $latestApplication->id ?? null,
            'latest_application_code' => $latestApplicationCode,
            'latest_program_title' => $latestProgramTitle,
        ];

        // Get available programs
        $availablePrograms = SponsorshipProgram::where('end_date', '>', now())
            ->orWhereNull('end_date')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('patient.dashboard', compact(
            'applications',
            'stats',
            'availablePrograms',
            'patient'
        ));
    }

    public function myApplications()
    {
        $user = Auth::user();

        $baseQuery = ProgramRegistration::where('user_id', $user->id)->with('program');

        $registrations = (clone $baseQuery)
            ->orderByDesc('created_at')
            ->paginate(10);

        $totalRegistrations = (clone $baseQuery)->count();
        $pendingRegistrations = (clone $baseQuery)->where('status', ProgramRegistration::STATUS_PENDING)->count();
        $approvedRegistrations = (clone $baseQuery)->where('status', ProgramRegistration::STATUS_APPROVED)->count();
        $rejectedRegistrations = (clone $baseQuery)->where('status', ProgramRegistration::STATUS_REJECTED)->count();

        return view('patient.my_application', compact(
            'registrations',
            'totalRegistrations',
            'pendingRegistrations',
            'approvedRegistrations',
            'rejectedRegistrations'
        ));
    }

    public function programsAndAids()
    {
        $programs = SponsorshipProgram::where('end_date', '>', now())
            ->orWhereNull('end_date')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Programs & Aids: use effective status (date-based) so applications go live on opening date
        $upcomingPrograms = Program::effectiveUpcoming()
            ->orderBy('application_start_date')
            ->orderBy('event_date')
            ->get();
        $ongoingPrograms = Program::effectiveOngoing()
            ->orderBy('application_end_date')
            ->orderBy('event_date')
            ->get();

        return view('patient.programs_and_aids', compact('programs', 'upcomingPrograms', 'ongoingPrograms'));
    }

    public function patientChats(Request $request)
    {
        $user = Auth::user();
        $patient = Patient::firstOrCreate(['user_id' => $user->id]);

        $applicationCaseManagers = Application::where('patient_id', $patient->id)
            ->whereNotNull('reviewer_id')
            ->pluck('reviewer_id');

        $registrationCaseManagers = ProgramRegistration::where('user_id', $user->id)
            ->whereNotNull('assigned_case_manager_id')
            ->pluck('assigned_case_manager_id');

        $caseManagerIds = $applicationCaseManagers
            ->merge($registrationCaseManagers)
            ->unique()
            ->values();

        $contacts = User::query()
            ->whereIn('id', $caseManagerIds)
            ->with(['profile', 'role'])
            ->get();

        $adminContacts = User::query()
            ->whereHas('role', fn ($query) => $query->where('name', 'admin'))
            ->with(['profile', 'role'])
            ->get();

        $contacts = $contacts->merge($adminContacts)->unique('id')->values();

        if ($contacts->isEmpty()) {
            return view('patient.patient_chats', [
                'contacts' => collect(),
                'activeContact' => null,
                'activeContactId' => null,
                'messagesPayload' => [],
            ]);
        }

        $activeContactId = (int) $request->query('contact', $contacts->first()->id);
        $activeContact = $contacts->firstWhere('id', $activeContactId) ?? $contacts->first();

        Message::markThreadAsRead($user->id, $activeContact->id);

        $messagesPayload = Message::betweenUsers($user->id, $activeContact->id)
            ->with(['sender.profile', 'receiver.profile'])
            ->orderBy('sent_at')
            ->limit(200)
            ->get()
            ->map->toFrontendPayload()
            ->values();

        $contactsPayload = $contacts->map(function (User $contact) use ($user) {
            $latestMessage = Message::betweenUsers($user->id, $contact->id)
                ->latest('sent_at')
                ->first();

            $unreadCount = Message::betweenUsers($user->id, $contact->id)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();

            return [
                'id' => $contact->id,
                'name' => optional($contact->profile)->full_name ?? $contact->email,
                'avatar_url' => $contact->avatar_url,
                'latest_message' => $latestMessage?->content,
                'latest_at' => optional($latestMessage?->sent_at)->format('H:i'),
                'unread_count' => $unreadCount,
                'fetch_url' => route('chat.messages.index', $contact),
                'send_url' => route('chat.messages.store', $contact),
            ];
        })->values();

        return view('patient.patient_chats', [
            'contacts' => $contactsPayload,
            'activeContact' => [
                'id' => $activeContact->id,
                'name' => optional($activeContact->profile)->full_name ?? $activeContact->email,
                'avatar_url' => $activeContact->avatar_url,
            ],
            'activeContactId' => $activeContact->id,
            'messagesPayload' => $messagesPayload,
        ]);
    }

    public function faq()
    {
        return view('patient.faq');
    }

    public function invoices()
    {
        $user = Auth::user();
        $patient = Patient::where('user_id', $user->id)->first();

        if (!$patient) {
            $patient = Patient::create(['user_id' => $user->id]);
        }

        // Get approved applications that could have invoices
        $approvedApplications = Application::where('patient_id', $patient->id)
            ->where('status', 'Approved')
            ->with('program')
            ->orderBy('decision_date', 'desc')
            ->get();

        $invoices = Invoice::whereHas('application', function ($query) {
            $query->where('patient_id', Auth::user()->patient->id);
        })->with('application')->get();

        return view('patient.invoices', compact('approvedApplications', 'invoices'));
    }

    public function setting()
    {
        $user = Auth::user();
        $patient = Patient::where('user_id', $user->id)->with('user.profile')->first();

        return view('patient.setting', compact('patient'));
    }

    public function profile()
    {
        $user = Auth::user();
        $patient = Patient::where('user_id', $user->id)->with('user.profile')->first();

        if (!$patient) {
            $patient = Patient::create(['user_id' => $user->id]);
        }

        return view('patient.profile', compact('patient'));
    }

    public function editApplication($id = null)
    {
        $user = Auth::user();
        $patient = Patient::where('user_id', $user->id)->first();

        if (!$patient) {
            $patient = Patient::create(['user_id' => $user->id]);
        }

        $application = null;
        if ($id) {
            $application = Application::where('id', $id)
                ->where('patient_id', $patient->id)
                ->with('program')
                ->first();
        }

        $programs = SponsorshipProgram::where('end_date', '>', now())
            ->orWhereNull('end_date')
            ->get();

        return view('patient.edit_application', compact('application', 'programs'));
    }

    public function viewApplication($id)
    {
        $user = Auth::user();
        $patient = Patient::where('user_id', $user->id)->first();

        $application = Application::where('id', $id)
            ->where('patient_id', $patient->id)
            ->with(['program', 'reviewer.profile', 'documents'])
            ->firstOrFail();

        return view('patient.view_application', compact('application'));
    }

    /**
     * List webinars for patients.
     */
    public function webinars()
    {
        $user = Auth::user();

        $webinars = Webinar::query()
            ->whereIn('audience', ['both', 'patient'])
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

        return view('patient.webinars', compact('webinars'));
    }

    /**
     * Register a patient for a webinar.
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
     * Cancel a patient's webinar registration.
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
