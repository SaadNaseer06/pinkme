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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class FinanceUserController extends Controller
{
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

        return view('finance.create_invoice', compact('registration'));
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

        $data = $request->validate([
            'payment_purpose' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'in:Bank Transfer,Credit Card,Cheque,Check,Cash,Other'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $invoice = RegistrationInvoice::create([
            'program_registration_id' => $registration->id,
            'issue_date' => now(),
            'payment_purpose' => $data['payment_purpose'],
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'],
            'status' => 'Paid',
            'notes' => $data['notes'] ?? null,
        ]);

        // Generate and store invoice PDF
        $registration->load('program');
        $pdf = Pdf::loadView('pdf.invoice', compact('invoice', 'registration'));
        $pdfContent = $pdf->output();
        $pdfPath = 'invoices/registration/' . $invoice->id . '_' . preg_replace('/[^a-zA-Z0-9\-]/', '', $invoice->invoice_number) . '.pdf';
        Storage::put($pdfPath, $pdfContent);
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
                    Mail::to($admin->email)->send(new BudgetAllocatedToAdmin($registration, $invoice, $pdfContent));
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        // Send email to patient (applicant)
        $patientEmail = $registration->user?->email ?? $registration->email;
        if ($patientEmail) {
            try {
                Mail::to($patientEmail)->send(new BudgetAllocatedToPatient($registration, $invoice, $pdfContent));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()
            ->route('finance.registrations.show', $registration)
            ->with('success', 'Invoice generated successfully. Budget has been allocated to the patient request.');
    }
}
