<!-- Footer -->
<footer
    class="mt-4 ml-6 mr-6 mb-3 bg-[#F3E8EF] p-4 flex md:flex-row flex-col justify-between items-center rounded-lg text-center md:text-left">
    <div class="text-[#213430] app-text mb-2 md:mb-0">
        Copyright © 2025 <span class="text-pink">Pink Me</span> All Rights
        Reserved | Design by Bmybrand
    </div>
    <div class="flex space-x-4 text-[#213430] app-text">
        <a href="{{ route('policy.privacy') }}" class="hover:text-gray-700">Privacy Policy</a>
        <a href="{{ route('policy.terms') }}" class="hover:text-gray-700">Terms &amp; Conditions</a>
    </div>
</footer>
</div>
<script>
    document.getElementById("fullscreenBtn").addEventListener("click", () => {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch((err) => {
                console.error(
                    `Error attempting to enable fullscreen: ${err.message} (${err.name})`
                );
            });
        } else {
            document.exitFullscreen();
        }
    });
    // Mobile sidebar toggle
    const mobileSidebar = document.getElementById("mobileSidebar");
    const hamburgerBtns = document.querySelectorAll(".hamburgerBtn"); // This gets all buttons with that class
    const closeBtn = document.getElementById("closeBtn");

    hamburgerBtns.forEach((hamburgerBtn) => {
        hamburgerBtn.addEventListener("click", () => {
            mobileSidebar.classList.add("active");

            // Hide all hamburger buttons
            hamburgerBtns.forEach((btn) => {
                btn.style.display = "none";
            });
        });
    });

    closeBtn.addEventListener("click", () => {
        mobileSidebar.classList.remove("active");

        // Show all hamburger buttons
        hamburgerBtns.forEach((btn) => {
            btn.style.display = "block";
        });
    });
</script>
