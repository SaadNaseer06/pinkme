<?php

namespace App\Http\Controllers;

use App\Mail\ProgramRegistrationStatus;
use App\Models\Application;
use App\Models\ApplicationMissingRequest;
use App\Models\Message;
use App\Models\Patient;
use App\Models\ProgramRegistration;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\UserProfile;
use App\Services\FinanceNotificationService;
use App\Support\BillingPaymentLinks;
use App\Support\PatientApplicationNotifications;
use App\Support\ProgramRegistrationNotifiers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CaseManagerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        return view('case_manager.dashboard', [
            'cm' => $this->buildCaseManagerDashboardData($user),
        ]);
    }
    // public function dashboard()
    // {
    //     $user = Auth::user();

    //     // Get applications assigned to this case manager
    //     $assignedApplications = Application::where('reviewer_id', $user->id)
    //         ->with(['patient.user.profile', 'program'])
    //         ->orderBy('submission_date', 'desc')
    //         ->get();

    //     // Calculate statistics
    //     $totalAssigned = $assignedApplications->count();
    //     $pendingReview = $assignedApplications->where('status', 'Pending')->count();
    //     $approved = $assignedApplications->where('status', 'Approved')->count();
    //     $rejected = $assignedApplications->where('status', 'Rejected')->count();

    //     // Get all patients for overview
    //     $totalPatients = Patient::count();

    //     // Get recent applications for review
    //     $recentApplications = Application::where('reviewer_id', $user->id)
    //         ->with(['patient.user.profile', 'program'])
    //         ->orderBy('submission_date', 'desc')
    //         ->limit(5)
    //         ->get();

    //     // Monthly application review data for charts
    //     $monthlyReviews = Application::where('reviewer_id', $user->id)
    //         ->whereNotNull('decision_date')
    //         ->select(
    //             DB::raw('MONTH(decision_date) as month'),
    //             DB::raw('COUNT(*) as count')
    //         )
    //         ->whereYear('decision_date', Carbon::now()->year)
    //         ->groupBy('month')
    //         ->pluck('count', 'month')
    //         ->toArray();

    //     // Fill missing months with 0
    //     $chartData = [];
    //     for ($i = 1; $i <= 12; $i++) {
    //         $chartData[] = $monthlyReviews[$i] ?? 0;
    //     }

    //     return view('case_manager.dashboard', compact(
    //         'assignedApplications',
    //         'totalAssigned',
    //         'pendingReview',
    //         'approved',
    //         'rejected',
    //         'totalPatients',
    //         'recentApplications',
    //         'chartData'
    //     ));
    // }

    public function myApplication()
    {
        $user = Auth::user();

        // Get applications assigned to this case manager
        $applications = Application::where('reviewer_id', $user->id)
            ->with(['patient.user.profile', 'program', 'documents'])
            ->orderBy('submission_date', 'desc')
            ->paginate(15);

        return view('case_manager.my_application', [
            'applications' => $applications,
            'cm' => $this->buildCaseManagerDashboardData($user),
        ]);
    }

    public function programRegistrations(Request $request)
    {
        $selectedStatus = strtolower((string) $request->query('status', ProgramRegistration::STATUS_PENDING));
        $validStatuses = [
            'all',
            ProgramRegistration::STATUS_PENDING,
            ProgramRegistration::STATUS_PENDING_FINANCE,
            ProgramRegistration::STATUS_APPROVED,
            ProgramRegistration::STATUS_REJECTED,
        ];

        if (! in_array($selectedStatus, $validStatuses, true)) {
            $selectedStatus = ProgramRegistration::STATUS_PENDING;
        }

        $visibleBase = $this->programRegistrationsVisibleToCaseManagerQuery(Auth::id());

        $query = (clone $visibleBase)
            ->with(['program:id,title', 'user:id,email'])
            ->orderByDesc('created_at');

        if ($selectedStatus === ProgramRegistration::STATUS_PENDING_FINANCE) {
            $query->where('status', ProgramRegistration::STATUS_PENDING_FINANCE)
                ->where('assigned_case_manager_id', Auth::id());
        } elseif ($selectedStatus !== 'all') {
            $query->where('status', $selectedStatus);
        }

        $registrations = $query
            ->paginate(15)
            ->appends($request->query());

        $counts = [
            'pending' => (clone $visibleBase)
                ->where('status', ProgramRegistration::STATUS_PENDING)
                ->count(),
            'pending_finance' => ProgramRegistration::where('assigned_case_manager_id', Auth::id())
                ->where('status', ProgramRegistration::STATUS_PENDING_FINANCE)
                ->count(),
            'approved' => ProgramRegistration::where('assigned_case_manager_id', Auth::id())
                ->where('status', ProgramRegistration::STATUS_APPROVED)
                ->count(),
            'rejected' => ProgramRegistration::where('assigned_case_manager_id', Auth::id())
                ->where('status', ProgramRegistration::STATUS_REJECTED)
                ->count(),
            'all' => (clone $visibleBase)->count(),
        ];

        $payload = [
            'registrations' => $registrations,
            'selectedStatus' => $selectedStatus,
            'counts' => $counts,
        ];

        if ($request->ajax()) {
            return view('case_manager.program_registrations._list_fragment', [
                'registrations' => $registrations,
            ]);
        }

        return view('case_manager.program_registrations.index', $payload);
    }

    public function showProgramRegistration(ProgramRegistration $registration)
    {
        $this->assertCaseManagerMayAccessProgramRegistration($registration);

        $registration->load(['program', 'user', 'reviewer', 'assignedCaseManager']);

        return view('case_manager.program_registrations.show', [
            'registration' => $registration,
        ]);
    }

    /**
     * Assigned case manager: save payment portal links provided by the applicant.
     */
    public function updateBillingPaymentLinks(Request $request, ProgramRegistration $registration)
    {
        $this->assertCaseManagerMayAccessProgramRegistration($registration);
        $this->claimProgramRegistrationIfUnassigned($registration);
        $registration->refresh();

        if ($registration->status !== ProgramRegistration::STATUS_PENDING) {
            return redirect()
                ->route('case_manager.program_registrations.show', $registration)
                ->with('error', 'Billing links can only be edited while the application is pending case review.');
        }

        $data = $request->validate([
            'billing_urls' => ['nullable', 'array'],
            'billing_urls.*' => ['nullable', 'string', 'max:2048'],
        ]);

        $registration->update([
            'payment_links' => BillingPaymentLinks::paymentLinksColumnValue($data['billing_urls'] ?? []),
        ]);

        return redirect()
            ->route('case_manager.program_registrations.show', $registration)
            ->with('success', 'Billing payment links saved.');
    }

    public function approveProgramRegistration(ProgramRegistration $registration, Request $request)
    {
        $this->assertCaseManagerMayAccessProgramRegistration($registration);
        $this->claimProgramRegistrationIfUnassigned($registration);
        $registration->refresh();

        if ($registration->status !== ProgramRegistration::STATUS_PENDING) {
            return redirect()
                ->route('case_manager.program_registrations.show', $registration)
                ->with('error', 'This registration has already been processed.');
        }

        $data = $request->validate([
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $registration->loadMissing(['program', 'user']);

        $registration->update([
            'status' => ProgramRegistration::STATUS_PENDING_FINANCE,
            'review_note' => $data['note'] ?? null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'finance_user_id' => null,
            'sent_to_finance_at' => now(),
        ]);

        PatientApplicationNotifications::notifyProgramRegistrationPatient(
            $registration,
            'Application passed case review',
            'Your application for "'.($registration->program?->title ?? 'a program').'" passed case manager review and is now with the finance team for final processing.',
            UserNotification::PRIORITY_IMPORTANT
        );

        $recipientEmail = $registration->user?->email ?? $registration->email;
        if ($recipientEmail) {
            try {
                Mail::to($recipientEmail)->queue(new ProgramRegistrationStatus(
                    $registration,
                    'forwarded to finance',
                    $data['note'] ?? null
                ));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        FinanceNotificationService::notifyFinanceTeamRegistrationQueued($registration);

        ProgramRegistrationNotifiers::notifyAdmins(
            'Application routed to finance',
            'A case manager approved an application; it is in the finance queue for budget allocation.',
            $registration
        );

        return redirect()
            ->route('case_manager.program_registrations.show', $registration)
            ->with('success', 'The registration has been approved.');
    }

    public function rejectProgramRegistration(ProgramRegistration $registration, Request $request)
    {
        $this->assertCaseManagerMayAccessProgramRegistration($registration);
        $this->claimProgramRegistrationIfUnassigned($registration);
        $registration->refresh();

        if ($registration->status !== ProgramRegistration::STATUS_PENDING) {
            return redirect()
                ->route('case_manager.program_registrations.show', $registration)
                ->with('error', 'This registration has already been processed.');
        }

        $data = $request->validate([
            'note' => ['required', 'string', 'max:2000'],
        ]);

        $registration->loadMissing(['program', 'user']);

        $registration->update([
            'status' => ProgramRegistration::STATUS_REJECTED,
            'review_note' => $data['note'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        PatientApplicationNotifications::notifyProgramRegistrationPatient(
            $registration,
            'Program registration rejected',
            'Your registration for "'.($registration->program?->title ?? 'a program').'" has been rejected. Reason: '.$data['note'],
            UserNotification::PRIORITY_IMPORTANT
        );

        $recipientEmail = $registration->user?->email ?? $registration->email;
        if ($recipientEmail) {
            try {
                Mail::to($recipientEmail)->queue(new ProgramRegistrationStatus($registration, 'Rejected', $data['note']));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        ProgramRegistrationNotifiers::notifyAdmins(
            'Application rejected by case manager',
            'A case manager rejected an application. Review details in the admin portal if needed.',
            $registration
        );

        return redirect()
            ->route('case_manager.program_registrations.show', $registration)
            ->with('success', 'The registration has been rejected.');
    }

    public function viewAssignedApplication($id)
    {
        $user = Auth::user();
        $role = optional($user->role)->name; // 'case_manager' | 'patient' | etc.

        // Case Manager: only see applications assigned to you
        if ($role === 'casemanager') {
            $application = Application::with([
                'program',
                'patient.user.profile',   // patient profile details
                'reviewer.profile',       // assigned reviewer details
                'documents',
            ])
                ->where('id', $id)
                ->where('reviewer_id', $user->id)
                ->firstOrFail();

            // Build comprehensive patient stats for the sidebar/overview
            $patient = $application->patient; // belongsTo Patient
            $patientUserId = optional($patient)->user_id;

            // All applications from this patient (any reviewer)
            $patientApplications = Application::where('patient_id', $patient->id)
                ->select(['id', 'status', 'submission_date', 'program_id', 'reviewer_id', 'created_at'])
                ->get();

            $totalRequests = $patientApplications->count();
            $approvedCount = $patientApplications->where('status', 'Approved')->count();
            $rejectedCount = $patientApplications->where('status', 'Rejected')->count();
            $pendingCount = $patientApplications->where('status', 'Pending')->count();
            $underReviewCnt = $patientApplications->where('status', 'Under Review')->count();

            // Programs the patient (user) has enrolled in
            $programRegs = \App\Models\ProgramRegistration::with(['program:id,title'])
                ->where('user_id', $patientUserId)
                ->get();
            $programsEnrolledCount = $programRegs->count();
            $programTitles = $programRegs->pluck('program.title')->filter()->values();

            $patientStats = [
                'total_requests' => $totalRequests,
                'approved' => $approvedCount,
                'rejected' => $rejectedCount,
                'pending' => $pendingCount,
                'under_review' => $underReviewCnt,
                'programs_enrolled' => $programsEnrolledCount,
                'program_titles' => $programTitles,
            ];

            return view('case_manager.view_assigned_application', compact('application', 'patientStats'));
        }
        // Patient: only see your own application
        if ($role === 'patient') {
            $patient = Patient::where('user_id', $user->id)->firstOrFail();

            $application = Application::with([
                'program',
                'reviewer.profile',      // show assigned case manager
                'documents',
            ])
                ->where('id', $id)
                ->where('patient_id', $patient->id)
                ->firstOrFail();

            return view('patient.view_assigned_application', compact('application'));
        }

        // Other roles: block or customize as needed
        abort(403, 'You are not authorized to view this application.');
    }

    public function approve(Application $application)
    {
        // Security: only assigned case manager can act
        if ($application->reviewer_id !== Auth::id()) {
            abort(403);
        }

        $application->loadMissing(['patient.user']);

        // Persist a consistent status that matches the enum definition.
        // The `applications` table stores status values in Title Case (e.g. "Approved").
        $application->update([
            'status' => 'Approved',
            'rejection_reason' => null, // clear any past reason
        ]);

        // Remove any existing missing document requests for this application
        ApplicationMissingRequest::where('application_id', $application->id)->delete();

        if ($patientUser = optional($application->patient)->user) {
            try {
                $code = $application->code ?: ('APP-'.str_pad((string) $application->id, 6, '0', STR_PAD_LEFT));
                UserNotification::create([
                    'user_id' => $patientUser->id,
                    'title' => 'Application Approved',
                    'message' => "Your application {$code} has been approved.",
                    'priority' => UserNotification::PRIORITY_IMPORTANT,
                    'link_url' => route('patient.viewApplication', $application->id),
                ]);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', 'The application has been approved successfully.');
    }

    public function reject(Application $application, Request $request)
    {
        // Only the assigned case manager can reject the application
        if ($application->reviewer_id !== Auth::id()) {
            abort(403);
        }

        // Validate the reason input; provide a friendly error message on failure
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:2000'],
        ]);

        $application->loadMissing(['patient.user']);

        // Update the application status to a valid enum value and store the rejection reason
        $application->update([
            'status' => 'Rejected',
            'rejection_reason' => $data['reason'],
        ]);

        // Remove any existing missing document requests for this application
        ApplicationMissingRequest::where('application_id', $application->id)->delete();

        if ($patientUser = optional($application->patient)->user) {
            try {
                $code = $application->code ?: ('APP-'.str_pad((string) $application->id, 6, '0', STR_PAD_LEFT));
                UserNotification::create([
                    'user_id' => $patientUser->id,
                    'title' => 'Application Rejected',
                    'message' => "Your application {$code} has been rejected. Reason: {$data['reason']}.",
                    'priority' => UserNotification::PRIORITY_IMPORTANT,
                    'link_url' => route('patient.viewApplication', $application->id),
                ]);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', 'The application has been rejected and the applicant has been notified.');
    }

    public function requestMissing(Application $application, Request $request)
    {
        // Security: only the assigned case manager can request missing documents
        if ($application->reviewer_id !== Auth::id()) {
            abort(403);
        }

        // Validate the incoming message
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        // Check if there's an existing missing request for this application
        $existingRequest = ApplicationMissingRequest::where('application_id', $application->id)
            ->first();

        if ($existingRequest) {
            // Update the existing record instead of creating a new one
            $existingRequest->update([
                'case_manager_id' => Auth::id(),
                'message' => $data['message'],
                'updated_at' => now(), // Explicitly update timestamp
            ]);

            // Update application status to "Required Docs"
            $application->update([
                'status' => 'Required Docs',
            ]);

            PatientApplicationNotifications::applicationMissingDocumentsRequested($application, $data['message']);

            return back()->with('success', 'Your request for missing documents has been updated successfully.');
        }

        // If no existing request, create a new one
        ApplicationMissingRequest::create([
            'application_id' => $application->id,
            'case_manager_id' => Auth::id(),
            'message' => $data['message'],
        ]);

        // Update application status to "Required Docs"
        $application->update([
            'status' => 'Required Docs',
        ]);

        PatientApplicationNotifications::applicationMissingDocumentsRequested($application, $data['message']);

        return back()->with('success', 'Your request for missing documents has been sent to the applicant.');
    }

    public function patientProfiles()
    {
        // Only show patients whose applications are assigned to the logged-in case manager
        $reviewerId = Auth::id();

        $patients = Patient::query()
            ->whereHas('applications', function ($q) use ($reviewerId) {
                $q->where('reviewer_id', $reviewerId);
            })
            ->with([
                'user.profile',
                'applications' => function ($q) use ($reviewerId) {
                    $q->where('reviewer_id', $reviewerId)
                        ->with('program')
                        ->orderByDesc('submission_date');
                },
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('case_manager.patient_profiles', compact('patients'));
    }

    /**
     * Claim exclusive ownership of a pending program application from the chat page (removes it from other case managers).
     */
    public function claimProgramRegistrationFromChat(ProgramRegistration $registration)
    {
        $userId = (int) Auth::id();
        $assignedId = $registration->assigned_case_manager_id;

        // Do not use assertCaseManagerMayAccessProgramRegistration here: a race where another CM
        // claimed first would abort(403). Redirect with a clear flash message instead.
        if ($assignedId !== null && (int) $assignedId !== $userId) {
            return redirect()
                ->route('case_manager.patientChats')
                ->with('error', 'Another case manager has already claimed this application.');
        }

        if ($registration->status !== ProgramRegistration::STATUS_PENDING) {
            return redirect()
                ->route('case_manager.patientChats')
                ->with('error', 'Only pending applications can be claimed from chat.');
        }

        $this->claimProgramRegistrationIfUnassigned($registration);
        $registration->refresh();

        if ($registration->user_id) {
            return redirect()
                ->route('case_manager.patientChats', ['contact' => $registration->user_id])
                ->with('success', 'You are now assigned to this application. It is removed from other case managers’ open lists.');
        }

        return redirect()
            ->route('case_manager.patientChats')
            ->with('success', 'Application claimed.');
    }

    public function patientChats(Request $request)
    {
        $user = Auth::user();

        $claimableProgramRegistrations = $this->claimableProgramRegistrationsForPatientChats();

        $applicationPatientIds = Patient::query()
            ->whereHas('applications', function ($query) use ($user) {
                $query->where('reviewer_id', $user->id);
            })
            ->pluck('user_id');

        $registrationPatientIds = ProgramRegistration::query()
            ->where(function ($q) use ($user) {
                $q->where('assigned_case_manager_id', $user->id)
                    ->orWhere(function ($q2) {
                        $q2->whereNull('assigned_case_manager_id')
                            ->where('status', ProgramRegistration::STATUS_PENDING);
                    });
            })
            ->whereNotNull('user_id')
            ->pluck('user_id');

        $patientUserIds = $applicationPatientIds
            ->merge($registrationPatientIds)
            ->unique()
            ->values();

        $patientUsers = User::query()
            ->whereIn('id', $patientUserIds)
            ->with(['profile', 'patient'])
            ->get();

        if ($patientUsers->isEmpty()) {
            return view('case_manager.patient_chats', [
                'contacts' => collect(),
                'activeContact' => null,
                'activeContactId' => null,
                'messagesPayload' => [],
                'claimableProgramRegistrations' => $claimableProgramRegistrations,
            ]);
        }

        $activeContactId = (int) $request->query('contact', $patientUsers->first()->id);
        $activeContact = $patientUsers->firstWhere('id', $activeContactId) ?? $patientUsers->first();

        Message::markThreadAsRead($user->id, $activeContact->id);

        $messagesPayload = Message::betweenUsers($user->id, $activeContact->id)
            ->with(['sender.profile', 'receiver.profile'])
            ->orderBy('sent_at')
            ->limit(200)
            ->get()
            ->map->toFrontendPayload()
            ->values();

        $contactSummaries = Message::contactSummariesForUser($user->id, $patientUsers->pluck('id')->all());
        $latestByContact = $contactSummaries['latest_by_contact'];
        $unreadByContact = $contactSummaries['unread_by_contact'];

        $contactsPayload = $patientUsers->map(function (User $contact) use ($latestByContact, $unreadByContact) {
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

        return view('case_manager.patient_chats', [
            'contacts' => $contactsPayload,
            'activeContact' => [
                'id' => $activeContact->id,
                'name' => optional($activeContact->profile)->full_name ?? $activeContact->email,
                'avatar_url' => $activeContact->avatar_url,
            ],
            'activeContactId' => $activeContact->id,
            'messagesPayload' => $messagesPayload,
            'claimableProgramRegistrations' => $claimableProgramRegistrations,
        ]);
    }

    /**
     * JSON + HTML for realtime refresh of the shared “open program applications” strip on Patient Chats.
     */
    public function patientChatsClaimableFragment()
    {
        $html = view('case_manager.partials.patient_chats_claimable', [
            'claimableProgramRegistrations' => $this->claimableProgramRegistrationsForPatientChats(),
        ])->render();

        return response()->json(['html' => $html]);
    }

    public function setting()
    {
        $user = Auth::user()->load('profile');

        return view('case_manager.setting', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);
        $patient = $user->patient ?? new Patient(['user_id' => $user->id]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');

            // Optional: delete old image
            if ($profile->avatar && Storage::exists($profile->avatar)) {
                Storage::delete($profile->avatar);
            }

            $path = $file->store('public/avatars');
            $profile->avatar = Storage::url($path);
        }

        $rules = [
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255|unique:user_profiles,username,'.($profile->id ?? 'NULL'),
            'phone' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'gender' => 'nullable|string|max:10',
            'blood_group' => 'nullable|string|max:5',
            'date_of_birth' => 'nullable|date',
            'marital_status' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
        ];
        $data = $request->validate($rules);

        // Update user email
        $user->email = $data['email'];
        $user->save();

        // Update or create profile fields
        $profile->first_name = $data['first_name'] ?? $profile->first_name;
        $profile->last_name = $data['last_name'] ?? $profile->last_name;
        $profile->username = $data['username'] ?? $profile->username;
        $profile->full_name = trim(($data['first_name'] ?? '').' '.($data['last_name'] ?? ''));
        $profile->phone = $data['phone'] ?? $profile->phone;
        $profile->gender = $data['gender'] ?? $profile->gender;
        $profile->date_of_birth = $data['date_of_birth'] ?? $profile->date_of_birth;
        $profile->country = $data['country'] ?? $profile->country;
        $profile->city = $data['city'] ?? $profile->city;
        $profile->state = $data['state'] ?? $profile->state;

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $profile->avatar = $path;
        }
        $profile->save();

        // Update patient details
        $patient->blood_group = $data['blood_group'] ?? $patient->blood_group;
        $patient->marital_status = $data['marital_status'] ?? $patient->marital_status;
        $patient->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Change the logged in user’s password. Validates the current
     * password and ensures the new password is confirmed.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();
        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Update email/SMS notification preferences for the user’s profile.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateNotifications(Request $request)
    {
        $profile = $request->user()->profile ?? new UserProfile(['user_id' => $request->user()->id]);
        $profile->email_notification = $request->has('email_notification');
        $profile->sms_notification = $request->has('sms_notification');
        $profile->notify_on_new_notifications = $request->has('notify_on_new_notifications');
        $profile->notify_on_direct_message = $request->has('notify_on_direct_message');
        $profile->save();

        return back()->with('success', 'Notification preferences updated.');
    }

    /**
     * Update account-level fields such as username and alternate email.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAccount(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);

        $data = $request->validate([
            'username' => 'nullable|string|max:255|unique:user_profiles,username,'.($profile->id ?? 'NULL'),
            'email' => 'required|email|unique:users,email,'.$user->id,
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
     * Update social media links for the authenticated user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSocial(Request $request)
    {
        $profile = $request->user()->profile ?? new UserProfile(['user_id' => $request->user()->id]);
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
     * Unclaimed pending program registrations with a linked patient account (shared inbox on Patient Chats).
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, ProgramRegistration>
     */
    private function claimableProgramRegistrationsForPatientChats()
    {
        return ProgramRegistration::query()
            ->where('status', ProgramRegistration::STATUS_PENDING)
            ->whereNull('assigned_case_manager_id')
            ->whereNotNull('user_id')
            ->with(['program:id,title', 'user.profile'])
            ->orderByDesc('created_at')
            ->limit(40)
            ->get();
    }

    /**
     * Case managers may open registrations assigned to them, or pending registrations not yet assigned.
     */
    private function assertCaseManagerMayAccessProgramRegistration(ProgramRegistration $registration): void
    {
        if ($registration->assigned_case_manager_id === Auth::id()) {
            return;
        }
        if ($registration->assigned_case_manager_id === null && $registration->status === ProgramRegistration::STATUS_PENDING) {
            return;
        }
        abort(403);
    }

    /**
     * First action (approve / reject / billing links) assigns an unclaimed pending registration to this case manager.
     */
    private function claimProgramRegistrationIfUnassigned(ProgramRegistration $registration): void
    {
        if ($registration->assigned_case_manager_id !== null) {
            return;
        }
        if ($registration->status !== ProgramRegistration::STATUS_PENDING) {
            return;
        }
        $registration->forceFill([
            'assigned_case_manager_id' => Auth::id(),
            'assigned_at' => now(),
        ])->save();
    }

    /**
     * Program registrations this case manager may see (assigned to them or pending and unassigned).
     */
    private function programRegistrationsVisibleToCaseManagerQuery(int $userId): Builder
    {
        return ProgramRegistration::query()->where(function ($w) use ($userId): void {
            $w->where('assigned_case_manager_id', $userId)
                ->orWhere(function ($w2): void {
                    $w2->whereNull('assigned_case_manager_id')
                        ->where('status', ProgramRegistration::STATUS_PENDING);
                });
        });
    }

    /**
     * Dashboard metrics: legacy applications (reviewer_id) + program registrations visible to this manager.
     *
     * @return array{
     *   totalCount:int,approvedCount:int,rejectedCount:int,pendingCount:int,underReviewCount:int,
     *   weeklyBars:list<array{label:string,apps:int,approved:int,rejected:int}>,
     *   acqPct:array<string,int>
     * }
     */
    private function buildCaseManagerDashboardData(User $user): array
    {
        $userId = $user->id;
        $appQ = Application::query()->where('reviewer_id', $userId);
        $regQ = $this->programRegistrationsVisibleToCaseManagerQuery($userId);

        $appTotal = (clone $appQ)->count();
        $appApproved = (clone $appQ)->where('status', Application::STATUS_APPROVED)->count();
        $appRejected = (clone $appQ)->where('status', Application::STATUS_REJECTED)->count();
        $appPending = (clone $appQ)->where('status', Application::STATUS_PENDING)->count();
        $underReviewCount = (clone $appQ)->where('status', Application::STATUS_UNDER_REVIEW)->count();

        $regTotal = (clone $regQ)->count();
        $regApproved = (clone $regQ)->where('status', ProgramRegistration::STATUS_APPROVED)->count();
        $regRejected = (clone $regQ)->where('status', ProgramRegistration::STATUS_REJECTED)->count();
        $regPending = (clone $regQ)->where('status', ProgramRegistration::STATUS_PENDING)->count();

        $totalCount = $appTotal + $regTotal;
        $approvedCount = $appApproved + $regApproved;
        $rejectedCount = $appRejected + $regRejected;
        $pendingCount = $appPending + $regPending;

        $weekdayToLabel = [1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thr', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun'];
        $weeklyBars = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $label = $weekdayToLabel[(int) $day->isoWeekday()];
            $dateStr = $day->toDateString();

            $dayAppTotal = (clone $appQ)->whereDate('created_at', $dateStr)->count();
            $dayRegTotal = (clone $regQ)->whereDate('created_at', $dateStr)->count();
            $dayTotal = $dayAppTotal + $dayRegTotal;

            $dayApproved = (clone $appQ)->where('status', Application::STATUS_APPROVED)->whereDate('created_at', $dateStr)->count()
                + (clone $regQ)->where('status', ProgramRegistration::STATUS_APPROVED)->whereDate('created_at', $dateStr)->count();
            $dayRejected = (clone $appQ)->where('status', Application::STATUS_REJECTED)->whereDate('created_at', $dateStr)->count()
                + (clone $regQ)->where('status', ProgramRegistration::STATUS_REJECTED)->whereDate('created_at', $dateStr)->count();
            $dayRemain = max(0, $dayTotal - $dayApproved - $dayRejected);

            if ($dayTotal > 0) {
                $appsPct = (int) round(($dayRemain / $dayTotal) * 100);
                $approvedPct = (int) round(($dayApproved / $dayTotal) * 100);
                $rejectedPct = max(0, 100 - $appsPct - $approvedPct);
            } else {
                $appsPct = $approvedPct = $rejectedPct = 0;
            }

            $weeklyBars[] = [
                'label' => $label,
                'apps' => $appsPct,
                'approved' => $approvedPct,
                'rejected' => $rejectedPct,
            ];
        }

        $acqCounts = [
            'applications' => $totalCount,
            'shortlisted' => $underReviewCount,
            'rejected' => $rejectedCount,
            'pending' => $pendingCount,
            'approved' => $approvedCount,
        ];
        $maxAcq = max(1, max($acqCounts));
        $acqPct = array_map(fn ($c) => (int) round(($c / $maxAcq) * 100), $acqCounts);

        return [
            'totalCount' => $totalCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'pendingCount' => $pendingCount,
            'underReviewCount' => $underReviewCount,
            'appTotal' => $appTotal,
            'regTotal' => $regTotal,
            'weeklyBars' => $weeklyBars,
            'acqPct' => $acqPct,
        ];
    }
}
