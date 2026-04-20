<?php

namespace App\Http\Controllers;

use App\Mail\BudgetAllocatedToAdmin;
use App\Mail\BudgetAllocatedToPatient;
use App\Models\ProgramRegistration;
use App\Models\RegistrationInvoice;
use App\Models\User;
use App\Models\UserNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class FinanceUserController extends Controller
{
    /**
     * Email to use for applicant-facing mail: form email first, then account email.
     * Ignores empty strings (PHP ?? alone does not fall back when user->email is '').
     */
    private function resolveApplicantEmail(ProgramRegistration $registration): ?string
    {
        $registration->loadMissing('user');

        foreach ([$registration->email, $registration->user?->email] as $candidate) {
            $e = strtolower(trim((string) $candidate));
            if ($e !== '' && filter_var($e, FILTER_VALIDATE_EMAIL)) {
                return $e;
            }
        }

        return null;
    }

    public function dashboard()
    {
        $financeUserId = Auth::id();

        $pendingRegistrations = ProgramRegistration::with(['program', 'user.profile', 'assignedCaseManager.profile'])
            ->where('finance_user_id', $financeUserId)
            ->whereDoesntHave('registrationInvoices')
            ->orderByDesc('sent_to_finance_at')
            ->paginate(10);

        $allocatedCount = ProgramRegistration::where('finance_user_id', $financeUserId)
            ->whereHas('registrationInvoices')
            ->count();

        return view('finance.dashboard', compact('pendingRegistrations', 'allocatedCount'));
    }

    public function registrations()
    {
        $financeUserId = Auth::id();

        $registrations = ProgramRegistration::with(['program', 'user.profile', 'assignedCaseManager.profile', 'registrationInvoices'])
            ->where('finance_user_id', $financeUserId)
            ->orderByDesc('sent_to_finance_at')
            ->paginate(15);

        return view('finance.registrations', compact('registrations'));
    }

    public function showRegistration(ProgramRegistration $registration)
    {
        if ($registration->finance_user_id !== Auth::id()) {
            abort(403);
        }

        $registration->load(['program', 'user.profile', 'assignedCaseManager.profile', 'registrationInvoices']);

        return view('finance.show_registration', compact('registration'));
    }

    /**
     * Download or preview a bill statement attachment. Ensures finance user has access.
     */
    public function downloadBillStatement(Request $request, ProgramRegistration $registration, int $index)
    {
        if ($registration->finance_user_id !== Auth::id()) {
            abort(403);
        }

        $paths = $registration->bill_statement_paths ?? [];
        if (!is_array($paths) || !isset($paths[$index])) {
            abort(404, 'Attachment not found.');
        }

        $path = $paths[$index];
        $publicPath = ltrim(str_replace('public/', '', $path), '/');

        if (!Storage::disk('public')->exists($publicPath)) {
            abort(404, 'File not found.');
        }

        $filename = basename($path);
        $fullPath = Storage::disk('public')->path($publicPath);
        $disposition = $request->boolean('preview') ? 'inline' : 'attachment';

        return response()->download($fullPath, $filename, [], $disposition);
    }

    public function createInvoice(ProgramRegistration $registration)
    {
        if ($registration->finance_user_id !== Auth::id()) {
            abort(403);
        }

        if ($registration->registrationInvoices()->exists()) {
            return redirect()
                ->route('finance.registrations.show', $registration)
                ->with('error', 'An invoice has already been generated for this registration.');
        }

        $registration->load(['program', 'user.profile']);

        $calculatedAmount = $registration->calculated_grant_amount;

        return view('finance.create_invoice', compact('registration', 'calculatedAmount'));
    }

    public function storeInvoice(Request $request, ProgramRegistration $registration)
    {
        if ($registration->finance_user_id !== Auth::id()) {
            abort(403);
        }

        if ($registration->registrationInvoices()->exists()) {
            return redirect()
                ->route('finance.registrations.show', $registration)
                ->with('error', 'An invoice has already been generated for this registration.');
        }

        $calculatedAmount = $registration->calculated_grant_amount;

        $amountRules = ['required', 'numeric', 'min:0.01'];
        if ($calculatedAmount !== null) {
            $amountRules[] = 'max:' . $calculatedAmount;
        }

        $data = $request->validate([
            'payment_purpose' => ['required', 'string', 'max:255'],
            'amount' => $amountRules,
            'payment_method' => ['required', 'string', 'in:Bank Transfer,Credit Card,Cheque,Check,Cash,Other'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        // Patient selection sets the maximum; finance may allocate a lower amount (not higher)
        $amount = $data['amount'];

        $invoice = RegistrationInvoice::create([
            'program_registration_id' => $registration->id,
            'issue_date' => now(),
            'payment_purpose' => $data['payment_purpose'],
            'amount' => $amount,
            'payment_method' => $data['payment_method'],
            'status' => 'Paid',
            'notes' => $data['notes'] ?? null,
        ]);

        // Generate and store invoice PDF (same disk used for mail attachments)
        $registration->load('program');
        $pdf = Pdf::loadView('pdf.invoice', compact('invoice', 'registration'));
        $pdfPath = 'invoices/registration/' . $invoice->id . '_' . preg_replace('/[^a-zA-Z0-9\-]/', '', $invoice->invoice_number) . '.pdf';
        $pdfDisk = (string) config('filesystems.default', 'local');
        Storage::disk($pdfDisk)->put($pdfPath, $pdf->output());
        $invoice->update(['file_path' => $pdfPath]);

        if ($registration->user) {
            try {
                UserNotification::create([
                    'user_id' => $registration->user_id,
                    'title' => 'Budget Allocated',
                    'message' => 'Budget has been allocated for your registration for "' . ($registration->program?->title ?? '') . '". Invoice #' . $invoice->invoice_number . ' has been generated.',
                    'priority' => UserNotification::PRIORITY_IMPORTANT,
                    'link_url' => null,
                ]);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $adminUsers = User::query()
            ->whereHas('role', fn ($q) => $q->where('name', 'admin'))
            ->get();

        foreach ($adminUsers as $admin) {
            try {
                UserNotification::create([
                    'user_id' => $admin->id,
                    'title' => 'Budget Allocated by Finance',
                    'message' => 'Finance has allocated budget for ' . ($registration->full_name ?? 'N/A') . ' - ' . ($registration->program?->title ?? 'Program') . '. Invoice #' . $invoice->invoice_number . ' ($' . number_format($invoice->amount, 2) . ').',
                    'priority' => UserNotification::PRIORITY_IMPORTANT,
                    'link_url' => route('admin.program_registrations.show', $registration),
                ]);
                if ($admin->email) {
                    Mail::to($admin->email)->send(new BudgetAllocatedToAdmin($registration, $invoice, $pdfPath, $pdfDisk));
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $patientEmail = $this->resolveApplicantEmail($registration);
        $applicantMailWarning = null;
        if ($patientEmail) {
            try {
                Mail::to($patientEmail)->send(new BudgetAllocatedToPatient($registration, $invoice, $pdfPath, $pdfDisk));
                Log::info('Budget allocation email sent to applicant', [
                    'registration_id' => $registration->id,
                    'email' => $patientEmail,
                ]);
            } catch (\Throwable $e) {
                report($e);
                Log::error('Budget allocation applicant email failed', [
                    'registration_id' => $registration->id,
                    'email' => $patientEmail,
                    'error' => $e->getMessage(),
                ]);
                $applicantMailWarning = 'The invoice was saved, but the email to the applicant could not be sent. Check mail configuration (MAIL_* in .env) or use “Resend invoice email” on this page.';
            }
        } else {
            Log::warning('Budget allocation skipped applicant email — no valid address', [
                'registration_id' => $registration->id,
                'registration_email_raw' => $registration->email,
                'user_id' => $registration->user_id,
            ]);
            $applicantMailWarning = 'The invoice was saved, but no valid applicant email was found (application form email and account email were missing or invalid), so the patient was not emailed.';
        }

        $redirect = redirect()
            ->route('finance.registrations.show', $registration)
            ->with('success', 'Invoice generated successfully. Budget has been allocated to the patient request.');
        if ($applicantMailWarning) {
            $redirect->with('warning', $applicantMailWarning);
        }

        return $redirect;
    }

    public function resendInvoiceEmails(Request $request, ProgramRegistration $registration, RegistrationInvoice $invoice)
    {
        if ($registration->finance_user_id !== Auth::id()) {
            abort(403);
        }
        if ($invoice->program_registration_id !== $registration->id) {
            abort(404);
        }

        $registration->load(['program', 'user.profile']);
        $pdfPath = $invoice->file_path;
        $pdfDisk = (string) config('filesystems.default', 'local');

        $adminUsers = User::query()
            ->whereHas('role', fn ($q) => $q->where('name', 'admin'))
            ->get();

        foreach ($adminUsers as $admin) {
            if ($admin->email) {
                try {
                    Mail::to($admin->email)->send(new BudgetAllocatedToAdmin($registration, $invoice, $pdfPath, $pdfDisk));
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }

        $patientEmail = $this->resolveApplicantEmail($registration);
        if ($patientEmail) {
            try {
                Mail::to($patientEmail)->send(new BudgetAllocatedToPatient($registration, $invoice, $pdfPath, $pdfDisk));
                Log::info('Budget allocation resend: email sent to applicant', [
                    'registration_id' => $registration->id,
                    'email' => $patientEmail,
                ]);
            } catch (\Throwable $e) {
                report($e);
                Log::error('Budget allocation resend: applicant email failed', [
                    'registration_id' => $registration->id,
                    'email' => $patientEmail,
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            Log::warning('Budget allocation resend skipped applicant — no valid address', [
                'registration_id' => $registration->id,
            ]);
        }

        return redirect()
            ->route('finance.registrations.show', $registration)
            ->with('success', 'Invoice PDF emails were sent again to the patient and administrators.');
    }
}
