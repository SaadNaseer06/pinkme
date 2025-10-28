@php
    use App\Support\EventHighlightFormatter;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $image = asset('public/images/' . $eventImages[$loopIndex % count($eventImages)]);
    $descriptionHtml = $event->description
        ? strip_tags($event->description, '<p><br><strong><em><u><ol><ul><li><a><span><div><blockquote>')
        : null;
    $descriptionText = trim(strip_tags($descriptionHtml ?? ''));
    $descriptionSummary = $descriptionText !== '' ? $descriptionText : 'Details will be announced soon.';
    $eventHighlightsHtml = EventHighlightFormatter::format($event->event_highlights);
    $primarySponsor = $event->primary_sponsor;
    $primarySponsorProfile = optional($primarySponsor)->profile;
    $primarySponsorDetail = optional($primarySponsor)->sponsorDetail;
    $primarySponsorName =
        $primarySponsorDetail->company_name ??
        ($primarySponsorProfile->full_name ?? ($primarySponsor->email ?? 'Sponsor to be announced'));
    $primarySponsorPhone =
        $primarySponsorDetail->company_phone ?? ($primarySponsorProfile->phone ?? 'Phone unavailable');
    $primarySponsorEmail = $primarySponsorDetail->company_email ?? ($primarySponsor->email ?? 'Email unavailable');
    $primarySponsorLogo =
        $primarySponsorDetail && $primarySponsorDetail->logo
            ? Storage::url($primarySponsorDetail->logo)
            : asset('public/images/brand.png');
    $primarySponsorAbout =
        $primarySponsorDetail && $primarySponsorDetail->company_type
            ? 'A ' . strtolower($primarySponsorDetail->company_type) . ' supporting patient care initiatives.'
            : 'Committed supporters partnering with us to impact more lives.';
    $totalSponsors = $event->sponsors->count();
    $totalRaised = number_format($event->total_sponsorship_amount ?? 0, 2);
    $location = $event->location ?: 'Location to be announced';
    $status = $event->status ?? 'upcoming';
    $timeLabel = $event->time_label ?? Carbon::parse($event->date)->format('g:i A');
@endphp

