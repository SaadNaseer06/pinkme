@extends('admin.layouts.admin')

@section('title', 'Edit Reviewer')

@section('content')
    <div class="max-w-8xl mx-auto mt-8">

        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-md mb-6" role="alert">
                <h4 class="font-semibold mb-1">Success!</h4>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- Error Message -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-md mb-6" role="alert">
                <h4 class="font-semibold mb-1">Oops! Something went wrong.</h4>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card Start -->
        <div class="bg-[#F3E8EF] rounded-lg shadow-lg p-8">
            <!-- Card Heading -->
            <h3 class="text-xl font-semibold text-[#213430] mb-6">Edit Reviewer Details</h3>

            <form method="POST" action="{{ route('admin.reviewers.update', $reviewer->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Avatar Section -->
                <div class="flex justify-start mb-6">
                    <div class="relative">
                        <div class="w-28 h-28 rounded-full overflow-hidden bg-white flex items-center justify-center">
                            <img id="avatarPreview"
                                src="{{ $reviewer->avatar_url }}"
                                alt="Profile" class="object-cover w-full h-full" />
                        </div>
                        <label for="avatar"
                            class="absolute bottom-[10px] right-[7px] bg-[#DB69A2] text-white rounded-full w-6 h-6 flex items-center justify-center cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </label>
                        <input id="avatar" name="avatar" type="file" class="hidden" />
                    </div>
                </div>

                <!-- Form Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="first_name" class="block text-[#213430] mb-2">First Name</label>
                        <input type="text" id="first_name" name="first_name"
                            value="{{ old('first_name', optional($reviewer->profile)->first_name) }}"
                            class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent" />
                    </div>

                    <div>
                        <label for="last_name" class="block text-[#213430] mb-2">Last Name</label>
                        <input type="text" id="last_name" name="last_name"
                            value="{{ old('last_name', optional($reviewer->profile)->last_name) }}"
                            class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent" />
                    </div>

                    <div>
                        <label for="username" class="block text-[#213430] mb-2">User Name</label>
                        <input type="text" id="username" name="username"
                            value="{{ old('username', optional($reviewer->profile)->username) }}"
                            class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent" />
                    </div>

                    <div>
                        <label for="phone" class="block text-[#213430] mb-2">Contact Number</label>
                        <input type="text" id="phone" name="phone"
                            value="{{ old('phone', optional($reviewer->profile)->phone) }}"
                            class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent" />
                    </div>

                    <div>
                        <label for="email" class="block text-[#213430] mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $reviewer->email) }}"
                            class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent" />
                    </div>

                    <div>
                        <label for="gender" class="block text-[#213430] mb-2">Gender</label>
                        <select id="gender" name="gender"
                            class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent">
                            <option value="">Select Gender</option>
                            <option value="female" @selected(old('gender', optional($reviewer->profile)->gender) == 'female')>Female</option>
                            <option value="male" @selected(old('gender', optional($reviewer->profile)->gender) == 'male')>Male</option>
                            <option value="other" @selected(old('gender', optional($reviewer->profile)->gender) == 'other')>Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="blood_group" class="block text-[#213430] mb-2">Blood Group</label>
                        <select id="blood_group" name="blood_group"
                            class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent">
                            <option value="">Select Blood Group</option>
                            @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                <option value="{{ $bg }}" @selected(old('blood_group', optional($reviewer->profile)->blood_group) == $bg)>{{ $bg }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-[#213430] mb-2">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth"
                            value="{{ old('date_of_birth', optional($reviewer->profile)->date_of_birth ? \Carbon\Carbon::parse($reviewer->profile->date_of_birth)->format('Y-m-d') : null) }}"
                            class="w-full px-4 py-2 rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent" />
                    </div>
                </div>

                <!-- Save and Cancel Buttons -->
                <div class="flex mt-8 space-x-4">
                    <button type="submit" class="px-6 py-2 bg-[#DB69A2] text-white rounded-md hover:bg-pink-600">Save
                        Changes</button>
                    <a href="{{ route('admin.reviewers') }}"
                        class="px-6 py-2 bg-[#FFF7FC] text-[#91848C] border border-[#DCCFD8] rounded-md hover:bg-[#F1E3EC]">Cancel</a>
                </div>
            </form>
        </div>
        <!-- Card End -->
    </div>

    <script>
        // Display selected avatar image preview
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
