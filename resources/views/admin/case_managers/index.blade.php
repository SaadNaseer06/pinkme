@extends('admin.layouts.admin')

@section('title', 'Case Managers')

@section('content')
    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <!---Main -->
        <main class="flex-1">
            <div class="max-w-8xl mx-auto">
                <!-- Status Cards -->
                @include('admin.partials.cards')

                <!-- Charts Section -->
                <div class="mt-6 bg-[#F3E8EF] rounded-lg p-6">
                    <div class="mb-4 ml-3">
                        <!-- Header -->
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-semibold text-[#213430] app-main">All Case Managers List</h2>
                            <a href="{{ route('admin.case-managers.create') }}"
                                class="bg-[#db69a2] text-white px-4 py-2 rounded-md text-sm hover:bg-[#C63A85] transition">
                                Add Case Manager
                            </a>
                        </div>

                        {{-- @if (session('success'))
                            <div class="mt-4 mb-4 rounded-md border border-green-300 bg-green-50 px-4 py-3 text-green-800">
                                {{ session('success') }}
                            </div>
                        @endif --}}
                    </div>

                    <div class="table-container">
                        <table class="min-w-full text-sm text-left mt-6">
                            <thead>
                                <tr class="border-t border-[#e0cfd8]">
                                    <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">
                                        #
                                    </th>
                                    <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">
                                        Name
                                    </th>
                                    <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">
                                        Email
                                    </th>
                                    <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">
                                        Phone
                                    </th>
                                    <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">
                                        Status
                                    </th>
                                    <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @forelse ($caseManagers as $cm)
                                    <tr class="border-t border-[#e0cfd8]">
                                        <td class="p-2">
                                            <span class="text-[#91848C] text-[16px] font-light app-text">
                                                {{ $loop->index + 1 }}
                                            </span>
                                        </td>

                                        <td class="p-2">
                                            <div class="flex items-center gap-3">
                                                <span class="text-[#91848C] text-[16px] font-light app-text">
                                                    {{ $cm->profile->full_name ?? '—' }}
                                                </span>
                                            </div>
                                        </td>

                                        <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                            {{ $cm->email }}
                                        </td>

                                        <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                            {{ $cm->profile->phone ?? '—' }}
                                        </td>

                                        <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                            @php $active = ($cm->profile->status ?? 0) == 1; @endphp
                                            <span
                                                class="inline-flex items-center gap-1 text-sm {{ $active ? 'text-green-500' : 'text-red-500' }}">
                                                <span
                                                    class="w-2 h-2 rounded-full {{ $active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                                {{ $active ? 'Active' : 'Inactive' }}
                                            </span>
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
                                                class="absolute right-[28px] top-10 w-[200px] max-w-none bg-[#F6EDF5] rounded-lg shadow-lg py-2 z-20 hidden">
                                                <a href="{{ route('admin.case-managers.edit', $cm) }}"
                                                    class="flex items-center px-4 py-2 text-[#91848C] hover:bg-pink-100 text-sm gap-2">
                                                    <i class="fa-solid fa-pen"></i> Edit Case Manager
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="border-t border-[#e0cfd8]">
                                        <td colspan="6"
                                            class="p-6 text-center text-[#91848C] text-[16px] font-light app-text">
                                            No case managers found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-between items-center">
                        <div class="mt-3 flex justify-start">
                            <h1 class="text-md text-[#91848C] font-light app-text">
                                Showing {{ $caseManagers->firstItem() ?? 0 }} to {{ $caseManagers->lastItem() ?? 0 }} of
                                {{ $caseManagers->total() }} Case Managers
                            </h1>
                        </div>
                        <div class="mt-3 flex justify-end space-x-1">
                            {{ $caseManagers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Dropdown toggle function
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
        });
    </script>
@endsection
