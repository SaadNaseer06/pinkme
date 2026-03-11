document.addEventListener("click", function (event) {
    const modal = document.getElementById("popupModal");
    if (!modal.classList.contains("hidden") && event.target === modal) {
        modal.classList.add("hidden");
    }
});
// Modal functions
function openModal() {
    document.getElementById("registerModal").style.display = "flex";
}

function closeModal() {
    document.getElementById("registerModal").style.display = "none";
}

// Close modal when clicking outside of it
window.onclick = function (event) {
    const modal = document.getElementById("registerModal");
    if (event.target === modal) {
        closeModal();
    }
};

function toggleDropdown(e) {
    e.stopPropagation();

    const wrapper = e.currentTarget.closest(".notificationWrapper");
    const dropdown = wrapper.querySelector(".notificationDropdown");

    // Hide all other dropdowns first
    document.querySelectorAll(".notificationDropdown").forEach((dd) => {
        if (dd !== dropdown) dd.classList.add("hidden");
    });

    dropdown.classList.toggle("hidden");
}

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

// Click outside to close both dropdown types
window.addEventListener("click", function (e) {
    document.querySelectorAll(".notificationDropdown").forEach((dd) => {
        const wrapper = dd.closest(".notificationWrapper");
        if (!wrapper.contains(e.target)) dd.classList.add("hidden");
    });

    document.querySelectorAll(".profileDropdown").forEach((dd) => {
        const wrapper = dd.closest(".profileWrapper");
        if (!wrapper.contains(e.target)) dd.classList.add("hidden");
    });
});

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
function toggleFAQ(clickedHeader) {
    const answer = clickedHeader.nextElementSibling;
    const wasOpen = !answer.classList.contains("hidden");
    const allHeaders = document.querySelectorAll(".faq-header");

    allHeaders.forEach((header) => {
        const question = header.querySelector("h3");
        const icon = header.querySelector("svg");
        const headerAnswer = header.nextElementSibling;

        question.classList.remove("text-pink-500");
        question.classList.add("text-[#91848C]");
        icon.classList.remove("rotate-180");
        if (headerAnswer) {
            headerAnswer.classList.add("hidden");
        }
    });

    if (!wasOpen) {
        const question = clickedHeader.querySelector("h3");
        const icon = clickedHeader.querySelector("svg");
        question.classList.add("text-pink-500");
        question.classList.remove("text-[#91848C]");
        icon.classList.add("rotate-180");
        answer.classList.remove("hidden");
    }
}

function showTab(tabId) {
    // Hide all tab contents
    const tabs = document.querySelectorAll(".tab-content");
    tabs.forEach((tab) => tab.classList.add("hidden"));

    // Remove active styles from all tab buttons
    const buttons = document.querySelectorAll(".tab-btn");
    buttons.forEach((btn) => {
        btn.classList.remove("bg-[#9E2469]", "text-white");
        btn.classList.add("bg-[#F3E8EF]", "text-[#91848C]");
    });

    // Show the selected tab content
    document.getElementById(tabId).classList.remove("hidden");

    // Add active styles to the clicked button
    const activeBtn = document.querySelector(`[onclick="showTab('${tabId}')"]`);
    activeBtn.classList.add("bg-[#9E2469]", "text-white");
    activeBtn.classList.remove("bg-[#F3E8EF]", "text-[#91848C]");
}
