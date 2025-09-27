@extends('admin.layouts.admin')

@section('title', 'View Application')

@section('content')
    <main>
        <div class="max-w-8xl mx-auto mt-6 px-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Patient Info --}}
                <div class="bg-[#F3E8EF] p-6 rounded-xl">
                    <h3 class="text-lg font-semibold text-[#213430] mb-3">Patient Info</h3>
                    <hr class="border-[#DCCFD8] mb-4" />
                    <div class="space-y-5">
                        <div class="flex justify-between">
                            <span class="font-medium">Full Name</span>
                            <span>{{ optional($application->patient?->user?->profile)->full_name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Patient ID</span>
                            <span>{{ $application->patient->id ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Email</span>
                            <span>{{ $application->patient?->user?->email ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#213430] text-[16px] font-medium app-text">Date of Birth</span>
                            <span class="text-[#91848C] text-[16px] font-normal app-text">
                                {{ optional($application->patient?->user?->profile)->date_of_birth ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#213430] text-[16px] font-medium app-text">Age</span>
                            <span class="text-[#91848C] text-[16px] font-normal app-text">
                                @if (optional($application->patient?->user?->profile)->date_of_birth)
                                    {{ \Carbon\Carbon::parse($application->patient->user->profile->date_of_birth)->age }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Application Info --}}
                <div class="bg-[#F3E8EF] p-6 rounded-xl">
                    <h3 class="text-lg font-semibold text-[#213430] mb-3">Application Info</h3>
                    <hr class="border-[#DCCFD8] mb-4" />
                    <div class="space-y-5">
                        <div class="flex justify-between">
                            <span class="font-medium">App Title</span>
                            <span>{{ $application->title ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Submission Date</span>
                            <span>{{ $application->created_at ? $application->created_at->format('F d, Y') : 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Application ID</span>
                            <span>{{ $application->id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#213430] text-[16px] font-medium app-text">Application Status</span>
                            <span class="text-[#91848C] text-[16px] font-normal app-text">
                                @if ($application->missingRequests && $application->missingRequests->isNotEmpty())
                                    <span class="">
                                        Missing Docs Requested
                                    </span>
                                @else
                                    {{ ucfirst($application->status) }}
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Assigned Reviewer</span>
                            <span>{{ optional($application->reviewer?->profile)->full_name ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Program & Description --}}
                <div class="bg-[#F3E8EF] p-6 rounded-xl">
                    <h3 class="text-lg font-semibold text-[#213430] mb-3">Program & Description</h3>
                    <hr class="border-[#DCCFD8] mb-4" />
                    <div class="space-y-5">
                        <div class="flex justify-between">
                            <span class="font-medium">Program</span>
                            <span>{{ $application->program->name ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="font-medium">Description</span>
                            <p class="text-[#91848C] mt-1">{{ $application->description ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status & Files --}}
        <div class="flex gap-6 max-w-8xl mx-auto mt-2 mb-12 px-5">
            {{-- Status & Reason --}}
            <div class="bg-[#F3E8EF] rounded-lg p-6 w-2/3 mt-6">
                <h2 class="text-lg font-semibold text-[#213430]">Status & Reason</h2>
                <hr class="border-[#DCCFD8] mt-4 mb-4" />
                <p class="mb-6">
                    @if ($application->missingRequests && $application->missingRequests->isNotEmpty())
                        <span>
                            Missing Docs Requested
                        </span>
                    @else
                        {{ ucfirst($application->status) }}
                    @endif
                </p>

                @if (strtolower($application->status) === 'rejected' && $application->rejection_reason)
                    <hr class="border-[#DCCFD8] mb-4" />
                    <h3 class="font-semibold mb-2">Rejection Reason:</h3>
                    <p>{{ $application->rejection_reason }}</p>
                @endif
            </div>

            {{-- Files --}}
            <div class="bg-[#F3E8EF] rounded-lg p-6 w-1/3 mt-6">
                <h2 class="text-lg font-semibold text-[#213430]">Files</h2>
                <p class="text-sm text-[#91848C]">Securely view documents</p>
                <hr class="border-[#DCCFD8] mb-4 mt-4" />
                <ul class="space-y-4">
                    @forelse($application->documents as $doc)
                        <li class="flex justify-between items-center">
                            <span>{{ $doc->filename }}</span>
                            <a href="{{ asset($doc->filepath) }}" target="_blank" class="text-pink-500">View</a>
                        </li>
                    @empty
                        <li class="text-[#91848C]">No documents uploaded.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </main>
@endsection
