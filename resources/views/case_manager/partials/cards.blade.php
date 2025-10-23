@php
    use Illuminate\Support\Facades\Auth;

    // Query directly in Blade (kept simple as requested)
    $userId = Auth::id();

    $q = \App\Models\Application::query()->where('reviewer_id', $userId);

    $totalCount = (clone $q)->count();
    $approvedCount = (clone $q)->where('status', 'approved')->count();
    $rejectedCount = (clone $q)->where('status', 'rejected')->count();
    $pendingCount = (clone $q)->where('status', 'pending')->count();

    // Short number formatter: 110150 -> "110.15K"
    $short = function ($n) {
        if ($n >= 1000) {
            return number_format($n / 1000, 2) . 'K';
        }
        return number_format($n);
    };
@endphp

<div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-6 status-cards">
    <!-- Total Applications -->
    <div class="bg-[#F3E8EF] rounded-lg px-4 py-4 flex items-center justify-between">
        <div class="bg-[#DB69A2] p-3 rounded-full mr-4">
            <img src="{{ asset('public/images/Case-D-1.svg') }}" alt="" class="h-8 w-8 status-cards-img" />
        </div>
        <div>
            <h3 class="text-[#213430] font-semibold status-cards-h1">Total Applications</h3>
            <p class="text-md font-normal text-[#DB69A2] text-right status-cards-p">
                {{ $short($totalCount) }}
            </p>
        </div>
    </div>

    <!-- Approved Applications -->
    <div class="bg-[#C5E8D1] rounded-lg px-4 py-4 flex items-center justify-between">
        <div class="bg-[#20B354] p-3 rounded-full mr-4">
            <img src="{{ asset('public/images/Case-D-2.svg') }}" alt="" class="h-8 w-8 status-cards-img" />
        </div>
        <div>
            <h3 class="text-[#213430] font-semibold status-cards-h1">Approve Applications</h3>
            <p class="text-md font-normal text-[#20B354] text-right status-cards-p">
                {{ $short($approvedCount) }}
            </p>
        </div>
    </div>

    <!-- Rejected Applications -->
    <div class="bg-[#E8C5C5] rounded-lg px-4 py-4 flex items-center justify-between">
        <div class="bg-[#B32020] p-3 rounded-full mr-4">
            <img src="{{ asset('public/images/Case-D-3.svg') }}" alt="" class="h-8 w-8 status-cards-img" />
        </div>
        <div>
            <h3 class="text-[#213430] font-semibold status-cards-h1">Rejected Applications</h3>
            <p class="text-md font-normal text-[#B32020] text-right status-cards-p">
                {{ $short($rejectedCount) }}
            </p>
        </div>
    </div>

    <!-- Pending Applications -->
    <div class="bg-[#E7D4DF] rounded-lg px-4 py-4 flex items-center justify-between">
        <div class="bg-[#91848C] p-3 rounded-full mr-4">
            <img src="{{ asset('public/images/Case-D-4.svg') }}" alt="" class="h-8 w-8 status-cards-img" />
        </div>
        <div>
            <h3 class="text-[#213430] font-semibold status-cards-h1">Pending Applications</h3>
            <p class="text-md font-normal text-[#91848C] text-right status-cards-p">
                {{ $short($pendingCount) }}
            </p>
        </div>
    </div>
</div>
