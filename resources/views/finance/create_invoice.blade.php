@extends('finance.layouts.app')

@section('title', 'Allocate Budget - Generate Invoice')

@section('content')
<main>
    <div class="max-w-8xl mx-auto mt-6 px-5">
        <div class="mb-6">
            <a href="{{ route('finance.registrations.show', $registration) }}" class="text-[#9E2469] hover:underline flex items-center gap-1">
                <i class="fas fa-arrow-left"></i> Back to Application
            </a>
        </div>

        <div class="bg-[#F3E8EF] rounded-lg p-8 max-w-2xl">
            <h1 class="text-2xl font-semibold text-[#213430] mb-2">Allocate Budget</h1>
            <p class="text-sm text-[#91848C] mb-6">Generate an invoice to allocate budget for: <strong>{{ $registration->full_name }}</strong> - {{ optional($registration->program)->title ?? 'Program' }}</p>

            <form method="POST" action="{{ route('finance.invoice.store', $registration) }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-[#213430] mb-2">Payment Purpose <span class="text-[#9E2469]">*</span></label>
                    <input type="text" name="payment_purpose" value="{{ old('payment_purpose', 'Budget allocation for ' . optional($registration->program)->title) }}" required
                        class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#9E2469]"
                        placeholder="e.g. Medical assistance funding">
                    @error('payment_purpose')
                        <p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#213430] mb-2">Amount ($) <span class="text-[#9E2469]">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0.01" required
                        class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#9E2469]"
                        placeholder="0.00">
                    @error('amount')
                        <p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#213430] mb-2">Payment Method <span class="text-[#9E2469]">*</span></label>
                    <select name="payment_method" required
                        class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#9E2469]">
                        <option value="Bank Transfer" {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer (Electronic)</option>
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
                    <textarea name="notes" rows="3"
                        class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#9E2469]"
                        placeholder="Additional notes...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3 pt-4">
                    <a href="{{ route('finance.registrations.show', $registration) }}"
                        class="px-6 py-3 border-2 border-[#DCCFD8] text-[#91848C] rounded-lg text-sm font-semibold hover:border-[#9E2469] hover:text-[#9E2469]">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-[#9E2469] hover:bg-[#B52D75] text-white rounded-lg text-sm font-semibold">
                        <i class="fas fa-file-invoice-dollar mr-1"></i> Generate Invoice & Allocate Budget
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection
