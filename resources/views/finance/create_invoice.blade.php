@extends('finance.layouts.app')

@section('title', 'Allocate Budget - Generate Invoice')

@section('content')
<main class="max-w-3xl mx-auto">
    <a href="{{ route('finance.registrations.show', $registration) }}" class="inline-flex items-center gap-2 text-[#9E2469] hover:text-[#B52D75] font-medium text-sm mb-6 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Application
    </a>

    <div class="bg-[#F3E8EF] rounded-2xl p-8 shadow-sm border border-[#E5D2DE]">
        <h1 class="text-2xl font-semibold text-[#213430] mb-2">Allocate Budget</h1>
        <p class="text-[#4C4047] mb-8">Generate an invoice to allocate budget for: <strong class="text-[#213430]">{{ $registration->full_name }}</strong> – {{ optional($registration->program)->title ?? 'Program' }}</p>

        <form method="POST" action="{{ route('finance.invoice.store', $registration) }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-[#213430] mb-2">Payment Purpose <span class="text-red-500">*</span></label>
                <input type="text" name="payment_purpose" value="{{ old('payment_purpose', 'Budget allocation for ' . optional($registration->program)->title) }}" required
                    class="w-full rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-[#4C4047] placeholder:text-[#B1A4AD] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-transparent transition">
                @error('payment_purpose')
                    <p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#213430] mb-2">Amount ($) <span class="text-red-500">*</span></label>
                <p class="text-xs text-[#6C5F67] mb-2">Based on patient's selected program(s): {{ collect($registration->programs_applied ?? [])->implode(', ') ?: 'N/A' }}</p>
                @if ($calculatedAmount !== null)
                    <input type="number" name="amount" value="{{ old('amount', $calculatedAmount) }}" step="0.01" min="0.01" required readonly
                        class="w-full rounded-xl border border-[#DCCFD8] bg-[#F7EBF3] px-4 py-3 text-[#4C4047] cursor-not-allowed">
                    <p class="text-xs text-[#6C5F67] mt-1">This amount is set by the patient's program selection and cannot be edited.</p>
                @else
                    <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0.01" required
                        class="w-full rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-[#4C4047] placeholder:text-[#B1A4AD] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-transparent transition"
                        placeholder="0.00">
                @endif
                @error('amount')
                    <p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#213430] mb-2">Payment Method <span class="text-red-500">*</span></label>
                <select name="payment_method" required
                    class="w-full rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-[#4C4047] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-transparent transition cursor-pointer">
                    <option value="Bank Transfer" {{ old('payment_method', 'Bank Transfer') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer (Electronic)</option>
                    <option value="Credit Card" {{ old('payment_method') == 'Credit Card' ? 'selected' : '' }}>Credit Card (Electronic)</option>
                    <option value="Cheque" {{ old('payment_method') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                    <option value="Check" {{ old('payment_method') == 'Check' ? 'selected' : '' }}>Check</option>
                    <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                    <option value="Other" {{ old('payment_method') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('payment_method')
                    <p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[#213430] mb-2">Notes (Optional)</label>
                <textarea name="notes" rows="4"
                    class="w-full rounded-xl border border-[#DCCFD8] bg-white px-4 py-3 text-[#4C4047] placeholder:text-[#B1A4AD] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-transparent transition resize-none"
                    placeholder="Additional notes...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col-reverse sm:flex-row gap-3 pt-6">
                <a href="{{ route('finance.registrations.show', $registration) }}"
                    class="inline-flex justify-center items-center px-6 py-3 bg-[#F7EBF3] text-[#4C4047] rounded-xl text-sm font-semibold hover:bg-[#F4D9E6] transition border border-[#E5D2DE]">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex justify-center items-center gap-2 px-6 py-3 bg-[#9E2469] hover:bg-[#B52D75] text-white rounded-xl text-sm font-semibold transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Generate Invoice & Allocate Budget
                </button>
            </div>
        </form>
    </div>
</main>
@endsection
