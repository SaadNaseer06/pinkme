<div class="overflow-x-auto">
    <table class="min-w-full text-sm text-left">
        <thead>
            <tr class="border-t border-[#e0cfd8] bg-white/40">
                <th class="p-3 text-[#91848C] font-medium app-h">Applicant</th>
                <th class="p-3 text-[#91848C] font-medium app-h">Program</th>
                <th class="p-3 text-[#91848C] font-medium app-h">Submitted</th>
                <th class="p-3 text-[#91848C] font-medium app-h">Status</th>
                <th class="p-3 text-[#91848C] font-medium app-h">Assignment</th>
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
                            $rowStatus = strtolower((string) $registration->status);
                            $badgeClasses = match ($rowStatus) {
                                \App\Models\ProgramRegistration::STATUS_APPROVED => 'bg-[#C5E8D1] text-[#20B354]',
                                \App\Models\ProgramRegistration::STATUS_SHIPPED => 'bg-[#D4E8FA] text-[#1A6BB3]',
                                \App\Models\ProgramRegistration::STATUS_REJECTED => 'bg-[#FAD4D4] text-[#B32020]',
                                \App\Models\ProgramRegistration::STATUS_PENDING_FINANCE => 'bg-amber-100 text-amber-900',
                                default => 'bg-[#FDE8F3] text-[#9E2469]',
                            };
                        @endphp
                        <span class="rounded-full text-xs font-semibold app-text {{ $badgeClasses }}">
                            {{ $registration->status_label }}
                        </span>
                    </td>
                    <td class="p-3 app-text">
                        @if ($registration->assigned_case_manager_id)
                            <span class="text-xs text-[#213430]">Assigned</span>
                        @else
                            <span class="rounded-full text-xs font-semibold bg-amber-100 text-amber-900 px-2 py-0.5">Unassigned</span>
                        @endif
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
                    <td colspan="6" class="p-6 text-center text-[#91848C] app-text">
                        No registration requests found for the selected filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6 cm-prog-reg-pagination">
    {{ $registrations->withQueryString()->links() }}
</div>
