@extends('finance.layouts.app')

@section('title', 'Payment — Application')

@section('content')
@php
    $ref = $registration->public_reference;
    $approvedAmount = $registration->calculated_grant_amount;
    $hasInvoice = $registration->registrationInvoices->isNotEmpty();
    $preProofs = $registration->finance_pre_payment_proof_paths ?? [];
    if (! is_array($preProofs)) {
        $preProofs = [];
    }
    $pendingFinance = strtolower((string) $registration->status) === \App\Models\ProgramRegistration::STATUS_PENDING_FINANCE;
@endphp
<main>
    <div class="max-w-8xl mx-auto mt-6 px-5">
        <div class="flex justify-between items-start mb-6 flex-wrap gap-4">
            <a href="{{ route('finance.registrations') }}" class="text-[#9E2469] hover:underline flex items-center gap-1">
                <i class="fas fa-arrow-left"></i> Back to Payments
            </a>
        </div>

        {{-- Workflow overview (matches finance flow infographic) --}}
        <div class="rounded-2xl border border-[#E5D2DE] bg-white p-6 mb-6 shadow-sm">
            <h2 class="text-lg font-semibold text-[#213430] mb-4">Finance workflow</h2>
            <ol class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <li class="rounded-xl bg-[#F3E8EF] p-4 border border-[#E5D2DE]">
                    <span class="font-semibold text-[#9E2469]">Step 1</span>
                    <p class="mt-2 text-[#4C4047]">Case manager approved → you receive <strong>Payment Ready for Processing</strong> by email.</p>
                </li>
                <li class="rounded-xl {{ ! $hasInvoice ? 'ring-2 ring-[#9E2469]' : 'bg-[#F9FAFB]' }} p-4 border border-[#E5D2DE]">
                    <span class="font-semibold text-[#9E2469]">Step 2</span>
                    <p class="mt-2 text-[#4C4047]">Optionally <strong>upload proof of bill payments</strong> below <strong>before</strong> recording payment. Then complete payment outside the portal and use <strong>Record bills paid</strong> (invoice).</p>
                </li>
                <li class="rounded-xl {{ $hasInvoice && $registration->registrationInvoices->contains(fn ($i) => ! $i->payment_proof_uploaded_at) ? 'ring-2 ring-[#9E2469]' : 'bg-[#F9FAFB]' }} p-4 border border-[#E5D2DE]">
                    <span class="font-semibold text-[#9E2469]">Step 3</span>
                    <p class="mt-2 text-[#4C4047]">Upload <strong>proof of bill payments</strong> (receipt). Admins and your Patient Support Coordinator are notified; patients do not receive payment receipts.</p>
                </li>
            </ol>
        </div>

        <div class="rounded-xl bg-[#FDF8FB] border border-[#E5D2DE] p-4 mb-6 flex flex-wrap gap-6 justify-between items-center">
            <div>
                <p class="text-xs uppercase tracking-wide text-[#91848C]">Application ID</p>
                <p class="text-lg font-semibold text-[#213430]">{{ $ref }}</p>
            </div>
            @if ($approvedAmount !== null)
                <div>
                    <p class="text-xs uppercase tracking-wide text-[#91848C]">Approved amount (patient selection)</p>
                    <p class="text-lg font-semibold text-[#213430]">${{ number_format($approvedAmount, 2) }}</p>
                </div>
            @endif
            <div>
                <p class="text-xs uppercase tracking-wide text-[#91848C]">Sent to finance</p>
                <p class="text-[#213430]">{{ $registration->sent_to_finance_at ? $registration->sent_to_finance_at->timezone(config('app.timezone'))->format('M j, Y g:i A') : '—' }}</p>
            </div>
        </div>

        @if ($pendingFinance && ! $hasInvoice)
            <div class="rounded-xl border border-[#E5D2DE] bg-white p-6 mb-6 shadow-sm">
                <h3 class="text-lg font-semibold text-[#213430] mb-2">Upload proof of bill payments (optional, before recording bills paid)</h3>
                <p class="text-sm text-[#6C5F67] mb-4">Attach proof or receipts here before you submit the payment record in Step 2.</p>
                @if (count($preProofs) > 0)
                    <ul class="space-y-2 mb-4">
                        @foreach ($preProofs as $idx => $item)
                            @php
                                $p = is_array($item) ? ($item['path'] ?? null) : null;
                                $name = is_array($item) ? ($item['original_name'] ?? basename((string) $p)) : '';
                            @endphp
                            @if ($p)
                                <li class="flex items-center justify-between gap-2 bg-[#F3E8EF] rounded px-3 py-2 text-sm">
                                    <span class="truncate">{{ $name }}</span>
                                    <a href="{{ storage_url($p) }}" target="_blank" class="text-[#9E2469] shrink-0 hover:underline">View</a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif
                <form method="POST" action="{{ route('finance.registrations.pre_payment_proofs', $registration) }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <input type="file" name="pre_payment_proofs[]" accept=".pdf,.jpg,.jpeg,.png" multiple required
                        class="block w-full text-sm text-[#4C4047] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-[#9E2469] file:text-white file:cursor-pointer" />
                    @error('pre_payment_proofs')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    @error('pre_payment_proofs.*')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-[#91848C] hover:bg-[#7a6f74] text-white rounded-lg text-sm font-semibold">
                        <i class="fas fa-paperclip"></i> Add receipts
                    </button>
                </form>
            </div>
        @endif

        <div class="flex justify-between items-center mb-4 flex-wrap gap-3">
            <h1 class="text-xl font-semibold text-[#213430]">Payment details</h1>
            @if (! $hasInvoice)
                <a href="{{ route('finance.invoice.create', $registration) }}" class="bg-[#9E2469] text-white px-4 py-2 rounded-md text-sm hover:bg-[#B52D75] inline-flex items-center gap-2">
                    <i class="fas fa-dollar-sign"></i> Step 2: Record bills paid &amp; generate invoice
                </a>
            @else
                <span class="px-4 py-2 rounded-md text-sm bg-green-100 text-green-800">Bills paid</span>
            @endif
        </div>

        @if ($pendingFinance && ! $hasInvoice && \Illuminate\Support\Facades\Route::has('finance.registrations.reject'))
            <div class="rounded-xl border border-red-200 bg-white p-6 mb-6 shadow-sm">
                <h3 class="text-lg font-semibold text-[#213430] mb-2">Reject application</h3>
                <p class="text-sm text-[#6C5F67] mb-4">If this case should not be paid (for example, it reached finance by mistake), you can reject it here. The applicant is notified.</p>
                <form method="POST" action="{{ route('finance.registrations.reject', $registration) }}" class="space-y-3 max-w-xl"
                    onsubmit="return confirm('Reject this application? The patient will be notified.');">
                    @csrf
                    <div>
                        <label for="finance_reject_note" class="block text-sm font-medium text-[#213430] mb-1">Reason <span class="text-red-600">*</span></label>
                        <textarea id="finance_reject_note" name="note" rows="4" required maxlength="2000"
                            class="w-full rounded-lg border border-[#E5D2DE] px-3 py-2 text-sm text-[#213430] focus:border-[#9E2469] focus:ring-1 focus:ring-[#9E2469]"
                            placeholder="Explain why this application is being rejected"></textarea>
                        @error('note')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-semibold bg-red-700 text-white hover:bg-red-800">
                        <i class="fas fa-times-circle"></i> Reject application
                    </button>
                </form>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-[#F3E8EF] p-6 rounded-xl">
                <h3 class="text-lg font-semibold text-[#213430] mb-3">Applicant</h3>
                <hr class="border-[#DCCFD8] mb-4" />
                <div class="space-y-5">
                    <div class="flex justify-between gap-2"><span class="font-medium">Name</span><span class="text-right">{{ $registration->full_name ?? 'N/A' }}</span></div>
                    <div class="flex justify-between gap-2"><span class="font-medium">Email</span><span class="text-right break-all">{{ $registration->email ?? 'N/A' }}</span></div>
                    <div class="flex justify-between gap-2"><span class="font-medium">Phone</span><span class="text-right">{{ $registration->phone ?? 'N/A' }}</span></div>
                </div>
            </div>
            <div class="bg-[#F3E8EF] p-6 rounded-xl">
                <h3 class="text-lg font-semibold text-[#213430] mb-3">Application</h3>
                <hr class="border-[#DCCFD8] mb-4" />
                <div class="space-y-5">
                    <div class="flex justify-between gap-2"><span class="font-medium">Program</span><span class="text-right">{{ optional($registration->program)->title ?? 'N/A' }}</span></div>
                    <div class="flex justify-between gap-2"><span class="font-medium">Status</span><span class="text-right">{{ ucfirst(str_replace('_', ' ', $registration->status)) }}</span></div>
                    <div class="flex justify-between gap-2"><span class="font-medium">Patient Support Coordinator</span><span class="text-right">{{ optional($registration->assignedCaseManager?->profile)->full_name ?? 'N/A' }}</span></div>
                </div>
            </div>
        </div>

        <div class="mt-6 bg-[#F3E8EF] rounded-lg p-6">
            <h3 class="text-lg font-semibold text-[#213430] mb-2">Billing payment links</h3>
            <p class="text-sm text-[#6C5F67] mb-4">Entered by the case manager from information provided by the applicant.</p>
            <hr class="border-[#DCCFD8] mb-4" />
            @if (filled($registration->payment_links))
                <div class="rounded-lg border border-[#DCCFD8] bg-white/90 p-4">
                    <div class="text-sm text-[#213430]">
                        {!! \App\Support\BillingPaymentLinks::toHtml($registration->payment_links) !!}
                    </div>
                </div>
            @else
                <p class="text-sm text-[#6C5F67]">No payment links on file yet.</p>
            @endif
        </div>

        <div class="mt-6 bg-[#F3E8EF] rounded-lg p-6">
            <h3 class="text-lg font-semibold text-[#213430] mb-3">Bill statement attachments</h3>
            <hr class="border-[#DCCFD8] mb-4" />
            @if (!empty($registration->bill_statements))
                <ul class="space-y-2">
                    @foreach ($registration->bill_statements as $index => $bill)
                        <li class="flex items-center justify-between gap-2 bg-white rounded-md px-4 py-3 border border-[#E5D2DE]">
                            <span class="text-[#213430] truncate">{{ $bill['filename'] }}</span>
                            <div class="flex items-center gap-3 shrink-0">
                                <a href="{{ route('finance.registrations.bill_statement.download', [$registration, $index]) }}?preview=1" target="_blank" class="text-[#9E2469] font-medium hover:underline">Preview</a>
                                <a href="{{ route('finance.registrations.bill_statement.download', [$registration, $index]) }}" class="text-[#213430] font-medium hover:underline">Download</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-[#6C5F67]">No bill statements uploaded.</p>
            @endif
        </div>

        @if ($registration->registrationInvoices->isNotEmpty())
            <div class="mt-6 space-y-6">
                @foreach ($registration->registrationInvoices as $inv)
                    <div class="bg-[#F3E8EF] rounded-lg p-6 border border-[#E5D2DE]">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-[#213430]">Invoice {{ $inv->invoice_number }}</h3>
                                <p class="text-sm text-[#6C5F67]">${{ number_format($inv->amount, 2) }} · {{ $inv->payment_purpose }} · {{ $inv->payment_method }}</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="text-green-700 font-medium">{{ $inv->status }}</span>
                                <form method="POST" action="{{ route('finance.invoice.resend_emails', [$registration, $inv]) }}" class="inline" onsubmit="return confirm('Resend invoice emails to the patient and admins?');">
                                    @csrf
                                    <button type="submit" class="text-sm text-[#9E2469] hover:underline">Resend invoice email</button>
                                </form>
                            </div>
                        </div>

                        @if ($inv->payment_proof_uploaded_at)
                            <div class="rounded-lg bg-green-50 border border-green-200 text-green-900 px-4 py-3 text-sm mb-4">
                                <strong>Payment proof uploaded</strong> — {{ $inv->payment_proof_original_name ?? 'file' }}
                                on {{ optional($inv->payment_proof_uploaded_at)?->format('M j, Y g:i A') ?? '—' }}.
                                @if ($inv->payment_proof_path)
                                    <a href="{{ storage_url($inv->payment_proof_path) }}" target="_blank" class="underline ml-2">View file</a>
                                @endif
                            </div>
                            <p class="text-xs text-[#6C5F67] mb-2">Need to replace the file? Upload again below.</p>
                        @endif

                        <div class="rounded-xl border-2 border-dashed border-[#DCCFD8] bg-white/80 p-6">
                            <h4 class="font-semibold text-[#213430] mb-2">Step 3: Upload proof of bill payments</h4>
                            <p class="text-sm text-[#6C5F67] mb-4">Attach receipt or bank confirmation after payment is completed outside the dashboard.</p>
                            <form method="POST" action="{{ route('finance.invoice.payment_proof', [$registration, $inv]) }}" enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                <input type="file" name="payment_proof" accept=".pdf,.jpg,.jpeg,.png" required
                                    class="block w-full text-sm text-[#4C4047] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-[#9E2469] file:text-white file:cursor-pointer" />
                                @error('payment_proof')
                                    <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#9E2469] text-white rounded-lg text-sm font-semibold hover:bg-[#B52D75]">
                                    <i class="fas fa-upload"></i> Submit proof
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</main>
@endsection
