<?php

namespace App\Http\Controllers;

use App\Mail\GrantAssistancePreviouslyReceivedMail;
use App\Models\Program;
use App\Models\ProgramRegistration;
use App\Support\MomentsThatMatterOptions;
use App\Support\PatientBillSupportExpenseTypes;
use App\Support\ProgramApplicationEthnicityOptions;
use App\Support\ProgramApplicationCapacity;
use App\Support\ProgramRegistrationNotifiers;
use App\Support\ProgramType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProgramRegistrationController extends Controller
{
    /** Laravel file size rules use kilobytes; 25MB per upload. */
    private const APPLICATION_FILE_MAX_KB = 25 * 1024;

    public function store(Request $request)
    {
        $program = Program::findOrFail($request->input('program_id'));

        if ($program->isMomentsThatMatter()) {
            return $this->storeMomentsThatMatter($request, $program);
        }

        $quarterOptions = ['option1', 'option2'];
        $incomeOptions = ['employed', 'self_employed', 'disabled', 'retired', 'student'];
        $programPickOne = [
            'Breast Cancer Treatment Assistance Program (up to $500)',
            'Survivor Health and Wellness Assistance Program (up to $250)',
        ];

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
            'breast_cancer_stage' => ['required', 'string', Rule::in(['0', '1', '2', '3', '4', 'unknown'])],
            'ethnicity' => ['required', 'string', 'max:160', Rule::in(ProgramApplicationEthnicityOptions::OPTIONS)],
            'assistance_type' => 'nullable|string|max:255',
            'justification' => 'nullable|string|max:1000',
            'quarter' => 'required|string|in:'.implode(',', $quarterOptions),
            'programs_applied' => ['required', 'string', Rule::in($programPickOne)],
            'active_treatment' => 'required|boolean',
            'family_history' => 'required|string|in:Yes,No',
            'assistance_history' => 'required|string|in:Yes,No',
            'heard_about' => 'required|string|max:255',
            'referral_type' => 'required|string|in:self,facility',
            'treatment_facility_name' => 'required|string|max:255',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:120',
            'state' => 'required|string|max:120',
            'postal_code' => 'required|string|max:20',
            'proof_of_income_status' => ['required', 'string', Rule::in($incomeOptions)],
            'story' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $wordCount = str_word_count($value);
                    if ($wordCount > 1000) {
                        $fail("Your story may not exceed 1000 words (currently {$wordCount} words).");
                    }
                },
            ],
            'story_notes' => 'nullable|string|max:2000',
            'authorization_choice' => 'required|string|in:allow,decline',
            'billing_details' => 'nullable|string|max:2000',
            'bill_name' => 'nullable|array',
            'bill_name.*' => 'nullable|string|max:500',
            'bill_url' => 'nullable|array',
            'bill_url.*' => 'nullable|string|max:2000',
            'bill_amount' => 'nullable|array',
            'bill_amount.*' => 'nullable|numeric|min:0|max:500',
            'bill_account' => 'nullable|array',
            'bill_account.*' => 'nullable|string|max:255',
            'bill_support_expense' => 'nullable|array',
            'bill_support_expense.*' => ['nullable', 'string', 'max:120', Rule::in(array_merge([''], PatientBillSupportExpenseTypes::OPTIONS))],
            'bill_provider_contact' => 'nullable|array',
            'bill_provider_contact.*' => 'nullable|string|max:255',
            'needs_food_assistance' => 'nullable|string|in:yes,no',
            'needs_medical_bills_assistance' => 'nullable|string|in:yes,no',
            'bill_notes' => 'nullable|array',
            'bill_notes.*' => 'nullable|string|max:1000',
            'signature_data' => 'required|string',
            'treatment_verification_letter' => 'required|file|mimes:pdf,jpg,jpeg,png|max:'.self::APPLICATION_FILE_MAX_KB,
            'bill_statements' => 'required|array|min:1|max:3',
            'bill_statements.*' => 'file|mimes:pdf,jpg,jpeg,png|max:'.self::APPLICATION_FILE_MAX_KB,
            'income_documents' => 'nullable|array|max:3',
            'income_documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:'.self::APPLICATION_FILE_MAX_KB,
            'story_media' => 'nullable|array|max:5',
            'story_media.*' => 'file|mimes:pdf,jpg,jpeg,png|max:'.self::APPLICATION_FILE_MAX_KB,
            'documents' => 'nullable|array',
            'documents.*' => 'nullable|file|max:'.self::APPLICATION_FILE_MAX_KB,
        ]);

        if ($request->assistance_history === 'Yes') {
            $mailSent = false;
            try {
                Mail::to($request->email)->send(new GrantAssistancePreviouslyReceivedMail((string) $request->first_name));
                $mailSent = true;
            } catch (\Throwable $e) {
                report($e);
                Log::warning('Grant one-time follow-up email failed to send', [
                    'exception' => $e->getMessage(),
                ]);
            }

            $message = $mailSent
                ? 'Thank you for your submission. At this time, our grants are offered on a one-time basis. We have sent a follow-up message to the email you provided.'
                : 'Thank you for your submission. At this time, our grants are offered on a one-time basis. We could not deliver the follow-up email—please check that the address is correct, look in your spam or promotions folder, or contact us if you need help.';

            return redirect()->back()->with('warning', $message);
        }

        if (! $program->isApplicationOpen()) {
            return redirect()->back()->withErrors([
                'program_id' => 'Applications for this program are not open yet or have closed. Please check the application start and end dates.',
            ]);
        }

        $alreadyAppliedThisCycle = ProgramRegistration::query()
            ->where('user_id', Auth::id())
            ->where('program_id', $program->id)
            ->where('quarter_applied', (string) $request->quarter)
            ->exists();
        if ($alreadyAppliedThisCycle) {
            return redirect()->back()->withErrors([
                'program_id' => 'You already applied for this program in the selected cycle.',
            ])->with('warning', 'Our records indicate that an application has already been submitted for this program. Please note only one submission is permitted per applicant during each application cycle.');
        }

        if ($program->hasReachedMaxApplications()) {
            return redirect()->back()->withErrors([
                'program_id' => \App\Support\ProgramApplicationCapacity::CLOSED_MESSAGE,
            ]);
        }

        $now = now()->format('Ymd_His');
        $userId = Auth::id() ?? 'guest';
        $makeFilename = function (string $label, string $extension) use ($userId, $now) {
            $safeLabel = preg_replace('/[^a-z0-9_]+/i', '_', $label);
            $safeExt = strtolower($extension ?: 'bin');

            return strtolower($safeLabel.'_'.$userId.'_'.$now.'_'.Str::random(6).'.'.$safeExt);
        };

        $treatmentLetterPath = $request->file('treatment_verification_letter')
            ? $request->file('treatment_verification_letter')->storeAs(
                'program_documents/treatment_letters',
                $makeFilename(
                    'program_'.$request->program_id.'_treatment_letter',
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
                        'program_'.$request->program_id.'_bill_statement',
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
                        'program_'.$request->program_id.'_income_document',
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
                        'program_'.$request->program_id.'_document',
                        $doc->getClientOriginalExtension()
                    ),
                    'public'
                );
            }
        }

        $storyMediaPaths = [];
        if ($request->hasFile('story_media')) {
            foreach ($request->file('story_media') as $file) {
                if (! $file) {
                    continue;
                }
                $storyMediaPaths[] = $file->storeAs(
                    'program_documents/story_media',
                    $makeFilename(
                        'program_'.$request->program_id.'_story_media',
                        $file->getClientOriginalExtension()
                    ),
                    'public'
                );
            }
        }

        $username = strtolower(preg_replace('/\s+/', '', $request->first_name.' '.$request->last_name));

        $authorizationChoice = $request->input('authorization_choice', 'allow');
        $authorizationAllow = $authorizationChoice === 'allow';
        $authorizationPermissions = [];

        $patientBillLineItems = [];
        $billNames = $request->input('bill_name', []);
        if (is_array($billNames)) {
            foreach ($billNames as $i => $name) {
                $n = trim((string) $name);
                $url = trim((string) ($request->input('bill_url')[$i] ?? ''));
                $amount = trim((string) ($request->input('bill_amount')[$i] ?? ''));
                $account = trim((string) ($request->input('bill_account')[$i] ?? ''));
                $notes = trim((string) ($request->input('bill_notes')[$i] ?? ''));
                $expense = trim((string) ($request->input('bill_support_expense')[$i] ?? ''));
                $providerContact = trim((string) ($request->input('bill_provider_contact')[$i] ?? ''));
                if ($n === '' && $url === '' && $amount === '' && $account === '' && $notes === '' && $expense === '' && $providerContact === '') {
                    continue;
                }
                $patientBillLineItems[] = [
                    'name' => $n,
                    'url' => $url,
                    'amount' => $amount,
                    'support_expense_type' => $expense !== '' ? $expense : null,
                    'provider_contact' => $providerContact !== '' ? $providerContact : null,
                    'account_number' => $account,
                    'notes' => $notes,
                ];
            }
        }

        $signaturePath = null;
        $signatureData = $request->input('signature_data');
        if ($signatureData) {
            if (preg_match('/^data:image\\/(png|jpeg);base64,/', $signatureData)) {
                $signaturePath = 'program_documents/signatures/'.$makeFilename(
                    'program_'.$request->program_id.'_signature',
                    'png'
                );
                $encoded = substr($signatureData, strpos($signatureData, ',') + 1);
                Storage::disk('public')->put($signaturePath, base64_decode($encoded));
            } else {
                return redirect()->back()->withErrors(['signature_data' => 'Invalid signature format. Please sign again.']);
            }
        }

        $registration = ProgramRegistration::create([
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
            'breast_cancer_stage' => $request->breast_cancer_stage,
            'ethnicity' => $request->ethnicity,
            'assistance_type' => (string) $request->programs_applied,
            'quarter_applied' => $request->quarter,
            'programs_applied' => [(string) $request->programs_applied],
            'active_treatment' => (bool) $request->active_treatment,
            'pregnant' => false,
            'family_history' => $request->family_history,
            'assistance_history' => $request->assistance_history,
            'heard_about' => $request->heard_about,
            'referral_type' => $request->referral_type,
            'treatment_facility_name' => $request->treatment_facility_name,
            'street_address' => $request->street_address,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'proof_of_income_status' => [(string) $request->proof_of_income_status],
            'story' => $request->story,
            'story_notes' => $request->story_notes,
            'story_media_paths' => $storyMediaPaths !== [] ? $storyMediaPaths : null,
            'authorization_allow' => $authorizationAllow,
            'authorization_permissions' => $authorizationPermissions,
            'billing_details' => $request->billing_details,
            'patient_bill_line_items' => $patientBillLineItems !== [] ? $patientBillLineItems : null,
            'signature' => $signaturePath,
            'justification' => $request->story ?? $request->justification,
            'document_paths' => $additionalDocuments,
            'treatment_letter_path' => $treatmentLetterPath,
            'bill_statement_paths' => $billStatements,
            'income_document_paths' => $incomeDocuments,
            'status' => ProgramRegistration::STATUS_PENDING,
        ]);

        ProgramRegistrationNotifiers::notifyAdmins(
            'New program application received',
            'A new financial assistance application has been submitted and is awaiting review.',
            $registration
        );

        ProgramRegistrationNotifiers::notifyCaseManagersInbox(
            'New application in your inbox',
            'A patient submitted a financial assistance application. Open it to review, add billing links if needed, and approve or reject.',
            $registration
        );

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

    protected function storeMomentsThatMatter(Request $request, Program $program)
    {
        $requiredAcks = MomentsThatMatterOptions::REQUIRED_ACKNOWLEDGMENTS;

        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'dob' => 'required|date',
            'street_address' => 'required|string|max:255',
            'apartment_suite' => 'nullable|string|max:120',
            'city' => 'required|string|max:120',
            'state' => 'required|string|max:120',
            'postal_code' => 'required|string|max:20',
            'mtm_package' => ['required', 'string', Rule::in(array_keys(MomentsThatMatterOptions::PACKAGES))],
            'applying_for' => ['required', 'string', Rule::in(array_keys(MomentsThatMatterOptions::APPLYING_FOR))],
            'patient_loved_one_name' => 'nullable|string|max:255',
            'mtm_treatment_status' => ['required', 'string', Rule::in(array_keys(MomentsThatMatterOptions::TREATMENT_STATUS))],
            'mtm_treatment_status_other' => 'nullable|string|max:255|required_if:mtm_treatment_status,other',
            'mtm_diagnosis_type' => 'nullable|string|max:255',
            'mtm_diagnosis_date' => 'nullable|date',
            'story' => 'required|string|max:10000',
            'mtm_story_permission' => ['required', 'string', Rule::in(array_keys(MomentsThatMatterOptions::STORY_PERMISSION))],
            'mtm_acknowledgments' => ['required', 'array', 'size:'.count($requiredAcks)],
            'mtm_acknowledgments.*' => ['string', Rule::in($requiredAcks)],
            'signature_data' => 'required|string',
            'signature_date' => 'required|date',
        ]);

        if (! $program->isApplicationOpen()) {
            return redirect()->back()->withErrors([
                'program_id' => 'Applications for this program are not open yet or have closed.',
            ]);
        }

        if (ProgramApplicationCapacity::userHasMomentsApplicationThisYear((int) Auth::id(), $program->id)) {
            return redirect()->back()->withErrors([
                'program_id' => 'Our records show you have already submitted a Moments That Matter request this year. Only one package per individual is provided annually.',
            ]);
        }

        if ($program->hasReachedMaxApplications()) {
            return redirect()->back()->withErrors([
                'program_id' => 'We are no longer accepting Moments That Matter applications at this time. Thank you for your interest.',
            ]);
        }

        if (in_array($request->applying_for, ['family_member', 'friend_loved_one'], true)
            && trim((string) $request->patient_loved_one_name) === '') {
            return redirect()->back()->withErrors([
                'patient_loved_one_name' => 'Please enter the name of the patient or loved one you are applying for.',
            ]);
        }

        $now = now()->format('Ymd_His');
        $userId = Auth::id() ?? 'guest';
        $makeFilename = function (string $label, string $extension) use ($userId, $now) {
            $safeLabel = preg_replace('/[^a-z0-9_]+/i', '_', $label);
            $safeExt = strtolower($extension ?: 'bin');

            return strtolower($safeLabel.'_'.$userId.'_'.$now.'_'.Str::random(6).'.'.$safeExt);
        };

        $signaturePath = null;
        $signatureData = $request->input('signature_data');
        if (preg_match('/^data:image\\/(png|jpeg);base64,/', (string) $signatureData)) {
            $signaturePath = 'program_documents/signatures/'.$makeFilename(
                'mtm_program_'.$request->program_id.'_signature',
                'png'
            );
            $encoded = substr($signatureData, strpos($signatureData, ',') + 1);
            Storage::disk('public')->put($signaturePath, base64_decode($encoded));
        } else {
            return redirect()->back()->withErrors(['signature_data' => 'Invalid signature format. Please sign again.']);
        }

        $username = strtolower(preg_replace('/\s+/', '', $request->first_name.' '.$request->last_name));
        $packageLabel = MomentsThatMatterOptions::PACKAGES[$request->mtm_package] ?? $request->mtm_package;

        $registration = ProgramRegistration::create([
            'program_id' => $program->id,
            'user_id' => Auth::id(),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $username,
            'email' => $request->email,
            'phone' => $request->phone,
            'dob' => $request->dob,
            'street_address' => $request->street_address,
            'apartment_suite' => $request->apartment_suite,
            'shipping_usa' => true,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'mtm_package' => $request->mtm_package,
            'applying_for' => $request->applying_for,
            'patient_loved_one_name' => $request->patient_loved_one_name,
            'mtm_treatment_status' => $request->mtm_treatment_status,
            'mtm_treatment_status_other' => $request->mtm_treatment_status === 'other' ? $request->mtm_treatment_status_other : null,
            'mtm_diagnosis_type' => $request->mtm_diagnosis_type,
            'mtm_diagnosis_date' => $request->mtm_diagnosis_date,
            'story' => $request->story,
            'mtm_story_permission' => $request->mtm_story_permission,
            'mtm_acknowledgments' => array_values($request->input('mtm_acknowledgments', [])),
            'assistance_type' => $packageLabel,
            'programs_applied' => [$packageLabel],
            'quarter_applied' => (string) now()->year,
            'signature' => $signaturePath,
            'signature_date' => $request->signature_date,
            'justification' => $request->story,
            'status' => ProgramRegistration::STATUS_PENDING,
        ]);

        ProgramRegistrationNotifiers::notifyAdmins(
            'New Moments That Matter application',
            'A new care package application has been submitted and is awaiting admin review.',
            $registration
        );

        if ($program->max_applications) {
            $currentCount = ProgramRegistration::where('program_id', $program->id)->count();
            if ($currentCount >= $program->max_applications && $program->status !== 'completed') {
                $program->update(['status' => 'completed']);
            }
        }

        return redirect()->back()->with('mtm_submission_notice', true);
    }

    public function show(ProgramRegistration $registration)
    {
        $user = Auth::user();
        abort_if(! $user || $registration->user_id !== $user->id, 403);

        $registration->load(['program', 'program.sponsorships.sponsor.profile']);

        return view('patient.program_registrations.show', [
            'registration' => $registration,
        ]);
    }
}
