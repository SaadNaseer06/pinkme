@extends('admin.layouts.admin')

@section('title', 'Add Sponsor')

@section('content')
    <div class="max-w-9xl mx-auto">
        <div class="bg-[#F3E8EF] rounded-xl p-6">
            <h1 class="text-2xl font-semibold text-[#213430] mb-4">Add Sponsor</h1>
            <form method="POST" action="{{ route('admin.sponsors.store') }}" class="space-y-6">
                @csrf

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-[#213430] mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full rounded-md border border-[#DCCFD8] px-4 py-2" />
                        @error('email')<p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#213430] mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required
                               class="w-full rounded-md border border-[#DCCFD8] px-4 py-2" />
                        @error('phone')<p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#213430] mb-1">Password</label>
                        <input type="password" name="password" required
                               class="w-full rounded-md border border-[#DCCFD8] px-4 py-2" />
                        @error('password')<p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#213430] mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full rounded-md border border-[#DCCFD8] px-4 py-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#213430] mb-1">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required
                               class="w-full rounded-md border border-[#DCCFD8] px-4 py-2" />
                        @error('first_name')<p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#213430] mb-1">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required
                               class="w-full rounded-md border border-[#DCCFD8] px-4 py-2" />
                        @error('last_name')<p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-[#213430] mb-1">Company Name <span class="text-xs text-[#91848C]">(Optional)</span></label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}"
                               class="w-full rounded-md border border-[#DCCFD8] px-4 py-2" />
                        @error('company_name')<p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#213430] mb-1">Company Email <span class="text-xs text-[#91848C]">(Optional)</span></label>
                        <input type="email" name="company_email" value="{{ old('company_email') }}"
                               class="w-full rounded-md border border-[#DCCFD8] px-4 py-2" />
                        @error('company_email')<p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#213430] mb-1">Company Phone <span class="text-xs text-[#91848C]">(Optional)</span></label>
                        <input type="text" name="company_phone" value="{{ old('company_phone') }}"
                               class="w-full rounded-md border border-[#DCCFD8] px-4 py-2" />
                        @error('company_phone')<p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#213430] mb-1">Registration # <span class="text-xs text-[#91848C]">(Optional)</span></label>
                        <input type="text" name="registration_number" value="{{ old('registration_number') }}"
                               class="w-full rounded-md border border-[#DCCFD8] px-4 py-2" />
                        @error('registration_number')<p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-[#213430] mb-1">Company Type <span class="text-xs text-[#91848C]">(Optional)</span></label>
                        <input type="text" name="company_type" value="{{ old('company_type') }}"
                               class="w-full rounded-md border border-[#DCCFD8] px-4 py-2" />
                        @error('company_type')<p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="px-6 py-2 bg-[#9E2469] text-white rounded-md">Create Sponsor</button>
                    <a href="{{ route('admin.sponsors') }}" class="px-6 py-2 border border-[#DCCFD8] text-[#91848C] rounded-md">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
