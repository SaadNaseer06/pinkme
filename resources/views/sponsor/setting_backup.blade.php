@extends('sponsor.layouts.app')

@section('title', 'Settings')

@section('content')

    @php
        /**
         * Pull the authenticated user and their related models to bind into the form.
         *
         * - $user         : the User model (email, etc.)
         * - $profile      : the UserProfile model (extended personal details)
         * - $sponsorDetail: the SponsorDetail model (company details)
         */
        $user = auth()->user();
        $profile = $user->profile;
        $sponsorDetail = $user->sponsorDetail;
    @endphp

    <!-- Settings Content -->
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
                        class="tab-btn w-full bg-[#F3E8EF] text-[#91848C] py-4 px-6 font-normal text-center rounded-b-lg md:rounded-b-none md:rounded-tl-none md:rounded-r-lg app-text">
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
                        <form method="POST" action="{{ route('sponsor.settings.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <!-- Avatar Upload -->
                            <div class="flex justify-center md:justify-start mb-6">
                                <div class="relative">
                                    <div class="w-28 h-28 rounded-full p-1">
                                        <div
                                            class="w-full h-full rounded-full overflow-hidden bg-white flex items-center justify-center">
                                            <img id="avatarPreview"
                                                src="{{ '/storage/' . $profile->avatar ?? asset('images/profile.png') }}" alt="Profile"
                                                class="object-cover w-full h-full" />
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
                                        value="{{ old('first_name', $profile->first_name) }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                </div>
                                <div>
                                    <label for="last_name" class="block text-[#213430] mb-1 app-text">Last Name</label>
                                    <input type="text" id="last_name" name="last_name"
                                        value="{{ old('last_name', $profile->last_name) }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                </div>
                                <div>
                                    <label for="username" class="block text-[#213430] mb-1 app-text">User Name</label>
                                    <input type="text" id="username" name="username"
                                        value="{{ old('username', $profile->username) }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                </div>
                                <div>
                                    <label for="phone" class="block text-[#213430] mb-1 app-text">Contact Number</label>
                                    <input type="text" id="phone" name="phone"
                                        value="{{ old('phone', $profile->phone) }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                </div>
                                <div>
                                    <label for="email" class="block text-[#213430] mb-1 app-text">Email</label>
                                    <input type="email" id="email" name="email"
                                        value="{{ old('email', $user->email) }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                </div>
                                <div>
                                    <label for="gender" class="block text-[#213430] mb-1 app-text">Gender</label>
                                    <select id="gender" name="gender"
                                        class="w-full appearance-none px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">
                                        <option value="">Select Gender</option>
                                        <option value="female" @selected(old('gender', $profile->gender) == 'female')>Female</option>
                                        <option value="male" @selected(old('gender', $profile->gender) == 'male')>Male</option>
                                        <option value="other" @selected(old('gender', $profile->gender) == 'other')>Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="date_of_birth" class="block text-[#213430] mb-1 app-text">Date of Birth</label>
                                    <input type="date" id="date_of_birth" name="date_of_birth"
                                        value="{{ old('date_of_birth', optional($profile->date_of_birth)->format('Y-m-d')) }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                </div>

                                <div>
                                    <label class="block font-light text-md text-[#213430] mb-1 app-text">
                                        Date Of Birth:
                                    </label>
                                    <div class="grid grid-cols-3 gap-2">
                                        <!-- Month dropdown with custom arrow -->
                                        <div class="relative">
                                            <select
                                                class="w-full appearance-none px-4 py-2 font-light rounded-md border border-[#DCCFD8] bg-transparent text-[#B1A4AD] focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">
                                                <option>Oct</option>
                                                <option>Nov</option>
                                                <option>Dec</option>
                                            </select>
                                            <img src="/images/down-arrow.svg" alt="Dropdown arrow"
                                                class="pointer-events-none absolute right-4 top-1/2 transform -translate-y-1/2 w-4 h-4" />
                                        </div>

                                        <!-- Day input -->
                                        <input type="text" value="05"
                                            class="px-4 py-2 font-light rounded-md border border-[#DCCFD8] bg-transparent text-[#B1A4AD] focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />

                                        <!-- Year input -->
                                        <input type="text" value="2003"
                                            class="px-4 py-2 font-light rounded-md border border-[#DCCFD8] bg-transparent text-[#B1A4AD] focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                    </div>
                                </div>

                                <div>
                                    <label for="maritalStatus"
                                        class="block font-light text-md text-[#213430] mb-1 app-text">Marital
                                        Status:</label>
                                    <input type="text" id="maritalStatus" value="Single"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] bg-transparent text-[#B1A4AD] focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                </div>
                                <div>
                                    <label for="age"
                                        class="block font-light text-md text-[#213430] mb-1 app-text">Age:</label>
                                    <input type="text" id="age" value="22"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] bg-transparent text-[#B1A4AD] focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                </div>

                                <div class="md:col-span-2">
                                    <div class="grid grid-cols-4 gap-4">
                                        <div class="col-span-4 md:col-span-1">
                                            <label for="country"
                                                class="block font-light text-md text-[#213430] mb-1 app-text">Country:</label>
                                            <input type="text" id="country" value="USA"
                                                class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#B1A4AD] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                        </div>
                                        <div class="col-span-4 md:col-span-1">
                                            <label for="city"
                                                class="block font-light text-md text-[#213430] mb-1 app-text">City:</label>
                                            <input type="text" id="city" value="San Francisco"
                                                class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#B1A4AD] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                        </div>
                                        <div class="col-span-4 md:col-span-2">
                                            <label for="state"
                                                class="block font-light text-md text-[#213430] mb-1 app-text">State:</label>
                                            <input type="text" id="state" value="California"
                                                class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#B1A4AD] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex mt-8 space-x-4">
                                <button type="button"
                                    class="px-6 py-2 bg-[#FFF7FC] text-[#91848C] border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-6 py-2 bg-[#DB69A2] text-white rounded-md hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">
                                    Submit
                                </button>
                            </div>
                        </form>

                        <!-- Company form -->

                        <form class="hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Company Name -->
                                <div>
                                    <label for="companyName"
                                        class="block font-light text-md text-[#213430] mb-1 app-text">
                                        Company Name:
                                    </label>
                                    <input type="text" id="companyName" value="Sara"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#B1A4AD] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                </div>

                                <!-- Company Email -->
                                <div>
                                    <label for="companyEmail"
                                        class="block font-light text-md text-[#213430] mb-1 app-text">
                                        Company Email:
                                    </label>
                                    <input type="email" id="companyEmail" value="saratylor232@"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#B1A4AD] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                </div>

                                <!-- Company Phone No -->
                                <div>
                                    <label for="companyPhone"
                                        class="block font-light text-md text-[#213430] mb-1 app-text">
                                        Company Phone No:
                                    </label>
                                    <input type="text" id="companyPhone" value="(415) 584-9700"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#B1A4AD] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                </div>

                                <!-- Registration Number -->
                                <div>
                                    <label for="registrationNumber"
                                        class="block font-light text-md text-[#213430] mb-1 app-text">
                                        Registration Number:
                                    </label>
                                    <input type="text" id="registrationNumber" value="AOW1230122"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#B1A4AD] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                </div>

                                <!-- Company Type -->
                                <div>
                                    <label for="companyType"
                                        class="block font-light text-md text-[#213430] mb-1 app-text">
                                        Company Type:
                                    </label>

                                    <div class="relative">
                                        <select id="companyType"
                                            class="appearance-none w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#B1A4AD] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">
                                            <option value="">Company Type</option>
                                            <option value="LLC">LLC</option>
                                            <option value="Corporation">Corporation</option>
                                            <option value="Sole Proprietorship">Sole Proprietorship</option>
                                        </select>
                                        <img src="/images/down-arrow.svg" alt="Dropdown arrow"
                                            class="pointer-events-none absolute right-4 top-1/2 transform -translate-y-1/2 w-4 h-4" />
                                    </div>
                                </div>


                                <!-- Country -->
                                <div>
                                    <label for="country" class="block font-light text-md text-[#213430] mb-1 app-text">
                                        Country:
                                    </label>
                                    <input type="text" id="country" value="USA"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#B1A4AD] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                </div>

                                <!-- City -->
                                <div>
                                    <label for="city" class="block font-light text-md text-[#213430] mb-1 app-text">
                                        City:
                                    </label>
                                    <input type="text" id="city" value="San Francisco"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#B1A4AD] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                                </div>

                                <!-- State -->
                                <div>
                                    <label for="state" class="block font-light text-md text-[#213430] mb-1 app-text">
                                        State:
                                    </label>
                                    <div class="relative">
                                        <select id="state"
                                            class="appearance-none w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#B1A4AD] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">
                                            <option value="">Select State</option>
                                            <option value="California">California</option>
                                            <option value="Texas">Texas</option>
                                            <option value="New York">New York</option>
                                            <option value="Florida">Florida</option>
                                            <!-- Add more as needed -->
                                        </select>
                                        <img src="/images/down-arrow.svg" alt="Dropdown arrow"
                                            class="pointer-events-none absolute right-4 top-1/2 transform -translate-y-1/2 w-4 h-4" />
                                    </div>
                                </div>

                            </div>

                            <!-- Buttons -->
                            <div class="flex mt-8 space-x-4">
                                <button type="button"
                                    class="px-6 py-2 bg-[#FFF7FC] text-[#91848C] border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-6 py-2 bg-[#DB69A2] text-white rounded-md hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">
                                    Submit
                                </button>
                            </div>
                        </form>


                    </div>
                </div>
                
                <!--- Tab 2--->
                <div id="password" class="tab-content hidden">
                    <div class="bg-[#F3E8EF] rounded-lg p-8">
                        <h2 class="text-xl text-gray-700 font-medium pb-4 border-b border-[#DCCFD8] mb-4 app-text">
                            Change Password
                        </h2>

                        <div class="space-y-6">
                            <!-- Field 1 -->
                            <div>
                                <div class="flex justify-between">
                                    <label for="currentPassword"
                                        class="block font-light text-md text-[#213430] mb-1 app-text">
                                        Current Password:
                                    </label>
                                    <label for="forgotPassword"
                                        class="block font-light text-md text-[#DB69A2] mb-1 app-text">
                                        Forgot Password?
                                    </label>
                                </div>

                                <input type="password" id="currentPassword" value="*************"
                                    class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#B1A4AD] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                            </div>

                            <!-- Field 2 -->
                            <div>
                                <label for="newPassword" class="block font-light text-md text-[#213430] mb-1 app-text">
                                    New Password:
                                </label>
                                <input type="password" id="newPassword" value="*************"
                                    class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#B1A4AD] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                            </div>

                            <!-- Field 3 -->
                            <div>
                                <label for="confirmPassword"
                                    class="block font-light text-md text-[#213430] mb-1 app-text">
                                    Confirm New Password:
                                </label>
                                <input type="password" id="confirmPassword" value="*************"
                                    class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#B1A4AD] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" />
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex mt-8 space-x-4">
                            <button type="button"
                                class="px-6 py-2 bg-[#FFF7FC] text-[#91848C] border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-6 py-2 bg-[#DB69A2] text-white rounded-md hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-300"
                                app-text>
                                Submit
                            </button>
                        </div>
                    </div>
                </div>

                <!----Tab 3--->
                <div id="notifications" class="tab-content hidden">
                    <div class="bg-[#F3E8EF] rounded-lg p-4">
                        <h2 class="text-xl text-gray-700 font-medium pb-4 border-b border-[#DCCFD8] mb-6 app-text">
                            Email And SMS
                        </h2>

                        <div class="space-y-6">
                            <!-- Email Notification Toggle -->
                            <div class="flex items-center space-x-[193px] tab-slider">
                                <label class="text-gray-800 font-light w-40 app-text">Email Notification:</label>
                                <label class="switch mini">
                                    <input type="checkbox">
                                    <span class="slider">
                                        <span class="circle"></span>
                                    </span>
                                </label>
                            </div>


                            <!-- SMS Notification Toggle -->
                            <div class="flex items-center space-x-[193px] tab-slider">
                                <label class="text-gray-800 font-light w-40 app-text">SMS Notification:</label>
                                <label class="switch mini">
                                    <input type="checkbox">
                                    <span class="slider">
                                        <span class="circle"></span>
                                    </span>
                                </label>
                            </div>

                            <!-- When To Email -->
                            <div class="flex flex-row  gap-[14.5rem] items-center mb-4 mobile-email-gap">
                                <!-- Label -->
                                <label
                                    class="text-gray-800 font-light mb-2 md:mb-0 w-full md:w-auto text-center md:text-left app-text mobile-email-w">
                                    When To Email
                                </label>

                                <!-- Checkboxes -->
                                <div class="space-y-2 pl-0 md:pl-2">
                                    <div class="flex items-center space-x-2">
                                        <input type="checkbox"
                                            class="accent-[#DB69A2] w-4 h-4 border border-[#91848C] rounded appearance-none checked:appearance-auto focus:ring-0" />
                                        <span class="text-sm text-gray-800 app-text">You have new notifications.</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <input type="checkbox"
                                            class="accent-[#DB69A2] w-4 h-4 border border-[#91848C] rounded appearance-none checked:appearance-auto focus:ring-0" />
                                        <span class="text-sm text-gray-800 app-text">You're sent a direct message</span>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Buttons -->
                        <div class="flex mt-8 space-x-4">
                            <button type="button"
                                class="px-6 py-2 bg-[#FFF7FC] text-[#91848C] border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-6 py-2 bg-[#DB69A2] text-white rounded-md hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">
                                Submit
                            </button>
                        </div>
                    </div>

                </div>

                <!----Tab 4-->
                <div id="social" class="tab-content hidden">
                    <div class="flex flex-wrap justify-center gap-10 social-tab">
                        <!-- Account Setting -->
                        <div class="w-full md:w-[48%] bg-[#F3E8EF] h-auto  p-6 rounded-xl ">
                            <h2 class="text-xl font-normal text-[#213430] mb-4 border-b p-2 border-[#DCCFD8] app-text ">
                                Account Setting</h2>
                            <form class="space-y-4">
                                <div>
                                    <label class="block text-sm font-light text-[#213430] mb-1 app-text">User Name:</label>
                                    <input type="text" value="saratylor232@"
                                        class="w-full border border-[#DCCFD8] rounded-md p-2 text-sm text-[#B1A4AD] font-light bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300" />
                                </div>
                                <div>
                                    <label class="block text-sm font-light text-[#213430] mb-1 app-text">Email Id:</label>
                                    <input type="email" value="saratylor232@company.com"
                                        class="w-full border border-[#DCCFD8] rounded-md p-2 text-sm text-[#B1A4AD] font-light bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300" />
                                </div>

                                <div>
                                    <label class="block text-sm font-light text-[#213430] mb-1 app-text">Alternate
                                        Email:</label>
                                    <input type="email" value="saratylor349a@company.com"
                                        class="w-full border border-[#DCCFD8] rounded-md p-2 text-sm text-[#B1A4AD] font-light bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300" />
                                </div>

                                <div class="flex mt-8 space-x-4">
                                    <button type="button"
                                        class="px-6 py-2 bg-[#FFF7FC] text-[#91848C] border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="px-6 py-2 bg-[#DB69A2] text-white rounded-md hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">
                                        Submit
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Social Media -->
                        <div class="w-full md:w-[48%] bg-[#F3E8EF] h-auto  p-6 rounded-xl ">
                            <h2 class="text-xl font-normal text-[#213430] mb-4 border-b p-2 border-[#DCCFD8]  app-text">
                                Social Media</h2>
                            <form class="space-y-4">
                                <div>
                                    <label class="block text-sm font-light text-[#213430] mb-1">Facebook:</label>
                                    <input type="text" value="www.facebook.com"
                                        class="w-full border border-[#DCCFD8] rounded-md p-2 text-sm text-[#B1A4AD] font-light bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300" />
                                </div>
                                <div>
                                    <label class="block text-sm font-light text-[#213430] mb-1">Twitter:</label>
                                    <input type="text" value="www.twitter.com"
                                        class="w-full border border-[#DCCFD8] rounded-md p-2 text-sm text-[#B1A4AD] font-light bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300" />
                                </div>

                                <div>
                                    <label class="block text-sm font-light text-[#213430] mb-1">Instagram:</label>
                                    <input type="text" value="www.google.com"
                                        class="w-full border border-[#DCCFD8] rounded-md p-2 text-sm text-[#B1A4AD] font-light bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300" />
                                </div>

                                <div class="flex mt-8 space-x-4">
                                    <button type="button"
                                        class="px-6 py-2 bg-[#FFF7FC] text-[#91848C] border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="px-6 py-2 bg-[#DB69A2] text-white rounded-md hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">
                                        Submit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

@endsection
