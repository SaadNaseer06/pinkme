@extends('admin.layouts.admin')

@section('title', 'Sponsors')

@section('content')
    <div class="flex-1 flex flex-col min-h-screen">
        <main class="flex-1 pb-8">
            <div class="max-w-8xl mx-auto space-y-8">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-semibold text-[#213430] app-main">Sponsors</h1>
                    <a href="{{ route('admin.sponsors.create') }}"
                        class="inline-flex items-center px-4 py-2 rounded-md bg-[#9E2469] text-white text-sm font-medium hover:bg-[#B52D75] transition">
                        Add Sponsor
                    </a>
                </div>
                <div class="mt-6 bg-[#F3E8EF] rounded-lg p-6">
                            <div x-data="{ showFilters: false }" class="mb-4 ml-3">
                                <!-- Header -->
                                <div class="flex justify-between items-center">
                                    <h2 class="text-xl font-semibold text-[#213430] app-main">
                                        Sponsors Lists
                                    </h2>
                                    <div class="flex items-center gap-3">
                                        <!-- Mobile Filters Button -->
                                        {{-- <button @click="showFilters = !showFilters"
                                            class="flex items-center border border-[#91848C] text-[#91848C] text-sm px-3 py-1.5 rounded-md app-h md:hidden">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 019 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                                            </svg>
                                        </button> --}}
                                        <!-- Desktop Filters Button -->
                                        {{-- <button @click="showFilters = !showFilters"
                                            class="hidden md:flex items-center border border-[#91848C] text-[#91848C] text-sm px-3 py-1.5 rounded-md app-h">
                                            Filters
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 ml-1" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 019 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                                            </svg>
                                        </button> --}}
                                        <!-- Mobile Export Button -->
                                        {{-- <button class="bg-[#9E2469] px-4 py-2 rounded-md text-sm md:hidden">
                                            <img src="{{ asset('public/images/export.svg') }}" alt="" class="w-4 h-4">
                                        </button> --}}
                                        <!-- Desktop Export Button -->
                                        {{-- <button
                                            class="hidden md:flex items-center bg-[#9E2469] text-white text-sm px-4 py-1.5 rounded-md app-h">
                                            Export
                                            <img src="{{ asset('public/images/export.svg') }}" alt="" class="w-3 h-3 ml-1">
                                        </button> --}}
                                    </div>
                                </div>
                                <!-- Filter Dropdowns -->
                                <div x-show="showFilters" class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 max-w-8xl">
                                    <div class="relative w-full">
                                        <select name="email_filter"
                                            class="bg-transparent border border-[#91848C] text-[#91848C] text-sm px-4 py-2 pr-8 rounded-md w-full appearance-none focus:outline-none">
                                            <option value="">Search By Email</option>
                                            @if (isset($sponsors))
                                                @foreach ($sponsors as $sponsor)
                                                    <option value="{{ $sponsor->email }}">{{ $sponsor->email }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-[#91848C]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="relative w-full">
                                        <select name="sponsor_type"
                                            class="bg-transparent border border-[#91848C] text-[#91848C] text-sm px-4 py-2 rounded-md w-full appearance-none focus:outline-none">
                                            <option value="">Filter By Sponsor Type</option>
                                            @if (isset($sponsorTypes))
                                                @foreach ($sponsorTypes as $type)
                                                    <option value="{{ $type }}">{{ $type }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-[#91848C]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="relative w-full">
                                        <select name="status_filter"
                                            class="bg-transparent border border-[#91848C] text-[#91848C] text-sm px-4 py-2 pr-8 rounded-md w-full appearance-none focus:outline-none">
                                            <option value="">Filter By Status</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-[#91848C]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-container">
                                <table class="min-w-full text-sm text-left mt-6">
                                    <thead>
                                        <tr class="border-t border-[#e0cfd8]">
                                            <!--<th class="p-2">-->
                                            <!--    <input type="checkbox"-->
                                            <!--        class="accent-[#9E2469] w-4 h-4 border border-[#91848C] rounded appearance-none checked:appearance-auto focus:ring-0" />-->
                                            <!--</th>-->
                                            <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">
                                                All Sponsors
                                            </th>
                                            <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">
                                                Company / Individual
                                            </th>
                                            <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">
                                                Funds Given
                                            </th>
                                            <th class="p-2 text-lg font-medium text-[#91848C] font-normal app-h">
                                                Email
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
                                        @forelse($sponsors ?? [] as $sponsor)
                                            <tr
                                                class="border-t border-[#e0cfd8] hover:bg-[#F6EDF5] transition-colors duration-200">
                                                <!--<td class="p-2">-->
                                                <!--    <input type="checkbox" name="selected_sponsors[]"-->
                                                <!--        value="{{ $sponsor->id }}"-->
                                                <!--        class="accent-[#9E2469] w-4 h-4 border border-[#91848C] rounded appearance-none checked:appearance-auto focus:ring-0" />-->
                                                <!--</td>-->
                                                <td class="p-2">
                                                    <div class="flex items-center gap-3">
                                                        @php
                                                            $profileImage = $sponsor->profile_image;
                                                            $profileImageUrl =
                                                                $profileImage &&
                                                                filter_var($profileImage, FILTER_VALIDATE_URL)
                                                                    ? $profileImage
                                                                    : ($sponsor->user
                                                                        ? $sponsor->user->avatar_url
                                                                        : asset('public/images/profile.png'));
                                                        @endphp
                                                        <img src="{{ $profileImageUrl }}" alt="{{ $sponsor->name }}"
                                                            class="w-8 h-8 rounded-full object-cover" />
                                                        <span
                                                            class="text-[#91848C] text-[16px] font-light app-text">{{ $sponsor->name }}</span>
                                                    </div>
                                                </td>
                                                <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                                    {{ $sponsor->company_name ?? 'Individual' }}
                                                </td>
                                                <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                                    ${{ number_format($sponsor->total_funds, 2) }}
                                                </td>
                                                <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                                    {{ $sponsor->email }}
                                                </td>
                                                <td class="p-2 align-middle">
                                                    <span class="inline-flex items-center gap-1 text-[#8E7C93] text-sm">
                                                        <span class="w-2 h-2 rounded-full bg-[#20B354]"></span>
                                                        Active
                                                    </span>
                                                </td>
                                                <td class="p-2 relative">
                                                    <button onclick="toggleDropdown(this)"
                                                        class="text-[#213430] p-2 rounded-md focus:outline-none">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                        </svg>
                                                    </button>
                                                    <div
                                                        class="absolute right-[28px] top-10 w-[250px] max-w-none bg-[#F6EDF5] rounded-lg shadow-lg py-2 z-20 hidden">
                                                        <a href="{{ route('admin.sponsors.show', $sponsor->id) }}"
                                                            class="flex items-center px-4 py-2 text-[#91848C] hover:bg-pink-100 text-sm">
                                                            <i class="fas fa-eye mr-2"></i> View Profile
                                                        </a>
                                                        <a href="{{ route('admin.sponsors.edit', $sponsor->id) }}"
                                                            class="flex items-center px-4 py-2 text-[#91848C] hover:bg-pink-100 text-sm gap-2">
                                                            <i class="fa-solid fa-pen"></i> Edit Sponsors Details
                                                        </a>
                                                        <form action="{{ route('admin.sponsors.destroy', $sponsor->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Remove this sponsor? If deletion fails due to dependencies, the sponsor will be deactivated.');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="w-full text-left flex items-center px-4 py-2 gap-2 text-[#91848C] text-sm transition-colors hover:bg-pink-100">
                                                                <i class="fa-solid fa-trash"></i> Remove Sponsors
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr class="border-t border-[#e0cfd8]">
                                                <td colspan="7" class="p-8 text-center text-[#91848C] app-text">
                                                    No sponsors found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                            <div class="flex justify-between items-center mt-6">
                                <div class="flex justify-start">
                                    <h1 class="text-md text-[#91848C] font-light app-text">
                                        @if (isset($sponsors) && $sponsors->count() > 0)
                                            Showing {{ $sponsors->firstItem() ?? 0 }} to {{ $sponsors->lastItem() ?? 0 }}
                                            of {{ $sponsors->total() }} Sponsors
                                        @else
                                            Showing 0 to 0 of 0 Sponsors
                                        @endif
                                    </h1>
                                </div>
                                <div class="flex justify-end space-x-1">
                                    @if (isset($sponsors) && method_exists($sponsors, 'onFirstPage'))
                                        @if ($sponsors->onFirstPage())
                                            <button disabled
                                                class="px-3 py-1 rounded-md bg-transparent text-[#91848C] border border-[#B9B1B6] opacity-50 cursor-not-allowed">
                                                &lt;
                                            </button>
                                        @else
                                            <a href="{{ $sponsors->previousPageUrl() }}"
                                                class="px-3 py-1 rounded-md bg-transparent text-[#91848C] border border-[#B9B1B6] hover:bg-[#F6EDF5] transition-colors duration-200">
                                                &lt;
                                            </a>
                                        @endif
                                        @foreach ($sponsors->getUrlRange(1, $sponsors->lastPage()) as $page => $url)
                                            <a href="{{ $url }}"
                                                class="px-4 py-1 rounded-md {{ $page == $sponsors->currentPage() ? 'bg-[#9E2469] text-white' : 'bg-transparent text-[#91848C] border border-[#B9B1B6] hover:bg-[#F6EDF5]' }} transition-colors duration-200">
                                                {{ $page }}
                                            </a>
                                        @endforeach
                                        @if ($sponsors->hasMorePages())
                                            <a href="{{ $sponsors->nextPageUrl() }}"
                                                class="px-3 py-1 rounded-md bg-transparent text-[#91848C] border border-[#B9B1B6] hover:bg-[#F6EDF5] transition-colors duration-200">
                                                &gt;
                                            </a>
                                        @else
                                            <button disabled
                                                class="px-3 py-1 rounded-md bg-transparent text-[#91848C] border border-[#B9B1B6] opacity-50 cursor-not-allowed">
                                                &gt;
                                            </button>
                                        @endif
                                    @else
                                        <button disabled
                                            class="px-3 py-1 rounded-md bg-transparent text-[#91848C] border border-[#B9B1B6] opacity-50 cursor-not-allowed">
                                            &lt;
                                        </button>
                                        <button class="px-4 py-1 rounded-md bg-[#9E2469] text-white">
                                            1
                                        </button>
                                        <button disabled
                                            class="px-3 py-1 rounded-md bg-transparent text-[#91848C] border border-[#B9B1B6] opacity-50 cursor-not-allowed">
                                            &gt;
                                        </button>
                                    @endif
                                </div>
                            </div>
                </div>
            </div>
        </main>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleDropdown(btn) {
            const dropdown = btn.parentElement.querySelector("div");
            if (dropdown) {
                dropdown.classList.toggle("hidden");
            }

            document.querySelectorAll("td .absolute").forEach((el) => {
                if (el !== dropdown) el.classList.add("hidden");
            });
        }

        window.addEventListener("click", function(e) {
            if (!e.target.closest("td")) {
                document.querySelectorAll("td .absolute").forEach((el) => el.classList.add("hidden"));
            }
        });
    </script>
@endpush
