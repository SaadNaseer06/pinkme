@extends('patient.layouts.app')

@section('title', 'My Application')

@section('content')

    {{-- @if (session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
            class="fixed top-6 right-6 z-50 bg-green-600 text-white px-6 py-3 rounded-md shadow-md transition-all">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 px-4 py-3 text-red-800 bg-red-100 border border-red-200 rounded-md shadow-sm">
            {{ session('error') }}
        </div>
    @endif --}}

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            @if (session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true,
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: "{{ session('error') }}",
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true,
                });
            @endif
        </script>
    @endpush



    <!-- Dashboard Content -->
    <main class="flex-1">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 box-gap">
            <!--- Box 1 -->
            <div class="bg-[#F3E8EF] rounded-lg p-4  flex items-center space-x-4 box-padding">
                <img src="{{ asset('public/images/app-icon-1.svg') }}" alt="Application Icon" class="w-24 h-24 app-img" />
                <div class="space-y-2">
                    <h1 class="text-lg font-semibold text-gray-800 app-text ">
                        All Applications
                    </h1>
                    <h2 class="text-md text-gray-400 app-text">{{ $totalApplications }}</h2>
                </div>
            </div>
            <!--- Box 2 -->
            <div class="bg-[#F3E8EF] rounded-lg p-4  flex items-center space-x-4 box-padding">
                <img src="{{ asset('public/images/app-icon-2.svg') }}" alt="Application Icon" class="w-24 h-24 app-img" />
                <div class="space-y-2">
                    <h1 class="text-lg font-semibold text-gray-800 app-text">
                        Pending Applications
                    </h1>
                    <h2 class="text-md text-gray-400 app-text">{{ $pendingApplications }}</h2>
                </div>
            </div>
            <!--- Box 3 -->
            <div class="bg-[#F3E8EF] rounded-lg p-4  flex items-center space-x-4 box-padding">
                <img src="{{ asset('public/images/app-icon-3.svg') }}" alt="Application Icon" class="w-24 h-24 app-img" />
                <div class="space-y-2">
                    <h1 class="text-lg font-semibold text-gray-800 app-text">
                        Approved Applications
                    </h1>
                    <h2 class="text-md text-gray-400 app-text">{{ $approvedApplications }}</h2>
                </div>
            </div>
            <!--- Box 4 -->
            <div class="bg-[#F3E8EF] rounded-lg p-4  flex items-center space-x-4 box-padding">
                <img src="{{ asset('public/images/app-icon-4.svg') }}" alt="Application Icon" class="w-24 h-24 app-img" />
                <div class="space-y-2">
                    <h1 class="text-lg font-semibold text-gray-800 app-text">
                        Rejected Applications
                    </h1>
                    <h2 class="text-md text-gray-400 app-text">{{ $rejectedApplications }}</h2>
                </div>
            </div>
        </div>

        <div class="mt-6 bg-[#F3E8EF] rounded-lg p-6">
            <div class="flex justify-between items-center mb-4 ml-3">
                <h2 class="text-xl font-semibold text-[#213430] app-main">
                    All Applications List
                </h2>
                <div class="flex space-x-4">
                    <div class="relative w-[200px] md:flex hidden">
                        <select
                            class="w-full appearance-none rounded-md px-3 py-2 pr-10 text-sm text-[#91848C] bg-transparent border border-[#91848C] focus:outline-none app-text">
                            <option>Last Week</option>
                            <option>Last Month</option>
                        </select>
                        <!-- Custom Arrow -->
                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[#91848C]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    <button
                        class="bg-[#F1C7DE] text-pink-800 px-4 py-2 rounded-md text-lg font-medium app-text md:hidden flex icon-text"
                        onclick="window.location='{{ route('patient.createApplication') }}'">
                        +
                    </button>
                    <button
                        onclick="this.innerHTML='Please wait...'; window.location='{{ route('patient.createApplication') }}'"
                        class="bg-[#F1C7DE] text-pink-800 px-8 py-2 rounded-md text-sm font-medium app-text md:flex hidden items-center justify-center">
                        + Add New
                    </button>
                    <div class="flex items-center space-x-1 bg-pink-500 px-4 py-2 rounded-md md:hidden flex">
                        <img src="{{ asset('public/images/export.svg') }}" alt="" />
                    </div>
                    {{-- <div class="flex items-center space-x-2 bg-pink-500 px-8 py-2 rounded-md md:flex hidden">
                        <button class="text-white text-sm font-medium app-text">Export</button>
                        <img src="{{ asset('public/images/export.svg') }}" alt="" />
                    </div> --}}
                </div>
            </div>
            <div class="table-container">
                <table class="min-w-full text-sm text-left mt-6">
                    <thead>
                        <tr class="border-t border-[#e0cfd8]">
                            <th class="p-2">
                                <input type="checkbox"
                                    class="accent-[#DB69A2] w-4 h-4 border border-[#91848C] rounded appearance-none checked:appearance-auto focus:ring-0" />
                            </th>

                            <th class="p-2 text-lg text-[#91848C] font-normal app-h">
                                Applications Title
                            </th>
                            <th class="p-2 text-lg text-[#91848C] font-normal app-h pad-left">
                                Applications ID
                            </th>
                            <th class="p-2 text-lg text-[#91848C] font-normal app-h">
                                Applications Status
                            </th>
                            <th class="p-2 text-lg text-[#91848C] font-normal app-h">Email</th>
                            <th class="p-2 text-lg text-[#91848C] font-normal app-h">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @forelse($applications as $application)
                            <tr class="border-t border-[#e0cfd8]">
                                <td class="p-2">
                                    <input type="checkbox"
                                        class="accent-[#DB69A2] w-4 h-4 border border-[#91848C] rounded appearance-none checked:appearance-auto focus:ring-0" />
                                </td>
                                <td class="p-2">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ asset('public/images/profile-1.png') }}" alt=""
                                            class="w-8 h-8 rounded-full" />
                                        <span
                                            class="text-[#91848C] text-[16px] font-light app-text">{{ $application->title }}</span>
                                    </div>
                                </td>
                                <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text pad-left">
                                    APP-{{ str_pad($application->id, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="p-2 align-middle">
                                    @php
                                        $status = $application->status;
                                        $statusClasses = [
                                            'Approved' => 'bg-[#C5E8D1] text-[#20B354]',
                                            'Rejected' => 'bg-[#E8C5C5] text-[#B32020]',
                                            'Pending' => 'bg-[#E4D7DF] text-[#91848C]',
                                            'Under Review' => 'bg-[#E4D7DF] text-[#91848C]',
                                        ];

                                        // New: Check for missing document requests
                                        $hasMissingDocRequest = $application->missingRequests->isNotEmpty();
                                    @endphp

                                    @if ($hasMissingDocRequest)
                                        {{-- Custom badge if missing docs are requested --}}
                                        <span
                                            class="bg-[#FFF2CC] text-[#D9980D] text-[16px] font-light px-4 py-2 rounded-sm text-xs font-medium app-text">
                                            Missing Docs Requested
                                        </span>
                                    @else
                                        <span
                                            class="{{ $statusClasses[$status] ?? 'bg-gray-200 text-gray-700' }} text-[16px] font-light px-4 py-2 rounded-sm text-xs font-medium app-text">
                                            {{ $status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="p-2 align-middle text-[#91848C] text-[16px] font-light app-text">
                                    {{ $application->patient->user->email ?? 'N/A' }}
                                </td>
                                <td class="p-2">
                                    <div class="flex items-center gap-2">
                                        <button class="bg-[#E4D7DF] px-4 py-2 rounded">
                                            <a href="{{ route('patient.viewApplication', $application->id) }}">
                                                <i class="fas fa-eye text-[#91848C]"></i>
                                            </a>
                                        </button>
                                        <button class="bg-[#F1C7DE] px-4 py-2 rounded">
                                            <a href="{{ route('patient.editApplication', $application->id) }}">
                                                <i class="fas fa-pen text-[#DB69A2]"></i>
                                            </a>
                                        </button>
                                        {{-- Delete button (optional, add route/controller if needed) --}}
                                        {{--
                                        <form action="{{ route('patient.deleteApplication', $application->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="bg-[#E8C5C5] px-4 py-2 rounded" onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash text-[#B32020]"></i>
                                            </button>
                                        </form>
                                        --}}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-[#91848C]">No applications found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="mt-6 flex justify-end">
                {{ $applications->links() }}
            </div>
        </div>
    </main>

    <script src="{{ asset('js/patient/dashboard.js') }}"></script>

@endsection
