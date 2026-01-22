<?php

namespace App\Http\Controllers;

use App\Models\ProgramRegistration;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProgramRegistrationController extends Controller
{
    public function store(Request $request)
    {
        $quarterOptions = ['q1', 'q2', 'q3', 'q4'];
        $incomeOptions = ['employed', 'self_employed', 'disabled', 'retired', 'student'];
        $authorizationOptions = ['full_name', 'story_anonymous', 'story_full', 'photos', 'contact_details'];

        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'dob' => 'required|date',
            'gender' => 'nullable|string|max:10',
            'blood_group' => 'nullable|string|max:5',
            'medical_condition' => 'nullable|string|max:1000',
            'assistance_type' => 'nullable|string|max:255',
            'justification' => 'nullable|string|max:1000',
            'quarter' => 'nullable|string|in:' . implode(',', $quarterOptions),
            'programs_applied' => 'required|array|min:1',
            'programs_applied.*' => 'string|max:255',
            'active_treatment' => 'required|boolean',
            'pregnant' => 'required|boolean',
            'family_history' => 'nullable|string|max:500',
            'assistance_history' => 'nullable|string|max:500',
            'heard_about' => 'nullable|string|max:255',
            'referral_type' => 'required|string|in:self,facility',
            'treatment_facility_name' => 'required|string|max:255',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:120',
            'state' => 'required|string|max:120',
            'postal_code' => 'required|string|max:20',
            'proof_of_income_status' => 'required|array|min:1',
            'proof_of_income_status.*' => 'in:' . implode(',', $incomeOptions),
            'story' => 'required|string',
            'authorization_choice' => 'required|string|in:allow,decline',
            'authorization_permissions' => 'required_if:authorization_choice,allow|array|min:1',
            'authorization_permissions.*' => 'in:' . implode(',', $authorizationOptions),
            'billing_details' => 'nullable|string|max:2000',
            'signature_data' => 'required|string',
            'treatment_verification_letter' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'bill_statements' => 'required|array|min:1|max:3',
            'bill_statements.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            'income_documents' => 'nullable|array|max:3',
            'income_documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            'documents' => 'nullable|array',
            'documents.*' => 'nullable|file|max:5120',
        ]);

        $program = Program::findOrFail($request->program_id);
        if ($program->max_applications) {
            $currentCount = ProgramRegistration::where('program_id', $program->id)->count();
            if ($currentCount >= $program->max_applications) {
                return redirect()->back()->withErrors([
                    'program_id' => 'This program is no longer accepting applications.',
                ]);
            }
        }

        $now = now()->format('Ymd_His');
        $userId = Auth::id() ?? 'guest';
        $makeFilename = function (string $label, string $extension) use ($userId, $now) {
            $safeLabel = preg_replace('/[^a-z0-9_]+/i', '_', $label);
            $safeExt = strtolower($extension ?: 'bin');
            return strtolower($safeLabel . '_' . $userId . '_' . $now . '_' . Str::random(6) . '.' . $safeExt);
        };

        $treatmentLetterPath = $request->file('treatment_verification_letter')
            ? $request->file('treatment_verification_letter')->storeAs(
                'program_documents/treatment_letters',
                $makeFilename(
                    'program_' . $request->program_id . '_treatment_letter',
                    $request->file('treatment_verification_letter')->getClientOriginalExtension()
                ),
                'public'
            )
            : null;

        $billStatements = [];
        if ($request->hasFile('bill_statements')) {
            foreach ($request->file('bill_statements') as $bill) {
                $billStatements[] = $bill->storeAs(
                    'program_documents/bill_statements',
                    $makeFilename(
                        'program_' . $request->program_id . '_bill_statement',
                        $bill->getClientOriginalExtension()
                    ),
                    'public'
                );
            }
        }

        $incomeDocuments = [];
        if ($request->hasFile('income_documents')) {
            foreach ($request->file('income_documents') as $doc) {
                $incomeDocuments[] = $doc->storeAs(
                    'program_documents/income',
                    $makeFilename(
                        'program_' . $request->program_id . '_income_document',
                        $doc->getClientOriginalExtension()
                    ),
                    'public'
                );
            }
        }

        $additionalDocuments = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $doc) {
                $additionalDocuments[] = $doc->storeAs(
                    'program_documents',
                    $makeFilename(
                        'program_' . $request->program_id . '_document',
                        $doc->getClientOriginalExtension()
                    ),
                    'public'
                );
            }
        }

        $username = strtolower(preg_replace('/\s+/', '', $request->first_name . ' ' . $request->last_name));

        $authorizationChoice = $request->input('authorization_choice', 'allow');
        $authorizationAllow = $authorizationChoice === 'allow';
        $authorizationPermissions = $authorizationAllow ? ($request->input('authorization_permissions', []) ?? []) : [];

        $signaturePath = null;
        $signatureData = $request->input('signature_data');
        if ($signatureData) {
            if (preg_match('/^data:image\\/(png|jpeg);base64,/', $signatureData)) {
                $signaturePath = 'program_documents/signatures/' . $makeFilename(
                    'program_' . $request->program_id . '_signature',
                    'png'
                );
                $encoded = substr($signatureData, strpos($signatureData, ',') + 1);
                Storage::disk('public')->put($signaturePath, base64_decode($encoded));
            } else {
                return redirect()->back()->withErrors(['signature_data' => 'Invalid signature format. Please sign again.']);
            }
        }

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
            'assistance_type' => implode(', ', $request->programs_applied ?? []),
            'quarter_applied' => $request->quarter,
            'programs_applied' => $request->programs_applied,
            'active_treatment' => (bool) $request->active_treatment,
            'pregnant' => (bool) $request->pregnant,
            'family_history' => $request->family_history,
            'assistance_history' => $request->assistance_history,
            'heard_about' => $request->heard_about,
            'referral_type' => $request->referral_type,
            'treatment_facility_name' => $request->treatment_facility_name,
            'street_address' => $request->street_address,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'proof_of_income_status' => $request->proof_of_income_status,
            'story' => $request->story,
            'authorization_allow' => $authorizationAllow,
            'authorization_permissions' => $authorizationPermissions,
            'billing_details' => $request->billing_details,
            'signature' => $signaturePath,
            'justification' => $request->story ?? $request->justification,
            'document_paths' => $additionalDocuments,
            'treatment_letter_path' => $treatmentLetterPath,
            'bill_statement_paths' => $billStatements,
            'income_document_paths' => $incomeDocuments,
            'status' => ProgramRegistration::STATUS_PENDING,
        ]);

        if ($program->max_applications) {
            $currentCount = ProgramRegistration::where('program_id', $program->id)->count();
            if ($currentCount >= $program->max_applications && $program->status !== 'completed') {
                $program->update(['status' => 'completed']);
            }
        }

        // Flash a professional success message back to the session and redirect
        return redirect()->back()->with(
            'success',
            'Your application has been submitted successfully. Our team will review your details and get in touch with you shortly.'
        );
    }

    public function show(ProgramRegistration $registration)
    {
        $user = Auth::user();
        abort_if(!$user || $registration->user_id !== $user->id, 403);

        $registration->load(['program', 'program.sponsorships.sponsor.profile']);

        return view('patient.program_registrations.show', [
            'registration' => $registration,
        ]);
    }
}
