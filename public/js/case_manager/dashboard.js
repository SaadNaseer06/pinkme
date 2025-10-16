// Dashboard JavaScript functionality

// Chart.js configuration and setup
function initializeDashboardCharts() {
    // Data for the chart
    const hours = [
        "8 AM",
        "9 AM",
        "10 AM",
        "11 AM",
        "12 PM",
        "1 PM",
        "2 PM",
        "3 PM",
        "4 PM",
        "5 PM",
    ];
    const values = [42, 50, 52, 75, 65, 55, 47, 50, 68, 66];

    // Get the canvas element
    const canvas = document.getElementById("statsChart");
    if (!canvas) {
        console.warn("Stats chart canvas not found");
        return;
    }

    // Create gradient for the chart fill
    const ctx = canvas.getContext("2d");
    const gradient = ctx.createLinearGradient(0, 0, 0, 240);
    gradient.addColorStop(0, "rgba(236, 72, 153, 0.3)");
    gradient.addColorStop(1, "rgba(236, 72, 153, 0.05)");

    // Setup chart
    const chart = new Chart(ctx, {
        type: "line",
        data: {
            labels: hours,
            datasets: [
                {
                    label: "Applications",
                    data: values,
                    borderColor: "#ec4899",
                    borderWidth: 2,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointBackgroundColor: "#ec4899",
                    pointHoverBackgroundColor: "#ec4899",
                    pointHoverBorderColor: "#fff",
                    pointHoverBorderWidth: 2,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    backgroundColor: "rgba(255, 255, 255, 0.9)",
                    titleColor: "#374151",
                    bodyColor: "#374151",
                    borderColor: "#e5e7eb",
                    borderWidth: 1,
                    padding: 10,
                    cornerRadius: 6,
                    displayColors: false,
                    titleFont: {
                        family: "Poppins",
                    },
                    bodyFont: {
                        family: "Poppins",
                    },
                    callbacks: {
                        title: function (tooltipItems) {
                            return tooltipItems[0].label;
                        },
                        label: function (context) {
                            return context.parsed.y + "%";
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false,
                    },
                    ticks: {
                        font: {
                            size: 12,
                            family: "Poppins",
                        },
                        color: "#9ca3af",
                    },
                },
                y: {
                    min: 20,
                    max: 80,
                    grid: {
                        color: "#e5e7eb",
                        drawBorder: false,
                        borderDash: [4, 4],
                    },
                    ticks: {
                        stepSize: 20,
                        font: {
                            size: 12,
                            family: "Poppins",
                        },
                        color: "#9ca3af",
                        callback: function (value) {
                            return value + "%";
                        },
                    },
                },
            },
            interaction: {
                mode: "index",
                intersect: false,
            },
            elements: {
                point: {
                    radius: function (context) {
                        return context.dataIndex === 6 ? 5 : 0;
                    },
                },
            },
        },
    });
}

// Fullscreen functionality
function initializeFullscreenButton() {
    const fullscreenBtn = document.getElementById("fullscreenBtn");
    if (fullscreenBtn) {
        fullscreenBtn.addEventListener("click", () => {
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
    }
}

// Mobile sidebar functionality
function initializeMobileSidebar() {
    const mobileSidebar = document.getElementById("mobileSidebar");
    const hamburgerBtns = document.querySelectorAll(".hamburgerBtn");
    const closeBtn = document.getElementById("closeBtn");

    if (!mobileSidebar || hamburgerBtns.length === 0 || !closeBtn) {
        console.warn("Mobile sidebar elements not found");
        return;
    }

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
}

// Initialize all dashboard functionality when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
    // Check if Chart.js is available
    if (typeof Chart !== "undefined") {
        initializeDashboardCharts();
    } else {
        console.warn(
            "Chart.js is not loaded. Dashboard charts will not be initialized."
        );
    }

    initializeFullscreenButton();
    initializeMobileSidebar();
});

// Export functions for potential external use
window.Dashboard = {
    initializeCharts: initializeDashboardCharts,
    initializeFullscreen: initializeFullscreenButton,
    initializeMobileSidebar: initializeMobileSidebar,
};

function openRejectModal() {
    const modal = document.getElementById("rejectModal");
    const rejectLink = document.getElementById("rejectLink");
    const viewDetailsLink = document.getElementById("viewDetailsLink");

    if (modal) {
        modal.classList.remove("hidden");
    }

    if (rejectLink) {
        rejectLink.classList.add("text-pink-600");
        rejectLink.classList.remove("text-gray-700");
    }

    if (viewDetailsLink) {
        viewDetailsLink.classList.remove("text-pink-600");
        viewDetailsLink.classList.add("text-gray-700");
    }
}

function closeRejectModal() {
    const modal = document.getElementById("rejectModal");
    const rejectLink = document.getElementById("rejectLink");
    const rejectForm = document.getElementById("rejectForm");

    if (modal) {
        modal.classList.add("hidden");
    }

    if (rejectForm) {
        rejectForm.reset();
    }

    if (rejectLink) {
        rejectLink.classList.remove("text-pink-600");
        rejectLink.classList.add("text-gray-700");
    }
}

// Optional: close modal when clicking outside
window.addEventListener("click", function (e) {
    const modal = document.getElementById("rejectModal");
    if (e.target === modal) {
        closeRejectModal();
    }
});

//Missing Document Model
function openMissingDocument(e) {
    if (e) {
        e.preventDefault(); // stop link jump
    }
    const link = document.getElementById("missingDocLink");
    const modal = document.getElementById("missingDocModal");

    if (modal) {
        modal.classList.remove("hidden");
    }
    if (link) {
        link.classList.replace("text-gray-700", "text-pink-600");
    }
}

function closeMissingDocument() {
    const link = document.getElementById("missingDocLink");
    const modal = document.getElementById("missingDocModal");
    const form = document.getElementById("missingDocForm");

    if (modal) {
        modal.classList.add("hidden");
    }
    if (link) {
        link.classList.replace("text-pink-600", "text-gray-700");
    }
    if (form) {
        const textarea = form.querySelector('textarea[name="message"]');
        if (textarea) {
            textarea.value = "";
        }
    }
}

// close when clicking outside the dialog
window.addEventListener("click", (e) => {
    const modal = document.getElementById("missingDocModal");
    if (e.target === modal) closeMissingDocument();
});

function toggleDropdown(btn) {
    if (!btn) {
        return;
    }
    const dropdown = btn.parentElement.querySelector("div");
    if (!dropdown) {
        return;
    }
    dropdown.classList.toggle("hidden");

    document.querySelectorAll("td .absolute").forEach((el) => {
        if (el !== dropdown) el.classList.add("hidden");
    });
}

// Close dropdowns when clicking outside
window.addEventListener("click", function (e) {
    if (!e.target.closest("td")) {
        document
            .querySelectorAll("td .absolute")
            .forEach((el) => el.classList.add("hidden"));
    }
});

const fullscreenBtn = document.getElementById("fullscreenBtn");
if (fullscreenBtn) {
    fullscreenBtn.addEventListener("click", () => {
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
    const activeBtn = document.querySelector(`[onclick="showTab('${tabId}')"]`);
    activeBtn.classList.add("bg-[#DB69A2]", "text-white");
    activeBtn.classList.remove("bg-[#F3E8EF]", "text-[#91848C]");
}