<div class="event-entry" data-event-status="{{ $status }}">
    <div class="bg-[#F3E8EF] rounded-lg p-4 mb-4 flex items-center justify-between md:flex hidden">
        <div class="flex items-center">
            <div
                class="flex flex-col items-center justify-center w-20 h-20 border-2 border-pink rounded-lg mr-4 bg-[#FFF7FC]">
                <span class="text-sm text-pink">{{ $event->month_label }}</span>
                <span class="text-4xl font-bold text-pink">{{ $event->day_label }}</span>
            </div>
            <div class="w-20 h-20 rounded-lg overflow-hidden mr-4">
                <img src="{{ $image }}" alt="{{ $event->title }}" class="w-full h-full object-cover" />
            </div>
            <div>
                <h3 class="text-xl font-semibold text-[#213430] mb-1 program-h">{{ $event->title }}</h3>
                <p class="text-sm text-[#91848C] program-p">{{ Str::limit($descriptionSummary, 150) }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <button type="button"
                class="bg-transparent border border-[#213430] text-[#213430] hover:bg-[#DB69A2] hover:border-none hover:text-white py-3 px-6 rounded-lg program-btn"
                onclick="openModal(this)" data-title="{{ e($event->title) }}"
                data-description="{{ e($descriptionSummary) }}"
                data-description-html="{{ e($descriptionHtml ?? '') }}"
                data-date="{{ e($event->date_label) }}" data-time="{{ e($timeLabel) }}"
                data-location="{{ e($location) }}" data-image="{{ $image }}"
                data-sponsor-name="{{ e($primarySponsorName) }}" data-sponsor-phone="{{ e($primarySponsorPhone) }}"
                data-sponsor-email="{{ e($primarySponsorEmail) }}" data-sponsor-logo="{{ $primarySponsorLogo }}"
                data-sponsor-about="{{ e($primarySponsorAbout) }}" data-total-sponsors="{{ $totalSponsors }}"
                data-total-raised="{{ $totalRaised }}" data-status="{{ $status }}"
                data-highlights="{{ e($eventHighlightsHtml ?? '') }}"
                data-event-id="{{ $event->id }}">
                Quick View
            </button>
            @if ($event->isRegistrationOpen() || $event->isSponsorRegistered(auth()->id()))
                <a href="{{ route('sponsor.events.show', $event) }}"
                    style="background: #DB69A2; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-block; transition: background 0.3s ease;"
                    onmouseover="this.style.background='linear-gradient(to right, #C63A85, #B02A72)'"
                    onmouseout="this.style.background='linear-gradient(to right, #DB69A2, #C63A85)'">
                    {{ $event->isSponsorRegistered(auth()->id()) ? 'View Registration' : 'Register Now' }}
                </a>
            @else
                <a href="{{ route('sponsor.events.show', $event) }}"
                    class="bg-gray-400 text-white py-3 px-6 rounded-lg program-btn">
                    View Details
                </a>
            @endif
        </div>
    </div>

    <div class="bg-[#F3E8EF] rounded-lg p-3 mb-4 flex items-center justify-between md:hidden flex">
        <div class="flex flex-col gap-2">
            <div class="w-[80px] h-[80px] rounded-lg overflow-hidden mr-4">
                <img src="{{ $image }}" alt="{{ $event->title }}" class="w-full h-full object-cover" />
            </div>
            <div
                class="flex flex-col items-center justify-center w-[80px] h-[60px] border-2 border-pink rounded-lg mr-4 bg-[#FFF7FC]">
                <span class="text-sm text-pink">{{ $event->month_label }}</span>
                <span class="text-4xl font-bold text-pink">{{ $event->day_label }}</span>
            </div>
        </div>
        <div class="flex flex-col gap-1 text-left">
            <h3 class="text-[15px] font-semibold text-[#213430]">{{ $event->title }}</h3>
            <p class="text-[13px] font-light text-[#91848C]">{{ Str::limit($descriptionSummary, 110) }}</p>
            <div class="flex gap-2 flex-col">
                <button type="button"
                    class="bg-transparent border border-[#213430] text-[#213430] hover:bg-[#db69a2] hover:text-white hover:border-none py-2 px-4 rounded-lg text-sm"
                    onclick="openModal(this)" data-title="{{ e($event->title) }}"
                    data-description="{{ e($descriptionSummary) }}"
                    data-description-html="{{ e($descriptionHtml ?? '') }}"
                    data-date="{{ e($event->date_label) }}"
                    data-time="{{ e($timeLabel) }}" data-location="{{ e($location) }}"
                    data-image="{{ $image }}" data-sponsor-name="{{ e($primarySponsorName) }}"
                    data-sponsor-phone="{{ e($primarySponsorPhone) }}"
                    data-sponsor-email="{{ e($primarySponsorEmail) }}" data-sponsor-logo="{{ $primarySponsorLogo }}"
                    data-sponsor-about="{{ e($primarySponsorAbout) }}" data-total-sponsors="{{ $totalSponsors }}"
                    data-total-raised="{{ $totalRaised }}" data-status="{{ $status }}"
                    data-highlights="{{ e($eventHighlightsHtml ?? '') }}"
                    data-event-id="{{ $event->id }}">
                    Quick View
                </button>
                @if ($event->isRegistrationOpen() || $event->isSponsorRegistered(auth()->id()))
                    <a href="{{ route('sponsor.events.show', $event) }}"
                        style="background: linear-gradient(to right, #DB69A2, #C63A85); color: white; padding: 8px 16px; border-radius: 8px; font-size: 0.875rem; text-align: center; text-decoration: none; display: inline-block; transition: background 0.3s ease;"
                        onmouseover="this.style.background='linear-gradient(to right, #C63A85, #B02A72)'"
                        onmouseout="this.style.background='linear-gradient(to right, #DB69A2, #C63A85)'">
                        {{ $event->isSponsorRegistered(auth()->id()) ? 'View Registration' : 'Register Now' }}
                    </a>
                @else
                    <a href="{{ route('sponsor.events.show', $event) }}"
                        class="bg-gray-400 text-white py-2 px-4 rounded-lg text-sm text-center">
                        View Details
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
