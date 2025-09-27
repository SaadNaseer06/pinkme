<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApplicationRequest;
use App\Models\Application;
use App\Models\Patient;
use App\Models\SponsorshipProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function createApplication()
    {
        $user = Auth::user();
        $patient = Patient::where('user_id', $user->id)->first();

        if (!$patient) {
            $patient = Patient::create(['user_id' => $user->id]);
        }

        $programs = SponsorshipProgram::where('end_date', '>', now())
            ->orWhereNull('end_date')
            ->get();
        // dd($programs);

        return view('patient.create_application', compact('programs'));
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

    public function edit($id)
    {
        $application = Application::findOrFail($id);
        $programs = SponsorshipProgram::all();

        return view('patient.edit_application', compact('application', 'programs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'blood_group' => 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'assistance_type' => 'required|string',
            'program_id' => 'required|exists:sponsorship_programs,id',
            'description' => 'required|string|min:20',
            'documents.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        DB::beginTransaction();

        try {
            $application = Application::findOrFail($id);

            $application->update([
                'title' => $request->title,
                'program_id' => $request->program_id,
                'description' => $request->description,
                'assistance_type' => $request->assistance_type,
                'blood_group' => $request->blood_group,
            ]);

            $hasNewDocuments = false;

            if ($request->hasFile('documents')) {
                $hasNewDocuments = true;

                // 1. Delete all existing documents and their files
                foreach ($application->documents as $existingDoc) {
                    if (Storage::disk('public')->exists($existingDoc->filepath)) {
                        Storage::disk('public')->delete($existingDoc->filepath);
                    }
                    $existingDoc->delete();
                }

                // 2. Save all new documents
                foreach ($request->file('documents') as $file) {
                    $path = $file->store('documents', 'public');

                    $application->documents()->create([
                        'filename' => $file->getClientOriginalName(),
                        'filepath' => $path,
                        'filetype' => $file->getClientMimeType(),
                    ]);
                }
            }


            // If documents were updated, check for missing request and update status
            if ($hasNewDocuments) {
                $missingRequest = DB::table('application_missing_requests')
                    ->where('application_id', $application->id)
                    ->first();

                if ($missingRequest) {
                    DB::table('application_missing_requests')
                        ->where('application_id', $application->id)
                        ->delete();

                    $application->update([
                        'status' => 'Pending'
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('patient.applications')
                ->with('success', 'Application updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Application Update Error: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Update failed: ' . $e->getMessage()]);
        }
    }


    public function storeApplication(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'blood_group' => 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'assistance_type' => 'required|string',
            'program_id' => 'required|exists:sponsorship_programs,id',
            'description' => 'required|string|min:20',
            'documents.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);
        try {
            $user = Auth::user();

            // Ensure patient exists
            $patient = Patient::firstOrCreate(['user_id' => $user->id]);

            // Create the application
            $application = Application::create([
                'patient_id' => $patient->id,
                'reviewer_id' => null,
                'program_id' => $request->program_id,
                'title' => $request->title,
                'blood_group' => $request->blood_group,
                'assistance_type' => $request->assistance_type,
                'description' => $request->description,
                'status' => 'Pending',
                'submission_date' => now(),
            ]);

            // Save documents
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $path = $file->store('documents', 'public');

                    $application->documents()->create([
                        'filename' => $file->getClientOriginalName(),
                        'filepath' => $path,
                        'filetype' => $file->getClientMimeType(),
                    ]);
                }
            }

            return redirect()->route('patient.applications')
                ->with('success', 'Your application was submitted successfully and is under review.');
        } catch (\Throwable $e) {
            // Log error if necessary
            return redirect()->back()->withInput()->with('error', 'An unexpected error occurred. Please try again.');
        }
    }
}
