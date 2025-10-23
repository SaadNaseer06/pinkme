@extends('admin.layouts.admin')

@section('title', 'Reviewers')

@section('content')

    @php
        $statusFilter = $status ?? request('status', 'active');
        if (! in_array($statusFilter, ['active', 'inactive', 'all'], true)) {
            $statusFilter = 'active';
        }

        $reviewerIdFilter = $reviewerId ?? request('reviewer_id', '');
        $emailFilter = $email ?? request('email', '');
        $searchFilter = $searchQuery ?? request('q', '');
        $hasReviewersFilters = ($statusFilter !== 'active') || $reviewerIdFilter !== '' || $emailFilter !== '' || $searchFilter !== '';
    @endphp

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <!---Main -->
        <main class="flex-1">

            <div class="max-w-8xl mx-auto">
                <!-- Status Cards -->
                @include('admin.partials.cards')

                <!-- Charts Section -->
                <div class="mt-6 bg-[#F3E8EF] rounded-lg p-6">
                    <div x-data="{ showFilters: {{ json_encode($hasReviewersFilters) }} }" class="mb-4 ml-3">
                        <!-- Header -->
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-semibold text-[#213430] app-main">All Reviewers List</h2>
                            <div class="flex items-center gap-3">
                                <!-- Filters Toggle Button -->
                                <button @click="showFilters = !showFilters"
                                    class="flex items-center border border-[#91848C] text-[#91848C] text-sm px-3 py-1.5 rounded-md app-h  md:hidden flex">

                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 " fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 019 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                                    </svg>
                                </button>
                                <button @click="showFilters = !showFilters"
                                    class="flex items-center border border-[#91848C] text-[#91848C] text-sm px-3 py-1.5 rounded-md app-h  md:flex hidden">
                                    Filters
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 ml-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 019 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                                    </svg>
                                </button>
                                <button class="bg-[#db69a2] px-4 py-2 rounded-md text-sm md:hidden flex">
                                    <img src="{{ asset('public/images/export.svg') }}" alt="" class="w-4 h-4">
                                </button>
                                <!-- Export Button -->
                                {{-- <button
                                    class="flex items-center bg-[#db69a2] text-white text-sm px-4 py-1.5 rounded-md app-h md:flex hidden">
                                    Export
                                    <img src="{{ asset('public/images/export.svg') }}" alt="" class="w-3 h-3 ml-1">
                                </button> --}}
                            </div>
                        </div>

                        <!-- Filter Dropdowns -->
                        <div x-show="showFilters" x-cloak class="mt-4">
                            <form method="GET" action="{{ route('admin.reviewers') }}"
                                class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-8xl">
                                {{-- <div class="relative w-full">
                                    <select name="status"
                                        class="bg-transparent border border-[#91848C] text-[#91848C] text-sm px-4 py-2 pr-8 rounded-md w-full appearance-none focus:outline-none">
                                        <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>
                                            Active Reviewers</option>
                                        <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>
                                            Inactive Reviewers</option>
                                        <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All Statuses
                                        </option>
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-[#91848C]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div> --}}

                                <div class="relative w-full">
                                    <input type="text" name="reviewer_id" value="{{ $reviewerIdFilter }}"
                                        placeholder="Search by reviewer ID"
                                        class="bg-transparent border border-[#91848C] text-[#91848C] text-sm px-4 py-2 rounded-md w-full focus:outline-none placeholder-[#B1A4AD]" />
                                </div>

                                <div class="relative w-full">
                                    <input type="text" name="email" value="{{ $emailFilter }}"
                                        placeholder="Search by reviewer's email"
                                        class="bg-transparent border border-[#91848C] text-[#91848C] text-sm px-4 py-2 rounded-md w-full focus:outline-none placeholder-[#B1A4AD]" />
                                </div>

                                <div class="relative w-full">
                                    <input type="text" name="q" value="{{ $searchFilter }}"
                                        placeholder="Search by name, username or phone"
                                        class="bg-transparent border border-[#91848C] text-[#91848C] text-sm px-4 py-2 rounded-md w-full focus:outline-none placeholder-[#B1A4AD]" />
                                </div>

                                <div class="flex items-center gap-3 md:col-span-4">
                                    <button type="submit"
                                        class="px-4 py-2 bg-[#DB69A2] text-white rounded-md text-sm font-medium app-text hover:bg-[#c95791] transition">
                                        Apply Filters
                                    </button>
                                    @if ($hasReviewersFilters)
                                        <a href="{{ route('admin.reviewers') }}"
                                            class="px-4 py-2 border border-[#DCCFD8] text-[#91848C] rounded-md text-sm app-text hover:bg-[#F9EFF5] transition">
                                            Reset
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-container">
                        <table class="min-w-full text-sm text-left mt-6">
                            <thead>
                                <tr class="border-t border-[#e0cfd8]">
                                    <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">
                                        #
                                    </th>
                                    {{-- <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">
                                        Reviewer Name
                                    </th> --}}
                                    <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">
                                        Reviewers ID
                                    </th>
                                    {{-- <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">
                                        Gender
                                    </th> --}}
                                    <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">Email</th>
                                    <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">Contact</th>
                                    <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">Assigned
                                        Applications</th>
                                    <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @foreach ($reviewers as $reviewer)
                                    <tr class="border-t border-[#e0cfd8]">
                                        <td class="p-2">
                                            <span
                                                class="text-[#91848C] text-[16px] font-light app-text">{{ $loop->index + 1 }}</span>
                                        </td>

                                        {{-- <td class="p-2">
                                            <div class="flex items-center gap-3">
                                                <span
                                                    class="text-[#91848C] text-[16px] font-light app-text">{{ $reviewer->username ?? 'Unknown' }}</span>
                                            </div>
                                        </td> --}}

                                        <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                            {{ $reviewer->reviewer_id }}
                                        </td>

                                        {{-- <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                            {{ $reviewer->gender }}
                                        </td> --}}

                                        <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                            {{ $reviewer->email }}
                                        </td>

                                        <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                            {{ $reviewer->phone }}
                                        </td>

                                        <td
                                            class="p-2 align-middle text-center text-[#91848C] text-[16px] font-light app-text">
                                            {{ $reviewer->applications_count }}
                                        </td>

                                        <td class="p-2 relative">
                                            <button onclick="toggleDropdown(this)"
                                                class="text-[#213430] p-2 rounded-md focus:outline-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                </svg>
                                            </button>
                                            <div
                                                class="absolute right-[28px] top-10 w-[250px] max-w-none bg-[#F6EDF5] rounded-lg shadow-lg py-2 z-20 hidden">
                                                <a href="{{ route('admin.reviewers.show', $reviewer->id) }}"
                                                    class="flex items-center px-4 py-2 text-[#91848C] hover:bg-pink-100 text-sm ">
                                                    <i class="fas fa-eye mr-2"></i> View Profile
                                                </a>
                                                <a href="{{ route('admin.reviewers.edit', $reviewer->id) }}"
                                                    class="flex items-center px-4 py-2 text-[#91848C] hover:bg-pink-100 text-sm gap-2">
                                                    <i class="fa-solid fa-pen"></i> Edit Reviewer Details
                                                </a>
                                                {{-- <a href="#"
                                                    class="flex items-center px-4 py-2 text-[#91848C] hover:bg-pink-100 text-sm gap-2">
                                                    <img src="{{ asset('public/images/assign.svg') }}" alt=""> Assign Applications
                                                </a> --}}
                                                <a href="javascript:void(0);"
                                                    onclick="viewAssignedApplications({{ $reviewer->id }})"
                                                    class="flex items-center px-4 py-2 text-[#91848C] hover:bg-pink-100 text-sm gap-2">
                                                    <img src="{{ asset('public/images/assign.svg') }}" alt="">View Assign Applications
                                                </a>
                                                {{-- <a href="javascript:void(0);"
                                                    onclick="openRejectModal({{ $reviewer->id }})"
                                                    class="flex items-center px-4 py-2 gap-2 text-[#91848C] text-sm transition-colors">
                                                    <i class="fa-solid fa-trash"></i> Inactivate Reviewer
                                                </a> --}}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-between items-center">
                        <div class="mt-3 flex justify-start">
                            <h1 class="text-md text-[#91848C] font-light app-text">Showing {{ $reviewers->firstItem() }}
                                to {{ $reviewers->lastItem() }} of {{ $reviewers->total() }} Reviewers</h1>
                        </div>
                        <div class="mt-3 flex justify-end space-x-1">
                            {{ $reviewers->links() }} <!-- This will generate the pagination links -->
                        </div>
                    </div>
                </div>
        </main>

    </div>
    <!-- Reject Modal (Hidden by Default) -->
    <div id="rejectModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
        <div class="bg-[#F9EEF6] rounded-lg shadow-lg p-6 w-full max-w-[20rem] md:max-w-md mx-auto text-left">
            <!-- Modal Title -->
            <h2 class="text-lg font-semibold text-[#1F2937] mb-4 text-center">Delete Application</h2>

            <!-- Description -->
            <p class="text-md text-center text-black mb-4">
                Are you sure you want to remove this reviewer?
            </p>

            <!-- Success Message (Hidden Initially) -->
            <div id="successMessage" class="hidden mt-4"></div>

            <!-- Buttons -->
            <div class="flex justify-center items-center gap-3 pt-2">
                <button onclick="removeReviewer()"
                    class="px-6 py-2 bg-[#DB69A2] hover:bg-[#FE6EB6] text-white rounded-md text-sm font-semibold transition">
                    Yes
                </button>
                <button onclick="closeRejectModal()"
                    class="px-6 py-2 border border-[#D6C6CE] text-[#8B7E88] rounded-md text-sm font-semibold hover:bg-[#DCCFD8] transition">
                    No
                </button>
            </div>
        </div>
    </div>

    <!-- View Assigned Applications Modal -->
    <div id="applicationsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-[#F9EEF6] rounded-xl shadow-lg w-full max-w-2xl p-6 max-h-[80vh] overflow-y-auto">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-[#213430]">Assigned Applications</h2>
                <button onclick="closeApplicationsModal()" class="text-[#91848C] hover:text-[#DB69A2]">
                    ✕
                </button>
            </div>

            <div id="applicationsContent">
                <!-- Dynamic content will be injected here via JS -->
                <p class="text-sm text-[#91848C]">Loading applications...</p>
            </div>
        </div>
    </div>

    <script>
        // Function to view unassigned applications for a specific reviewer
        function viewUnassignedApplications() {
            const modal = document.getElementById("unassignedApplicationsModal");
            const content = document.getElementById("unassignedApplicationsContent");

            modal.classList.remove("hidden");
            content.innerHTML = `<p class="text-sm text-[#91848C]">Loading unassigned applications...</p>`;

            // Send a request to fetch the unassigned applications
            fetch(`/admin/reviewers/unassigned-applications`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        content.innerHTML = `<p class="text-[#91848C] text-sm">No unassigned applications found.</p>`;
                        return;
                    }

                    let html = `
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="text-[#91848C] border-b border-[#E4D2DB]">
                            <th class="py-2">Application ID</th>
                            <th class="py-2">Assigned Date</th>
                            <th class="py-2">Status</th>
                            <th class="py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

                    data.forEach(app => {
                        html += `
                    <tr class="border-t border-[#E4D2DB] text-[#213430]">
                        <td class="py-2">${app.application_id}</td>
                        <td class="py-2">${app.created_at ? app.created_at : 'N/A'}</td>
                        <td class="py-2">
                            <span class="inline-flex items-center gap-1 text-sm ${app.status === 'Approved' ? 'text-green-500' : 'text-red-500'}">
                                <span class="w-2 h-2 rounded-full ${app.status === 'Approved' ? 'bg-green-500' : 'bg-red-500'}"></span>
                                ${app.status.charAt(0).toUpperCase() + app.status.slice(1)}
                            </span>
                        </td>
                        <td class="py-2">
                            <button class="assignBtn px-4 py-2 bg-[#DB69A2] text-white text-sm rounded-md"
                                onclick="assignApplicationToReviewer(${app.id})">
                                Assign
                            </button>
                        </td>
                    </tr>
                `;
                    });

                    html += "</tbody></table>";
                    content.innerHTML = html;
                })
                .catch(error => {
                    console.error("Error fetching applications:", error);
                    content.innerHTML = `<p class="text-red-500">Failed to load applications.</p>`;
                });
        }

        // Function to close the new Unassigned Applications Modal
        function closeUnassignedApplicationsModal() {
            document.getElementById("unassignedApplicationsModal").classList.add("hidden");
        }

        // Function to assign application to reviewer
        function assignApplicationToReviewer(applicationId) {
            const reviewerId = document.querySelector('#reviewerId')
            .value; // Assuming the reviewer ID is stored somewhere (like a hidden input field)

            fetch(`/admin/applications/${applicationId}/assign-reviewer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        reviewer_id: reviewerId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Application assigned successfully');
                        // Close the modal and reload the page (or update the table)
                        closeUnassignedApplicationsModal();
                    } else {
                        alert('Failed to assign the application');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Ensure CSRF token is defined at the top before any function is called
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Function to close the applications modal
        function closeApplicationsModal() {
            document.getElementById("applicationsModal").classList.add("hidden");
        }

        // Function to view assigned applications for a specific reviewer
        function viewAssignedApplications(reviewerId) {
            const modal = document.getElementById("applicationsModal");
            const content = document.getElementById("applicationsContent");

            modal.classList.remove("hidden");
            content.innerHTML = `<p class="text-sm text-[#91848C]">Loading applications...</p>`;

            // Send a request to fetch the applications for the given reviewer ID
            fetch(`/admin/reviewers/${reviewerId}/applications`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        content.innerHTML = `<p class="text-[#91848C] text-sm">No assigned applications found.</p>`;
                        return;
                    }

                    let html = `
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="text-[#91848C] border-b border-[#E4D2DB]">
                                <th class="py-2">Application ID</th>
                                <th class="py-2">Assigned Date</th>
                                <th class="py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                    data.forEach(app => {
                        html += `
                        <tr class="border-t border-[#E4D2DB] text-[#213430]">
                            <td class="py-2">${app.application_id}</td>
                            <td class="py-2">${app.assigned_date ?? 'N/A'}</td>
                            <td class="py-2">
                                <span class="inline-flex items-center gap-1 text-sm ${app.status === 'Approved' ? 'text-green-500' : 'text-red-500'}">
                                    <span class="w-2 h-2 rounded-full ${app.status === 'Approved' ? 'bg-green-500' : 'bg-red-500'}"></span>
                                    ${app.status.charAt(0).toUpperCase() + app.status.slice(1)}
                                </span>
                            </td>
                        </tr>
                    `;
                    });

                    html += "</tbody></table>";
                    content.innerHTML = html;
                })
                .catch(error => {
                    console.error("Error fetching applications:", error);
                    content.innerHTML = `<p class="text-red-500">Failed to load applications.</p>`;
                });
        }

        // Dropdown toggle function (optional if you use dropdowns elsewhere)
        function toggleDropdown(btn) {
            const dropdown = btn.parentElement.querySelector("div");
            dropdown.classList.toggle("hidden");

            // Hide other dropdowns
            document.querySelectorAll("td .absolute").forEach((el) => {
                if (el !== dropdown) el.classList.add("hidden");
            });
        }

        // Close dropdowns when clicking outside
        window.addEventListener("click", function(e) {
            if (!e.target.closest("td")) {
                document.querySelectorAll("td .absolute").forEach((el) => el.classList.add("hidden"));
            }

            // Close modals when clicking outside
            const rejectModal = document.getElementById("rejectModal");
            if (e.target === rejectModal) {
                closeRejectModal();
            }
        });

        // Function to open the reject modal and pass the reviewer ID
        function openRejectModal(reviewerId) {
            const modal = document.getElementById("rejectModal");
            modal.setAttribute("data-reviewer-id", reviewerId);
            modal.classList.remove("hidden");
        }

        // Close the reject modal
        function closeRejectModal() {
            const modal = document.getElementById("rejectModal");
            modal.classList.add("hidden");
        }

        // Function to remove reviewer (called on 'Yes' button click)
        function removeReviewer() {
            const modal = document.getElementById("rejectModal");
            const reviewerId = modal.getAttribute("data-reviewer-id");
            const successMessage = document.getElementById("successMessage");

            // Show loading state
            successMessage.innerHTML = `<p class="text-blue-600 font-semibold text-center">Removing reviewer...</p>`;
            successMessage.classList.remove('hidden');

            fetch(`/admin/reviewers/${reviewerId}/remove`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        reviewerId: reviewerId
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        successMessage.innerHTML =
                            `<p class="text-green-600 font-semibold text-center">${data.message}</p>`;

                        // Optionally reload the page or remove the row from the table
                        setTimeout(() => {
                            location.reload(); // or remove the specific table row
                        }, 2000);
                    } else {
                        successMessage.innerHTML =
                            `<p class="text-red-500 font-semibold text-center">${data.message}</p>`;
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    successMessage.innerHTML =
                        `<p class="text-red-500 font-semibold text-center">Failed to remove reviewer. Please try again.</p>`;
                });
        }

        // Function to open the assign modal
        function openAssignModal() {
            document.getElementById('assignModal').classList.remove('hidden');
        }

        // Close the assign modal
        function closeAssignModal() {
            document.getElementById('assignModal').classList.add('hidden');
        }
    </script>
@endsection

