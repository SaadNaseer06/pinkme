<!-- Top Navigation Bar -->
<header
    class="mt-4 ml-6 mr-6 bg-[#F3E8EF] p-4  justify-between items-center rounded-lg tab-head md:flex hidden tab-header-1">
    <!-- Search Bar -->
    <div class="relative">
        {{-- <input type="text" placeholder="Type here to search..."
            class="pl-4 pr-10 py-2 rounded-md bg-transparent text-[#B9B1B6] text-sm border border-[#B9B1B6] tab-search focus:outline-none focus:border-[#DB69A2] focus:ring-1 focus:ring-[#DB69A2]" />
        <button class="absolute right-2 top-1/2 transform -translate-y-1/2 text-[#DB69A2] mt-[12px]">
            <i class="fas fa-search"></i>
        </button> --}}
    </div>

    <!-- User Menu -->
    <div class="flex items-center space-x-4 tab-p">
        <div class="flex items-center space-x-6 ml-2">

            <img src="/images/HAM.svg" alt="Menu " class="hamburger hamburgerBtn">

            <button id="fullscreenBtn">
                <img src="/images/scanner.svg" alt="Scanner" class="h-3" />
            </button>
            <img src="/images/notification.svg" alt="Scanner" class="h-4" />
        </div>

        <!-- Profile Dropdown -->
        <div class="relative inline-block profileWrapper">
            <div class="flex items-center space-x-2 cursor-pointer" onclick="toggleProfileDropdown(event)">
                <div class="w-10 h-10 overflow-hidden rounded-full border">
                    <img src="/images/profile.png" alt="Profile Picture" class="w-full h-full object-cover" />
                </div>
                <div class="text-left">
                    <p class="text-sm font-normal text-[#213430]">Sarah Tyler</p>
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
                        <a href="{{ route('patient.profile') }}" class="flex items-start space-x-3 p-2 rounded-md">
                            <img src="/images/p-1.svg" class="w-5 h-5 mt-1" />
                            <div>
                                <p class="text-sm font-semibold text-[#213430]">My Profile</p>
                                <p class="text-xs text-[#A9A9A9]">View personal profile details.</p>
                            </div>
                        </a>
                        <div class="border-b border-[#B9B1B6]"></div>
                        <a href="{{ route('patient.setting') }}" class="flex items-start space-x-3 p-2 rounded-md">
                            <img src="/images/p-2.svg" class="w-5 h-5 mt-1" />
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
    class="mt-4 ml-6 mr-6 bg-[#F3E8EF] p-4 flex justify-between items-center rounded-lg tab-head md:hidden tab-header">


    <!-- User Menu -->
    <div class="flex items-center justify-between w-full  ">
        <img src="/images/HAM.svg" alt="Menu " class="hamburger mt-2 ml-2 hamburgerBtn">
        <div class="flex items-center space-x-4 tab-p">
            <div class="flex items-center space-x-6 ml-2">



                <button id="fullscreenBtn">
                    <img src="/images/scanner.svg" alt="Scanner" class="h-3" />
                </button>
                <img src="/images/notification.svg" alt="Scanner" class="h-4" />
            </div>

            <div class="flex items-center space-x-2 ">
                <div class="w-10 h-10 overflow-hidden">
                    <img src="/images/profile.png" alt="Profile Picture" class="w-full h-full object-cover" />
                </div>
                <div class="text-left">
                    <p class="text-sm font-normal text-[#213430]">Sarah Tyler</p>
                    <p class="text-xs text-[#DB69A2]">Online</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Search Bar -->
    <div class="relative mt-4">
        {{-- <input type="text" placeholder="Type here to search..."
            class="pl-4 pr-10 py-2 rounded-md bg-transparent text-[#B9B1B6] text-sm border border-[#B9B1B6] tab-search focus:outline-none focus:border-[#DB69A2] focus:ring-1 focus:ring-[#DB69A2]" />
        <button class="absolute right-2 top-1/2 transform -translate-y-1/2 text-[#DB69A2] mt-[12px]">
            <i class="fas fa-search"></i>
        </button> --}}
    </div>
</header>


