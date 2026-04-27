<?php

namespace App\Http\Controllers;

use App\Mail\BudgetAllocatedToAdmin;
use App\Mail\BudgetAllocatedToPatient;
use App\Models\Message;
use App\Models\ProgramRegistration;
use App\Models\RegistrationInvoice;
use App\Models\User;
use App\Models\UserNotification;
use App\Support\PatientApplicationNotifications;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    /**
     * Registrations awaiting budget allocation: shared finance queue (pending_finance) plus legacy rows assigned to this user.
     */
    private function financeBudgetQueueBaseQuery(int $financeUserId): Builder
    {
        return ProgramRegistration::query()
            ->whereDoesntHave('registrationInvoices')
            ->where(function ($outer) use ($financeUserId): void {
                $outer->where(function ($q) use ($financeUserId): void {
                    $q->where('status', ProgramRegistration::STATUS_PENDING_FINANCE)
                        ->whereNotNull('sent_to_finance_at')
                        ->where(function ($q2) use ($financeUserId): void {
                            $q2->whereNull('finance_user_id')
                                ->orWhere('finance_user_id', $financeUserId);
                        });
                })->orWhere(function ($q) use ($financeUserId): void {
                    $q->where('status', ProgramRegistration::STATUS_APPROVED)
                        ->whereNotNull('sent_to_finance_at')
                        ->where('finance_user_id', $financeUserId);
                });
            });
    }

    private function financeUserOwnsRegistration(ProgramRegistration $registration): bool
    {
        return (int) $registration->finance_user_id === (int) Auth::id();
    }

    public function dashboard()
    {
        $financeUserId = Auth::id();

        $pendingRegistrations = $this->financeBudgetQueueBaseQuery($financeUserId)
            ->with(['program', 'user.profile', 'assignedCaseManager.profile', 'financeUser.profile'])
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

        $registrations = ProgramRegistration::with(['program', 'user.profile', 'assignedCaseManager.profile', 'financeUser.profile', 'registrationInvoices'])
            ->where(function ($q) use ($financeUserId): void {
                $q->where('finance_user_id', $financeUserId)
                    ->orWhere(function ($q2): void {
                        $q2->where('status', ProgramRegistration::STATUS_PENDING_FINANCE)
                            ->whereNull('finance_user_id')
                            ->whereNotNull('sent_to_finance_at');
                    });
            })
            ->orderByDesc('sent_to_finance_at')
            ->paginate(15);

        return view('finance.registrations', compact('registrations'));
    }

    public function showRegistration(ProgramRegistration $registration)
    {
        if ($registration->registrationInvoices()->exists()) {
            if (! $this->financeUserOwnsRegistration($registration)) {
                abort(403);
            }
        } elseif (strtolower((string) $registration->status) === ProgramRegistration::STATUS_PENDING_FINANCE) {
            if ($registration->finance_user_id !== null && ! $this->financeUserOwnsRegistration($registration)) {
                return redirect()
                    ->route('finance.dashboard')
                    ->with('error', 'Another finance team member is handling this application.');
            }
            if ($registration->finance_user_id === null) {
                DB::transaction(function () use ($registration): void {
                    $reg = ProgramRegistration::whereKey($registration->id)->lockForUpdate()->first();
                    if (
                        ! $reg
                        || strtolower((string) $reg->status) !== ProgramRegistration::STATUS_PENDING_FINANCE
                        || $reg->registrationInvoices()->exists()
                    ) {
                        return;
                    }
                    if ($reg->finance_user_id !== null) {
                        return;
                    }
                    $reg->forceFill(['finance_user_id' => Auth::id()])->save();
                });
                $registration->refresh();
            }
            if (! $this->financeUserOwnsRegistration($registration)) {
                return redirect()
                    ->route('finance.dashboard')
                    ->with('error', 'This application was claimed by another team member.');
            }
        } elseif (
            strtolower((string) $registration->status) === ProgramRegistration::STATUS_APPROVED
            && $registration->sent_to_finance_at
        ) {
            if (! $this->financeUserOwnsRegistration($registration)) {
                abort(403);
            }
        } else {
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
        if (! $this->financeUserOwnsRegistration($registration)) {
            abort(403);
        }

        $paths = $registration->bill_statement_paths ?? [];
        if (! is_array($paths) || ! isset($paths[$index])) {
            abort(404, 'Attachment not found.');
        }

        $path = $paths[$index];
        $publicPath = ltrim(str_replace('public/', '', $path), '/');

        if (! Storage::disk('public')->exists($publicPath)) {
            abort(404, 'File not found.');
        }

        $filename = basename($path);
        $fullPath = Storage::disk('public')->path($publicPath);
        $disposition = $request->boolean('preview') ? 'inline' : 'attachment';

        return response()->download($fullPath, $filename, [], $disposition);
    }

    public function createInvoice(ProgramRegistration $registration)
    {
        if (! $this->financeUserOwnsRegistration($registration)) {
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
        if (! $this->financeUserOwnsRegistration($registration)) {
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
            $amountRules[] = 'max:'.$calculatedAmount;
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
        $pdfPath = 'invoices/registration/'.$invoice->id.'_'.preg_replace('/[^a-zA-Z0-9\-]/', '', $invoice->invoice_number).'.pdf';
        $pdfDisk = (string) config('filesystems.default', 'local');
        Storage::disk($pdfDisk)->put($pdfPath, $pdf->output());
        $invoice->update(['file_path' => $pdfPath]);

        $registration->refresh();
        if (strtolower((string) $registration->status) === ProgramRegistration::STATUS_PENDING_FINANCE) {
            $registration->update(['status' => ProgramRegistration::STATUS_APPROVED]);
        }

        PatientApplicationNotifications::programRegistrationBudgetAllocated($registration, $invoice);

        $adminUsers = User::query()
            ->whereHas('role', fn ($q) => $q->where('name', 'admin'))
            ->get();

        foreach ($adminUsers as $admin) {
            try {
                UserNotification::create([
                    'user_id' => $admin->id,
                    'title' => 'Budget Allocated by Finance',
                    'message' => 'Finance has allocated budget for '.($registration->full_name ?? 'N/A').' - '.($registration->program?->title ?? 'Program').'. Invoice #'.$invoice->invoice_number.' ($'.number_format($invoice->amount, 2).').',
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
        if (! $this->financeUserOwnsRegistration($registration)) {
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

    /**
     * Claim a finance-queue registration from the team chat page (exclusive for other finance users).
     */
    public function claimRegistrationFromChat(ProgramRegistration $registration)
    {
        if (strtolower((string) $registration->status) !== ProgramRegistration::STATUS_PENDING_FINANCE) {
            return redirect()
                ->route('finance.team_chats')
                ->with('error', 'Only applications in the finance queue can be claimed here.');
        }

        if ($registration->registrationInvoices()->exists()) {
            return redirect()
                ->route('finance.team_chats')
                ->with('error', 'This application already has a budget allocation.');
        }

        DB::transaction(function () use ($registration): void {
            $reg = ProgramRegistration::whereKey($registration->id)->lockForUpdate()->first();
            if (
                ! $reg
                || strtolower((string) $reg->status) !== ProgramRegistration::STATUS_PENDING_FINANCE
                || $reg->registrationInvoices()->exists()
            ) {
                return;
            }
            if ($reg->finance_user_id !== null && (int) $reg->finance_user_id !== (int) Auth::id()) {
                return;
            }
            if ($reg->finance_user_id === null) {
                $reg->forceFill(['finance_user_id' => Auth::id()])->save();
            }
        });

        $registration->refresh();

        if ((int) $registration->finance_user_id !== (int) Auth::id()) {
            return redirect()
                ->route('finance.team_chats')
                ->with('error', 'Another finance user claimed this application first.');
        }

        return redirect()
            ->route('finance.team_chats')
            ->with('success', 'You have claimed this application. Other finance users no longer see it as open.');
    }

    /**
     * Chat with administrators (and claim finance-queue items from the same page).
     */
    public function teamChats(Request $request)
    {
        $user = Auth::user();

        $financeClaimableRegistrations = $this->financeClaimableRegistrationsForTeamChats((int) $user->id);

        $adminUsers = User::query()
            ->whereHas('role', fn ($q) => $q->where('name', 'admin'))
            ->with('profile')
            ->orderBy('email')
            ->get();

        if ($adminUsers->isEmpty()) {
            return view('finance.team_chats', [
                'contacts' => collect(),
                'activeContact' => null,
                'activeContactId' => null,
                'messagesPayload' => [],
                'financeClaimableRegistrations' => $financeClaimableRegistrations,
            ]);
        }

        $activeContactId = (int) $request->query('contact', $adminUsers->first()->id);
        $activeContact = $adminUsers->firstWhere('id', $activeContactId) ?? $adminUsers->first();

        Message::markThreadAsRead($user->id, $activeContact->id);

        $messagesPayload = Message::betweenUsers($user->id, $activeContact->id)
            ->with(['sender.profile', 'receiver.profile'])
            ->orderBy('sent_at')
            ->limit(200)
            ->get()
            ->map->toFrontendPayload()
            ->values();

        $contactsPayload = $adminUsers->map(function (User $contact) use ($user) {
            $latestMessage = Message::betweenUsers($user->id, $contact->id)
                ->latest('sent_at')
                ->first();

            $unreadCount = Message::betweenUsers($user->id, $contact->id)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();

            return [
                'id' => $contact->id,
                'name' => optional($contact->profile)->full_name ?? $contact->email,
                'avatar_url' => $contact->avatar_url,
                'latest_message' => $latestMessage?->content,
                'latest_at' => optional($latestMessage?->sent_at)->format('H:i'),
                'unread_count' => $unreadCount,
                'fetch_url' => route('chat.messages.index', $contact),
                'send_url' => route('chat.messages.store', $contact),
            ];
        })->values();

        return view('finance.team_chats', [
            'contacts' => $contactsPayload,
            'activeContact' => [
                'id' => $activeContact->id,
                'name' => optional($activeContact->profile)->full_name ?? $activeContact->email,
                'avatar_url' => $activeContact->avatar_url,
            ],
            'activeContactId' => $activeContact->id,
            'messagesPayload' => $messagesPayload,
            'financeClaimableRegistrations' => $financeClaimableRegistrations,
        ]);
    }

    /**
     * JSON + HTML for realtime refresh of the shared finance queue strip on Team Chats.
     */
    public function teamChatsClaimableFragment()
    {
        $userId = (int) Auth::id();
        $html = view('finance.partials.team_chats_claimable', [
            'financeClaimableRegistrations' => $this->financeClaimableRegistrationsForTeamChats($userId),
        ])->render();

        return response()->json(['html' => $html]);
    }

    /**
     * Pending-finance registrations without an invoice, visible on Team Chats (pool + rows claimed by this user).
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, ProgramRegistration>
     */
    private function financeClaimableRegistrationsForTeamChats(int $financeUserId)
    {
        return ProgramRegistration::query()
            ->where('status', ProgramRegistration::STATUS_PENDING_FINANCE)
            ->whereDoesntHave('registrationInvoices')
            ->where(function ($q) use ($financeUserId): void {
                $q->whereNull('finance_user_id')
                    ->orWhere('finance_user_id', $financeUserId);
            })
            ->with(['program:id,title'])
            ->orderByDesc('sent_to_finance_at')
            ->limit(40)
            ->get();
    }
}
