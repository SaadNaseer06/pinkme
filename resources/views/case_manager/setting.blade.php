@extends('case_manager.layouts.app')

@section('title', 'Settings')

@section('content')

    @php
        $user = auth()->user();
        $profile = $user->profile;
        $patient = $user->patient;
        // dd($profile->date_of_birth);
    @endphp

    <main class="flex-1">
        <div class="max-w-8xl mx-auto">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-md mb-6" role="alert">
                    <h4 class="font-semibold mb-1">Thank you!</h4>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <!-- Tab Navigation -->
            <div class="flex flex-wrap mb-10">
                <div class="w-full md:w-1/4">
                    <button type="button" id="tab-personal" onclick="showTab('personal')"
                        class="tab-btn w-full bg-[#DB69A2] text-white py-4 px-6 font-normal text-center rounded-t-lg md:rounded-tr-none md:rounded-l-lg app-text">
                        Personal Information
                    </button>
                </div>
                <div class="w-full md:w-1/4">
                    <button type="button" id="tab-password" onclick="showTab('password')"
                        class="tab-btn w-full bg-[#F3E8EF] text-[#91848C] py-4 px-6 font-normal text-center app-text">
                        Change Password
                    </button>
                </div>
                <div class="w-full md:w-1/4">
                    <button type="button" id="tab-notifications" onclick="showTab('notifications')"
                        class="tab-btn w-full bg-[#F3E8EF] text-[#91848C] py-4 px-6 font-normal text-center app-text">
                        Email & SMS
                    </button>
                </div>
                <div class="w-full md:w-1/4">
                    <button type="button" id="tab-social" onclick="showTab('social')"
                        class="tab-btn app-text w-full bg-[#F3E8EF] text-[#91848C] py-4 px-6 font-normal text-center rounded-b-lg md:rounded-b-none md:rounded-tl-none md:rounded-r-lg">
                        Account & Social
                    </button>
                </div>
            </div>

            <div id="tabContents">
                <!-- Personal Information Tab -->
                <div id="personal" class="tab-content">
                    <div class="bg-[#F3E8EF] rounded-lg p-8">
                        <h2 class="text-xl font-medium text-[#213430] border-b border-[#DCCFD8] pb-3 mb-4 app-main">
                            Personal Information
                        </h2>
                        <form method="POST" action="{{ route('case_manager.settings.update') }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Avatar Upload -->
                            <div class="flex justify-center md:justify-start mb-6">
                                <div class="relative">
                                    <div class="w-28 h-28 rounded-full p-1">
                                        <div
                                            class="w-full h-full rounded-full overflow-hidden bg-white flex items-center justify-center">
                                            <img id="avatarPreview"
                                                src="{{ optional($profile)->avatar ? asset('storage/' . $profile->avatar) : asset('images/profile.png') }}"
                                                alt="Profile" class="object-cover w-full h-full" />
                                        </div>
                                    </div>
                                    <label for="avatar"
                                        class="absolute bottom-[10px] right-[7px] bg-[#DB69A2] text-white rounded-full w-6 h-6 flex items-center justify-center cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </label>
                                    <input id="avatar" name="avatar" type="file" class="hidden" />
                                </div>
                            </div>

                            <!-- Form Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="first_name" class="block text-[#213430] mb-1 app-text">First Name</label>
                                    <input type="text" id="first_name" name="first_name"
                                        value="{{ old('first_name', optional($profile)->first_name ?? 'No record') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent" />
                                </div>

                                <div>
                                    <label for="last_name" class="block text-[#213430] mb-1 app-text">Last Name</label>
                                    <input type="text" id="last_name" name="last_name"
                                        value="{{ old('last_name', optional($profile)->last_name ?? 'No record') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent" />
                                </div>

                                <div>
                                    <label for="username" class="block text-[#213430] mb-1 app-text">User Name</label>
                                    <input type="text" id="username" name="username"
                                        value="{{ old('username', optional($profile)->username ?? 'No record') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent" />
                                </div>

                                <div>
                                    <label for="phone" class="block text-[#213430] mb-1 app-text">Contact Number</label>
                                    <input type="text" id="phone" name="phone"
                                        value="{{ old('phone', optional($profile)->phone ?? 'No record') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent" />
                                </div>

                                <div>
                                    <label for="email" class="block text-[#213430] mb-1 app-text">Email</label>
                                    <input type="email" id="email" name="email"
                                        value="{{ old('email', $user->email ?? 'No record') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent" />
                                </div>

                                <div>
                                    <label for="gender" class="block text-[#213430] mb-1 app-text">Gender</label>
                                    <select id="gender" name="gender"
                                        class="w-full appearance-none px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent">
                                        <option value="">Select Gender</option>
                                        <option value="female" @selected(old('gender', optional($profile)->gender) == 'female')>Female</option>
                                        <option value="male" @selected(old('gender', optional($profile)->gender) == 'male')>Male</option>
                                        <option value="other" @selected(old('gender', optional($profile)->gender) == 'other')>Other</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="blood_group" class="block text-[#213430] mb-1 app-text">Blood
                                        Group</label>
                                    <select id="blood_group" name="blood_group"
                                        class="w-full appearance-none px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent">
                                        <option value="">Select Blood Group</option>
                                        @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                            <option value="{{ $bg }}" @selected(old('blood_group', optional($patient)->blood_group) == $bg)>
                                                {{ $bg }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="date_of_birth" class="block text-[#213430] mb-1 app-text">Date of
                                        Birth</label>
                                    <input type="date" id="date_of_birth" name="date_of_birth"
                                        value="{{ old('date_of_birth', optional($profile)->date_of_birth ? \Carbon\Carbon::parse(optional($profile)->date_of_birth)->format('Y-m-d') : null) }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent" />
                                </div>

                                <div>
                                    <label for="marital_status" class="block text-[#213430] mb-1 app-text">Marital
                                        Status</label>
                                    <input type="text" id="marital_status" name="marital_status"
                                        value="{{ old('marital_status', optional($patient)->marital_status ?? 'No record') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent" />
                                </div>

                                <div>
                                    @php
                                        $age = optional($profile)->date_of_birth
                                            ? \Carbon\Carbon::parse($profile->date_of_birth)->age
                                            : 'No record';
                                    @endphp
                                    <label for="age" class="block text-[#213430] mb-1 app-text">Age</label>
                                    <input type="text" id="age" name="age" value="{{ $age }}"
                                        readonly
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent" />
                                </div>

                                <div>
                                    <label for="country" class="block text-[#213430] mb-1 app-text">Country</label>
                                    <input type="text" id="country" name="country"
                                        value="{{ old('country', optional($profile)->country ?? 'No record') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent" />
                                </div>

                                <div>
                                    <label for="city" class="block text-[#213430] mb-1 app-text">City</label>
                                    <input type="text" id="city" name="city"
                                        value="{{ old('city', optional($profile)->city ?? 'No record') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent" />
                                </div>

                                <div>
                                    <label for="state" class="block text-[#213430] mb-1 app-text">State</label>
                                    <input type="text" id="state" name="state"
                                        value="{{ old('state', optional($profile)->state ?? 'No record') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent" />
                                </div>
                            </div>

                            <div class="flex mt-6 space-x-4">
                                {{-- <button type="button"
                                    class="px-6 py-2 bg-[#FFF7FC] text-[#91848C] border border-gray-300 rounded-md hover:bg-gray-50">Cancel</button> --}}
                                <button type="submit"
                                    class="px-6 py-2 bg-[#DB69A2] text-white rounded-md hover:bg-pink-600">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Change Password Tab -->
                <div id="password" class="tab-content hidden">
                    <div class="bg-[#F3E8EF] rounded-lg p-6">
                        <h2 class="text-xl font-medium text-[#213430] border-b border-[#DCCFD8] pb-3 mb-4 app-main">Change
                            Password</h2>
                        <form method="POST" action="{{ route('case_manager.setting.password') }}">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4">
                                <div>
                                    <label for="currentPassword" class="block text-[#213430] mb-1 app-text">Current
                                        Password</label>
                                    <input type="password" id="currentPassword" name="current_password"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                </div>
                                <div>
                                    <label for="newPassword" class="block text-[#213430] mb-1 app-text">New
                                        Password</label>
                                    <input type="password" id="newPassword" name="password"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                </div>
                                <div>
                                    <label for="confirmPassword" class="block text-[#213430] mb-1 app-text">Confirm New
                                        Password</label>
                                    <input type="password" id="confirmPassword" name="password_confirmation"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                </div>
                            </div>
                            <div class="flex mt-6 space-x-4">
                                {{-- <button type="button"
                                    class="px-6 py-2 bg-[#FFF7FC] text-[#91848C] border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">Cancel</button> --}}
                                <button type="submit"
                                    class="px-6 py-2 bg-[#DB69A2] text-white rounded-md hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Email & SMS Tab -->
                <div id="notifications" class="tab-content hidden">
                    <div class="bg-[#F3E8EF] rounded-lg p-6">
                        <h2 class="text-xl font-medium text-[#213430] border-b border-[#DCCFD8] pb-3 mb-4 app-main">Email &
                            SMS</h2>
                        <form method="POST" action="{{ route('case_manager.setting.notifications') }}">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <label class="text-[#213430] font-light app-text">Email Notification</label>
                                    <label class="switch">
                                        <input type="checkbox" name="email_notification"
                                            {{ old('email_notification', $profile->email_notification) ? 'checked' : '' }} />
                                        <span class="slider"><span class="circle"></span></span>
                                    </label>
                                </div>
                                <div class="flex justify-between items-center">
                                    <label class="text-[#213430] font-light app-text">SMS Notification</label>
                                    <label class="switch">
                                        <input type="checkbox" name="sms_notification"
                                            {{ old('sms_notification', $profile->sms_notification) ? 'checked' : '' }} />
                                        <span class="slider"><span class="circle"></span></span>
                                    </label>
                                </div>
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                                    <span class="text-[#213430] font-light app-text">Notify me when...</span>
                                    <div class="space-y-2 md:space-y-0 md:space-x-4">
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" name="notify_on_new_notifications"
                                                {{ old('notify_on_new_notifications', $profile->notify_on_new_notifications) ? 'checked' : '' }}
                                                class="accent-[#DB69A2] w-4 h-4 border border-[#91848C] rounded appearance-none checked:appearance-auto focus:ring-0" />
                                            <span class="text-sm text-[#213430] app-text">You have new notifications</span>
                                        </label>
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" name="notify_on_direct_message"
                                                {{ old('notify_on_direct_message', $profile->notify_on_direct_message) ? 'checked' : '' }}
                                                class="accent-[#DB69A2] w-4 h-4 border border-[#91848C] rounded appearance-none checked:appearance-auto focus:ring-0" />
                                            <span class="text-sm text-[#213430] app-text">You're sent a direct
                                                message</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="flex mt-6 space-x-4">
                                {{-- <button type="button"
                                    class="px-6 py-2 bg-[#FFF7FC] text-[#91848C] border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">Cancel</button> --}}
                                <button type="submit"
                                    class="px-6 py-2 bg-[#DB69A2] text-white rounded-md hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Account & Social Tab -->
                <div id="social" class="tab-content hidden">
                    <div class="flex flex-wrap flex-center gap-6">
                        <!-- Account Settings Form -->
                        <div class="w-full md:w-[49%] bg-[#F3E8EF] rounded-lg p-6">
                            <h2 class="text-xl font-medium text-[#213430] border-b border-[#DCCFD8] pb-3 mb-4 app-main">
                                Account Settings</h2>
                            <form method="POST" action="{{ route('case_manager.setting.account') }}">
                                @csrf
                                @method('PUT')
                                <div class="space-y-4">
                                    <div>
                                        <label for="account_username" class="block text-[#213430] mb-1 app-text">User
                                            Name</label>
                                        <input type="text" id="account_username" name="username"
                                            value="{{ old('username', $profile->username) }}"
                                            class="w-full border border-[#DCCFD8] rounded-md p-2 text-sm text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                    </div>
                                    <div>
                                        <label for="account_email"
                                            class="block text-[#213430] mb-1 app-text">Email</label>
                                        <input type="email" id="account_email" name="email"
                                            value="{{ old('email', $user->email) }}"
                                            class="w-full border border-[#DCCFD8] rounded-md p-2 text-sm text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                    </div>
                                    <div>
                                        <label for="alternate_email" class="block text-[#213430] mb-1 app-text">Alternate
                                            Email</label>
                                        <input type="email" id="alternate_email" name="alternate_email"
                                            value="{{ old('alternate_email', $profile->alternate_email) }}"
                                            class="w-full border border-[#DCCFD8] rounded-md p-2 text-sm text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                    </div>
                                </div>
                                <div class="flex mt-6 space-x-4">
                                    {{-- <button type="button"
                                        class="px-6 py-2 bg-[#FFF7FC] text-[#91848C] border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">Cancel</button> --}}
                                    <button type="submit"
                                        class="px-6 py-2 bg-[#DB69A2] text-white rounded-md hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">Save</button>
                                </div>
                            </form>
                        </div>
                        <!-- Social Media Settings Form -->
                        <div class="w-full md:w-[49%] bg-[#F3E8EF] rounded-lg p-6">
                            <h2 class="text-xl font-medium text-[#213430] border-b border-[#DCCFD8] pb-3 mb-4 app-main">
                                Social Media</h2>
                            <form method="POST" action="{{ route('case_manager.setting.social') }}">
                                @csrf
                                @method('PUT')
                                <div class="space-y-4">
                                    <div>
                                        <label for="facebook" class="block text-[#213430] mb-1 app-text">Facebook</label>
                                        <input type="text" id="facebook" name="facebook"
                                            value="{{ old('facebook', $profile->facebook) }}"
                                            class="w-full border border-[#DCCFD8] rounded-md p-2 text-sm text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                    </div>
                                    <div>
                                        <label for="twitter" class="block text-[#213430] mb-1 app-text">Twitter</label>
                                        <input type="text" id="twitter" name="twitter"
                                            value="{{ old('twitter', $profile->twitter) }}"
                                            class="w-full border border-[#DCCFD8] rounded-md p-2 text-sm text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                    </div>
                                    <div>
                                        <label for="instagram"
                                            class="block text-[#213430] mb-1 app-text">Instagram</label>
                                        <input type="text" id="instagram" name="instagram"
                                            value="{{ old('instagram', $profile->instagram) }}"
                                            class="w-full border border-[#DCCFD8] rounded-md p-2 text-sm text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                    </div>
                                </div>
                                <div class="flex mt-6 space-x-4">
                                    {{-- <button type="button"
                                        class="px-6 py-2 bg-[#FFF7FC] text-[#91848C] border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">Cancel</button> --}}
                                    <button type="submit"
                                        class="px-6 py-2 bg-[#DB69A2] text-white rounded-md hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function showTab(id) {
            const tabs = document.querySelectorAll('.tab-content');
            const buttons = {
                personal: document.getElementById('tab-personal'),
                password: document.getElementById('tab-password'),
                notifications: document.getElementById('tab-notifications'),
                social: document.getElementById('tab-social'),
            };

            tabs.forEach((tab) => tab.classList.add('hidden'));
            Object.values(buttons).forEach((btn) => {
                btn.classList.remove('bg-[#DB69A2]', 'text-white');
                btn.classList.add('bg-[#F3E8EF]', 'text-[#91848C]');
            });

            const activeTab = document.getElementById(id);
            if (activeTab) activeTab.classList.remove('hidden');

            const activeButton = buttons[id];
            if (activeButton) {
                activeButton.classList.remove('bg-[#F3E8EF]', 'text-[#91848C]');
                activeButton.classList.add('bg-[#DB69A2]', 'text-white');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            showTab('personal');
        });

        document.getElementById('avatar').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('avatarPreview');
            if (file && preview) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>

@endsection
