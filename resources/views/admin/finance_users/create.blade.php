@extends('admin.layouts.admin')

@section('title', 'Add Finance User')

@section('content')
<div class="flex-1 flex flex-col">
    <main class="flex-1">
        <div class="max-w-8xl mx-auto">
            <div class="mb-6 flex items-center gap-4">
                <a href="{{ route('admin.finance-users.index') }}" class="flex items-center justify-center w-10 h-10 rounded-full bg-white border border-[#e0cfd8] text-[#91848C] hover:bg-[#F9EEF6] hover:text-[#9E2469] transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div>
                    <h1 class="text-2xl font-semibold text-[#213430] app-main">Add New Finance User</h1>
                    <p class="text-sm text-[#91848C] mt-1 app-text">Fill in the details to create a new finance user account</p>
                </div>
            </div>

            <div class="bg-[#F3E8EF] rounded-lg p-8">
                <form method="POST" action="{{ route('admin.finance-users.store') }}" class="space-y-8">
                    @csrf
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-[#213430] mb-2 app-h">First Name <span class="text-[#9E2469]">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white px-4 py-3 text-sm text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-[#9E2469] transition app-text" placeholder="Enter first name">
                            @error('first_name')<p class="text-xs text-[#9E2469] mt-2">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#213430] mb-2 app-h">Last Name <span class="text-[#9E2469]">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white px-4 py-3 text-sm text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-[#9E2469] transition app-text" placeholder="Enter last name">
                            @error('last_name')<p class="text-xs text-[#9E2469] mt-2">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-[#213430] mb-2 app-h">Email Address <span class="text-[#9E2469]">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white pl-10 pr-4 py-3 text-sm text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-[#9E2469] transition app-text" placeholder="example@email.com">
                            @error('email')<p class="text-xs text-[#9E2469] mt-2">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#213430] mb-2 app-h">Password <span class="text-[#9E2469]">*</span></label>
                            <input type="password" name="password" required class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white pl-10 pr-4 py-3 text-sm text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-[#9E2469] transition app-text" placeholder="Enter secure password">
                            @error('password')<p class="text-xs text-[#9E2469] mt-2">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-[#213430] mb-2 app-h">Phone Number <span class="text-[#9E2469]">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white pl-10 pr-4 py-3 text-sm text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-[#9E2469] transition app-text" placeholder="+1 (555) 000-0000">
                            @error('phone')<p class="text-xs text-[#9E2469] mt-2">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#213430] mb-2 app-h">Username <span class="text-[#91848C] text-xs font-normal ml-1">(Optional)</span></label>
                            <input type="text" name="username" value="{{ old('username') }}" class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white pl-10 pr-4 py-3 text-sm text-[#213430] placeholder-[#91848C] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-[#9E2469] transition app-text" placeholder="Enter username">
                            @error('username')<p class="text-xs text-[#9E2469] mt-2">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-[#213430] mb-2 app-h">Status <span class="text-[#9E2469]">*</span></label>
                            <select name="status" class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white px-4 py-3 text-sm text-[#213430] appearance-none focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-[#9E2469] transition app-text cursor-pointer">
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            @error('status')<p class="text-xs text-[#9E2469] mt-2">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-6 border-t-2 border-[#e0cfd8]">
                        <p class="text-sm text-[#91848C] app-text"><span class="text-[#9E2469]">*</span> Required fields</p>
                        <div class="flex gap-3">
                            <a href="{{ route('admin.finance-users.index') }}" class="px-6 py-3 border-2 border-[#DCCFD8] text-[#91848C] rounded-lg text-sm font-semibold hover:bg-white hover:border-[#9E2469] hover:text-[#9E2469] transition app-h">Cancel</a>
                            <button type="submit" class="px-6 py-3 bg-[#9E2469] hover:bg-[#B52D75] text-white rounded-lg text-sm font-semibold shadow-md hover:shadow-lg transition app-h flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                Create Finance User
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
@endsection
