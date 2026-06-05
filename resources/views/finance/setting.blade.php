@extends('finance.layouts.app')

@section('title', 'Profile settings')

@section('content')
    <div class="max-w-8xl mx-auto">
        <div class="bg-[#F3E8EF] rounded-lg p-6 md:p-8">
            <h1 class="text-2xl font-semibold text-[#213430] app-main border-b border-[#DCCFD8] pb-4 mb-2">
                Profile
            </h1>
            <p class="text-sm text-[#91848C] app-text mb-8">
                Update your finance account details and photo. This is the same kind of profile used elsewhere in the portal (name, email, phone, optional username, profile picture).
            </p>

            <form action="{{ route('finance.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="flex justify-center md:justify-start">
                    <div class="relative">
                        <div class="w-28 h-28 rounded-full p-1">
                            <div class="w-full h-full rounded-full overflow-hidden bg-white flex items-center justify-center border border-[#DCCFD8]">
                                <img id="financeAvatarPreview" src="{{ $user->avatar_url }}" alt="Profile photo"
                                    class="object-cover w-full h-full" />
                            </div>
                        </div>
                        <label for="finance_avatar"
                            class="absolute bottom-[10px] right-[7px] bg-[#9E2469] text-white rounded-full w-8 h-8 flex items-center justify-center cursor-pointer shadow-md hover:bg-[#B52D75]"
                            title="Change photo">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </label>
                        <input id="finance_avatar" name="avatar" type="file" class="hidden" accept="image/jpeg,image/png,image/gif,image/webp" />
                    </div>
                </div>
                @error('avatar')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="full_name" class="block font-light text-md text-[#213430] mb-1 app-text">Full name</label>
                        <input type="text" id="full_name" name="full_name"
                            value="{{ old('full_name', optional($user->profile)->full_name ?? $user->email ?? '') }}"
                            class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-white focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                            placeholder="Your full name" />
                        @error('full_name')
                            <p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="username" class="block font-light text-md text-[#213430] mb-1 app-text">Username</label>
                        <input type="text" id="username" name="username"
                            value="{{ old('username', optional($user->profile)->username ?? '') }}"
                            class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-white focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                            placeholder="Username (optional)" />
                        @error('username')
                            <p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="block font-light text-md text-[#213430] mb-1 app-text">Email <span class="text-[#9E2469]">*</span></label>
                        <input type="email" id="email" name="email" required
                            value="{{ old('email', $user->email ?? '') }}"
                            class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-white focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                        @error('email')
                            <p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="phone" class="block font-light text-md text-[#213430] mb-1 app-text">Phone</label>
                        <input type="text" id="phone" name="phone"
                            value="{{ old('phone', optional($user->profile)->phone ?? '') }}"
                            class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-white focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                            placeholder="Contact number" />
                        @error('phone')
                            <p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="px-6 py-2 bg-[#9E2469] text-white rounded-md shadow hover:bg-[#B52D75] transition app-text">
                        Save changes
                    </button>
                    <a href="{{ route('finance.dashboard') }}" class="px-6 py-2 border border-[#DCCFD8] text-[#91848C] rounded-md hover:bg-white/80 app-text inline-flex items-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var input = document.getElementById('finance_avatar');
            var preview = document.getElementById('financeAvatarPreview');
            if (!input || !preview) return;
            input.addEventListener('change', function (e) {
                var file = e.target.files && e.target.files[0];
                if (!file || !file.type.startsWith('image/')) return;
                var reader = new FileReader();
                reader.onload = function (ev) {
                    preview.src = ev.target.result;
                };
                reader.readAsDataURL(file);
            });
        });
    </script>
@endsection
