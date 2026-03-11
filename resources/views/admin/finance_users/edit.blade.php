@extends('admin.layouts.admin')

@section('title', 'Edit Finance User')

@section('content')
<div class="flex-1 flex flex-col">
    <main class="flex-1">
        <div class="max-w-8xl mx-auto">
            <div class="mb-6 flex items-center gap-4">
                <a href="{{ route('admin.finance-users.index') }}" class="flex items-center justify-center w-10 h-10 rounded-full bg-white border border-[#e0cfd8] text-[#91848C] hover:bg-[#F9EEF6] hover:text-[#9E2469] transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div>
                    <h1 class="text-2xl font-semibold text-[#213430] app-main">Edit Finance User</h1>
                    <p class="text-sm text-[#91848C] mt-1 app-text">Update finance user information</p>
                </div>
            </div>

            @if (session('success'))
                <div class="mb-6 rounded-lg border-2 border-green-300 bg-green-50 px-4 py-3 text-green-800">{{ session('success') }}</div>
            @endif

            <div class="bg-[#F3E8EF] rounded-lg p-8">
                <form method="POST" action="{{ route('admin.finance-users.update', $user) }}" class="space-y-8">
                    @csrf
                    @method('PUT')
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-[#213430] mb-2 app-h">First Name <span class="text-[#9E2469]">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name', $user->profile->first_name) }}" required class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white px-4 py-3 text-sm text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-[#9E2469] transition app-text" placeholder="Enter first name">
                            @error('first_name')<p class="text-xs text-[#9E2469] mt-2">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#213430] mb-2 app-h">Last Name <span class="text-[#9E2469]">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name', $user->profile->last_name) }}" required class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white px-4 py-3 text-sm text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-[#9E2469] transition app-text" placeholder="Enter last name">
                            @error('last_name')<p class="text-xs text-[#9E2469] mt-2">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-[#213430] mb-2 app-h">Email Address <span class="text-[#9E2469]">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white pl-10 pr-4 py-3 text-sm text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-[#9E2469] transition app-text" placeholder="example@email.com">
                            @error('email')<p class="text-xs text-[#9E2469] mt-2">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#213430] mb-2 app-h">New Password <span class="text-[#91848C] text-xs font-normal ml-1">(Leave blank to keep current)</span></label>
                            <input type="password" name="password" class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white pl-10 pr-4 py-3 text-sm text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-[#9E2469] transition app-text" placeholder="Enter new password">
                            @error('password')<p class="text-xs text-[#9E2469] mt-2">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-[#213430] mb-2 app-h">Phone Number <span class="text-[#9E2469]">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone', $user->profile->phone) }}" required class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white pl-10 pr-4 py-3 text-sm text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-[#9E2469] transition app-text" placeholder="+1 (555) 000-0000">
                            @error('phone')<p class="text-xs text-[#9E2469] mt-2">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#213430] mb-2 app-h">Username <span class="text-[#91848C] text-xs font-normal ml-1">(Optional)</span></label>
                            <input type="text" name="username" value="{{ old('username', $user->profile->username) }}" class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white pl-10 pr-4 py-3 text-sm text-[#213430] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-[#9E2469] transition app-text" placeholder="Enter username">
                            @error('username')<p class="text-xs text-[#9E2469] mt-2">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-[#213430] mb-2 app-h">Status <span class="text-[#9E2469]">*</span></label>
                            <select name="status" class="w-full rounded-lg border-2 border-[#DCCFD8] bg-white px-4 py-3 text-sm text-[#213430] appearance-none focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:border-[#9E2469] transition app-text cursor-pointer">
                                <option value="1" @selected(old('status', $user->profile->status) == 1)>Active</option>
                                <option value="0" @selected(old('status', $user->profile->status) == 0)>Inactive</option>
                            </select>
                            @error('status')<p class="text-xs text-[#9E2469] mt-2">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-6 border-t-2 border-[#e0cfd8]">
                        <p class="text-sm text-[#91848C] app-text"><span class="text-[#9E2469]">*</span> Required fields</p>
                        <div class="flex gap-3">
                            <a href="{{ route('admin.finance-users.index') }}" class="px-6 py-3 border-2 border-[#DCCFD8] text-[#91848C] rounded-lg text-sm font-semibold hover:bg-white hover:border-[#9E2469] hover:text-[#9E2469] transition app-h">Cancel</a>
                            <button type="submit" class="px-6 py-3 bg-[#9E2469] hover:bg-[#B52D75] text-white rounded-lg text-sm font-semibold shadow-md hover:shadow-lg transition app-h flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
@endsection
