<?php

namespace App\Http\Controllers;

use App\Models\ProgramRegistration;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminProgramRegistrationController extends Controller
{
    /**
     * Display a listing of program registration requests.
     */
    public function index(Request $request)
    {
        $selectedStatus = strtolower((string) $request->query('status', ProgramRegistration::STATUS_PENDING));
        $validStatuses  = [
            'all',
            ProgramRegistration::STATUS_PENDING,
            ProgramRegistration::STATUS_APPROVED,
            ProgramRegistration::STATUS_REJECTED,
        ];

        if (!in_array($selectedStatus, $validStatuses, true)) {
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
            'pending'  => ProgramRegistration::where('status', ProgramRegistration::STATUS_PENDING)->count(),
            'approved' => ProgramRegistration::where('status', ProgramRegistration::STATUS_APPROVED)->count(),
            'rejected' => ProgramRegistration::where('status', ProgramRegistration::STATUS_REJECTED)->count(),
            'all'      => ProgramRegistration::count(),
        ];

        return view('admin.program_registrations.index', [
            'registrations'   => $registrations,
            'selectedStatus'  => $selectedStatus,
            'counts'          => $counts,
        ]);
    }

    /**
     * Show the details for a single registration.
     */
    public function show(ProgramRegistration $registration)
    {
        $registration->load(['program', 'user', 'reviewer']);

        return view('admin.program_registrations.show', [
            'registration' => $registration,
        ]);
    }

    /**
     * Approve a registration request.
     */
    public function approve(ProgramRegistration $registration, Request $request)
    {
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
            'status'      => ProgramRegistration::STATUS_APPROVED,
            'review_note' => $data['note'] ?? null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        if ($registration->user) {
            UserNotification::create([
                'user_id' => $registration->user_id,
                'title' => 'Program Registration Approved',
                'message' => 'Your registration for "' . ($registration->program->title ?? 'a program') . '" has been approved.',
                'priority' => UserNotification::PRIORITY_IMPORTANT,
                'link_url' => route('patient.programRegistrations.show', $registration),
            ]);
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
            'status'      => ProgramRegistration::STATUS_REJECTED,
            'review_note' => $data['note'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        if ($registration->user) {
            UserNotification::create([
                'user_id' => $registration->user_id,
                'title' => 'Program Registration Rejected',
                'message' => 'Your registration for "' . ($registration->program->title ?? 'a program') . '" has been rejected. Reason: ' . $data['note'],
                'priority' => UserNotification::PRIORITY_IMPORTANT,
                'link_url' => route('patient.programRegistrations.show', $registration),
            ]);
        }

        return redirect()
            ->route('admin.program_registrations.show', $registration)
            ->with('success', 'The registration has been rejected.');
    }
}
