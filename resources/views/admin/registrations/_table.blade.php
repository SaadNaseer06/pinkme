<div class="overflow-x-auto overflow-y-visible">
    <table class="min-w-full text-sm">
        <thead class="bg-[#F7EEF3] border-b border-pink-200">
            <tr>
                <th class="px-6 py-3 text-left text-[11px] font-semibold text-[#7B5B6B] uppercase tracking-[0.14em]">PINK “ME” Friend</th>
                <th class="px-6 py-3 text-left text-[11px] font-semibold text-[#7B5B6B] uppercase tracking-[0.14em]">Programs</th>
                <th class="px-6 py-3 text-left text-[11px] font-semibold text-[#7B5B6B] uppercase tracking-[0.14em]">Date Submitted</th>
                <th class="px-6 py-3 text-left text-[11px] font-semibold text-[#7B5B6B] uppercase tracking-[0.14em]">Status</th>
                <th class="px-6 py-3 text-left text-[11px] font-semibold text-[#7B5B6B] uppercase tracking-[0.14em]">Assigned to</th>
                <th class="px-6 py-3 text-left text-[11px] font-semibold text-[#7B5B6B] uppercase tracking-[0.14em]">Grant Status</th>
                <th class="px-6 py-3 text-left text-[11px] font-semibold text-[#7B5B6B] uppercase tracking-[0.14em] w-24">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($programRegistrations as $registration)
                @php
                    $isMtmRow = $registration->isMomentsThatMatterApplication();
                @endphp
                <tr class="hover:bg-[#FBF5F8] transition">
                    <td class="px-6 py-4 align-top">
                        <div class="flex flex-col">
                            <span class="font-semibold text-gray-900">{{ $registration->full_name }}</span>
                            <span class="text-xs text-gray-500">{{ $registration->email }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 align-top">
                        <div class="flex flex-col gap-1">
                            <span>{{ $registration->program->title ?? 'N/A' }}</span>
                            @if ($isMtmRow)
                                <span class="inline-flex w-fit items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#FDE8F3] text-[#9E2469] border border-[#F4BBD5]">Moments That Matter</span>
                            @else
                                <span class="inline-flex w-fit items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">Financial Assistance</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 align-top">
                        {{ $registration->created_at?->timezone(config('app.timezone'))->format('M d, Y h:i A') ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 align-top">
                        @php
                            $status = strtolower((string) $registration->status);
                            $badgeClasses = match ($status) {
                                \App\Models\ProgramRegistration::STATUS_APPROVED => 'bg-green-100 text-green-800',
                                \App\Models\ProgramRegistration::STATUS_SHIPPED => 'bg-blue-100 text-blue-800',
                                \App\Models\ProgramRegistration::STATUS_REJECTED => 'bg-red-100 text-red-800',
                                \App\Models\ProgramRegistration::STATUS_PENDING_FINANCE => 'bg-amber-100 text-amber-900',
                                default => 'bg-pink-100 text-pink-800',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-semibold rounded-full {{ $badgeClasses }}">
                            {{ $registration->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700 align-top">
                        @if ($isMtmRow)
                            <span class="text-[#6C5F67] text-xs">Admin review</span>
                        @else
                            {{ $registration->assignedCaseManager?->profile?->full_name ?? $registration->assignedCaseManager?->email ?? 'Unassigned' }}
                        @endif
                    </td>
                    <td class="px-6 py-4 align-top">
                        @if ($isMtmRow)
                            @if ($status === \App\Models\ProgramRegistration::STATUS_SHIPPED)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <i class="fas fa-truck"></i> Care package shipped
                                </span>
                            @elseif ($status === \App\Models\ProgramRegistration::STATUS_APPROVED)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-box"></i> Awaiting shipment
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-full bg-pink-100 text-pink-800">
                                    <i class="fas fa-clock"></i> Awaiting shipment
                                </span>
                            @endif
                        @elseif ($registration->registrationInvoices->isNotEmpty())
                            @php
                                $financePaid = $registration->registrationInvoices->contains(fn ($inv) => strtolower((string) $inv->status) === 'paid');
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle"></i>
                                <a href="{{ route('admin.program_registrations.show', $registration) }}#finance" class="hover:underline">{{ $financePaid ? 'Patient bills paid' : 'Bills recorded' }}</a>
                            </span>
                        @elseif ($registration->finance_user_id || (strtolower($registration->status ?? '') === \App\Models\ProgramRegistration::STATUS_PENDING_FINANCE && $registration->sent_to_finance_at))
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-full bg-amber-100 text-amber-800">
                                <i class="fas fa-clock"></i>
                                @if ($registration->finance_user_id)
                                    Sent to Finance
                                @else
                                    Finance queue (open)
                                @endif
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-medium align-top w-24">
                        <div class="inline-flex">
                            <button type="button" data-actions-toggle
                                class="inline-flex items-center justify-center h-8 w-8 rounded-full border border-gray-400 text-gray-600 hover:bg-gray-100 transition"
                                aria-haspopup="true" aria-expanded="false" title="Actions">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6h.01M12 12h.01M12 18h.01" />
                                </svg>
                            </button>
                            <div data-actions-menu
                                class="hidden absolute right-0 mt-2 w-48 rounded-xl border border-gray-200 bg-white shadow-xl z-30 max-h-56 overflow-y-auto">
                                <div class="p-2">
                                    <a href="{{ route('admin.program_registrations.show', $registration) }}"
                                        class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md">
                                        View Details
                                    </a>
                                    @if (! $isMtmRow && $registration->registrationInvoices->isEmpty() && strtolower($registration->status ?? '') === 'pending')
                                        @php
                                            $assignedName = $registration->assignedCaseManager?->profile?->full_name ?? $registration->assignedCaseManager?->email ?? null;
                                        @endphp
                                        @if ($registration->assigned_case_manager_id && $assignedName)
                                            <div class="px-3 py-2 text-xs text-gray-500 border-b border-gray-100">
                                                <span class="font-medium text-gray-700">Case Manager:</span> {{ $assignedName }}
                                            </div>
                                            <button type="button"
                                                data-assign-trigger
                                                data-assign-path="{{ route('admin.program_registrations.assign', $registration, false) }}"
                                                data-assign-current="{{ $registration->assigned_case_manager_id }}"
                                                data-assign-name="{{ $assignedName }}"
                                                class="w-full flex items-center gap-2 px-2 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md">
                                                <i class="fas fa-user-edit text-pink-600"></i> Change Case Manager
                                            </button>
                                        @else
                                            <button type="button"
                                                data-assign-trigger
                                                data-assign-path="{{ route('admin.program_registrations.assign', $registration, false) }}"
                                                data-assign-current="{{ $registration->assigned_case_manager_id ?? '' }}"
                                                data-assign-name=""
                                                class="w-full flex items-center gap-2 px-2 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md">
                                                <i class="fas fa-user-plus text-pink-600"></i> Assign Coordinator
                                            </button>
                                        @endif
                                    @endif
                                    @php
                                        $st = strtolower($registration->status ?? '');
                                        $canRouteFinance = ! $isMtmRow
                                            && $registration->registrationInvoices->isEmpty()
                                            && ($st === \App\Models\ProgramRegistration::STATUS_APPROVED
                                                || $st === \App\Models\ProgramRegistration::STATUS_PENDING_FINANCE);
                                    @endphp
                                    @if ($canRouteFinance)
                                        <button type="button"
                                            data-send-finance-trigger
                                            data-registration-id="{{ $registration->id }}"
                                            class="w-full flex items-center gap-2 px-2 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md">
                                            <i class="fas fa-dollar-sign text-pink-600"></i>
                                            {{ $st === \App\Models\ProgramRegistration::STATUS_PENDING_FINANCE ? 'Re-route / assign finance' : 'Send to Finance' }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        No program registrations found for the selected filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($programRegistrations->hasPages())
    <div class="program-pagination px-6 py-4 border-t border-gray-200 bg-[#FFF8FC]">
        {{ $programRegistrations->links() }}
    </div>
@endif
