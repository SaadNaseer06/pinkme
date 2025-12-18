@extends('admin.layouts.admin')

@section('content')
    <div class="container mx-auto py-6">
        <div class="flex justify-between flex-col gap-4 md:flex-row md:items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Event Registration Requests</h1>
                <p class="text-gray-600">Review and manage sponsor registration requests for events</p>
            </div>
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:space-x-3">
                <form method="GET" class="flex items-center gap-2">
                    <div class="relative">
                        <select name="event_id" onchange="this.form.submit()"
                            class="appearance-none rounded-md border border-[#DCCFD8] bg-white px-4 py-2 pr-10 text-sm text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#DB69A2]">
                            <option value="">All Events</option>
                            @foreach ($eventsForFilter as $eventOption)
                                <option value="{{ $eventOption->id }}"
                                    {{ (string) $selectedEventId === (string) $eventOption->id ? 'selected' : '' }}>
                                    {{ $eventOption->title }}
                                </option>
                            @endforeach
                        </select>
                        <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[#91848C]">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </div>
                    @if ($selectedEventId)
                        <a href="{{ route('events.registrations.index') }}"
                            class="px-3 py-2 text-sm text-[#91848C] border border-[#DCCFD8] rounded-md hover:text-[#213430]">
                            Reset
                        </a>
                    @endif
                </form>
                <a href="{{ route('events.index') }}"
                    class="bg-pink-600 text-white px-4 py-2 rounded-md hover:bg-pink-700 transition">
                    Back to Events
                </a>
            </div>
        </div>

        <!-- Pending Registrations Section -->
        @if ($pendingRegistrations->count() > 0)
            <div class="bg-pink-50 border border-pink-200 rounded-lg p-6 mb-8">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-pink-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z">
                        </path>
                    </svg>
                    <h2 class="text-lg font-semibold text-pink-800">Pending Approvals ({{ $pendingRegistrations->count() }})
                    </h2>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    @foreach ($pendingRegistrations as $registration)
                        <div class="bg-white rounded-lg border border-pink-300 p-4 shadow-sm">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800">{{ $registration->event->title }}</h3>
                                    <p class="text-sm text-gray-600">
                                        Sponsor: {{ $registration->sponsor->name ?? $registration->sponsor->email }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Amount: <span
                                            class="font-semibold">${{ number_format($registration->amount, 2) }}</span>
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Registered: {{ $registration->formatted_registered_at }}
                                    </p>
                                </div>
                                <div class="flex flex-col space-y-2">
                                    <form method="POST" action="{{ route('events.registrations.approve', $registration) }}"
                                        class="inline">
                                        @csrf
                                        <button type="submit"
                                            onclick="return confirm('Are you sure you want to approve this registration?')"
                                            class="bg-pink-600 text-white px-3 py-1 text-sm rounded hover:bg-pink-700 transition">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('events.registrations.reject', $registration) }}"
                                        class="inline">
                                        @csrf
                                        <button type="submit"
                                            onclick="return confirm('Are you sure you want to reject this registration?')"
                                            class="bg-red-600 text-white px-3 py-1 text-sm rounded hover:bg-red-700 transition">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @if ($registration->message)
                                <div class="bg-gray-50 p-3 rounded text-sm">
                                    <strong>Message:</strong> {{ $registration->message }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-pink-50 border border-pink-200 rounded-lg p-6 mb-8">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-pink-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-pink-800 font-medium">No pending registrations at the moment!</p>
                </div>
            </div>
        @endif

        <!-- All Registrations Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-pink-50 border-b border-pink-200">
                <h3 class="text-lg font-medium text-gray-900">All Event Registrations</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-pink-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-pink-800 uppercase tracking-wider">
                                Event & Sponsor
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-pink-800 uppercase tracking-wider">
                                Amount
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-pink-800 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-pink-800 uppercase tracking-wider">
                                Dates
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-pink-800 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($allRegistrations as $registration)
                            <tr class="hover:bg-pink-50 transition">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $registration->event->title }}
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            {{ $registration->sponsor->name ?? $registration->sponsor->email }}
                                        </div>
                                        @if ($registration->message)
                                            <div class="text-xs text-gray-500 mt-1">
                                                "{{ Str::limit($registration->message, 50) }}"
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    ${{ number_format($registration->amount, 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClasses = match ($registration->registration_status) {
                                            'confirmed' => 'bg-green-100 text-green-800',
                                            'pending' => 'bg-pink-100 text-pink-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses }}">
                                        {{ $registration->status_text }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div>Registered: {{ $registration->formatted_registered_at }}</div>
                                    @if ($registration->confirmed_at)
                                        <div>Confirmed: {{ $registration->formatted_confirmed_at }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-3">
                                        @if ($registration->canBeApproved())
                                            <form method="POST"
                                                action="{{ route('events.registrations.approve', $registration) }}">
                                                @csrf
                                                <button type="submit"
                                                    onclick="return confirm('Approve this registration?')"
                                                    class="inline-flex items-center px-3 py-1.5 text-green-600 hover:text-green-800 font-semibold rounded-md hover:bg-green-50 transition-colors focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Approve
                                                </button>
                                            </form>
                                        @endif

                                        @if ($registration->canBeRejected())
                                            <form method="POST"
                                                action="{{ route('events.registrations.reject', $registration) }}">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Reject this registration?')"
                                                    class="inline-flex items-center px-3 py-1.5 text-red-600 hover:text-red-800 font-semibold rounded-md hover:bg-red-50 transition-colors focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    {{ $registration->registration_status === 'confirmed' ? 'Cancel' : 'Reject' }}
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('events.show', $registration->event) }}"
                                            class="inline-flex items-center px-3 py-1.5 text-pink-600 hover:text-pink-800 font-semibold rounded-md hover:bg-pink-50 transition-colors focus:outline-none focus:ring-2 focus:ring-pink-400 focus:ring-offset-2">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View Event
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No event registrations found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($allRegistrations->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $allRegistrations->links() }}
                </div>
            @endif
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-pink-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l2.5 2.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Pending</div>
                        <div class="text-2xl font-semibold text-gray-900">
                            {{ $allRegistrations->where('registration_status', 'pending')->count() }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Confirmed</div>
                        <div class="text-2xl font-semibold text-gray-900">
                            {{ $allRegistrations->where('registration_status', 'confirmed')->count() }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Cancelled</div>
                        <div class="text-2xl font-semibold text-gray-900">
                            {{ $allRegistrations->where('registration_status', 'cancelled')->count() }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-pink-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Total Value</div>
                        <div class="text-2xl font-semibold text-gray-900">
                            ${{ number_format($allRegistrations->where('registration_status', 'confirmed')->sum('amount'), 0) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
