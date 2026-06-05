<?php

namespace App\Http\Controllers;

use App\Mail\ProgramRegistrationAdminNotice;
use App\Mail\ProgramRegistrationStatus;
use App\Models\ProgramRegistration;
use App\Models\Role;
use App\Models\User;
use App\Models\UserNotification;
use App\Support\PatientApplicationNotifications;
use App\Support\ProgramRegistrationNotifiers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\TransactionalMail;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminProgramRegistrationController extends Controller
{
    /**
     * Display a listing of program registration requests.
     */
    public function index(Request $request)
    {
        $selectedStatus = strtolower((string) $request->query('status', ProgramRegistration::STATUS_PENDING));
        $validStatuses = [
            'all',
            ProgramRegistration::STATUS_PENDING,
            ProgramRegistration::STATUS_PENDING_FINANCE,
            ProgramRegistration::STATUS_APPROVED,
            ProgramRegistration::STATUS_SHIPPED,
            ProgramRegistration::STATUS_REJECTED,
        ];

        if (! in_array($selectedStatus, $validStatuses, true)) {
            $selectedStatus = ProgramRegistration::STATUS_PENDING;
        }

        $query = ProgramRegistration::query()
            ->with(['program:id,title', 'user:id,email'])
            ->orderByDesc('created_at');

        if ($selectedStatus !== 'all') {
            $query->where('status', $selectedStatus);
        }

        $registrations = $query
            ->paginate(15)
            ->appends($request->query());

        $counts = [
            'pending' => ProgramRegistration::where('status', ProgramRegistration::STATUS_PENDING)->count(),
            'pending_finance' => ProgramRegistration::where('status', ProgramRegistration::STATUS_PENDING_FINANCE)->count(),
            'approved' => ProgramRegistration::where('status', ProgramRegistration::STATUS_APPROVED)->count(),
            'shipped' => ProgramRegistration::where('status', ProgramRegistration::STATUS_SHIPPED)->count(),
            'rejected' => ProgramRegistration::where('status', ProgramRegistration::STATUS_REJECTED)->count(),
            'all' => ProgramRegistration::count(),
        ];

        return view('admin.program_registrations.index', [
            'registrations' => $registrations,
            'selectedStatus' => $selectedStatus,
            'counts' => $counts,
        ]);
    }

    /**
     * Show the details for a single registration.
     */
    public function show(ProgramRegistration $registration)
    {
        $registration->load(['program', 'user', 'reviewer', 'shipper.profile', 'assignedCaseManager.profile']);

        $caseManagerRoleId = Role::where('name', 'casemanager')->value('id');
        $caseManagers = $caseManagerRoleId
            ? User::where('role_id', $caseManagerRoleId)->with('profile')->orderBy('email')->get()
            : collect();

        return view('admin.program_registrations.show', [
            'registration' => $registration,
            'caseManagers' => $caseManagers,
        ]);
    }

    /**
     * Approve a registration request.
     */
    public function approve(ProgramRegistration $registration, Request $request)
    {
        $registration->loadMissing('program');
        if ($registration->isMomentsThatMatterApplication()) {
            return redirect()
                ->route('admin.program_registrations.show', $registration)
                ->with('error', 'Moments That Matter applications are fulfilled by marking them as shipped only.');
        }

        if ($registration->status !== ProgramRegistration::STATUS_PENDING) {
            return redirect()
                ->route('admin.program_registrations.show', $registration)
                ->with('error', 'This registration has already been processed.');
        }

        $data = $request->validate([
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $registration->loadMissing(['program', 'user']);

        $registration->update([
            'status' => ProgramRegistration::STATUS_APPROVED,
            'review_note' => $data['note'] ?? null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $isMtm = $registration->isMomentsThatMatterApplication();
        $programTitle = $registration->program?->title ?? 'a program';

        PatientApplicationNotifications::notifyProgramRegistrationPatient(
            $registration,
            $isMtm ? 'Care package application approved' : 'Program registration approved',
            $isMtm
                ? 'Your Moments That Matter application for "'.$programTitle.'" has been approved. We will prepare and ship your care package soon.'
                : 'Your registration for "'.$programTitle.'" has been approved.',
            UserNotification::PRIORITY_IMPORTANT
        );

        $recipientEmail = $registration->user?->email ?? $registration->email;
        if ($recipientEmail) {
            try {
                TransactionalMail::send($recipientEmail, new ProgramRegistrationStatus($registration, 'Approved', $data['note'] ?? null));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()
            ->route('admin.program_registrations.show', $registration)
            ->with('success', 'The registration has been approved.');
    }

    /**
     * Reject a registration request.
     */
    public function reject(ProgramRegistration $registration, Request $request)
    {
        $registration->loadMissing('program');
        if ($registration->isMomentsThatMatterApplication()) {
            return redirect()
                ->route('admin.program_registrations.show', $registration)
                ->with('error', 'Moments That Matter applications cannot be rejected from this workflow. Mark as shipped when the care package is sent.');
        }

        if ($registration->status !== ProgramRegistration::STATUS_PENDING) {
            return redirect()
                ->route('admin.program_registrations.show', $registration)
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
                TransactionalMail::send($recipientEmail, new ProgramRegistrationStatus($registration, 'Rejected', $data['note']));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()
            ->route('admin.program_registrations.show', $registration)
            ->with('success', 'The registration has been rejected.');
    }

    /**
     * Moments That Matter: mark application as shipped (admin only).
     */
    public function markShipped(ProgramRegistration $registration, Request $request): RedirectResponse
    {
        $registration->loadMissing('program');

        if (! $registration->isMomentsThatMatterApplication()) {
            return redirect()
                ->route('admin.program_registrations.show', $registration)
                ->with('error', 'Only Moments That Matter applications can be marked as shipped.');
        }

        $shippableStatuses = [
            ProgramRegistration::STATUS_PENDING,
            ProgramRegistration::STATUS_APPROVED,
        ];
        if (! in_array($registration->status, $shippableStatuses, true)) {
            return redirect()
                ->route('admin.program_registrations.show', $registration)
                ->with('error', 'This application has already been marked as shipped.');
        }

        $data = $request->validate([
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $registration->loadMissing(['program', 'user']);

        $registration->update([
            'status' => ProgramRegistration::STATUS_SHIPPED,
            'shipped_at' => now(),
            'shipped_by' => Auth::id(),
            'review_note' => $data['note'] ?? $registration->review_note,
            'reviewed_by' => $registration->reviewed_by ?? Auth::id(),
            'reviewed_at' => $registration->reviewed_at ?? now(),
        ]);

        $programTitle = $registration->program?->title ?? 'Moments That Matter';
        $body = 'Your care package for "'.$programTitle.'" has been shipped.';
        if (! empty($data['note'])) {
            $body .= ' Note: '.$data['note'];
        }

        PatientApplicationNotifications::notifyProgramRegistrationPatient(
            $registration,
            'Care package shipped',
            $body,
            UserNotification::PRIORITY_IMPORTANT
        );

        $recipientEmail = $registration->user?->email ?? $registration->email;
        if ($recipientEmail) {
            try {
                TransactionalMail::send($recipientEmail, new ProgramRegistrationStatus($registration, 'Shipped', $data['note'] ?? null));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()
            ->route('admin.program_registrations.show', $registration)
            ->with('success', 'The application has been marked as shipped.');
    }

    /**
     * Assign a case manager to a registration.
     */
    public function assignCaseManager(ProgramRegistration $registration, Request $request): JsonResponse|RedirectResponse
    {
        if ($request->isMethod('GET')) {
            return redirect()->route('admin.program_registrations.show', $registration);
        }

        $registration->loadMissing('program');
        if ($registration->isMomentsThatMatterApplication()) {
            return response()->json([
                'success' => false,
                'message' => 'Moments That Matter applications are reviewed by admin only and cannot be assigned to a case manager.',
            ], 400);
        }

        $data = $request->validate([
            'case_manager_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        if (! empty($data['case_manager_id'])) {
            $caseManagerRoleId = Role::where('name', 'casemanager')->value('id');
            $isCaseManager = $caseManagerRoleId
                ? User::where('id', $data['case_manager_id'])->where('role_id', $caseManagerRoleId)->exists()
                : false;

            if (! $isCaseManager) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected user is not a case manager.',
                ], 400);
            }
        }

        $registration->update([
            'assigned_case_manager_id' => $data['case_manager_id'] ?? null,
            'assigned_at' => ! empty($data['case_manager_id']) ? now() : null,
        ]);

        $caseManagerId = $data['case_manager_id'] ?? null;
        if ($caseManagerId) {
            $registrationId = $registration->id;
            dispatch(function () use ($registrationId, $caseManagerId): void {
                $registration = ProgramRegistration::query()->with(['program', 'user'])->find($registrationId);
                if (! $registration) {
                    return;
                }

                $caseManager = User::with('profile')->find($caseManagerId);
                ProgramRegistrationNotifiers::notifyAdmins(
                    'Application assigned to case manager',
                    'An application has been assigned to a case manager for review.',
                    $registration
                );
                if ($caseManager?->email) {
                    TransactionalMail::send($caseManager->email, new ProgramRegistrationAdminNotice(
                        'You were assigned a new application',
                        $registration,
                        'You have been assigned to review a financial assistance application.',
                        route('case_manager.program_registrations.show', $registration)
                    ));
                }
                $patientEmail = $registration->user?->email ?? $registration->email;
                if ($patientEmail) {
                    TransactionalMail::send($patientEmail, new ProgramRegistrationAdminNotice(
                        'Your application is under review',
                        $registration,
                        'Your application has been assigned to a case manager. You will be notified when there is an update.',
                        route('patient.programRegistrations.show', $registration)
                    ));
                }
            })->afterResponse();
        }

        $registration->loadMissing(['assignedCaseManager.profile']);
        $assignedName = $registration->assignedCaseManager?->profile?->full_name
            ?? $registration->assignedCaseManager?->email
            ?? 'Unassigned';

        return response()->json([
            'success' => true,
            'message' => 'Case manager assignment updated.',
            'data' => [
                'registration_id' => $registration->id,
                'assigned_case_manager_id' => $registration->assigned_case_manager_id,
                'assigned_name' => $assignedName,
            ],
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $selectedStatus = strtolower((string) $request->query('status', 'all'));
        $validStatuses = [
            'all',
            ProgramRegistration::STATUS_PENDING,
            ProgramRegistration::STATUS_PENDING_FINANCE,
            ProgramRegistration::STATUS_APPROVED,
            ProgramRegistration::STATUS_SHIPPED,
            ProgramRegistration::STATUS_REJECTED,
        ];
        if (! in_array($selectedStatus, $validStatuses, true)) {
            $selectedStatus = 'all';
        }

        $query = ProgramRegistration::query()
            ->with(['program:id,title'])
            ->orderByDesc('created_at');
        if ($selectedStatus !== 'all') {
            $query->where('status', $selectedStatus);
        }

        $filename = 'program_applications_'.date('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'ID',
                'First name',
                'Last name',
                'Email',
                'Phone',
                'Program',
                'Status',
                'Submitted at',
                'Quarter',
                'Programs applied',
                'Billing payment links (finance)',
                'Billing details',
            ]);
            $query->chunk(500, function ($chunk) use ($out) {
                foreach ($chunk as $r) {
                    fputcsv($out, [
                        $r->id,
                        $r->first_name,
                        $r->last_name,
                        $r->email,
                        $r->phone,
                        $r->program?->title,
                        $r->status,
                        $r->created_at?->toIso8601String(),
                        $r->quarter_applied,
                        is_array($r->programs_applied) ? implode('; ', $r->programs_applied) : '',
                        $r->payment_links,
                        $r->billing_details,
                    ]);
                }
            });
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
