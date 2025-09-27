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
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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
        $applications = Application::with(['patient.user.profile', 'reviewer.profile', 'program'])
            ->whereNotNull('reviewer_id')
            ->orderBy('submission_date', 'desc')
            ->paginate(20);
        // dd($applications);

        return view('admin.assigned', compact('applications'));
    }



    public function reviewers()
    {
        $reviewers = User::whereHas('role', function ($query) {
            $query->where('name', 'casemanager');
        })
            ->whereHas('profile', function ($query) {
                $query->where('status', 1); // Check the status in the user_profiles table
            })
            ->with(['profile', 'applications'])
            ->withCount('applications')
            ->paginate(20);


        return view('admin.reviewers', compact('reviewers'));
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

    public function sponsors()
    {
        $sponsors = User::whereHas('role', function ($query) {
            $query->where('name', 'sponsor');
        })->with(['profile', 'sponsorDetail'])->paginate(20);

        $events = Event::with(['sponsors'])
            ->orderBy('date', 'desc')
            ->get();
            
        $programs = Program::with(['registrations'])
            ->orderBy('event_date', 'desc')
            ->orderBy('event_time', 'desc')
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
        $range = $request->string('range')->toString();
        $q     = trim($request->string('q')->toString());

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
            // search: name, email, code, id
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->whereHas('patient.user.profile', fn($qq) => $qq->where('full_name', 'like', "%{$q}%"))
                        ->orWhereHas('patient.user', fn($qq) => $qq->where('email', 'like', "%{$q}%"))
                        ->orWhere('code', 'like', "%{$q}%");

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
}
