@php
    $user = auth()->user();
    $profile = $user->profile;
    $fullName = $profile ? $profile->full_name : 'Unknown User';
@endphp

<!-- Top Navigation Bar -->
<header
    class="mt-4 ml-6 mr-6 bg-[#F3E8EF] p-4 flex justify-between items-center rounded-lg overflow-visible tab-head md:flex hidden tab-header-1">
    <!-- Search Bar -->
    <div class="relative">
        {{-- <input type="text" placeholder="Type here to search..."
            class="pl-4 pr-10 py-2 rounded-md bg-transparent text-[#B9B1B6] text-sm border border-[#B9B1B6] focus:outline-none focus:border-[#DB69A2] focus:ring-1 focus:ring-[#DB69A2] tab-search" />
        <button class="absolute right-2 top-1/2 transform -translate-y-1/2 text-[#DB69A2]">
            <i class="fas fa-search"></i>
        </button> --}}
    </div>

    <!-- User Menu -->
    <div class="flex items-center space-x-4 tab-p">
        <!-- Left Menu Icons -->
        <div class="flex items-center space-x-6 ml-2">
            <img src="{{ asset('public/images/HAM.svg') }}" alt="Menu" class="hamburger hamburgerBtn" />

            <button id="fullscreenBtn">
                <img src="{{ asset('public/images/scanner.svg') }}" alt="Scanner" class="h-3" />
            </button>

            <!-- Notification Wrapper -->
            <!--<div class="relative inline-block notificationWrapper">-->
            <!--    <div class="cursor-pointer" onclick="toggleDropdown(event)">-->
            <!--        <img src="{{ asset('public/images/notification.svg') }}" alt="Notifications" class="h-4" />-->
            <!--    </div>-->

                <!-- Notification Dropdown -->
            <!--    <div-->
            <!--        class="hidden fixed right-10 mt-2 w-80 bg-[#F3E8EF] rounded-md shadow-lg z-50 notificationDropdown">-->
            <!--        <div class="flex justify-between items-center px-4 py-3 bg-[#db69a2] text-white rounded-t-md">-->
            <!--            <span class="font-semibold">All Notifications</span>-->
            <!--            <span class="bg-white text-[#db69a2] text-sm font-bold rounded px-2 py-0.5">4</span>-->
            <!--        </div>-->

            <!--        <div class="divide-y divide-[#E5D6E0] max-h-80 overflow-y-auto">-->
                        <!-- Example Notification Item -->
            <!--            <div class="flex items-center px-4 py-3 space-x-3 cursor-pointer">-->
            <!--                <img src="{{ asset('public/images/patient-1.png') }}" class="w-10 h-10 rounded-full" />-->
            <!--                <div class="flex-1">-->
            <!--                    <p class="text-sm font-semibold text-[#213430]">Lorem ipsum</p>-->
            <!--                    <p class="text-xs text-[#A9A9A9]">Sed ut</p>-->
            <!--                </div>-->
            <!--                <span class="text-xs text-[#A9A9A9] whitespace-nowrap">Just Now</span>-->
            <!--            </div>-->
            <!--            <div class="flex items-center px-4 py-3 space-x-3 cursor-pointer">-->
            <!--                <img src="{{ asset('public/images/patient-11.png') }}" class="w-10 h-10 rounded-full" />-->
            <!--                <div class="flex-1">-->
            <!--                    <p class="text-sm font-semibold text-[#213430]">-->
            <!--                        Lorem ipsum-->
            <!--                    </p>-->
            <!--                    <p class="text-xs text-[#A9A9A9]">Sed ut</p>-->
            <!--                </div>-->
            <!--                <span class="text-xs text-[#A9A9A9] whitespace-nowrap">Just Now</span>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
        </div>

        <!-- Profile Dropdown -->
        <div class="relative inline-block profileWrapper">
            <div class="flex items-center space-x-2 cursor-pointer" onclick="toggleProfileDropdown(event)">
                <div class="w-10 h-10 overflow-hidden rounded-full border">
                    <img src="{{ auth()->user()->avatar_url }}" alt="Profile Picture" class="w-full h-full object-cover" />
                </div>
                <div class="text-left">
                    <p class="text-sm font-normal text-[#213430]">{{ $fullName }}</p>
                    <p class="text-xs text-[#DB69A2]">Online</p>
                </div>
            </div>

            <div class="hidden fixed right-10 mt-2 w-72 bg-[#F7EBF3] rounded-xl z-50 profileDropdown">
                <div class="px-4 py-3 bg-[#DB69A2] rounded-t-xl">
                    <p class="text-white text-sm font-semibold">All Notification</p>
                    <p class="text-white text-xs font-light">Available</p>
                </div>

                <div class="divide-y divide-[#E5D6E0] max-h-80 overflow-y-auto bg-transparent">
                    <div class="p-3 space-y-3">
                        <a href="{{ route('patient.setting') }}" class="flex items-start space-x-3 p-2 rounded-md">
                            <img src="{{ asset('public/images/p-1.svg') }}" class="w-5 h-5 mt-1" />
                            <div>
                                <p class="text-sm font-semibold text-[#213430]">My Profile</p>
                                <p class="text-xs text-[#A9A9A9]">View personal profile details.</p>
                            </div>
                        </a>
                        <div class="border-b border-[#B9B1B6]"></div>
                        <a href="{{ route('patient.setting') }}" class="flex items-start space-x-3 p-2 rounded-md">
                            <img src="{{ asset('public/images/p-2.svg') }}" class="w-5 h-5 mt-1" />
                            <div>
                                <p class="text-sm font-semibold text-[#213430]">Edit Profile</p>
                                <p class="text-xs text-[#A9A9A9]">Modify your personal details.</p>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="px-4 pb-3">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full text-center bg-[#db69a2] text-white text-sm font-semibold py-2 rounded-md flex items-center justify-center gap-2">
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
<header
    class="mt-4 ml-6 mr-6 bg-[#F3E8EF] p-4 flex justify-between items-center rounded-lg overflow-visible tab-head md:hidden tab-header">


    <!-- User Menu -->
    <div class="flex items-center justify-between w-full  ">
        <img src="{{ asset('public/images/HAM.svg') }}" alt="Menu " class="hamburger mt-2 ml-2 hamburgerBtn">
        <div class="flex items-center space-x-4 tab-p">
            <!-- Left Menu Icons -->
            <div class="flex items-center space-x-6 ml-2">

                <button id="fullscreenBtn">
                    <img src="{{ asset('public/images/scanner.svg') }}" alt="Scanner" class="h-3" />
                </button>

                <!-- Notification Wrapper -->
                <!--<div class="relative inline-block notificationWrapper">-->
                <!--    <div class="cursor-pointer" onclick="toggleDropdown(event)">-->
                <!--        <img src="{{ asset('public/images/notification.svg') }}" alt="Notifications" class="h-4" />-->
                <!--    </div>-->

                    <!-- Notification Dropdown -->
                <!--    <div-->
                <!--        class="hidden fixed right-10 mt-2 w-80 bg-[#F3E8EF] rounded-md shadow-lg z-50 notificationDropdown">-->
                <!--        <div class="flex justify-between items-center px-4 py-3 bg-[#db69a2] text-white rounded-t-md">-->
                <!--            <span class="font-semibold">All Notifications</span>-->
                <!--            <span class="bg-white text-[#db69a2] text-sm font-bold rounded px-2 py-0.5">4</span>-->
                <!--        </div>-->

                <!--        <div class="divide-y divide-[#E5D6E0] max-h-80 overflow-y-auto">-->
                            <!-- Example Notification Item -->
                <!--            <div class="flex items-center px-4 py-3 space-x-3 cursor-pointer">-->
                <!--                <img src="{{ asset('public/images/patient-1.png') }}" class="w-10 h-10 rounded-full" />-->
                <!--                <div class="flex-1">-->
                <!--                    <p class="text-sm font-semibold text-[#213430]">Lorem ipsum</p>-->
                <!--                    <p class="text-xs text-[#A9A9A9]">Sed ut</p>-->
                <!--                </div>-->
                <!--                <span class="text-xs text-[#A9A9A9] whitespace-nowrap">Just Now</span>-->
                <!--            </div>-->
                <!--            <div class="flex items-center px-4 py-3 space-x-3 cursor-pointer">-->
                <!--                <img src="{{ asset('public/images/patient-11.png') }}" class="w-10 h-10 rounded-full" />-->
                <!--                <div class="flex-1">-->
                <!--                    <p class="text-sm font-semibold text-[#213430]">-->
                <!--                        Lorem ipsum-->
                <!--                    </p>-->
                <!--                    <p class="text-xs text-[#A9A9A9]">Sed ut</p>-->
                <!--                </div>-->
                <!--                <span class="text-xs text-[#A9A9A9] whitespace-nowrap">Just Now</span>-->
                <!--            </div>-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</div>-->
            </div>

            <!-- Profile Dropdown -->
            <div class="relative inline-block profileWrapper">
                <div class="flex items-center space-x-2 cursor-pointer" onclick="toggleProfileDropdown(event)">
                    <div class="w-10 h-10 overflow-hidden rounded-full border">
                        <img src="{{ auth()->user()->avatar_url }}" alt="Profile Picture"
                            class="w-full h-full object-cover" />
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-normal text-[#213430]">{{ $fullName }}</p>
                        <p class="text-xs text-[#DB69A2]">Online</p>
                    </div>
                </div>

                <div class="hidden fixed right-10 mt-2 w-72 bg-[#F7EBF3] rounded-xl z-50 profileDropdown">
                    <div class="px-4 py-3 bg-[#DB69A2] rounded-t-xl">
                        <p class="text-white text-sm font-semibold">All Notification</p>
                        <p class="text-white text-xs font-light">Available</p>
                    </div>

                    <div class="divide-y divide-[#E5D6E0] max-h-80 overflow-y-auto bg-transparent">
                        <div class="p-3 space-y-3">
                            <a href="{{ route('patient.profile') }}"
                                class="flex items-start space-x-3 p-2 rounded-md">
                                <img src="{{ asset('public/images/p-1.svg') }}" class="w-5 h-5 mt-1" />
                                <div>
                                    <p class="text-sm font-semibold text-[#213430]">My Profile</p>
                                    <p class="text-xs text-[#A9A9A9]">View personal profile details.</p>
                                </div>
                            </a>
                            <div class="border-b border-[#B9B1B6]"></div>
                            <a href="{{ route('patient.setting') }}"
                                class="flex items-start space-x-3 p-2 rounded-md">
                                <img src="{{ asset('public/images/p-2.svg') }}" class="w-5 h-5 mt-1" />
                                <div>
                                    <p class="text-sm font-semibold text-[#213430]">Edit Profile</p>
                                    <p class="text-xs text-[#A9A9A9]">Modify your personal details.</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="px-4 pb-3">
                        <button
                            class="w-full text-center bg-[#db69a2] text-white text-sm font-semibold py-2 rounded-md flex items-center justify-center gap-2">
                            Sign Out
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Search Bar -->
    <div class="relative mt-4">
        {{-- <input type="text" placeholder="Type here to search..."
            class="pl-4 pr-10 py-2 rounded-md bg-transparent text-[#B9B1B6] text-sm border border-[#B9B1B6] focus:outline-none focus:border-[#DB69A2] focus:ring-1 focus:ring-[#DB69A2] tab-search" />
        <button class="absolute right-2 top-1/2 transform -translate-y-1/2 text-[#DB69A2]">
            <i class="fas fa-search"></i>
        </button> --}}
    </div>
</header>
