@php
    $user = auth()->user();
    $profile = $user->profile ?? null;
    $fullName = $profile?->full_name ?? 'Finance User';
@endphp
<header class="mt-4 mr-6 bg-[#F3E8EF] p-4 justify-between items-center rounded-lg tab-head md:flex hidden tab-header-1">
    <div class="relative flex items-center">
        <img src="{{ asset('public/images/HAM.svg') }}" alt="Menu" class="hamburger hamburgerBtn">
    </div>
    <div class="flex items-center space-x-4 tab-p">
        <div class="flex items-center space-x-6 ml-2">
            @include('partials.notification-center', ['icon' => asset('public/images/notification.svg'), 'iconClass' => 'h-4'])
        </div>
        <div class="relative inline-block profileWrapper">
            <div class="flex items-center space-x-2 cursor-pointer" onclick="toggleProfileDropdown(event)">
                <div class="w-10 h-10 overflow-hidden rounded-full border">
                    <img src="{{ auth()->user()->avatar_url ?? asset('public/images/profile.png') }}" alt="Profile" class="w-full h-full object-cover" />
                </div>
                <div class="text-left">
                    <p class="text-sm font-normal text-[#213430]">{{ $fullName }}</p>
                    <p class="text-xs text-[#9E2469]">Finance</p>
                </div>
            </div>
            <div class="hidden fixed right-10 mt-2 w-72 bg-[#F7EBF3] rounded-xl z-50 profileDropdown">
                <div class="px-4 py-3 bg-[#9E2469] rounded-t-xl">
                    <p class="text-white text-sm font-semibold">{{ $fullName }}</p>
                    <p class="text-white text-xs font-light">Finance</p>
                </div>
                <div class="px-4 pb-3 pt-3">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full text-center bg-[#9E2469] text-white text-sm font-semibold py-2 rounded-md flex items-center justify-center gap-2">
                            Sign Out
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
<header class="mt-4 ml-6 mr-6 bg-[#F3E8EF] p-4 flex justify-between items-center rounded-lg tab-head md:hidden tab-header">
    <div class="flex items-center justify-between w-full">
        <img src="{{ asset('public/images/HAM.svg') }}" alt="Menu" class="hamburger mt-2 ml-2 hamburgerBtn">
        <div class="flex items-center space-x-4 tab-p">
            <div class="flex items-center space-x-6 ml-2">
                @include('partials.notification-center', ['icon' => asset('public/images/notification.svg'), 'iconClass' => 'h-4'])
            </div>
            <div class="relative inline-block profileWrapper">
                <div class="flex items-center space-x-2 cursor-pointer" onclick="toggleProfileDropdown(event)">
                    <div class="w-10 h-10 overflow-hidden rounded-full border">
                        <img src="{{ auth()->user()->avatar_url ?? asset('public/images/profile.png') }}" alt="Profile" class="w-full h-full object-cover" />
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-normal text-[#213430]">{{ $fullName }}</p>
                        <p class="text-xs text-[#9E2469]">Finance</p>
                    </div>
                </div>
                <div class="hidden fixed right-6 mt-2 w-72 bg-[#F7EBF3] rounded-xl z-50 profileDropdown">
                    <div class="px-4 py-3 bg-[#9E2469] rounded-t-xl">
                        <p class="text-white text-sm font-semibold">{{ $fullName }}</p>
                        <p class="text-white text-xs font-light">Finance</p>
                    </div>
                    <div class="px-4 pb-3 pt-3">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full text-center bg-[#9E2469] text-white text-sm font-semibold py-2 rounded-md flex items-center justify-center gap-2">
                                Sign Out
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<script>
    function toggleProfileDropdown(e) {
        e.stopPropagation();
        const wrapper = e.currentTarget.closest(".profileWrapper");
        const dropdown = wrapper.querySelector(".profileDropdown");
        if (!dropdown) return;
        document.querySelectorAll(".profileDropdown").forEach(function(dd) {
            if (dd !== dropdown) dd.classList.add("hidden");
        });
        dropdown.classList.toggle("hidden");
    }
    document.addEventListener('click', function() {
        document.querySelectorAll(".profileDropdown").forEach(function(dd) {
            dd.classList.add("hidden");
        });
    });
    document.querySelectorAll(".profileWrapper").forEach(function(wrapper) {
        wrapper.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
</script>