<script>
    // Modal functions
    function openModal() {
        document.getElementById('registerModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('registerModal').style.display = 'none';
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        const modal = document.getElementById('registerModal');
        if (event.target === modal) {
            closeModal();
        }
    }

    function showTab(tabId) {
        // Hide all tab contents
        const tabs = document.querySelectorAll(".tab-content");
        tabs.forEach((tab) => tab.classList.add("hidden"));

        // Remove active styles from all tab buttons
        const buttons = document.querySelectorAll(".tab-btn");
        buttons.forEach((btn) => {
            btn.classList.remove("bg-[#DB69A2]", "text-white");
            btn.classList.add("bg-[#F3E8EF]", "text-[#91848C]");
        });

        // Show the selected tab content
        document.getElementById(tabId).classList.remove("hidden");

        // Add active styles to the clicked button
        const activeBtn = document.querySelector(
            `[onclick="showTab('${tabId}')"]`
        );
        activeBtn.classList.add("bg-[#DB69A2]", "text-white");
        activeBtn.classList.remove("bg-[#F3E8EF]", "text-[#91848C]");
    }

    document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.getElementById('carousel');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const dots = document.querySelectorAll('.dot');

        const itemWidth = carousel.querySelector('.carousel-item').offsetWidth + 16; // Account for gap
        let currentIndex = 1; // Start with second item
        const maxIndex = carousel.children.length - 1;

        // Set initial scroll position
        carousel.scrollLeft = itemWidth * currentIndex;

        function updateDots() {
            dots.forEach((dot, i) => {
                dot.className = 'dot h-4 w-4 rounded-full cursor-pointer';
                dot.innerHTML = '';
                dot.style.backgroundColor = 'transparent';

                if (i === currentIndex) {
                    dot.innerHTML = `
        <div class="relative w-4 h-4 flex items-center justify-center">
          <div class="h-2 w-2 rounded-full bg-[#db69a2] z-10"></div>
          <div class="absolute inset-0 rounded-full border-2 border-[#db69a2] z-0"></div>
        </div>
      `;
                } else {
                    dot.style.backgroundColor = '#c8a9bb';
                }
            });
        }


        function updateButtons() {
            if (currentIndex <= 0) {
                prevBtn.classList.replace('bg-[#db69a2]', 'bg-[#E9D8E3]');
                prevBtn.classList.replace('text-white', 'text-gray-700');
            } else {
                prevBtn.classList.replace('bg-[#E9D8E3]', 'bg-[#db69a2]');
                prevBtn.classList.replace('text-gray-700', 'text-white');
            }

            if (currentIndex >= maxIndex) {
                nextBtn.classList.replace('bg-[#db69a2]', 'bg-[#E9D8E3]');
                nextBtn.classList.replace('text-white', 'text-gray-700');
            } else {
                nextBtn.classList.replace('bg-[#E9D8E3]', 'bg-[#db69a2]');
                nextBtn.classList.replace('text-gray-700', 'text-white');
            }
        }

        function scrollToIndex(index) {
            currentIndex = Math.max(0, Math.min(index, maxIndex));
            carousel.scrollLeft = itemWidth * currentIndex;
            updateDots();
            updateButtons();
        }

        nextBtn.addEventListener('click', () => {
            if (currentIndex < maxIndex) {
                scrollToIndex(currentIndex + 1);
            }
        });

        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) {
                scrollToIndex(currentIndex - 1);
            }
        });

        dots.forEach((dot, i) => {
            dot.addEventListener('click', () => {
                scrollToIndex(i);
            });
        });

        carousel.addEventListener('scroll', () => {
            const scrollPos = carousel.scrollLeft;
            const newIndex = Math.round(scrollPos / itemWidth);
            if (newIndex !== currentIndex) {
                currentIndex = newIndex;
                updateDots();
                updateButtons();
            }
        });

        // Initial UI update
        updateDots();
        updateButtons();
    });



    document.getElementById("fullscreenBtn").addEventListener("click", () => {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch((err) => {
                console.error(`Error attempting to enable fullscreen: ${err.message} (${err.name})`);
            });
        } else {
            document.exitFullscreen();
        }
    });

    // Mobile sidebar toggle
    const mobileSidebar = document.getElementById('mobileSidebar');
    const hamburgerBtns = document.querySelectorAll('.hamburgerBtn'); // This gets all buttons with that class
    const closeBtn = document.getElementById('closeBtn');

    hamburgerBtns.forEach(hamburgerBtn => {
        hamburgerBtn.addEventListener('click', () => {
            mobileSidebar.classList.add('active');

            // Hide all hamburger buttons
            hamburgerBtns.forEach(btn => {
                btn.style.display = 'none';
            });
        });
    });

    closeBtn.addEventListener('click', () => {
        mobileSidebar.classList.remove('active');

        // Show all hamburger buttons
        hamburgerBtns.forEach(btn => {
            btn.style.display = 'block';
        });
    });


    function toggleProfileDropdown(e) {
        e.stopPropagation();

        const wrapper = e.currentTarget.closest(".profileWrapper");
        const dropdown = wrapper.querySelector(".profileDropdown");

        // Hide all other dropdowns first
        document.querySelectorAll(".profileDropdown").forEach((dd) => {
            if (dd !== dropdown) dd.classList.add("hidden");
        });

        dropdown.classList.toggle("hidden");
    }
</script>
