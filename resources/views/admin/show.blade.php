@extends('admin.layouts.admin')

@section('title', 'Reviewer Profile')

@section('content')

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <main class="flex-1">
            <div class="max-w-8xl mx-auto">
                <div>
                    <!-- Header Section -->
                    <div class="flex justify-end items-center mb-6">
                        {{-- <h2 class="text-xl font-semibold text-[#213430] app-main">Reviewer Profile</h2> --}}
                        <a href="{{ route('admin.reviewers') }}"
                            class="text-[#DB69A2] hover:text-[#FE6EB6] transition duration-200">Back to All Reviewers</a>
                    </div>

                    <!-- Cards Container -->
                    <div>

                        <!-- Reviewer Profile Card -->
                        <div class="bg-[#F3E8EF] p-6 rounded-lg shadow-md">
                            <h3 class="text-2xl font-semibold text-[#213430] mb-4">Reviewer Profile</h3>
                            <div class="flex flex-col md:flex-row gap-6">
                                <!-- Reviewer Avatar -->
                                <div class="flex-shrink-0">
                                    <img src="{{ $reviewer->profile ? asset('storage/' . $reviewer->profile->image) : '/images/default-avatar.png' }}"
                                        alt="{{ $reviewer->name }}"
                                        class="w-32 h-32 rounded-full object-cover shadow-md border-4 border-[#DB69A2]">
                                </div>
                                <!-- Reviewer Information -->
                                <div class="flex flex-col justify-center gap-2">
                                    <p class="text-md text-[#91848C]">Name: <span
                                            class="font-semibold text-[#213430]">{{ $reviewer->name ?? 'Unknown' }}</span>
                                    </p>
                                    <p class="text-md text-[#91848C]">ID: <span
                                            class="font-semibold text-[#213430]">{{ $reviewer->reviewer_id }}</span></p>
                                    <p class="text-md text-[#91848C]">Email: <span
                                            class="font-semibold text-[#213430]">{{ $reviewer->email }}</span></p>
                                    <p class="text-md text-[#91848C]">Phone: <span
                                            class="font-semibold text-[#213430]">{{ $reviewer->phone ?? 'N/A' }}</span></p>
                                    <p class="text-md text-[#91848C]">Gender: <span
                                            class="font-semibold text-[#213430]">{{ $reviewer->gender ?? 'N/A' }}</span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Assigned Applications Card -->
                        <div class="bg-[#F3E8EF] p-6 rounded-lg shadow-md">
                            <h3 class="text-2xl font-semibold text-[#213430] mb-4">Assigned Applications</h3>
                            @if ($reviewer->applications->isEmpty())
                                <p class="text-md text-[#91848C]">No applications assigned to this reviewer yet.</p>
                            @else
                                <table class="min-w-full text-sm text-left mt-6">
                                    <thead>
                                        <tr class="border-t border-[#e0cfd8]">
                                            <th class="p-3 text-lg font-medium text-[#91848C]">Application ID</th>
                                            <th class="p-3 text-lg font-medium text-[#91848C]">Assigned Date</th>
                                            <th class="p-3 text-lg font-medium text-[#91848C]">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700">
                                        @foreach ($reviewer->applications as $application)
                                            <tr
                                                class="border-t border-[#e0cfd8] hover:bg-[#F9EEF6] transition duration-200">
                                                <td class="p-3">{{ $application->id }}</td>
                                                <td class="p-3">
                                                    {{ $application->updated_at ? $application->updated_at->format('F j, Y') : 'N/A' }}
                                                </td>
                                                <td class="p-3">
                                                    <span
                                                        class="inline-flex items-center gap-1 text-sm {{ $application->status == 'Approved' ? 'text-green-500' : 'text-red-500' }}">
                                                        <span
                                                            class="w-2 h-2 rounded-full {{ $application->status == 'Approved' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                                        {{ ucfirst($application->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </main>
    </div>

@endsection
