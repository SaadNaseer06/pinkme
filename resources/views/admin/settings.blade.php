@extends('admin.layouts.admin')

@section('title', 'Settings')

@section('content')

    {{-- @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif --}}

    <!-- Dashboard Content -->
    <main class="flex-1 p-6">
        <div class="max-w-8xl mx-auto">

            <!-- Navigation Tabs -->
            <div class="flex flex-wrap mb-10">
                <div class="w-full md:w-1/3">
                    <button onclick="showTab('general')" id="general-tab"
                        class="tab-btn w-full bg-[#DB69A2] text-white py-4 px-6 font-normal text-center rounded-t-lg md:rounded-tr-none md:rounded-l-lg app-text">
                        General Details
                    </button>
                </div>
                <div class="w-full md:w-1/3">
                    <button onclick="showTab('privacy')" id="privacy-tab"
                        class="tab-btn w-full bg-[#F3E8EF] text-[#91848C] py-4 px-6 font-normal text-center app-text">
                        Privacy Policy
                    </button>
                </div>
                <div class="w-full md:w-1/3">
                    <button onclick="showTab('terms')" id="terms-tab"
                        class="tab-btn w-full bg-[#F3E8EF] text-[#91848C] py-4 px-6 font-normal text-center rounded-b-lg md:rounded-b-none md:rounded-r-lg app-text">
                        Terms & Conditions
                    </button>
                </div>
            </div>

            <!-- Tab Contents -->
            <div id="tabContents">

                <!-- General Details -->
                <div id="general" class="tab-content">
                    <div class="bg-[#F3E8EF] rounded-lg p-8">
                        <h2 class="text-xl text-gray-700 font-medium pb-4 border-b border-[#DCCFD8] mb-6 app-main">
                            General Site Settings
                        </h2>

                        <form action="{{ route('admin.settings.update', ['tab' => 'general']) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <!-- Site Name -->
                                <div>
                                    <label class="block font-light text-md text-[#213430] mb-1 app-text">Site Name</label>
                                    <input type="text" name="site_name"
                                        value="{{ old('site_name', $settings->site_name ?? '') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                                        required />
                                </div>

                                <!-- Site Description -->
                                <div>
                                    <label class="block font-light text-md text-[#213430] mb-1 app-text">Site
                                        Description</label>
                                    <textarea name="site_description" rows="3"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text">{{ old('site_description', $settings->site_description ?? '') }}</textarea>
                                </div>

                                <!-- Contact Email -->
                                <div>
                                    <label class="block font-light text-md text-[#213430] mb-1 app-text">Contact
                                        Email</label>
                                    <input type="email" name="contact_email"
                                        value="{{ old('contact_email', $settings->contact_email ?? '') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                                        required />
                                </div>

                                <!-- Contact Phone -->
                                <div>
                                    <label class="block font-light text-md text-[#213430] mb-1 app-text">Contact
                                        Phone</label>
                                    <input type="text" name="contact_phone"
                                        value="{{ old('contact_phone', $settings->contact_phone ?? '') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                                        required />
                                </div>

                                <!-- Address -->
                                <div class="md:col-span-2">
                                    <label class="block font-light text-md text-[#213430] mb-1 app-text">Address</label>
                                    <input type="text" name="address"
                                        value="{{ old('address', $settings->address ?? '') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                                        required />
                                </div>

                                <!-- Social Links -->
                                <div>
                                    <label class="block font-light text-md text-[#213430] mb-1 app-text">Facebook
                                        URL</label>
                                    <input type="url" name="facebook_url"
                                        value="{{ old('facebook_url', $settings->facebook_url ?? '') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430]"
                                        required />
                                </div>
                                <div>
                                    <label class="block font-light text-md text-[#213430] mb-1 app-text">Twitter URL</label>
                                    <input type="url" name="twitter_url"
                                        value="{{ old('twitter_url', $settings->twitter_url ?? '') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430]"
                                        required />
                                </div>
                                <div>
                                    <label class="block font-light text-md text-[#213430] mb-1 app-text">Instagram
                                        URL</label>
                                    <input type="url" name="instagram_url"
                                        value="{{ old('instagram_url', $settings->instagram_url ?? '') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430]"
                                        required />
                                </div>
                                <div>
                                    <label class="block font-light text-md text-[#213430] mb-1 app-text">LinkedIn
                                        URL</label>
                                    <input type="url" name="linkedin_url"
                                        value="{{ old('linkedin_url', $settings->linkedin_url ?? '') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430]"
                                        required />
                                </div>

                                <!-- About Us -->
                                <div class="md:col-span-2">
                                    <label class="block font-light text-md text-[#213430] mb-1 app-text">About Us</label>
                                    <textarea name="about_us_content" rows="4"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430]" required>{{ old('about_us_content', $settings->about_us_content ?? '') }}</textarea>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                    class="px-6 py-2 bg-[#DB69A2] text-white rounded-md shadow">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Privacy Policy -->
                <div id="privacy" class="tab-content hidden">
                    <div class="bg-[#F3E8EF] rounded-lg p-8">
                        <h2 class="text-xl text-gray-700 font-medium pb-4 border-b border-[#DCCFD8] mb-6 app-main">
                            Privacy Policy
                        </h2>

                        <form action="{{ route('admin.settings.update', ['tab' => 'privacy']) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block font-light text-md text-[#213430] mb-1 app-text">Privacy Policy
                                    Content</label>
                                <textarea id="privacy_editor" name="privacy_policy_content" rows="8"
                                    class="w-full px-4 py-2 rounded-md border border-[#DCCFD8]" required>
@if (!empty(old('privacy_policy_content', $settings->privacy_policy_content ?? '')))
{{ old('privacy_policy_content', $settings->privacy_policy_content ?? '') }}
@else
<h3>Privacy Policy</h3>
    <p>We value your privacy. This policy explains how we collect, use, and protect your personal information when using our services.</p>
    <ul>
        <li>We do not share your data with third parties without your consent.</li>
        <li>We use cookies to improve your experience.</li>
        <li>You can contact us anytime to update or delete your data.</li>
    </ul>
@endif
</textarea>
                            </div>

                            <div class="mt-4">
                                <label class="block font-light text-md text-[#213430] mb-1 app-text">Last Updated</label>
                                <input type="date" name="privacy_last_updated"
                                    value="{{ old('privacy_last_updated', $settings->privacy_last_updated ?? '') }}"
                                    class="w-full px-4 py-2 rounded-md border border-[#DCCFD8]" required />
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                    class="px-6 py-2 bg-[#DB69A2] text-white rounded-md shadow">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Terms & Conditions -->
                <div id="terms" class="tab-content hidden">
                    <div class="bg-[#F3E8EF] rounded-lg p-8">
                        <h2 class="text-xl text-gray-700 font-medium pb-4 border-b border-[#DCCFD8] mb-6 app-main">
                            Terms & Conditions
                        </h2>

                        <form action="{{ route('admin.settings.update', ['tab' => 'terms']) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block font-light text-md text-[#213430] mb-1 app-text">Terms & Conditions
                                    Content</label>
                                <textarea id="terms_editor" name="terms_conditions_content" rows="8"
                                    class="w-full px-4 py-2 rounded-md border border-[#DCCFD8]" required>
@if (!empty(old('terms_conditions_content', $settings->terms_conditions_content ?? '')))
{{ old('terms_conditions_content', $settings->terms_conditions_content ?? '') }}
@else
<h3>Terms & Conditions</h3>
    <p>By using our website and services, you agree to the following terms and conditions:</p>
    <ol>
        <li>You must be at least 18 years old to use our services.</li>
        <li>Do not misuse or attempt to hack our system.</li>
        <li>We reserve the right to update these terms at any time.</li>
    </ol>
@endif
</textarea>
                            </div>

                            <div class="mt-4">
                                <label class="block font-light text-md text-[#213430] mb-1 app-text">Last Updated</label>
                                <input type="date" name="terms_last_updated"
                                    value="{{ old('terms_last_updated', $settings->terms_last_updated ?? '') }}"
                                    class="w-full px-4 py-2 rounded-md border border-[#DCCFD8]" required />
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                    class="px-6 py-2 bg-[#DB69A2] text-white rounded-md shadow">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#privacy_editor'))
            .catch(error => {
                console.error(error);
            });

        ClassicEditor
            .create(document.querySelector('#terms_editor'))
            .catch(error => {
                console.error(error);
            });
    </script>

    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('bg-[#DB69A2]', 'text-white');
                el.classList.add('bg-[#F3E8EF]', 'text-[#91848C]');
            });

            document.getElementById(tabId).classList.remove('hidden');
            document.getElementById(tabId + '-tab').classList.remove('bg-[#F3E8EF]', 'text-[#91848C]');
            document.getElementById(tabId + '-tab').classList.add('bg-[#DB69A2]', 'text-white');
        }

        // Show general tab by default
        showTab('general');
    </script>

@endsection
