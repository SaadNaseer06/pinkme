@extends('admin.layouts.admin')

@section('title', 'Patient Applications')

@section('content')
    <div class="flex-1 flex flex-col">
        <main class="flex-1">
            <div class="max-w-6xl mx-auto">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-semibold text-[#213430] app-main">
                            Applications for {{ $patient->user?->profile?->full_name ?? 'Patient' }}
                        </h1>
                        <p class="text-sm text-[#91848C]">
                            {{ $patient->user?->email }} · {{ $patient->user?->profile?->phone }}
                        </p>
                    </div>
                    <a href="{{ route('admin.patients') }}"
                        class="px-4 py-2 bg-[#9E2469] text-white rounded-md hover:bg-[#B52D75] transition">
                        Back to Patients
                    </a>
                </div>

                <div class="bg-[#F3E8EF] rounded-lg p-6">
                    @if ($applications->isEmpty())
                        <p class="text-center text-[#91848C]">No applications found for this patient.</p>
                    @else
                        <div class="table-container">
                            <table class="min-w-full text-sm text-left">
                                <thead>
                                    <tr class="border-t border-[#E0CFD8]">
                                        <th class="p-2 text-[#91848C] font-medium">Application</th>
                                        <th class="p-2 text-[#91848C] font-medium">Program</th>
                                        <th class="p-2 text-[#91848C] font-medium">Status</th>
                                        <th class="p-2 text-[#91848C] font-medium">Assigned Reviewer</th>
                                        <th class="p-2 text-[#91848C] font-medium">Submitted</th>
                                        <th class="p-2 text-[#91848C] font-medium">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-[#213430]">
                                    @foreach ($applications as $application)
                                        <tr class="border-t border-[#E0CFD8] hover:bg-[#F6EDF5] transition">
                                            <td class="p-3">
                                                <p class="font-semibold">{{ $application->title }}</p>
                                                <p class="text-xs text-[#91848C]">#{{ $application->id }}</p>
                                            </td>
                                            <td class="p-3">
                                                {{ $application->program?->title ?? 'N/A' }}
                                            </td>
                                            <td class="p-3">
                                                <span
                                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ match (strtolower($application->status)) {
                                                        'approved' => 'bg-green-100 text-green-700',
                                                        'rejected' => 'bg-red-100 text-red-700',
                                                        'under review' => 'bg-yellow-100 text-yellow-700',
                                                        default => 'bg-slate-200 text-slate-700',
                                                    } }}">
                                                    {{ $application->status }}
                                                </span>
                                            </td>
                                            <td class="p-3">
                                                {{ $application->reviewer?->profile?->full_name ?? 'Unassigned' }}
                                            </td>
                                            <td class="p-3">
                                                {{ optional($application->submission_date)->format('M d, Y h:i A') ?? 'N/A' }}
                                            </td>
                                            <td class="p-3">
                                                <a href="{{ route('admin.viewApplication', $application->id) }}"
                                                    class="inline-flex items-center justify-center rounded-md border border-[#9E2469] px-3 py-1.5 text-xs font-medium text-[#9E2469] hover:bg-[#F6EDF5] transition">
                                                    View Application
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $applications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
@endsection
