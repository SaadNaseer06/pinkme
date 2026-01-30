@extends('sponsor.layouts.app')

@section('title', 'My Event Registrations')

@section('content')
    <div class="flex-1 flex flex-col">
        <main class="flex-1">
            <div class="max-w-8xl mx-auto">
                @include('sponsor.partials.cards')

                <div class="bg-[#F3E8EF] p-2 md:p-4 rounded-lg mb-6">
                    <h2 class="text-lg font-medium text-[#91848C] app-main">My Event Registrations</h2>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-white rounded-lg p-6 shadow-sm border">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-[#91848C] app-text">Total Commitment</p>
                                <p class="text-2xl font-bold text-[#213430] program-main">${{ number_format($totalAmount, 2) }}</p>
                            </div>
                            <div class="bg-pink-100 p-3 rounded-full">
                                <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-6 shadow-sm border">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-[#91848C] app-text">Confirmed Amount</p>
                                <p class="text-2xl font-bold text-green-600 program-main">${{ number_format($confirmedAmount, 2) }}</p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-6 shadow-sm border">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-[#91848C] app-text">Pending Amount</p>
                                <p class="text-2xl font-bold text-yellow-600 program-main">${{ number_format($pendingAmount, 2) }}</p>
                            </div>
                            <div class="bg-yellow-100 p-3 rounded-full">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M12 8v4l2.5 2.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registration Status Tabs -->
                <div class="mb-6">
                    <div class="border-b border-[#DCCFD8]">
                        <nav class="-mb-px flex space-x-8">
                            <button class="tab-button active py-2 px-1 border-b-2 border-[#DB69A2] font-medium text-sm text-[#DB69A2] app-text" 
                                    data-tab="confirmed">
                                Confirmed ({{ $confirmed->count() }})
                            </button>
                            <button class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-[#91848C] hover:text-[#213430] app-text" 
                                    data-tab="pending">
                                Pending ({{ $pending->count() }})
                            </button>
                            <button class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-[#91848C] hover:text-[#213430] app-text" 
                                    data-tab="cancelled">
                                Cancelled ({{ $cancelled->count() }})
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Confirmed Registrations -->
                <div id="confirmed-tab" class="tab-content">
                    @if($confirmed->count() > 0)
                        <div class="space-y-4">
                            @foreach($confirmed as $registration)
                                @if($registration->event)
                                    <div class="bg-white rounded-lg shadow-sm border border-green-200 overflow-hidden">
                                        <div class="p-6">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-3 mb-2">
                                                        <h3 class="text-lg font-semibold text-[#213430] program-main">
                                                            {{ $registration->event->title }}
                                                        </h3>
                                                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-medium">
                                                            Confirmed
                                                        </span>
                                                        @if($registration->event->is_upcoming)
                                                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                                                Upcoming
                                                            </span>
                                                        @elseif($registration->event->is_today)
                                                            <span class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full">
                                                                Today
                                                            </span>
                                                        @else
                                                            <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">
                                                                Past
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                                        <div class="flex items-center text-sm text-[#91848C] app-text">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                            {{ $registration->event->formatted_date }} at {{ $registration->event->formatted_time }}
                                                        </div>
                                                        
                                                        @if($registration->event->location)
                                                            <div class="flex items-center text-sm text-[#91848C] app-text">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                          d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                </svg>
                                                                {{ $registration->event->location }}
                                                            </div>
                                                        @endif

                                                        <div class="flex items-center text-sm text-[#91848C] app-text">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                            </svg>
                                                            Sponsorship: ${{ number_format($registration->amount, 2) }}
                                                        </div>
                                                    </div>

                                                    @if($registration->event->funding_goal)
                                                        <div class="mb-4">
                                                            <div class="flex justify-between text-sm mb-1">
                                                                <span class="text-[#91848C] app-text">Event Funding Progress</span>
                                                                <span class="font-semibold text-[#213430]">
                                                                    {{ $registration->event->funding_progress }}% 
                                                                    (${{ number_format($registration->event->remaining_funding, 0) }} remaining)
                                                                </span>
                                                            </div>
                                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                                <div class="bg-[#DB69A2] h-2 rounded-full" 
                                                                     style="width: {{ $registration->event->funding_progress }}%"></div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if($registration->message)
                                                        <div class="bg-[#F3E8EF] p-3 rounded-lg mb-4">
                                                            <p class="text-sm text-[#213430] app-text">
                                                                <strong>Your message:</strong> "{{ $registration->message }}"
                                                            </p>
                                                        </div>
                                                    @endif

                                                    <div class="flex items-center justify-between text-xs text-[#91848C] app-text">
                                                        <span>Registered: {{ $registration->registered_at ? $registration->registered_at->format('M d, Y') : 'N/A' }}</span>
                                                        @if($registration->confirmed_at)
                                                            <span>Confirmed: {{ $registration->confirmed_at->format('M d, Y') }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="flex flex-col gap-2 ml-6">
                                                    <a href="{{ route('sponsor.events.show', $registration->event) }}" 
                                                       class="inline-flex items-center px-3 py-2 bg-[#DB69A2] text-white text-sm rounded-lg hover:bg-[#C63A85] transition app-text">
                                                        View Event
                                                    </a>
                                                    
                                                    @if($registration->event->is_upcoming && ($registration->payment_status ?? 'pending') !== 'paid')
                                                        <form method="POST" action="{{ route('sponsor.events.cancel', $registration->event) }}" 
                                                              onsubmit="return confirm('Are you sure you want to cancel your registration?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition app-text">
                                                                Cancel
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="bg-[#F3E8EF] rounded-lg p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-[#91848C] mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-[#213430] mb-2 program-main">No Confirmed Registrations</h3>
                            <p class="text-[#91848C] app-text">You don't have any confirmed event registrations yet.</p>
                            <a href="{{ route('sponsor.events') }}" 
                               class="inline-block mt-4 px-6 py-2 bg-[#DB69A2] text-white rounded-lg hover:bg-[#C63A85] transition app-text">
                                Browse Events
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Pending Registrations -->
                <div id="pending-tab" class="tab-content hidden">
                    @if($pending->count() > 0)
                        <div class="space-y-4">
                            @foreach($pending as $registration)
                                @if($registration->event)
                                    <div class="bg-white rounded-lg shadow-sm border border-yellow-200 overflow-hidden">
                                        <div class="p-6">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-3 mb-2">
                                                        <h3 class="text-lg font-semibold text-[#213430] program-main">
                                                            {{ $registration->event->title }}
                                                        </h3>
                                                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-medium">
                                                            Pending Review
                                                        </span>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                        <div class="flex items-center text-sm text-[#91848C] app-text">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                            {{ $registration->event->formatted_date }}
                                                        </div>
                                                        
                                                        <div class="flex items-center text-sm text-[#91848C] app-text">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                            </svg>
                                                            Sponsorship: ${{ number_format($registration->amount, 2) }}
                                                        </div>
                                                    </div>

                                                    @if($registration->message)
                                                        <div class="bg-yellow-50 p-3 rounded-lg mb-4">
                                                            <p class="text-sm text-[#213430] app-text">
                                                                <strong>Your message:</strong> "{{ $registration->message }}"
                                                            </p>
                                                        </div>
                                                    @endif

                                                    <p class="text-xs text-[#91848C] app-text">
                                                        Registered: {{ $registration->registered_at ? $registration->registered_at->format('M d, Y') : 'N/A' }}
                                                    </p>
                                                </div>

                                                <div class="ml-6">
                                                    <a href="{{ route('sponsor.events.show', $registration->event) }}" 
                                                       class="inline-flex items-center px-3 py-2 bg-[#DB69A2] text-white text-sm rounded-lg hover:bg-[#C63A85] transition app-text">
                                                        View Event
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="bg-[#F3E8EF] rounded-lg p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-[#91848C] mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M12 8v4l2.5 2.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-[#213430] mb-2 program-main">No Pending Registrations</h3>
                            <p class="text-[#91848C] app-text">You don't have any registrations waiting for review.</p>
                        </div>
                    @endif
                </div>

                <!-- Cancelled Registrations -->
                <div id="cancelled-tab" class="tab-content hidden">
                    @if($cancelled->count() > 0)
                        <div class="space-y-4">
                            @foreach($cancelled as $registration)
                                @if($registration->event)
                                    <div class="bg-white rounded-lg shadow-sm border border-red-200 overflow-hidden opacity-75">
                                        <div class="p-6">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-3 mb-2">
                                                        <h3 class="text-lg font-semibold text-[#213430] program-main">
                                                            {{ $registration->event->title }}
                                                        </h3>
                                                        <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full font-medium">
                                                            Cancelled
                                                        </span>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                        <div class="flex items-center text-sm text-[#91848C] app-text">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                            {{ $registration->event->formatted_date }}
                                                        </div>
                                                        
                                                        <div class="flex items-center text-sm text-[#91848C] app-text">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                            </svg>
                                                            Was: ${{ number_format($registration->amount, 2) }}
                                                        </div>
                                                    </div>

                                                    <p class="text-xs text-[#91848C] app-text">
                                                        Originally registered: {{ $registration->registered_at ? $registration->registered_at->format('M d, Y') : 'N/A' }}
                                                    </p>
                                                </div>

                                                <div class="ml-6">
                                                    <a href="{{ route('sponsor.events.show', $registration->event) }}" 
                                                       class="inline-flex items-center px-3 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition app-text">
                                                        View Event
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="bg-[#F3E8EF] rounded-lg p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-[#91848C] mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-[#213430] mb-2 program-main">No Cancelled Registrations</h3>
                            <p class="text-[#91848C] app-text">You haven't cancelled any event registrations.</p>
                        </div>
                    @endif
                </div>

                <div class="mt-8 text-center">
                    <a href="{{ route('sponsor.events') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#DB69A2] to-[#C63A85] text-white rounded-lg hover:from-[#C63A85] hover:to-[#B02A72] transition app-text">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back to All Events
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');
                    
                    // Remove active class from all buttons
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'border-[#DB69A2]', 'text-[#DB69A2]');
                        btn.classList.add('border-transparent', 'text-[#91848C]');
                    });
                    
                    // Add active class to clicked button
                    this.classList.add('active', 'border-[#DB69A2]', 'text-[#DB69A2]');
                    this.classList.remove('border-transparent', 'text-[#91848C]');
                    
                    // Hide all tab contents
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });
                    
                    // Show target tab content
                    document.getElementById(targetTab + '-tab').classList.remove('hidden');
                });
            });
        });
    </script>
@endsection
