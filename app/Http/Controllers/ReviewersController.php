<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SponsorReview;
use App\Models\User;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewersController extends Controller
{
    /**
     * Display a listing of reviewers.
     */
    public function index(Request $request)
    {
        $query = User::reviewers()
            ->with(['profile', 'reviewerApplications'])
            ->withCount('reviewerApplications as assigned_applications_count');

        if ($request->filled('status')) {
            if ($request->status === 'Active') {
                $query->whereHas('reviewerApplications', function ($q) {
                    $q->where('updated_at', '>=', now()->subDays(30));
                });
            } elseif ($request->status === 'Inactive') {
                $query->whereDoesntHave('reviewerApplications', function ($q) {
                    $q->where('updated_at', '>=', now()->subDays(30));
                });
            }
        }

        if ($request->filled('reviewer_id')) {
            $reviewerId = str_replace('RVW-', '', $request->reviewer_id);
            $query->where('id', $reviewerId);
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', '%' . $search . '%')
                    ->orWhereHas('profile', function ($profile) use ($search) {
                        $profile->where('full_name', 'like', '%' . $search . '%');
                    });
            });
        }

        $query->orderBy('created_at', 'desc');
        $reviewers = $query->paginate(10)->appends($request->query());

        $totalReviewers = User::reviewers()->count();
        $activeReviewers = User::reviewers()
            ->whereHas('reviewerApplications', function ($q) {
                $q->where('updated_at', '>=', now()->subDays(30));
            })->count();
        $inactiveReviewers = $totalReviewers - $activeReviewers;
        $totalApplicationsAssigned = Application::whereNotNull('reviewer_id')->count();

        $reviewerIds = User::reviewers()->get()->map(function ($user) {
            return $user->reviewer_id;
        });
        $reviewerEmails = User::reviewers()->pluck('email');

        return view('admin.reviewers', compact(
            'reviewers',
            'totalReviewers',
            'activeReviewers',
            'inactiveReviewers',
            'totalApplicationsAssigned',
            'reviewerIds',
            'reviewerEmails'
        ));
    }

    /**
     * Show the form for creating a new reviewer.
     */
    public function create()
    {
        return view('admin.reviewers.create');
    }

    /**
     * Store a newly created reviewer in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'comment' => 'nullable|string|max:1000',
        ]);

        try {
            SponsorReview::create([
                'sponsor_id' => $request->route('sponsor_id') ?? Auth::id(),
                'rating' => $request->rating,
                'comment' => $request->comment,
                'user_id' => Auth::id(), // Store the authenticated user's ID
            ]);

            return redirect()->back()->with('success', 'Review submitted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to submit review. Please try again.');
        }
    }

    /**
     * Display the specified reviewer.
     */
    public function show(User $reviewer)
    {
        $reviewer->load(['profile', 'reviewerApplications.patient']);
        return view('admin.reviewers.show', compact('reviewer'));
    }

    /**
     * Show the form for editing the specified reviewer.
     */
    public function edit(User $reviewer)
    {
        $reviewer->load('profile');
        return view('admin.reviewers.edit', compact('reviewer'));
    }

    /**
     * Update the specified reviewer in storage.
     */
    public function update(Request $request, User $reviewer)
    {
        // Implementation for updating reviewer
    }

    /**
     * Remove the specified reviewer from storage.
     */
    public function destroy(User $reviewer)
    {
        try {
            // Remove reviewer assignments from applications
            $reviewer->reviewerApplications()->update(['reviewer_id' => null]);

            // Delete the reviewer's profile
            $reviewer->profile()->delete();

            // Delete the reviewer
            $reviewer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Reviewer removed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove reviewer'
            ], 500);
        }
    }

    /**
     * Assign applications to a reviewer
     */
    public function assignApplications(Request $request, User $reviewer)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:applications,id'
        ]);

        try {
            Application::whereIn('id', $request->application_ids)
                ->update(['reviewer_id' => $reviewer->id, 'updated_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Applications assigned successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign applications'
            ], 500);
        }
    }

    /**
     * Get available reviewers for assignment
     */
    public function getAvailableReviewers(Request $request)
    {
        $reviewers = User::reviewers()
            ->with('profile')
            ->when($request->search, function ($query, $search) {
                $query->whereHas('profile', function ($q) use ($search) {
                    $q->where('full_name', 'like', '%' . $search . '%');
                });
            })
            ->limit(10)
            ->get();

        return response()->json($reviewers);
    }

    public function getApplications($id)
    {
        // Fetch applications where the reviewer_id matches the provided reviewer ID
        $applications = Application::where('reviewer_id', $id)
            ->get(); // You can use pagination if needed

        $formattedApplications = $applications->map(function ($app) {
            return [
                'application_id' => $app->id,
                'assigned_date' => $app->updated_at ? $app->updated_at->format('F j, Y') : 'N/A',
                'status' => $app->status,
            ];
        });

        return response()->json($formattedApplications);
    }


    public function removeReviewer($reviewerId)
    {
        try {
            // Find the reviewer
            $reviewer = User::findOrFail($reviewerId);

            // Check if the user has a profile
            if (!$reviewer->profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reviewer profile not found!'
                ], 404);
            }

            // Check if the profile is already inactive
            if ($reviewer->profile->status == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reviewer is already inactive!'
                ], 400);
            }

            // Update the status to 0 in the user_profiles table
            $reviewer->profile->update(['status' => 0]);

            return response()->json([
                'success' => true,
                'message' => 'Reviewer successfully removed!'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Reviewer not found!'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error removing reviewer: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while removing the reviewer.'
            ], 500);
        }
    }




    /**
     * Export reviewers data
     */
    public function export(Request $request)
    {
        // Implementation for exporting reviewers data (CSV/Excel)
        // You can use Laravel Excel package for this
    }
}
