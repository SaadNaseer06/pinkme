<?php

namespace App\Http\Controllers;

use App\Models\ApplicationMissingRequest;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Patient;
use App\Models\Message;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CaseManagerController extends Controller
{

    public function dashboard()
    {
        $user = Auth::user();

        // Fetch all applications where this user is the reviewer
        $applications = Application::where('reviewer_id', $user->id)->get();

        $total = $applications->count();
        $pending = $applications->where('status', 'Pending')->count();
        $underReview = $applications->where('status', 'Under Review')->count();
        $approved = $applications->where('status', 'Approved')->count();
        $rejected = $applications->where('status', 'Rejected')->count();

        // Helper closure to compute percentages safely
        $percentage = function ($count) use ($total) {
            return $total > 0 ? round($count / $total * 100) : 0;
        };

        return view('case_manager.dashboard', [
            'totalAssigned'         => $total,
            'pendingCount'          => $pending,
            'underReviewCount'      => $underReview,
            'approvedCount'         => $approved,
            'rejectedCount'         => $rejected,
            'pendingPercentage'     => $percentage($pending),
            'underReviewPercentage' => $percentage($underReview),
            'approvedPercentage'    => $percentage($approved),
            'rejectedPercentage'    => $percentage($rejected),
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

        return view('case_manager.my_application', compact('applications'));
    }

    public function viewAssignedApplication($id)
    {
        $user = Auth::user();
        $role = optional($user->role)->name; // 'case_manager' | 'patient' | etc.

        // Case Manager: only see applications assigned to you
        if ($role === 'casemanager') {
            $application = Application::with([
                'program',
                'patient.user',           // so you can show patient email/name
                'documents',
            ])
                ->where('id', $id)
                ->where('reviewer_id', $user->id)
                ->firstOrFail();

            return view('case_manager.view_assigned_application', compact('application'));
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

        // Persist a consistent status that matches the enum definition.
        // The `applications` table stores status values in Title Case (e.g. "Approved").
        $application->update([
            'status' => 'Approved',
            'rejection_reason' => null, // clear any past reason
        ]);

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

        // Update the application status to a valid enum value and store the rejection reason
        $application->update([
            'status' => 'Rejected',
            'rejection_reason' => $data['reason'],
        ]);

        // Optional: trigger a notification to the applicant here

        return back()->with('success', 'The application has been rejected and the applicant has been notified.');
    }

    public function requestMissing(Application $application, Request $request)
    {
        if ($application->reviewer_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        ApplicationMissingRequest::create([
            'application_id' => $application->id,
            'case_manager_id' => Auth::id(),
            'message' => $data['message'],
        ]);

        // TODO (optional): notify the patient to upload the required documents

        return back()->with('success', 'Your request for missing documents has been sent to the applicant.');
    }


    public function patientProfiles()
    {
        // Get all patients with their profiles and applications
        $patients = Patient::with(['user.profile', 'applications.program'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('case_manager.patient_profiles', compact('patients'));
    }

    public function patientChats()
    {
        $user = Auth::user();

        // Get messages for this case manager
        $messages = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with(['sender.profile', 'receiver.profile'])
            ->orderBy('sent_at', 'desc')
            ->paginate(20);

        return view('case_manager.patient_chats', compact('messages'));
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
            'first_name'     => 'nullable|string|max:255',
            'last_name'      => 'nullable|string|max:255',
            'username'       => 'nullable|string|max:255|unique:user_profiles,username,' . ($profile->id ?? 'NULL'),
            'phone'          => 'nullable|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $user->id,
            'gender'         => 'nullable|string|max:10',
            'blood_group'    => 'nullable|string|max:5',
            'date_of_birth'  => 'nullable|date',
            'marital_status' => 'nullable|string|max:255',
            'country'        => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:255',
            'state'          => 'nullable|string|max:255',
        ];
        $data = $request->validate($rules);

        // Update user email
        $user->email = $data['email'];
        $user->save();

        // Update or create profile fields
        $profile->first_name    = $data['first_name'] ?? $profile->first_name;
        $profile->last_name     = $data['last_name'] ?? $profile->last_name;
        $profile->username      = $data['username'] ?? $profile->username;
        $profile->full_name     = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
        $profile->phone         = $data['phone'] ?? $profile->phone;
        $profile->gender        = $data['gender'] ?? $profile->gender;
        $profile->date_of_birth = $data['date_of_birth'] ?? $profile->date_of_birth;
        $profile->country       = $data['country'] ?? $profile->country;
        $profile->city          = $data['city'] ?? $profile->city;
        $profile->state         = $data['state'] ?? $profile->state;

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $profile->avatar = $path;
        }
        $profile->save();

        // Update patient details
        $patient->blood_group    = $data['blood_group'] ?? $patient->blood_group;
        $patient->marital_status = $data['marital_status'] ?? $patient->marital_status;
        $patient->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Change the logged in user’s password. Validates the current
     * password and ensures the new password is confirmed.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = $request->user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Update email/SMS notification preferences for the user’s profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateNotifications(Request $request)
    {
        $profile = $request->user()->profile ?? new UserProfile(['user_id' => $request->user()->id]);
        $profile->email_notification          = $request->has('email_notification');
        $profile->sms_notification            = $request->has('sms_notification');
        $profile->notify_on_new_notifications = $request->has('notify_on_new_notifications');
        $profile->notify_on_direct_message    = $request->has('notify_on_direct_message');
        $profile->save();

        return back()->with('success', 'Notification preferences updated.');
    }

    /**
     * Update account-level fields such as username and alternate email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAccount(Request $request)
    {
        $user    = $request->user();
        $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);

        $data = $request->validate([
            'username'        => 'nullable|string|max:255|unique:user_profiles,username,' . ($profile->id ?? 'NULL'),
            'email'           => 'required|email|unique:users,email,' . $user->id,
            'alternate_email' => 'nullable|email',
        ]);

        $user->email = $data['email'];
        $user->save();

        $profile->username        = $data['username'] ?? $profile->username;
        $profile->alternate_email = $data['alternate_email'] ?? $profile->alternate_email;
        $profile->save();

        return back()->with('success', 'Account settings updated successfully.');
    }

    /**
     * Update social media links for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSocial(Request $request)
    {
        $profile = $request->user()->profile ?? new UserProfile(['user_id' => $request->user()->id]);
        $data = $request->validate([
            'facebook'  => 'nullable|url',
            'twitter'   => 'nullable|url',
            'instagram' => 'nullable|url',
        ]);

        $profile->facebook  = $data['facebook'] ?? $profile->facebook;
        $profile->twitter   = $data['twitter'] ?? $profile->twitter;
        $profile->instagram = $data['instagram'] ?? $profile->instagram;
        $profile->save();

        return back()->with('success', 'Social media links updated.');
    }
}
