<?php

namespace App\Http\Controllers;

use App\Mail\WebinarRegistrationConfirmation;
use App\Models\Application;
use App\Models\Invoice;
use App\Models\Message;
use App\Models\Patient;
use App\Models\Program;
use App\Models\ProgramRegistration;
use App\Models\SponsorshipProgram;
use App\Models\User;
use App\Models\Webinar;
use App\Models\WebinarRegistration;
use App\Support\ProgramApplicationCapacity;
use App\Support\TransactionalMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PatientController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $patient = Patient::where('user_id', $user->id)->first();

        if (! $patient) {
            // Create patient record if doesn't exist
            $patient = Patient::create(['user_id' => $user->id]);
        }

        // Legacy patient applications
        $applications = Application::where('patient_id', $patient->id)
            ->with('program')
            ->orderByDesc('submission_date')
            ->orderByDesc('created_at')
            ->get();

        // Program registrations (newer financial assistance flow)
        $programRegistrations = ProgramRegistration::where('user_id', $user->id)
            ->with('program')
            ->orderByDesc('created_at')
            ->get();

        // Get application statistics
        $totalApplications = $applications->count();
        $pendingApplications = $applications->filter(
            fn ($app) => strcasecmp($app->status ?? '', 'Pending') === 0
        )->count();
        $approvedApplications = $applications->filter(
            fn ($app) => strcasecmp($app->status ?? '', 'Approved') === 0
        )->count();
        $rejectedApplications = $applications->filter(
            fn ($app) => strcasecmp($app->status ?? '', 'Rejected') === 0
        )->count();

        // Get last submission date across both flows
        $latestApplication = $applications->first();
        $latestApplicationSubmittedAt = $latestApplication
            ? ($latestApplication->submission_date ?? $latestApplication->created_at)
            : null;
        $latestRegistration = $programRegistrations->first();
        $latestRegistrationSubmittedAt = $latestRegistration?->created_at;

        $latestSubmittedAt = collect([$latestApplicationSubmittedAt, $latestRegistrationSubmittedAt])
            ->filter()
            ->map(fn ($dt) => Carbon::parse($dt))
            ->sortDesc()
            ->first();

        $lastApplicationDate = $latestSubmittedAt ? $latestSubmittedAt->format('d/m/Y') : 'N/A';

        $inReviewApplications = $applications->filter(function ($app) {
            $status = strtolower((string) $app->status);

            return in_array($status, ['pending', 'under review', 'under_review'], true);
        })->count();
        $inReviewRegistrations = $programRegistrations->filter(function ($registration) {
            $status = strtolower((string) $registration->status);

            return in_array($status, [ProgramRegistration::STATUS_PENDING, ProgramRegistration::STATUS_PENDING_FINANCE], true);
        })->count();
        $inReviewTotal = $inReviewApplications + $inReviewRegistrations;

        $latestIsApplication = $latestApplicationSubmittedAt
            && (! $latestRegistrationSubmittedAt || Carbon::parse($latestApplicationSubmittedAt)->gte(Carbon::parse($latestRegistrationSubmittedAt)));

        $latestStatus = $latestIsApplication
            ? ($latestApplication?->status ?: 'N/A')
            : ($latestRegistration?->status ?: 'N/A');

        $latestCode = $latestIsApplication
            ? ($latestApplication ? ($latestApplication->code ?: ('APP-'.str_pad((string) $latestApplication->id, 6, '0', STR_PAD_LEFT))) : null)
            : ($latestRegistration ? ('REG-'.str_pad((string) $latestRegistration->id, 6, '0', STR_PAD_LEFT)) : null);

        $latestProgramTitle = $latestIsApplication
            ? optional(optional($latestApplication)->program)->title
            : optional(optional($latestRegistration)->program)->title;
        $latestBreastCancerStage = $latestRegistration?->breast_cancer_stage;

        $latestItemType = $latestIsApplication ? 'application' : 'registration';
        $latestItemId = $latestIsApplication
            ? ($latestApplication->id ?? null)
            : ($latestRegistration->id ?? null);
        $hasSubmission = (bool) $latestItemId;

        // Prepare stats for the view
        $stats = [
            'total_applications' => $totalApplications,
            'pending_applications' => $pendingApplications,
            'approved_applications' => $approvedApplications,
            'rejected_applications' => $rejectedApplications,
            'last_application_date' => $lastApplicationDate,
            'in_review_applications' => $inReviewTotal,
            'latest_application_status' => $latestStatus,
            'latest_application_id' => $latestItemId,
            'latest_application_code' => $latestCode,
            'latest_program_title' => $latestProgramTitle,
            'latest_breast_cancer_stage' => $latestBreastCancerStage,
            'latest_item_type' => $latestItemType,
            'has_submission' => $hasSubmission,
        ];

        // Get available programs
        $availablePrograms = SponsorshipProgram::where('end_date', '>', now())
            ->orWhereNull('end_date')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $ongoingPrograms = Program::effectiveOngoing()->get();
        $financialAssistanceClosed = ProgramApplicationCapacity::financialAssistanceClosed($ongoingPrograms);

        return view('patient.dashboard', compact(
            'applications',
            'stats',
            'availablePrograms',
            'patient',
            'financialAssistanceClosed'
        ));
    }

    public function myApplications(Request $request)
    {
        $user = Auth::user();

        $baseQuery = ProgramRegistration::where('user_id', $user->id)->with('program');

        $totalRegistrations = (clone $baseQuery)->count();
        $pendingRegistrations = (clone $baseQuery)->where('status', ProgramRegistration::STATUS_PENDING)->count();
        $approvedRegistrations = (clone $baseQuery)->whereIn('status', [
            ProgramRegistration::STATUS_APPROVED,
            ProgramRegistration::STATUS_SHIPPED,
        ])->count();
        $rejectedRegistrations = (clone $baseQuery)->where('status', ProgramRegistration::STATUS_REJECTED)->count();

        $validTabs = ['all', ProgramRegistration::STATUS_PENDING, ProgramRegistration::STATUS_APPROVED, ProgramRegistration::STATUS_REJECTED];
        $activeTab = strtolower((string) $request->query('status', 'all'));
        if (! in_array($activeTab, $validTabs, true)) {
            $activeTab = 'all';
        }

        $listQuery = clone $baseQuery;
        if ($activeTab === ProgramRegistration::STATUS_APPROVED) {
            $listQuery->whereIn('status', [
                ProgramRegistration::STATUS_APPROVED,
                ProgramRegistration::STATUS_SHIPPED,
            ]);
        } elseif ($activeTab !== 'all') {
            $listQuery->where('status', $activeTab);
        }

        $registrations = $listQuery
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->only('status'));

        return view('patient.my_application', compact(
            'registrations',
            'totalRegistrations',
            'pendingRegistrations',
            'approvedRegistrations',
            'rejectedRegistrations',
            'activeTab'
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
            ->orderByDesc('created_at')
            ->orderBy('application_start_date')
            ->orderBy('event_date')
            ->get();
        $ongoingPrograms = Program::effectiveOngoing()
            ->orderByDesc('created_at')
            ->orderBy('application_end_date')
            ->orderBy('event_date')
            ->get();

        $financialAssistanceClosed = ProgramApplicationCapacity::financialAssistanceClosed($ongoingPrograms);
        $programsAtCapacityIds = ProgramApplicationCapacity::programIdsAtCapacity($ongoingPrograms);

        return view('patient.programs_and_aids', compact(
            'programs',
            'upcomingPrograms',
            'ongoingPrograms',
            'financialAssistanceClosed',
            'programsAtCapacityIds'
        ));
    }

    public function patientChats(Request $request)
    {
        $user = Auth::user();

        if (! Patient::userHasAssignedCaseManager($user)) {
            return redirect()
                ->route('patient.myApplication')
                ->with('warning', __('Chat is available after a case manager is assigned to your application.'));
        }

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

        $contactSummaries = Message::contactSummariesForUser($user->id, $contacts->pluck('id')->all());
        $latestByContact = $contactSummaries['latest_by_contact'];
        $unreadByContact = $contactSummaries['unread_by_contact'];

        $contactsPayload = $contacts->map(function (User $contact) use ($latestByContact, $unreadByContact) {
            $latestMessage = $latestByContact[$contact->id] ?? null;
            $unreadCount = $unreadByContact[$contact->id] ?? 0;

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

        if (! $patient) {
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

        if (! $patient) {
            $patient = Patient::create(['user_id' => $user->id]);
        }

        return view('patient.profile', compact('patient'));
    }

    public function editApplication($id = null)
    {
        $user = Auth::user();
        $patient = Patient::where('user_id', $user->id)->first();

        if (! $patient) {
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
                'registrations as attendee_count' => fn ($query) => $query->where('status', 'registered'),
            ])
            ->with(['registrations' => fn ($query) => $query->where('user_id', $user->id)])
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

        if (! $webinar->isJoinable()) {
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

        if (! $registration) {
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
            TransactionalMail::send($user->email, new WebinarRegistrationConfirmation($webinar, $user));
        } catch (\Throwable $e) {
            Log::warning('Failed to send webinar registration email', [
                'user_id' => $user->id ?? null,
                'webinar_id' => $webinar->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
