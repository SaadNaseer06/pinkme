@php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $eventImages = ['program-1.png', 'program-2.png', 'program-3.png', 'program-4.png', 'program-5.png'];
@endphp

@extends('sponsor.layouts.app')

@section('title', 'Events')

@section('content')
    <div class="flex-1 flex flex-col">
        <main class="flex-1">
            <div class="max-w-8xl mx-auto">
                @include('sponsor.partials.cards')

                <div class="bg-[#F3E8EF] p-2 md:p-4 rounded-lg mb-6">
                    <h2 class="text-lg font-medium text-[#91848C] app-main">All Events</h2>
                </div>

                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-semibold text-[#213430] mb-4 program-main">Ongoing Events</h2>
                        <div class="relative md:w-60 w-[160px]">
                            <select name="eventFilter" id="eventFilter"
                                class="appearance-none w-full bg-[#F3E8EF] rounded-md pl-4 pr-10 py-2 text-sm text-[#91848C] focus:outline-none focus:ring-2 focus:ring-[#db69a2] app-text">
                                <option value="all">All Events</option>
                                <option value="upcoming">Upcoming</option>
                                <option value="inProgress">In Progress</option>
                                <option value="past">Past</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[#91848C]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 9l6 6 6-6" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div id="eventList" class="space-y-4">
                        @forelse ($ongoingEvents as $event)
                            @include('sponsor.events._event_card', [
                                'event' => $event,
                                'loopIndex' => $loop->index,
                            ])
                        @empty
                            <div class="bg-[#F3E8EF] rounded-lg p-6 text-center text-[#91848C] app-text">
                                No ongoing events available.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-semibold text-[#213430] mb-4 program-main">Upcoming Events</h2>
                    </div>

                    <div id="upcomingEventList" class="space-y-4">
                        @forelse ($upcomingEvents as $event)
                            @include('sponsor.events._event_card', [
                                'event' => $event,
                                'loopIndex' => $loop->index,
                            ])
                        @empty
                            <div class="bg-[#F3E8EF] rounded-lg p-6 text-center text-[#91848C] app-text">
                                No upcoming events available.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div id="registerModal" class="modal-overlay">
                    <div
                        class="modal-content p-4 max-w-2xl w-full bg-[#F3E8EF] rounded-2xl shadow-lg overflow-y-auto max-h-[95vh]">
                        <div class="border border-[#DCCFD8] p-2 rounded-md">
                            <div class="p-2 mb-2 border-b border-[#DCCFD8] rounded-md">
                                <h2 id="modalEventTitle" class="text-2xl font-semibold text-gray-900 program-main">Event
                                    Details</h2>
                            </div>

                            <div class="w-full h-64 overflow-hidden rounded-md mb-2">
                                <img id="modalEventImage" src="/images/program-details.png" alt="Event banner"
                                    class="w-full h-full object-cover" />
                            </div>

                            <div class="py-3 text-md text-gray-800 space-y-6">
                                <p id="modalEventDescription" class="text-[#91848C] app-text">
                                    A nonprofit initiative supporting women battling breast cancer, raising awareness about
                                    early
                                    detection and survivorship.
                                </p>

                                <div>
                                    <h3 class="text-lg font-medium text-[#213430] mb-4 app-main">Date And Time</h3>
                                    <div class="flex justify-between gap-6 border border-[#DCCFD8] py-4 px-4 rounded-lg">
                                        <div class="flex flex-col gap-2 text-[#91848C] text-sm app-text">
                                            <div>
                                                <i class="far fa-calendar font-bold text-[#91848C]"></i>
                                                <span>Date</span>
                                            </div>
                                            <span id="modalEventDate"
                                                class="text-[#213430] text-base font-medium">TBD</span>
                                        </div>
                                        <div class="flex flex-col gap-2 text-[#91848C] text-sm app-text">
                                            <div>
                                                <i class="far fa-clock font-bold text-[#91848C]"></i>
                                                <span>Time</span>
                                            </div>
                                            <span id="modalEventTime"
                                                class="text-[#213430] text-base font-medium">TBD</span>
                                        </div>
                                        <div class="flex flex-col gap-2 text-[#91848C] text-sm app-text">
                                            <div>
                                                <i class="fas fa-map-marker-alt font-bold text-[#91848C]"></i>
                                                <span>Location</span>
                                            </div>
                                            <span id="modalEventLocation"
                                                class="text-[#213430] text-base font-medium">TBA</span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-lg font-medium text-[#213430] mb-2 app-main">Event Highlights</h3>
                                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-[#213430] app-text">
                                        <li class="flex items-center gap-2 font-medium"><span
                                                class="text-pink text-2xl">&#8226;</span> Community awareness sessions</li>
                                        <li class="flex items-center gap-2 font-medium"><span
                                                class="text-pink text-2xl">&#8226;</span> Support resources and counselling
                                        </li>
                                        <li class="flex items-center gap-2 font-medium"><span
                                                class="text-pink text-2xl">&#8226;</span> Health screenings and
                                            consultations</li>
                                        <li class="flex items-center gap-2 font-medium"><span
                                                class="text-pink text-2xl">&#8226;</span> Stories from survivors & experts
                                        </li>
                                    </ul>
                                </div>

                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-4 app-main">Sponsored By</h3>
                                    <div class="border border-[#DCCFD8] p-2 rounded-md">
                                        <div class="flex items-start gap-4 p-4 rounded-md max-w-xl">
                                            <div
                                                class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center overflow-hidden">
                                                <img id="modalSponsorLogo" src="/images/brand.png" alt="Sponsor logo"
                                                    class="w-10 h-10 object-contain" />
                                            </div>
                                            <div class="flex flex-col">
                                                <span id="modalSponsorName"
                                                    class="font-semibold text-[#213430] mb-1 app-text">Sponsor</span>
                                                <div class="flex flex-col sm:flex-row gap-2 text-sm text-gray-600 app-text">
                                                    <div class="flex items-center gap-2">
                                                        <i class="fas fa-phone-alt text-pink-500"></i>
                                                        <span id="modalSponsorPhone">Phone unavailable</span>
                                                    </div>
                                                    <div class="flex items-center gap-2 app-text">
                                                        <i class="far fa-envelope text-pink-500"></i>
                                                        <span id="modalSponsorEmail">Email unavailable</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="bg-[#E4D6DF] p-4 rounded-lg text-sm app-text">
                                            <h4 class="font-semibold text-[#213430] mb-1">ABOUT SPONSOR</h4>
                                            <p id="modalSponsorAbout" class="text-[#213430]">
                                                Committed supporters partnering with us to impact more lives.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex justify-between gap-4 text-sm text-[#213430] app-text border border-[#DCCFD8] rounded-lg p-4">
                                    <div class="flex flex-col">
                                        <span class="font-semibold">Total Sponsors</span>
                                        <span id="modalTotalSponsors">0</span>
                                    </div>
                                    <div class="flex flex-col text-right">
                                        <span class="font-semibold">Total Sponsorship Raised</span>
                                        <span id="modalTotalRaised">$0.00</span>
                                    </div>
                                </div>

                                <div class="flex justify-between gap-4 pt-4">
                                    <button type="button" onclick="closeModal()"
                                        class="px-5 py-3 bg-transparent border border-[#DCCFD8] text-[#91848C] rounded-md app-text">
                                        Cancel
                                    </button>
                                    <!-- Inside the modal -->
                                    <a href="#" id="modalViewEventButton"
                                        style="padding: 14px 24px; background-color: #DB69A2; color: white; border-radius: 8px; text-decoration: none; display: inline-block; font-size: 14px; font-weight: 500; transition: background-color 0.3s ease;"
                                        onmouseover="this.style.backgroundColor='#C63A85'"
                                        onmouseout="this.style.backgroundColor='#DB69A2'">
                                        View Event Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    const modalOverlay = document.getElementById('registerModal');
                    const eventFilter = document.getElementById('eventFilter');

                    function openModal(trigger) {
                        if (!modalOverlay) return;

                        const {
                            title,
                            description,
                            date,
                            time,
                            location,
                            image,
                            sponsorName,
                            sponsorPhone,
                            sponsorEmail,
                            sponsorLogo,
                            sponsorAbout,
                            totalSponsors,
                            totalRaised,
                            eventId
                        } = trigger.dataset;

                        document.getElementById('modalEventTitle').textContent = title || 'Event Details';
                        const imageEl = document.getElementById('modalEventImage');
                        imageEl.src = image || '/images/program-details.png';
                        imageEl.alt = title || 'Event banner';
                        document.getElementById('modalEventDescription').textContent = description || '';
                        document.getElementById('modalEventDate').textContent = date || 'TBD';
                        document.getElementById('modalEventTime').textContent = time || 'TBD';
                        document.getElementById('modalEventLocation').textContent = location || 'Location to be announced';
                        document.getElementById('modalSponsorName').textContent = sponsorName || 'Sponsor';
                        document.getElementById('modalSponsorPhone').textContent = sponsorPhone || 'Phone unavailable';
                        document.getElementById('modalSponsorEmail').textContent = sponsorEmail || 'Email unavailable';
                        document.getElementById('modalSponsorLogo').src = sponsorLogo || '/images/logo-white.png';
                        document.getElementById('modalSponsorLogo').alt = sponsorName || 'Sponsor logo';
                        document.getElementById('modalSponsorAbout').textContent = sponsorAbout ||
                            'Committed supporters partnering with us to impact more lives.';
                        document.getElementById('modalTotalSponsors').textContent = totalSponsors || '0';
                        document.getElementById('modalTotalRaised').textContent = totalRaised ? `$${totalRaised}` : '$0.00';

                        // Update the event detail link
                        const eventDetailBtn = document.getElementById('modalViewEventButton');
                        if (eventDetailBtn && eventId) {
                            eventDetailBtn.href = `/sponsor/events/${eventId}`;
                        }

                        modalOverlay.style.display = 'flex';
                    }

                    function closeModal() {
                        if (modalOverlay) {
                            modalOverlay.style.display = 'none';
                        }
                    }

                    window.addEventListener('click', (event) => {
                        if (event.target === modalOverlay) {
                            closeModal();
                        }
                    });

                    if (eventFilter) {
                        eventFilter.addEventListener('change', () => {
                            const value = eventFilter.value;
                            document.querySelectorAll('[data-event-status]').forEach((wrapper) => {
                                const shouldShow = value === 'all' || wrapper.dataset.eventStatus === value;
                                wrapper.classList.toggle('hidden', !shouldShow);
                            });
                        });
                    }

                    document.getElementById('fullscreenBtn')?.addEventListener('click', () => {
                        if (!document.fullscreenElement) {
                            document.documentElement.requestFullscreen().catch((err) => {
                                console.error(`Error attempting to enable fullscreen: ${err.message} (${err.name})`);
                            });
                        } else {
                            document.exitFullscreen();
                        }
                    });
                </script>
            @endsection
