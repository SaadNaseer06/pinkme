<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\SponsorshipProgram;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
        $patient = Patient::where('user_id', $user->id)->first();

        if (!$patient) {
            $patient = Patient::create(['user_id' => $user->id]);
        }

        $applications = Application::where('patient_id', $patient->id)
            ->with(['program', 'reviewer.profile', 'documents'])
            ->orderBy('submission_date', 'desc')
            ->paginate(10);

        $totalApplications = $applications->count();
        $pendingApplications = $applications->where('status', 'Pending')->count();
        $approvedApplications = $applications->where('status', 'Approved')->count();
        $rejectedApplications = $applications->where('status', 'Rejected')->count();

        return view('patient.my_application', compact('applications', 'totalApplications', 'pendingApplications', 'approvedApplications', 'rejectedApplications'));
    }

    public function programsAndAids()
    {
        $programs = SponsorshipProgram::where('end_date', '>', now())
            ->orWhereNull('end_date')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('patient.programs_and_aids', compact('programs'));
    }

    public function patientChats()
    {
        $user = Auth::user();

        // Get messages for this patient
        $messages = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with(['sender.profile', 'receiver.profile'])
            ->orderBy('sent_at', 'desc')
            ->paginate(20);

        return view('patient.patient_chats', compact('messages'));
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
}
