<?php

namespace App\Http\Controllers;

use App\Models\ProgramRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramRegistrationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'dob' => 'required|date',
            'gender' => 'nullable|string|max:10',
            'blood_group' => 'nullable|string|max:5',
            'medical_condition' => 'required|string|max:1000',
            'assistance_type' => 'required|string|max:255',
            'justification' => 'required|string|max:1000',
            'documents.*' => 'nullable|file|max:5120',
        ]);

        $documentPaths = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $doc) {
                $path = $doc->store('program_documents', 'public');
                $documentPaths[] = $path;
            }
        }

        $username = strtolower(preg_replace('/\s+/', '', $request->first_name . ' ' . $request->last_name));

        ProgramRegistration::create([
            'program_id' => $request->program_id,
            'user_id' => Auth::id(),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $username,
            'email' => $request->email,
            'phone' => $request->phone,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'blood_group' => $request->blood_group,
            'medical_condition' => $request->medical_condition,
            'assistance_type' => $request->assistance_type,
            'justification' => $request->justification,
            'document_paths' => $documentPaths,
        ]);

        // Flash a professional success message back to the session and redirect
        return redirect()->back()->with(
            'success',
            'Your application has been submitted successfully. Our team will review your details and get in touch with you shortly.'
        );
    }
}
