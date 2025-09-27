@php
    $image = asset('images/' . $eventImages[$loopIndex % count($eventImages)]);
    $description = $event->description ?: 'Details will be announced soon.';
    $primarySponsor = $event->primary_sponsor;
    $primarySponsorProfile = optional($primarySponsor)->profile;
    $primarySponsorDetail = optional($primarySponsor)->sponsorDetail;
    $primarySponsorName = $primarySponsorDetail->company_name
        ?? $primarySponsorProfile->full_name
        ?? ($primarySponsor->email ?? 'Sponsor to be announced');
    $primarySponsorPhone = $primarySponsorDetail->company_phone
        ?? $primarySponsorProfile->phone
        ?? 'Phone unavailable';
    $primarySponsorEmail = $primarySponsorDetail->company_email
        ?? ($primarySponsor->email ?? 'Email unavailable');
    $primarySponsorLogo = $primarySponsorDetail && $primarySponsorDetail->logo
        ? Storage::url($primarySponsorDetail->logo)
        : asset('images/brand.png');
    $primarySponsorAbout = $primarySponsorDetail && $primarySponsorDetail->company_type
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
            <div class="flex flex-col items-center justify-center w-20 h-20 border-2 border-pink rounded-lg mr-4 bg-[#FFF7FC]">
                <span class="text-sm text-pink">{{ $event->month_label }}</span>
                <span class="text-4xl font-bold text-pink">{{ $event->day_label }}</span>
            </div>
            <div class="w-20 h-20 rounded-lg overflow-hidden mr-4">
                <img src="{{ $image }}" alt="{{ $event->title }}" class="w-full h-full object-cover" />
            </div>
            <div>
                <h3 class="text-xl font-semibold text-[#213430] mb-1 program-h">{{ $event->title }}</h3>
                <p class="text-sm text-[#91848C] program-p">{{ Str::limit($description, 150) }}</p>
            </div>
        </div>
        <button type="button"
            class="bg-transparent border border-[#213430] text-[#213430] hover:bg-[#DB69A2] hover:border-none hover:text-white py-3 px-8 rounded-lg program-btn"
            onclick="openModal(this)"
            data-title="{{ e($event->title) }}"
            data-description="{{ e($description) }}"
            data-date="{{ e($event->date_label) }}"
            data-time="{{ e($timeLabel) }}"
            data-location="{{ e($location) }}"
            data-image="{{ $image }}"
            data-sponsor-name="{{ e($primarySponsorName) }}"
            data-sponsor-phone="{{ e($primarySponsorPhone) }}"
            data-sponsor-email="{{ e($primarySponsorEmail) }}"
            data-sponsor-logo="{{ $primarySponsorLogo }}"
            data-sponsor-about="{{ e($primarySponsorAbout) }}"
            data-total-sponsors="{{ $totalSponsors }}"
            data-total-raised="{{ $totalRaised }}"
            data-status="{{ $status }}">
            View Details
        </button>
    </div>

    <div class="bg-[#F3E8EF] rounded-lg p-3 mb-4 flex items-center justify-between md:hidden flex">
        <div class="flex flex-col gap-2">
            <div class="w-[80px] h-[80px] rounded-lg overflow-hidden mr-4">
                <img src="{{ $image }}" alt="{{ $event->title }}" class="w-full h-full object-cover" />
            </div>
            <div class="flex flex-col items-center justify-center w-[80px] h-[60px] border-2 border-pink rounded-lg mr-4 bg-[#FFF7FC]">
                <span class="text-sm text-pink">{{ $event->month_label }}</span>
                <span class="text-4xl font-bold text-pink">{{ $event->day_label }}</span>
            </div>
        </div>
        <div class="flex flex-col gap-1 text-left">
            <h3 class="text-[15px] font-semibold text-[#213430]">{{ $event->title }}</h3>
            <p class="text-[13px] font-light text-[#91848C]">{{ Str::limit($description, 110) }}</p>
            <button type="button"
                class="bg-transparent border border-[#213430] text-[#213430] hover:bg-[#db69a2] hover:text-white hover:border-none py-2 px-6 rounded-lg"
                onclick="openModal(this)"
                data-title="{{ e($event->title) }}"
                data-description="{{ e($description) }}"
                data-date="{{ e($event->date_label) }}"
                data-time="{{ e($timeLabel) }}"
                data-location="{{ e($location) }}"
                data-image="{{ $image }}"
                data-sponsor-name="{{ e($primarySponsorName) }}"
                data-sponsor-phone="{{ e($primarySponsorPhone) }}"
                data-sponsor-email="{{ e($primarySponsorEmail) }}"
                data-sponsor-logo="{{ $primarySponsorLogo }}"
                data-sponsor-about="{{ e($primarySponsorAbout) }}"
                data-total-sponsors="{{ $totalSponsors }}"
                data-total-raised="{{ $totalRaised }}"
                data-status="{{ $status }}">
                View Details
            </button>
        </div>
    </div>
</div>
