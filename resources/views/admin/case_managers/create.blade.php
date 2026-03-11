@extends('admin.layouts.admin')

@section('title', 'Add Case Manager')

@section('content')
    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <main class="flex-1">
            <div class="max-w-8xl mx-auto">
                <!-- Back Button and Title -->
                <div class="mb-6 flex items-center gap-4">
                    <a href="{{ route('admin.case-managers.index') }}"
                        class="flex items-center justify-center w-10 h-10 rounded-full bg-white border border-[#e0cfd8] text-[#91848C] hover:bg-[#F9EEF6] hover:text-[#9E2469] transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-semibold text-[#213430] app-main">Add New Case Manager</h1>
                        <p class="text-sm text-[#91848C] mt-1 app-text">Fill in the details to create a new case manager
                            account</p>
                    </div>
                </div>

                <!-- Form Section -->
                <div class="bg-[#F3E8EF] rounded-lg p-8">
                    <form method="POST" action="{{ route('admin.case-managers.store') }}" class="space-y-8">
                        @csrf

                        <!-- Personal Information Section -->
                        <div>
                            {{-- <h3 class="text-lg font-semibold text-[#213430] mb-4 app-h flex items-center gap-2">
                                <span
                                    class="w-8 h-8 bg-[#9E2469] text-white rounded-full flex items-center justify-center text-sm">1</span>
                                Personal Information
                            </h3> --}}
                            <div class="grid gap-6 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-[#213430] mb-2 app-h">
                                        First Name <span class="text-[#9E2469]">*</span>
                                    </label>
                                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                                        class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white px-4 py-3 text-sm text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-[#9E2469] transition app-text"
                                        placeholder="Enter first name">
                                    @error('first_name')
                                        <p class="text-xs text-[#9E2469] mt-2 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#213430] mb-2 app-h">
                                        Last Name <span class="text-[#9E2469]">*</span>
                                    </label>
                                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                                        class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white px-4 py-3 text-sm text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-[#9E2469] transition app-text"
                                        placeholder="Enter last name">
                                    @error('last_name')
                                        <p class="text-xs text-[#9E2469] mt-2 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Account Credentials Section -->
                        <div>
                            {{-- <h3 class="text-lg font-semibold text-[#213430] mb-4 app-h flex items-center gap-2">
                                <span
                                    class="w-8 h-8 bg-[#9E2469] text-white rounded-full flex items-center justify-center text-sm">2</span>
                                Account Credentials
                            </h3> --}}
                            <div class="grid gap-6 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-[#213430] mb-2 app-h">
                                        Email Address <span class="text-[#9E2469]">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#91848C]" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                            </svg>
                                        </div>
                                        <input type="email" name="email" value="{{ old('email') }}" required
                                            class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white pl-10 pr-4 py-3 text-sm text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-[#9E2469] transition app-text"
                                            placeholder="example@email.com">
                                    </div>
                                    @error('email')
                                        <p class="text-xs text-[#9E2469] mt-2 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#213430] mb-2 app-h">
                                        Password <span class="text-[#9E2469]">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#91848C]" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </div>
                                        <input type="password" name="password" required
                                            class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white pl-10 pr-4 py-3 text-sm text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-[#9E2469] transition app-text"
                                            placeholder="Enter secure password">
                                    </div>
                                    @error('password')
                                        <p class="text-xs text-[#9E2469] mt-2 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Section -->
                        <div>
                            {{-- <h3 class="text-lg font-semibold text-[#213430] mb-4 app-h flex items-center gap-2">
                                <span
                                    class="w-8 h-8 bg-[#9E2469] text-white rounded-full flex items-center justify-center text-sm">3</span>
                                Contact Information
                            </h3> --}}
                            <div class="grid gap-6 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-[#213430] mb-2 app-h">
                                        Phone Number <span class="text-[#9E2469]">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#91848C]" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                        </div>
                                        <input type="text" name="phone" value="{{ old('phone') }}" required
                                            class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white pl-10 pr-4 py-3 text-sm text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-[#9E2469] transition app-text"
                                            placeholder="+1 (555) 000-0000">
                                    </div>
                                    @error('phone')
                                        <p class="text-xs text-[#9E2469] mt-2 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#213430] mb-2 app-h">
                                        Username
                                        <span class="text-[#91848C] text-xs font-normal ml-1">(Optional)</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#91848C]" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <input type="text" name="username" value="{{ old('username') }}"
                                            class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white pl-10 pr-4 py-3 text-sm text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-[#9E2469] transition app-text"
                                            placeholder="Enter username">
                                    </div>
                                    @error('username')
                                        <p class="text-xs text-[#9E2469] mt-2 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Account Status Section -->
                        <div>
                            {{-- <h3 class="text-lg font-semibold text-[#213430] mb-4 app-h flex items-center gap-2">
                                <span
                                    class="w-8 h-8 bg-[#9E2469] text-white rounded-full flex items-center justify-center text-sm">4</span>
                                Account Status
                            </h3> --}}
                            <div class="grid gap-6 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-[#213430] mb-2 app-h">
                                        Status <span class="text-[#9E2469]">*</span>
                                    </label>
                                    <div class="relative">
                                        <select name="status"
                                            class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white px-4 py-3 text-sm text-[#213430] appearance-none focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-[#9E2469] transition app-text cursor-pointer">
                                            <option value="1" selected>Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[#91848C]">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                    @error('status')
                                        <p class="text-xs text-[#9E2469] mt-2 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                    <p class="text-xs text-[#91848C] mt-2 app-text">Set the initial status for this account
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between pt-6 border-t-2 border-[#e0cfd8]">
                            <p class="text-sm text-[#91848C] app-text">
                                <span class="text-[#9E2469]">*</span> Required fields
                            </p>
                            <div class="flex gap-3">
                                <a href="{{ route('admin.case-managers.index') }}"
                                    class="px-6 py-3 border-2 border-[#DCCFD8] text-[#91848C] rounded-lg text-sm font-semibold hover:bg-white hover:border-[#9E2469] hover:text-[#9E2469] transition app-h">
                                    Cancel
                                </a>
                                <button type="submit"
                                    class="px-6 py-3 bg-[#9E2469] hover:bg-[#B52D75] text-white rounded-lg text-sm font-semibold shadow-md hover:shadow-lg transition transform hover:scale-105 app-h flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Create Case Manager
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
@endsection
