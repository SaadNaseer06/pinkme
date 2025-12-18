<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\User;
use App\Models\Patient;
use App\Models\SponsorshipProgram;
use App\Models\Sponsorship;
use App\Models\Role;
use App\Models\Event;
use App\Models\Program;
use App\Models\SiteSetting;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get statistics for dashboard cards
        $totalApplications = Application::count();
        $pendingApplications = Application::where('status', 'Pending')->count();
        $approvedApplications = Application::where('status', 'Approved')->count();
        $rejectedApplications = Application::where('status', 'Rejected')->count();

        $totalPatients = Patient::count();
        $totalSponsors = User::whereHas('role', function ($query) {
            $query->where('name', 'sponsor');
        })->count();

        $totalPrograms = SponsorshipProgram::count();
        $totalRaised = Sponsorship::sum('amount');

        // Get recent applications with patient details
        $recentApplications = Application::with(['patient.user.profile', 'program'])
            ->orderBy('submission_date', 'desc')
            ->limit(5)
            ->get();

        // Get monthly application data for charts
        $monthlyApplications = Application::selectRaw('MONTH(submission_date) as month, COUNT(*) as count')
            ->whereYear('submission_date', Carbon::now()->year)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Fill missing months with 0
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $monthlyApplications[$i] ?? 0;
        }

        return view('admin.dashboard', compact(
            'totalApplications',
            'pendingApplications',
            'approvedApplications',
            'rejectedApplications',
            'totalPatients',
            'totalSponsors',
            'totalPrograms',
            'totalRaised',
            'recentApplications',
            'chartData'
        ));
    }

    public function applications()
    {
        $applications = Application::with(['patient.user.profile', 'reviewer.profile', 'program'])
            ->orderBy('submission_date', 'desc')
            ->paginate(20);

        return view('admin.applications', compact('applications'));
    }

    public function viewApplication($id)
    {
        $application = Application::with(['patient.user.profile', 'reviewer.profile', 'program'])->find($id);

        if (!$application) {
            return redirect()->route('admin.applications')->with('error', 'Application not found.');
        }

        return view('admin.view_application', compact('application'));
    }

    public function assigned()
    {
        return redirect()->route('admin.applications', ['tab' => 'assigned']);
    }

    public function deleteApplication($id)
    {
        $application = Application::with(['documents', 'missingRequests'])->find($id);

        if (! $application) {
            return response()->json(['message' => 'Application not found.'], 404);
        }

        DB::beginTransaction();

        try {
            foreach ($application->documents as $document) {
                if ($document->filepath && Storage::disk('public')->exists($document->filepath)) {
                    Storage::disk('public')->delete($document->filepath);
                }
            }

            $application->documents()->delete();
            $application->missingRequests()->delete();
            $application->delete();

            DB::commit();

            return response()->json(['message' => 'Application deleted successfully.']);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Failed to delete application', [
                'application_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to delete application. Please try again later.',
            ], 500);
        }
    }



    public function reviewers(Request $request)
    {
        $status      = strtolower((string) $request->query('status', 'active'));
        $reviewerId  = trim((string) $request->query('reviewer_id', ''));
        $email       = trim((string) $request->query('email', ''));
        $searchQuery = trim((string) $request->query('q', ''));

        if (! in_array($status, ['active', 'inactive', 'all'], true)) {
            $status = 'active';
        }

        $reviewers = User::query()
            ->whereHas('role', function ($query) {
                $query->where('name', 'casemanager');
            })
            ->whereHas('profile')
            ->with([
                'profile:id,user_id,full_name,username,phone,status,gender',
                'applications',
            ])
            ->withCount('applications')
            ->when($status === 'active', function ($query) {
                $query->whereHas('profile', fn($profile) => $profile->where('status', 1));
            })
            ->when($status === 'inactive', function ($query) {
                $query->whereHas('profile', fn($profile) => $profile->where('status', '!=', 1)->orWhereNull('status'));
            })
            ->when($status === 'all', function ($query) {
                // No additional constraint; include any status
            })
            ->when($reviewerId !== '', function ($query) use ($reviewerId) {
                $numericId = (int) ltrim(preg_replace('/\D/', '', $reviewerId), '0');

                if ($numericId > 0) {
                    $query->where('id', $numericId);
                } else {
                    $query->whereRaw('1 = 0');
                }
            })
            ->when($email !== '', function ($query) use ($email) {
                $query->where('email', 'like', '%' . $email . '%');
            })
            ->when($searchQuery !== '', function ($query) use ($searchQuery) {
                $query->where(function ($inner) use ($searchQuery) {
                    $inner->where('email', 'like', '%' . $searchQuery . '%')
                        ->orWhereHas('profile', function ($profileQuery) use ($searchQuery) {
                            $profileQuery->where('full_name', 'like', '%' . $searchQuery . '%')
                                ->orWhere('username', 'like', '%' . $searchQuery . '%')
                                ->orWhere('phone', 'like', '%' . $searchQuery . '%');
                        });
                });
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->appends($request->query());

        return view('admin.reviewers', compact('reviewers', 'status', 'reviewerId', 'email', 'searchQuery'));
    }

    public function getUnassignedApplications($reviewerId)
    {
        // Fetch applications with null reviewer_id for the modal
        $applications = Application::where('reviewer_id', null)
            ->with(['patient.user.profile', 'program']) // include any relevant relationships
            ->get();

        return response()->json($applications);
    }


    public function show($id)
    {
        $reviewer = User::with('profile', 'applications') // Eager load profile and applications
            ->where('id', $id)
            ->firstOrFail(); // Get the reviewer or fail if not found

        return view('admin.show', compact('reviewer'));
    }




    public function assignReviewer(Request $request, $id)
    {
        try {
            // 1) Validate input
            $validated = $request->validate([
                'reviewer_id' => ['required', 'integer', 'exists:users,id'],
            ]);

            // 2) Do everything atomically & lock the row to avoid races
            return DB::transaction(function () use ($validated, $id) {
                // Lock the application row for update
                $application = Application::whereKey($id)->lockForUpdate()->first();

                if (!$application) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Application not found.',
                    ], 404);
                }

                // Load reviewer (+profile for checks)
                $reviewer = User::with('profile')->find($validated['reviewer_id']);
                if (!$reviewer) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected reviewer not found.',
                    ], 404);
                }

                // Optional: enforce case manager role
                if ($caseManagerRoleId = Role::where('name', 'casemanager')->value('id')) {
                    if ((int)$reviewer->role_id !== (int)$caseManagerRoleId) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Selected user is not a case manager.',
                        ], 400);
                    }
                }

                // Optional: only active reviewers
                if (optional($reviewer->profile)->status != 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected reviewer is not active.',
                    ], 400);
                }

                $previousReviewerId = $application->reviewer_id;
                $noChange = (int)$previousReviewerId === (int)$reviewer->id;

                // If no change, return success (idempotent)
                if ($noChange) {
                    $reviewerName = $reviewer->profile->full_name ?? $reviewer->email ?? 'Unknown Reviewer';
                    return response()->json([
                        'success' => true,
                        'message' => "Reviewer already assigned: {$reviewerName}.",
                        'data' => [
                            'application_id' => $application->id,
                            'reviewer_id'    => $reviewer->id,
                            'reviewer_name'  => $reviewerName,
                            'status'         => $application->status,
                        ],
                    ], 200);
                }

                // 3) Assign
                $application->reviewer_id = $reviewer->id;
                // Optional: if you want to bump status on first assignment:
                // if (is_null($previousReviewerId) && $application->status === 'submitted') {
                //     $application->status = 'assigned';
                // }
                $application->save();

                $reviewerName = $reviewer->profile->full_name ?? $reviewer->email ?? 'Unknown Reviewer';

                Log::info('Reviewer assigned to application', [
                    'application_id' => $application->id,
                    'previous_reviewer_id' => $previousReviewerId,
                    'new_reviewer_id' => $reviewer->id,
                    'assigned_by' => Auth::id(),
                ]);

                $message = is_null($previousReviewerId)
                    ? "Reviewer assigned successfully to {$reviewerName}."
                    : "Reviewer changed to {$reviewerName}.";

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data'    => [
                        'application_id' => $application->id,
                        'reviewer_id'    => $reviewer->id,
                        'reviewer_name'  => $reviewerName,
                        'status'         => $application->status,
                    ],
                ], 200);
            });
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Assign reviewer error', [
                'application_id' => $id ?? 'unknown',
                'reviewer_id'    => $request->reviewer_id ?? 'unknown',
                'error_message'  => $e->getMessage(),
                'error_file'     => $e->getFile(),
                'error_line'     => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while assigning the reviewer.',
            ], 500);
        }
    }

    public function patients()
    {
        $patients = Patient::with(['user.profile', 'applications'])
            ->paginate(20);

        return view('admin.patients', compact('patients'));
    }

    public function showPatient(Patient $patient)
    {
        $patient->load([
            'user.profile',
            'applications.program',
        ]);

        return view('admin.patients.show', [
            'patient' => $patient,
            'applicationsCount' => $patient->applications->count(),
        ]);
    }

    public function editPatient(Patient $patient)
    {
        $patient->load(['user.profile']);

        return view('admin.patients.edit', [
            'patient' => $patient,
        ]);
    }

    public function updatePatient(Request $request, Patient $patient)
    {
        $user = $patient->user;

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', $user ? 'unique:users,email,' . $user->id : 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'blood_group' => ['nullable', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'diagnosis' => ['nullable', 'string', 'max:255'],
            'disease_type' => ['nullable', 'string', 'max:255'],
            'disease_stage' => ['nullable', 'string', 'max:255'],
            'genetic_test' => ['nullable', 'string', 'max:255'],
            'diagnosis_date' => ['nullable', 'date'],
        ]);

        DB::beginTransaction();

        try {
            if ($user) {
                $user->email = $validated['email'];
                $user->save();

                $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);
                $profile->full_name = $validated['full_name'];
                $profile->phone = $validated['phone'] ?? $profile->phone;
                $profile->save();
            }

            $patient->update([
                'blood_group' => $validated['blood_group'] ?? $patient->blood_group,
                'diagnosis' => $validated['diagnosis'] ?? $patient->diagnosis,
                'disease_type' => $validated['disease_type'] ?? $patient->disease_type,
                'disease_stage' => $validated['disease_stage'] ?? $patient->disease_stage,
                'genetic_test' => $validated['genetic_test'] ?? $patient->genetic_test,
                'diagnosis_date' => $validated['diagnosis_date'] ?? $patient->diagnosis_date,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.patients.edit', $patient)
                ->with('success', 'Patient details updated successfully.');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Failed to update patient', [
                'patient_id' => $patient->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update patient. Please try again.']);
        }
    }

    public function patientApplications(Patient $patient)
    {
        $applications = $patient->applications()
            ->with(['program', 'reviewer.profile'])
            ->orderByDesc('submission_date')
            ->paginate(10);

        return view('admin.patients.applications', [
            'patient' => $patient->load('user.profile'),
            'applications' => $applications,
        ]);
    }

    public function sponsors()
    {
        $sponsors = User::whereHas('role', function ($query) {
            $query->where('name', 'sponsor');
        })->with(['profile', 'sponsorDetail'])->paginate(20);

        $events = Event::with(['sponsors'])
            ->withCount('sponsors')
            ->withSum('sponsorships as total_raised', 'amount')
            ->orderByDesc('date')
            ->get();

        $programs = Program::with(['registrations'])
            ->withCount('registrations')
            ->withSum('sponsorships as total_raised', 'amount')
            ->orderByRaw("CASE WHEN payment_type = 'full' THEN 0 WHEN payment_type = 'flexible' THEN 1 ELSE 2 END")
            ->orderByDesc('event_date')
            ->orderByDesc('event_time')
            ->get();

        return view('admin.sponsors', compact('sponsors', 'events', 'programs'));
    }

    public function settings()
    {
        $settings = SiteSetting::first();
        return view('admin.settings', compact('settings'));
    }



    public function editReviewer($id)
    {
        // Fetch the reviewer by id, along with their profile
        $reviewer = User::with('profile')->findOrFail($id);

        // Return the view with the reviewer details
        return view('admin.edit_casemanager_details', compact('reviewer'));
    }


    public function updateReviewer(Request $request, $id)
    {
        // Validation rules
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:user_profiles,username,' . $id . ',user_id', // Validation for the username in the profile table
            'phone' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'gender' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Avatar image validation
            'date_of_birth' => 'nullable|date', // Validation for date_of_birth
            'blood_group' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-', // Validation for blood_group
        ]);

        // Retrieve the reviewer by ID
        $reviewer = User::findOrFail($id);

        // Update the user's email
        $reviewer->email = $request->email;
        $reviewer->save(); // Save the changes to the user

        // Retrieve the profile associated with the reviewer
        $profile = $reviewer->profile;

        // Update the profile details
        $profile->first_name = $request->first_name;
        $profile->last_name = $request->last_name;
        $profile->username = $request->username;
        $profile->phone = $request->phone;
        $profile->gender = $request->gender;
        $profile->date_of_birth = $request->date_of_birth; // Update date_of_birth
        $profile->blood_group = $request->blood_group; // Update blood_group

        // Handle Avatar Image Upload if provided
        if ($request->hasFile('avatar')) {
            // Delete the old avatar if exists
            if ($profile->avatar) {
                Storage::disk('public')->delete($profile->avatar);
            }

            // Store the new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $profile->avatar = $avatarPath; // Save the new avatar path
        }

        // Save the profile with updated details
        $profile->save();

        // Redirect back to the reviewers page with a success message
        return redirect()->route('admin.reviewers')->with('success', 'Reviewer details updated successfully.');
    }


    public function applicationsIndex(Request $request)
    {
        // initial render (non-AJAX) just returns the page; data is loaded via AJAX too
        return view('admin.applications');
    }


    public function applicationsList(Request $request)
    {
        $range  = $request->string('range')->toString();
        $q      = trim($request->string('q')->toString());
        $status = strtolower($request->string('status')->toString());

        $allowedStatuses = ['pending', 'under_review', 'approved', 'rejected'];
        $statusFilter    = in_array($status, $allowedStatuses, true) ? $status : null;

        $startDate = match ($range) {
            'week'  => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            default => null,
        };

        $apps = Application::query()
            ->with([
                'program:id,title',
                'patient:id,user_id',
                'patient.user:id,email',
                'patient.user.profile:id,user_id,full_name,phone,avatar',
                'reviewer.profile:id,user_id,full_name,avatar,status',
                'missingRequests',
            ])
            ->when($startDate, fn($q2) => $q2->where('created_at', '>=', $startDate))
            ->when($statusFilter, fn($q2) => $q2->where('status', $statusFilter))
            // search: name, email, code, id
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->whereHas('patient.user.profile', fn($qq) => $qq->where('full_name', 'like', "%{$q}%"))
                        ->orWhereHas('patient.user', fn($qq) => $qq->where('email', 'like', "%{$q}%"))
                        ->orWhere('title', 'like', "%{$q}%");

                    // also allow typing numeric id like "123"
                    if (ctype_digit($q)) {
                        $w->orWhere('id', (int)$q);
                    }
                });
            })
            // unassigned first, then newest
            ->orderByRaw('CASE WHEN reviewer_id IS NULL THEN 0 ELSE 1 END')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->query());

        // Return the partial HTML for the table + pagination
        return view('admin.applications._table', [
            'apps'  => $apps,
            'range' => $range,
        ]);
    }

    public function applicationsExport(Request $request)
    {
        $range  = $request->string('range')->toString();
        $q      = trim($request->string('q')->toString());
        $status = strtolower($request->string('status')->toString());

        $allowedStatuses = ['pending', 'under_review', 'approved', 'rejected'];
        $statusFilter    = in_array($status, $allowedStatuses, true) ? $status : null;

        $startDate = match ($range) {
            'week'  => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            default => null,
        };

        $apps = Application::query()
            ->with([
                'program:id,title',
                'patient:id,user_id',
                'patient.user:id,email',
                'patient.user.profile:id,user_id,full_name,phone',
                'reviewer.profile:id,user_id,full_name',
                'missingRequests',
            ])
            ->when($startDate, fn($q2) => $q2->where('created_at', '>=', $startDate))
            ->when($statusFilter, fn($q2) => $q2->where('status', $statusFilter))
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->whereHas('patient.user.profile', fn($qq) => $qq->where('full_name', 'like', "%{$q}%"))
                        ->orWhereHas('patient.user', fn($qq) => $qq->where('email', 'like', "%{$q}%"))
                        ->orWhere('title', 'like', "%{$q}%");

                    if (ctype_digit($q)) {
                        $w->orWhere('id', (int) $q);
                    }
                });
            })
            ->orderByRaw('CASE WHEN reviewer_id IS NULL THEN 0 ELSE 1 END')
            ->orderByDesc('created_at')
            ->get();

        $filename = 'applications_' . now()->format('Ymd_His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $statusLabel = fn($statusValue) => match (strtolower((string) $statusValue)) {
            'approved'     => 'Approved',
            'rejected'     => 'Rejected',
            'under_review' => 'Under Review',
            'pending'      => 'Pending',
            default        => ucfirst(str_replace('_', ' ', (string) $statusValue)),
        };

        $timezone = config('app.timezone');

        return response()->stream(function () use ($apps, $statusLabel, $timezone) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Application ID',
                'Patient Name',
                'Email',
                'Contact',
                'Program',
                'Assigned Reviewer',
                'Status',
                'Missing Docs Requested',
                'Submitted At',
            ]);

            foreach ($apps as $app) {
                $patientProfile  = $app->patient?->user?->profile;
                $reviewerProfile = $app->reviewer?->profile;
                $missingDocs     = $app->missingRequests->isNotEmpty() ? 'Yes' : 'No';
                $submittedAt     = $app->created_at
                    ? $app->created_at->timezone($timezone)->format('Y-m-d H:i:s')
                    : '';

                fputcsv($handle, [
                    $app->code ?: ('APP-' . str_pad((string) $app->id, 6, '0', STR_PAD_LEFT)),
                    $patientProfile->full_name ?? 'Unknown',
                    $app->patient?->user?->email ?? 'N/A',
                    $patientProfile->phone ?? 'N/A',
                    $app->program?->title ?? 'N/A',
                    $reviewerProfile->full_name ?? 'Unassigned',
                    $missingDocs === 'Yes' ? 'Missing Docs Requested' : $statusLabel($app->status),
                    $missingDocs,
                    $submittedAt,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
