@php
    use App\Models\ProgramRegistration;
@endphp

@extends('case_manager.layouts.app')

@section('title', 'Program Registration Requests')

@section('content')
    <main class="flex-1">
        <div class="max-w-8xl mx-auto">
            <div class="mt-6 bg-[#F3E8EF] rounded-lg p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4 border-b border-[#DCCFD8] pb-4">
                    <div>
                        <h2 id="programRegHeading" class="text-2xl font-semibold text-[#213430] app-main">
                            {{ $selectedStatus === 'approved'
                                ? 'Approved Program Registrations'
                                : ($selectedStatus === 'rejected'
                                    ? 'Rejected Program Registrations'
                                    : ($selectedStatus === 'all'
                                        ? 'All Program Registrations'
                                        : 'Pending Program Registrations')) }}
                        </h2>
                        <p class="text-sm text-[#91848C] app-text mt-1">
                            Review and manage program registration requests assigned to you.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 text-sm text-[#91848C] app-text">
                        <span>Pending: <strong class="text-[#213430]">{{ $counts['pending'] }}</strong></span>
                        <span>Approved: <strong class="text-[#213430]">{{ $counts['approved'] }}</strong></span>
                        <span>Rejected: <strong class="text-[#213430]">{{ $counts['rejected'] }}</strong></span>
                        <span>Total: <strong class="text-[#213430]">{{ $counts['all'] }}</strong></span>
                    </div>
                </div>

                <form method="GET" class="flex flex-col md:flex-row md:items-center gap-3 mb-6">
                    <div class="relative w-full md:w-48">
                        <select name="status" id="programRegStatus"
                            class="w-full appearance-none rounded-md px-3 py-2 pr-10 text-sm text-[#213430] bg-white border border-[#91848C] focus:outline-none">
                            <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $selectedStatus === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $selectedStatus === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>All</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[#91848C]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                    <button type="submit"
                        class="px-4 py-2 bg-[#9E2469] text-white rounded-md text-sm font-medium hover:bg-[#B52D75] transition app-text">
                        Apply Filter
                    </button>
                    @if ($selectedStatus !== 'pending')
                        <a href="{{ route('case_manager.program_registrations.index') }}"
                            class="px-4 py-2 border border-[#DCCFD8] text-[#91848C] rounded-md text-sm app-text hover:bg-[#F9EFF5] transition">
                            Reset
                        </a>
                    @endif
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead>
                            <tr class="border-t border-[#e0cfd8] bg-white/40">
                                <th class="p-3 text-[#91848C] font-medium app-h">Applicant</th>
                                <th class="p-3 text-[#91848C] font-medium app-h">Program</th>
                                <th class="p-3 text-[#91848C] font-medium app-h">Submitted</th>
                                <th class="p-3 text-[#91848C] font-medium app-h">Status</th>
                                <th class="p-3 text-[#91848C] font-medium app-h text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-[#213430]">
                            @forelse ($registrations as $registration)
                                <tr class="border-t border-[#e0cfd8] hover:bg-white/60">
                                    <td class="p-3">
                                        <div class="flex flex-col">
                                            <span class="font-medium app-text">{{ $registration->full_name }}</span>
                                            <span class="text-xs text-[#91848C] app-text">{{ $registration->email }}</span>
                                        </div>
                                    </td>
                                    <td class="p-3 app-text">
                                        {{ $registration->program->title ?? 'N/A' }}
                                    </td>
                                    <td class="p-3 app-text">
                                        {{ $registration->created_at?->format('d M Y, h:i A') ?? 'N/A' }}
                                    </td>
                                    <td class="p-3">
                                        @php
                                            $status = strtolower($registration->status);
                                            $badgeClasses = match ($status) {
                                                ProgramRegistration::STATUS_APPROVED => 'bg-[#C5E8D1] text-[#20B354]',
                                                ProgramRegistration::STATUS_REJECTED => 'bg-[#FAD4D4] text-[#B32020]',
                                                default => 'bg-[#FDE8F3] text-[#9E2469]',
                                            };
                                        @endphp
                                        <span class="rounded-full text-xs font-semibold app-text {{ $badgeClasses }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <a href="{{ route('case_manager.program_registrations.show', $registration) }}"
                                            class="inline-flex items-center px-3 py-2 text-sm text-[#9E2469] border border-[#9E2469] rounded-md hover:bg-[#9E2469] hover:text-white transition app-text">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-[#91848C] app-text">
                                        No registration requests found for the selected filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $registrations->links() }}
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const statusSelect = document.getElementById('programRegStatus');
            const heading = document.getElementById('programRegHeading');
            const labels = {
                pending: 'Pending Program Registrations',
                approved: 'Approved Program Registrations',
                rejected: 'Rejected Program Registrations',
                all: 'All Program Registrations'
            };

            if (statusSelect && heading) {
                statusSelect.addEventListener('change', () => {
                    const next = labels[statusSelect.value] || 'Program Registration Requests';
                    heading.textContent = next;
                });
            }
        });
    </script>
@endsection
