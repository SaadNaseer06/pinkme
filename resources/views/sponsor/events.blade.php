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
                                <option value="all" @selected(($selectedType ?? null) === null)>All Events</option>
                                <option value="flexible" @selected(($selectedType ?? null) === 'flexible')>Flexible Sponsorship</option>
                                <option value="full" @selected(($selectedType ?? null) === 'full')>Full Sponsorship</option>
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
                                <img id="modalEventImage" src="{{ asset('images/program-details.png') }}" alt="Event banner"
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

                                <div id="modalHighlightsWrapper" class="space-y-2">
                                    <h3 class="text-lg font-medium text-[#213430] mb-2 app-main">Event Highlights</h3>
                                    <div id="modalHighlightsContent" class="text-[#213430] app-text space-y-2">
                                        <p class="text-[#91848C]">Highlights will appear here.</p>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-4 app-main">Sponsored By</h3>
                                    <div class="border border-[#DCCFD8] p-2 rounded-md">
                                        <div class="flex items-start gap-4 p-4 rounded-md max-w-xl">
                                            <div
                                                class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center overflow-hidden">
                                                <img id="modalSponsorLogo" src="{{ asset('images/brand.png') }}" alt="Sponsor logo"
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

                <!-- Register Event Modal (opens on Register Now) -->
                <div id="registerEventModal" class="register-event-modal" style="display: none;">
                    <div class="register-event-modal__backdrop" onclick="closeRegisterModal()"></div>
                    <div class="register-event-modal__content">
                        <div class="register-event-modal__inner">
                            <div class="register-event-modal__header">
                                <h2 id="registerModalEventTitle" class="register-event-modal__title">Register</h2>
                                <button type="button" onclick="closeRegisterModal()" class="register-event-modal__close" aria-label="Close">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <form id="registerEventForm" method="POST" action="">
                                @csrf
                                <input type="hidden" name="amount" id="registerModalAmount" value="">
                                <div id="registerFullPanel" class="hidden">
                                    <p class="register-event-modal__intro">Pay the remaining amount to fully fund this event.</p>
                                    <div class="register-event-modal__amount-box">
                                        <span id="registerFullAmountDisplay" class="register-event-modal__amount">$0.00</span>
                                        <span class="register-event-modal__amount-label">remaining</span>
                                    </div>
                                    <button type="submit" id="registerFullSubmit" class="register-event-modal__btn register-event-modal__btn--primary">Pay &amp; Register</button>
                                </div>
                                <div id="registerFlexiblePanel" class="hidden">
                                    <p class="register-event-modal__intro">Choose your amount. You’ll complete payment on the next step.</p>
                                    <p id="registerFlexibleRemaining" class="register-event-modal__remaining hidden">Remaining to fund: <span id="registerFlexibleRemainingAmount" class="font-semibold text-[#213430]">$0</span></p>
                                    <div class="register-event-modal__amount-section">
                                        <p class="register-event-modal__label">Amount</p>
                                        <div class="flex flex-wrap gap-2 mb-2" id="registerPresetButtons"></div>
                                        <div id="registerOtherWrap" class="hidden mt-2">
                                            <div class="register-event-modal__input-wrap">
                                                <span class="register-event-modal__input-prefix">$</span>
                                                <input type="number" id="registerOtherInput" min="0.50" step="0.01" placeholder="Enter amount" class="register-event-modal__input">
                                            </div>
                                            <p id="registerOtherMax" class="register-event-modal__hint mt-1 hidden">Max: $<span id="registerOtherMaxVal">0</span></p>
                                        </div>
                                    </div>
                                    <button type="submit" id="registerFlexibleSubmit" disabled class="register-event-modal__btn register-event-modal__btn--disabled">Pay &amp; Register</button>
                                </div>
                                <details class="register-event-modal__details">
                                    <summary class="register-event-modal__details-summary">Add a message (optional)</summary>
                                    <textarea name="message" rows="2" class="register-event-modal__textarea mt-2" placeholder="Why you're supporting this event..." maxlength="500"></textarea>
                                </details>
                            </form>
                        </div>
                    </div>
                </div>
                <style>
                    .register-event-modal {
                        position: fixed;
                        inset: 0;
                        z-index: 50;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        padding: 1rem;
                        background-color: rgba(33, 52, 48, 0.4);
                        backdrop-filter: blur(4px);
                    }
                    .register-event-modal__backdrop {
                        position: absolute;
                        inset: 0;
                    }
                    .register-event-modal__content {
                        position: relative;
                        width: 100%;
                        max-width: 28rem;
                        max-height: 90vh;
                        overflow-y: auto;
                        background: #fff;
                        border-radius: 1rem;
                        box-shadow: 0 20px 40px rgba(198, 58, 133, 0.15), 0 0 0 1px rgba(219, 105, 162, 0.1);
                    }
                    .register-event-modal__inner {
                        padding: 1.5rem;
                    }
                    .register-event-modal__header {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 1.25rem;
                        padding-bottom: 1rem;
                        border-bottom: 2px solid #DB69A2;
                    }
                    .register-event-modal__title {
                        font-size: 1.25rem;
                        font-weight: 600;
                        color: #213430;
                        margin: 0;
                    }
                    .register-event-modal__close {
                        padding: 0.25rem;
                        color: #6C5B68;
                        background: none;
                        border: none;
                        border-radius: 0.5rem;
                        cursor: pointer;
                        transition: color 0.2s, background 0.2s;
                    }
                    .register-event-modal__close:hover {
                        color: #213430;
                        background: #F3E8EF;
                    }
                    .register-event-modal__intro {
                        font-size: 0.875rem;
                        color: #6C5B68;
                        margin-bottom: 1rem;
                    }
                    .register-event-modal__amount-box {
                        padding: 1rem 1.25rem;
                        margin-bottom: 1.25rem;
                        background: linear-gradient(135deg, #FDF7FB 0%, #F6EDF5 100%);
                        border: 1px solid #E9DCE7;
                        border-radius: 0.75rem;
                    }
                    .register-event-modal__amount {
                        font-size: 1.75rem;
                        font-weight: 700;
                        color: #C63A85;
                    }
                    .register-event-modal__amount-label {
                        font-size: 0.875rem;
                        color: #6C5B68;
                        margin-left: 0.25rem;
                    }
                    .register-event-modal__btn {
                        width: 100%;
                        padding: 0.75rem 1rem;
                        font-size: 1rem;
                        font-weight: 600;
                        border: none;
                        border-radius: 0.75rem;
                        cursor: pointer;
                        transition: opacity 0.2s, transform 0.1s;
                    }
                    .register-event-modal__btn--primary {
                        background: linear-gradient(135deg, #DB69A2 0%, #C63A85 100%);
                        color: #fff;
                        box-shadow: 0 4px 12px rgba(198, 58, 133, 0.35);
                    }
                    .register-event-modal__btn--primary:hover {
                        opacity: 0.95;
                        transform: translateY(-1px);
                        box-shadow: 0 6px 16px rgba(198, 58, 133, 0.4);
                    }
                    .register-event-modal__btn--disabled {
                        background: #E5E7EB;
                        color: #9CA3AF;
                        cursor: not-allowed;
                    }
                    .register-event-modal__remaining { font-size: 0.75rem; color: #6C5B68; margin-bottom: 0.75rem; }
                    .register-event-modal__amount-section { margin-bottom: 1rem; }
                    .register-event-modal__label { font-size: 0.875rem; font-weight: 500; color: #213430; margin-bottom: 0.5rem; }
                    .register-event-modal__input-wrap { position: relative; }
                    .register-event-modal__input-prefix {
                        position: absolute;
                        left: 0.75rem;
                        top: 50%;
                        transform: translateY(-50%);
                        color: #6C5B68;
                        font-size: 0.875rem;
                    }
                    .register-event-modal__input {
                        width: 100%;
                        padding: 0.5rem 1rem 0.5rem 2rem;
                        font-size: 0.875rem;
                        border: 1px solid #DCCFD8;
                        border-radius: 0.75rem;
                        outline: none;
                        transition: border-color 0.2s, box-shadow 0.2s;
                    }
                    .register-event-modal__input:focus {
                        border-color: #DB69A2;
                        box-shadow: 0 0 0 3px rgba(219, 105, 162, 0.2);
                    }
                    .register-event-modal__hint { font-size: 0.75rem; color: #6C5B68; }
                    .register-event-modal__details { font-size: 0.875rem; color: #6C5B68; margin-top: 1rem; }
                    .register-event-modal__details-summary {
                        cursor: pointer;
                        transition: color 0.2s;
                    }
                    .register-event-modal__details-summary:hover { color: #213430; }
                    .register-event-modal__textarea {
                        width: 100%;
                        padding: 0.5rem 1rem;
                        font-size: 0.875rem;
                        border: 1px solid #DCCFD8;
                        border-radius: 0.75rem;
                        outline: none;
                        resize: vertical;
                        transition: border-color 0.2s, box-shadow 0.2s;
                    }
                    .register-event-modal__textarea:focus {
                        border-color: #DB69A2;
                        box-shadow: 0 0 0 3px rgba(219, 105, 162, 0.2);
                    }
                </style>

                <script>
                    window.sponsorEventsRegisterBaseUrl = "{{ url('sponsor/events') }}";
                    function formatAmountDisplay(num) {
                        return num % 1 === 0 ? String(num) : parseFloat(num).toFixed(2);
                    }
                    const modalOverlay = document.getElementById('registerModal');
                    const eventFilter = document.getElementById('eventFilter');
                    const decodeHtmlEntities = (input = '') => {
                        if (!input) {
                            return '';
                        }
                        const textarea = document.createElement('textarea');
                        textarea.innerHTML = input;
                        return textarea.value;
                    };

                    function openModal(trigger) {
                        if (!modalOverlay) return;

                        const {
                            title,
                            description: encodedDescription = '',
                            descriptionHtml: encodedDescriptionHtml = '',
                            date,
                            time,
                            location,
                            highlights: encodedHighlights = '',
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
                        const descriptionHtml = decodeHtmlEntities(encodedDescriptionHtml);
                        const descriptionText = decodeHtmlEntities(encodedDescription);
                        const highlights = decodeHtmlEntities(encodedHighlights);

                        document.getElementById('modalEventTitle').textContent = title || 'Event Details';
                        const imageEl = document.getElementById('modalEventImage');
                        imageEl.src = image || "{{ asset('images/program-details.png') }}";
                        imageEl.alt = title || 'Event banner';
                        const descriptionEl = document.getElementById('modalEventDescription');
                        if (descriptionHtml && descriptionHtml.trim().length) {
                            descriptionEl.innerHTML = descriptionHtml;
                        } else if (descriptionText && descriptionText.trim().length) {
                            descriptionEl.textContent = descriptionText;
                        } else {
                            descriptionEl.textContent = 'Details will be announced soon.';
                        }
                        document.getElementById('modalEventDate').textContent = date || 'TBD';
                        document.getElementById('modalEventTime').textContent = time || 'TBD';
                        document.getElementById('modalEventLocation').textContent = location || 'Location to be announced';
                        document.getElementById('modalSponsorName').textContent = sponsorName || 'Sponsor';
                        document.getElementById('modalSponsorPhone').textContent = sponsorPhone || 'Phone unavailable';
                        document.getElementById('modalSponsorEmail').textContent = sponsorEmail || 'Email unavailable';
                        document.getElementById('modalSponsorLogo').src = sponsorLogo || "{{ asset('images/pink_me_logo.png') }}";
                        document.getElementById('modalSponsorLogo').alt = sponsorName || 'Sponsor logo';
                        document.getElementById('modalSponsorAbout').textContent = sponsorAbout ||
                            'Committed supporters partnering with us to impact more lives.';
                        const highlightsWrapper = document.getElementById('modalHighlightsWrapper');
                        const highlightsContent = document.getElementById('modalHighlightsContent');
                        if (highlightsContent && highlightsWrapper) {
                            const applyListStyling = () => {
                                highlightsContent.querySelectorAll('ul').forEach((list) => {
                                    list.style.listStyleType = 'disc';
                                    list.style.paddingLeft = '1.5rem';
                                    list.style.margin = '0';
                                });
                                highlightsContent.querySelectorAll('ol').forEach((list) => {
                                    list.style.listStyleType = 'decimal';
                                    list.style.paddingLeft = '1.5rem';
                                    list.style.margin = '0';
                                });
                                highlightsContent.querySelectorAll('li').forEach((item) => {
                                    item.style.marginBottom = '0.35rem';
                                });
                            };

                            if (highlights && highlights.trim().length) {
                                highlightsWrapper.classList.remove('hidden');
                                highlightsContent.innerHTML = highlights;
                                applyListStyling();
                            } else {
                                highlightsWrapper.classList.remove('hidden');
                                highlightsContent.innerHTML = '<p class="text-[#91848C]">Highlights will be announced soon.</p>';
                            }
                        }
                        document.getElementById('modalTotalSponsors').textContent = totalSponsors || '0';
                        document.getElementById('modalTotalRaised').textContent = totalRaised ? `$${totalRaised}` : '$0.00';

                        // Update the event detail link
                        const eventDetailBtn = document.getElementById('modalViewEventButton');
                        if (eventDetailBtn && eventId) {
                            eventDetailBtn.href = `/pinkme/sponsor/events/${eventId}`;
                        }

                        modalOverlay.style.display = 'flex';
                    }

                    function closeModal() {
                        if (modalOverlay) {
                            modalOverlay.style.display = 'none';
                        }
                    }

                    const registerEventModal = document.getElementById('registerEventModal');
                    const registerEventForm = document.getElementById('registerEventForm');
                    const registerFullPanel = document.getElementById('registerFullPanel');
                    const registerFlexiblePanel = document.getElementById('registerFlexiblePanel');
                    const registerModalAmount = document.getElementById('registerModalAmount');
                    const registerFullAmountDisplay = document.getElementById('registerFullAmountDisplay');
                    const registerFullSubmit = document.getElementById('registerFullSubmit');
                    const registerPresetButtons = document.getElementById('registerPresetButtons');
                    const registerOtherWrap = document.getElementById('registerOtherWrap');
                    const registerOtherInput = document.getElementById('registerOtherInput');
                    const registerFlexibleSubmit = document.getElementById('registerFlexibleSubmit');
                    const registerFlexibleRemaining = document.getElementById('registerFlexibleRemaining');
                    const registerFlexibleRemainingAmount = document.getElementById('registerFlexibleRemainingAmount');
                    const registerOtherMax = document.getElementById('registerOtherMax');
                    const registerOtherMaxVal = document.getElementById('registerOtherMaxVal');

                    function openRegisterModal(trigger) {
                        if (!registerEventModal || !trigger) return;
                        const eventId = trigger.getAttribute('data-register-event-id');
                        const title = trigger.getAttribute('data-register-title') || 'Event';
                        const paymentType = trigger.getAttribute('data-register-payment-type') || 'flexible';
                        const remaining = parseFloat(trigger.getAttribute('data-register-remaining') || '0') || 0;
                        const hasFundingGoal = (trigger.getAttribute('data-register-funding-goal') || '0') !== '0' && trigger.getAttribute('data-register-funding-goal') !== '';

                        registerEventForm.action = window.sponsorEventsRegisterBaseUrl + '/' + eventId + '/register';
                        document.getElementById('registerModalEventTitle').textContent = 'Register: ' + title;

                        registerFullPanel.classList.add('hidden');
                        registerFlexiblePanel.classList.add('hidden');
                        registerModalAmount.value = '';
                        registerOtherInput.value = '';
                        registerFlexibleSubmit.disabled = true;
                        registerFlexibleSubmit.className = 'register-event-modal__btn register-event-modal__btn--disabled';
                        registerFlexibleSubmit.textContent = 'Pay & Register';

                        if (paymentType === 'full') {
                            registerFullPanel.classList.remove('hidden');
                            registerModalAmount.value = remaining.toFixed(2);
                            registerFullAmountDisplay.textContent = '$' + formatAmountDisplay(remaining);
                            registerFullSubmit.textContent = 'Pay $' + formatAmountDisplay(remaining) + ' & Register';
                        } else {
                            registerFlexiblePanel.classList.remove('hidden');
                            if (hasFundingGoal && remaining > 0) {
                                registerFlexibleRemaining.classList.remove('hidden');
                                registerFlexibleRemainingAmount.textContent = '$' + formatAmountDisplay(remaining);
                                registerOtherMax.classList.remove('hidden');
                                registerOtherMaxVal.textContent = formatAmountDisplay(remaining);
                                registerOtherInput.setAttribute('max', remaining);
                            } else {
                                registerOtherInput.removeAttribute('max');
                                registerFlexibleRemaining.classList.add('hidden');
                                registerOtherMax.classList.add('hidden');
                            }
                            const presets = [50, 100, 250, 500];
                            const maxAmount = hasFundingGoal ? remaining : 99999;
                            registerPresetButtons.innerHTML = '';
                            presets.forEach(function(p) {
                                if (p <= maxAmount) {
                                    const btn = document.createElement('button');
                                    btn.type = 'button';
                                    btn.className = 'register-amount-btn px-4 py-2 rounded-xl border-2 border-[#DCCFD8] text-[#213430] font-medium hover:border-[#DB69A2] hover:bg-[#FDF7FB] transition-colors';
                                    btn.setAttribute('data-amount', p);
                                    btn.textContent = '$' + p;
                                    registerPresetButtons.appendChild(btn);
                                }
                            });
                            const otherBtn = document.createElement('button');
                            otherBtn.type = 'button';
                            otherBtn.className = 'register-amount-btn px-4 py-2 rounded-xl border-2 border-dashed border-[#DCCFD8] text-[#6C5B68] font-medium hover:border-[#DB69A2] transition-colors';
                            otherBtn.setAttribute('data-amount', 'other');
                            otherBtn.textContent = 'Other';
                            registerPresetButtons.appendChild(otherBtn);

                            registerPresetButtons.querySelectorAll('.register-amount-btn').forEach(function(btn) {
                                btn.addEventListener('click', function() {
                                    registerPresetButtons.querySelectorAll('.register-amount-btn').forEach(function(b) {
                                        b.classList.remove('border-[#DB69A2]', 'bg-[#FDF7FB]');
                                        b.classList.add('border-[#DCCFD8]');
                                    });
                                    btn.classList.add('border-[#DB69A2]', 'bg-[#FDF7FB]');
                                    btn.classList.remove('border-[#DCCFD8]');
                                    const amt = btn.getAttribute('data-amount');
                                    if (amt === 'other') {
                                        registerOtherWrap.classList.remove('hidden');
                                        registerModalAmount.value = '';
                                        registerFlexibleSubmit.disabled = true;
                                        registerFlexibleSubmit.className = 'register-event-modal__btn register-event-modal__btn--disabled';
                                        registerFlexibleSubmit.textContent = 'Pay & Register';
                                        registerOtherInput.focus();
                                    } else {
                                        registerOtherWrap.classList.add('hidden');
                                        registerOtherInput.value = '';
                                        registerModalAmount.value = amt;
                                        registerFlexibleSubmit.disabled = false;
                                        registerFlexibleSubmit.className = 'register-event-modal__btn register-event-modal__btn--primary';
                                        registerFlexibleSubmit.textContent = 'Pay $' + formatAmountDisplay(parseFloat(amt)) + ' & Register';
                                    }
                                });
                            });
                        }

                        registerEventModal.setAttribute('data-register-max', String(remaining));
                        registerEventModal.setAttribute('data-register-has-goal', hasFundingGoal ? '1' : '0');
                        registerEventModal.style.display = 'flex';
                    }

                    if (registerOtherInput) {
                        registerOtherInput.addEventListener('input', function() {
                            const v = this.value.replace(/[^0-9.]/g, '');
                            this.value = v;
                            if (v === '') {
                                registerModalAmount.value = '';
                                registerFlexibleSubmit.disabled = true;
                                registerFlexibleSubmit.className = 'register-event-modal__btn register-event-modal__btn--disabled';
                                registerFlexibleSubmit.textContent = 'Pay & Register';
                                return;
                            }
                            let num = parseFloat(v);
                            if (num < 0.5) return;
                            const maxVal = parseFloat(registerEventModal.getAttribute('data-register-max') || '0') || 99999;
                            const hasGoal = registerEventModal.getAttribute('data-register-has-goal') === '1';
                            if (hasGoal && num > maxVal) {
                                num = maxVal;
                                this.value = formatAmountDisplay(num);
                            }
                            registerModalAmount.value = num.toFixed(2);
                            registerFlexibleSubmit.disabled = false;
                            registerFlexibleSubmit.className = 'register-event-modal__btn register-event-modal__btn--primary';
                            registerFlexibleSubmit.textContent = 'Pay $' + formatAmountDisplay(num) + ' & Register';
                        });
                    }

                    function closeRegisterModal() {
                        if (registerEventModal) {
                            registerEventModal.style.display = 'none';
                        }
                    }

                    if (registerEventForm) {
                        registerEventForm.addEventListener('submit', function(e) {
                            if (registerFlexiblePanel && !registerFlexiblePanel.classList.contains('hidden')) {
                                if (registerOtherWrap && !registerOtherWrap.classList.contains('hidden') && registerOtherInput.value) {
                                    registerModalAmount.value = parseFloat(registerOtherInput.value) || '';
                                }
                                if (!registerModalAmount.value || parseFloat(registerModalAmount.value) < 0.5) {
                                    e.preventDefault();
                                    alert('Please choose or enter an amount (min $0.50).');
                                    return false;
                                }
                            }
                        });
                    }

                    window.openRegisterModal = openRegisterModal;
                    window.closeRegisterModal = closeRegisterModal;

                    window.addEventListener('click', (event) => {
                        if (event.target === modalOverlay) {
                            closeModal();
                        }
                        if (event.target === registerEventModal || (event.target && event.target.classList.contains('register-event-modal__backdrop'))) {
                            closeRegisterModal();
                        }
                    });

                    if (eventFilter) {
                        const applyFilter = () => {
                            const value = eventFilter.value;
                            document.querySelectorAll('[data-event-status]').forEach((wrapper) => {
                                const matchesStatus = value === 'all' || wrapper.dataset.eventStatus === value;
                                const matchesType = value === 'flexible' || value === 'full'
                                    ? wrapper.dataset.eventType === value
                                    : true;
                                const shouldShow = value === 'all'
                                    ? true
                                    : (value === 'flexible' || value === 'full')
                                        ? matchesType
                                        : matchesStatus;
                                wrapper.classList.toggle('hidden', !shouldShow);
                            });
                        };

                        eventFilter.addEventListener('change', applyFilter);
                        applyFilter();
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

