<!-- Status Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-6 status-cards">
    <!-- Total Applications -->
    <div class="bg-[#F3E8EF] rounded-lg px-4 py-4 flex items-center justify-between">
        <div class="bg-[#DB69A2] p-3 rounded-full mr-4">
            <img src="{{ asset('public/images/Sponsor-1.svg') }}" alt="" class="h-8 w-8 status-cards-img">
        </div>
        <div>
            <h3 class="text-[#213430] font-semibold status-cards-h1">Active Sponsorships</h3>
            <p class="text-md font-normal text-[#DB69A2] text-right status-cards-p">{{ number_format($stats['active_sponsorships'] ?? 0) }}</p>
        </div>
    </div>

    <!-- Approved Applications -->
    <div class="bg-[#C5E8D1] rounded-lg px-4 py-4 flex items-center justify-between">
        <div class="bg-[#20B354] p-3 rounded-full mr-4">
            <img src="{{ asset('public/images/Sponsor-2.svg') }}" alt="" class="h-8 w-8 status-cards-img">
        </div>
        <div>
            <h3 class="text-[#213430] font-semibold status-cards-h1">Ongoing Events</h3>
            <p class="text-md font-normal text-[#20B354] text-right status-cards-p">{{ number_format($stats['ongoing_events'] ?? 0) }}</p>
        </div>
    </div>


    <!-- Rejected Applications -->
    <div class="bg-[#E8C5C5] rounded-lg px-4 py-4 flex items-center justify-between">
        <div class="bg-[#B32020] p-3 rounded-full mr-4">
            <img src="{{ asset('public/images/Sponsor-3.svg') }}" alt="" class="h-8 w-8 status-cards-img">
        </div>
        <div>
            <h3 class="text-[#213430] font-semibold status-cards-h1">Total Events</h3>
            <p class="text-md font-normal text-[#B32020] text-right status-cards-p">{{ number_format($stats['support_programs'] ?? 0) }}</p>
        </div>
    </div>

    <!-- Pending Applications -->
    <div class="bg-[#E7D4DF] rounded-lg px-4 py-4 flex items-center justify-between">
        <div class="bg-[#91848C] p-3 rounded-full mr-4">
            <img src="{{ asset('public/images/Sponsor-4.svg') }}" alt="" class="h-8 w-8 status-cards-img">
        </div>
        <div>
            <h3 class="text-[#213430] font-semibold status-cards-h1">Upcoming Events</h3>
            <p class="text-md font-normal text-[#91848C] text-right status-cards-p">{{ number_format($stats['upcoming_events'] ?? 0) }}</p>
        </div>
    </div>
</div>
