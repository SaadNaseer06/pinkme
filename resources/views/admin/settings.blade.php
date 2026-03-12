@extends('admin.layouts.admin')

@section('title', 'Settings')

@section('content')
    @php
        $defaultPrivacyContent = <<<'HTML'
<h3>Privacy Policy</h3>
<p>We value your privacy. This policy explains how we collect, use, and protect your personal information when using our services.</p>
<ul>
    <li>We do not share your data with third parties without your consent.</li>
    <li>We use cookies to improve your experience.</li>
    <li>You can contact us anytime to update or delete your data.</li>
</ul>
HTML;
        $defaultTermsContent = <<<'HTML'
<h3>Terms &amp; Conditions</h3>
<p>By using our website and services, you agree to the following terms and conditions:</p>
<ol>
    <li>You must be at least 18 years old to use our services.</li>
    <li>Do not misuse or attempt to hack our system.</li>
    <li>We reserve the right to update these terms at any time.</li>
</ol>
HTML;
        $privacyContent = old('privacy_policy_content');
        if ($privacyContent === null) {
            $privacyContent = $settings->privacy_policy_content ?? $defaultPrivacyContent;
        }
        $termsContent = old('terms_conditions_content');
        if ($termsContent === null) {
            $termsContent = $settings->terms_conditions_content ?? $defaultTermsContent;
        }
        $privacyLastUpdated = old('privacy_last_updated')
            ?? optional(optional($settings)->privacy_last_updated)->format('Y-m-d')
            ?? now()->format('Y-m-d');
        $termsLastUpdated = old('terms_last_updated')
            ?? optional(optional($settings)->terms_last_updated)->format('Y-m-d')
            ?? now()->format('Y-m-d');
        $activeTab = session('active_tab', request('tab', 'general'));
    @endphp

    {{-- @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif --}}

    <!-- Dashboard Content -->
    <main class="flex-1 p-6">
        <div class="max-w-8xl mx-auto">

            <!-- Navigation Tabs -->
            <div class="flex flex-wrap mb-10">
                <div class="w-full md:w-1/4">
                    <button onclick="showTab('general')" id="general-tab"
                        class="tab-btn w-full bg-[#9E2469] text-white py-4 px-6 font-normal text-center rounded-t-lg md:rounded-tr-none md:rounded-l-lg app-text">
                        General Details
                    </button>
                </div>
                <div class="w-full md:w-1/4">
                    <button onclick="showTab('site')" id="site-tab"
                        class="tab-btn w-full bg-[#F3E8EF] text-[#91848C] py-4 px-6 font-normal text-center app-text">
                        Site Settings
                    </button>
                </div>
                <div class="w-full md:w-1/4">
                    <button onclick="showTab('privacy')" id="privacy-tab"
                        class="tab-btn w-full bg-[#F3E8EF] text-[#91848C] py-4 px-6 font-normal text-center app-text">
                        Privacy Policy
                    </button>
                </div>
                <div class="w-full md:w-1/4">
                    <button onclick="showTab('terms')" id="terms-tab"
                        class="tab-btn w-full bg-[#F3E8EF] text-[#91848C] py-4 px-6 font-normal text-center rounded-b-lg md:rounded-b-none md:rounded-r-lg app-text">
                        Terms & Conditions
                    </button>
                </div>
            </div>

            <!-- Tab Contents -->
            <div id="tabContents">

                <!-- General Details (Admin Profile) -->
                <div id="general" class="tab-content">
                    <div class="bg-[#F3E8EF] rounded-lg p-8">
                        <h2 class="text-xl text-gray-700 font-medium pb-4 border-b border-[#DCCFD8] mb-6 app-main">
                            Admin Profile
                        </h2>
                        <p class="text-sm text-[#91848C] mb-6 app-text">Update your admin account details.</p>

                        @if (session('success'))
                            <div class="mb-6 rounded-lg border-2 border-green-300 bg-green-50 px-4 py-3 text-green-800">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('admin.settings.profile') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block font-light text-md text-[#213430] mb-1 app-text">Full Name</label>
                                    <input type="text" name="full_name"
                                        value="{{ old('full_name', optional($admin->profile)->full_name ?? $admin->email ?? '') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                                        placeholder="Your full name" />
                                    @error('full_name')<p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block font-light text-md text-[#213430] mb-1 app-text">Username</label>
                                    <input type="text" name="username"
                                        value="{{ old('username', optional($admin->profile)->username ?? '') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                                        placeholder="Username (optional)" />
                                    @error('username')<p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block font-light text-md text-[#213430] mb-1 app-text">Email <span class="text-[#9E2469]">*</span></label>
                                    <input type="email" name="email"
                                        value="{{ old('email', $admin->email ?? '') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                                        required />
                                    @error('email')<p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block font-light text-md text-[#213430] mb-1 app-text">Phone</label>
                                    <input type="text" name="phone"
                                        value="{{ old('phone', optional($admin->profile)->phone ?? '') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                                        placeholder="Contact number" />
                                    @error('phone')<p class="text-xs text-[#9E2469] mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit" class="px-6 py-2 bg-[#9E2469] text-white rounded-md shadow">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Site Settings -->
                <div id="site" class="tab-content hidden">
                    <div class="bg-[#F3E8EF] rounded-lg p-8">
                        <h2 class="text-xl text-gray-700 font-medium pb-4 border-b border-[#DCCFD8] mb-6 app-main">
                            General Site Settings
                        </h2>

                        <form action="{{ route('admin.settings.update', ['tab' => 'site']) }}" method="POST">
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
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                                        required />
                                </div>
                                <div>
                                    <label class="block font-light text-md text-[#213430] mb-1 app-text">Twitter URL</label>
                                    <input type="url" name="twitter_url"
                                        value="{{ old('twitter_url', $settings->twitter_url ?? '') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                                        required />
                                </div>
                                <div>
                                    <label class="block font-light text-md text-[#213430] mb-1 app-text">Instagram
                                        URL</label>
                                    <input type="url" name="instagram_url"
                                        value="{{ old('instagram_url', $settings->instagram_url ?? '') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                                        required />
                                </div>
                                <div>
                                    <label class="block font-light text-md text-[#213430] mb-1 app-text">LinkedIn
                                        URL</label>
                                    <input type="url" name="linkedin_url"
                                        value="{{ old('linkedin_url', $settings->linkedin_url ?? '') }}"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text"
                                        required />
                                </div>

                                <!-- About Us -->
                                <div class="md:col-span-2">
                                    <label class="block font-light text-md text-[#213430] mb-1 app-text">About Us</label>
                                    <textarea name="about_us_content" rows="4"
                                        class="w-full px-4 py-2 font-light rounded-md border border-[#DCCFD8] text-[#213430] bg-transparent focus:outline-none focus:ring-2 focus:ring-pink-300 app-text" required>{{ old('about_us_content', $settings->about_us_content ?? '') }}</textarea>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                    class="px-6 py-2 bg-[#9E2469] text-white rounded-md shadow">Save</button>
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
                                    class="w-full px-4 py-2 rounded-md border border-[#DCCFD8]" required>{!! $privacyContent !!}</textarea>
                            </div>

                            <div class="mt-4">
                                <label class="block font-light text-md text-[#213430] mb-1 app-text">Last Updated</label>
                                <input type="date" name="privacy_last_updated"
                                    value="{{ $privacyLastUpdated }}"
                                    class="w-full px-4 py-2 rounded-md border border-[#DCCFD8]" required />
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                    class="px-6 py-2 bg-[#9E2469] text-white rounded-md shadow">Save</button>
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
                                    class="w-full px-4 py-2 rounded-md border border-[#DCCFD8]" required>{!! $termsContent !!}</textarea>
                            </div>

                            <div class="mt-4">
                                <label class="block font-light text-md text-[#213430] mb-1 app-text">Last Updated</label>
                                <input type="date" name="terms_last_updated"
                                    value="{{ $termsLastUpdated }}"
                                    class="w-full px-4 py-2 rounded-md border border-[#DCCFD8]" required />
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                    class="px-6 py-2 bg-[#9E2469] text-white rounded-md shadow">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const baseUploadUrl = @json(route('admin.settings.upload'));

        const getEditorConfig = () => ({
            toolbar: {
                items: [
                    'heading',
                    '|',
                    'bold',
                    'italic',
                    'link',
                    'bulletedList',
                    'numberedList',
                    'blockQuote',
                    '|',
                    'insertTable',
                    'imageUpload',
                    'undo',
                    'redo'
                ]
            },
            // Use the CKFinder adapter shipped with the Classic build.
            // Pass the CSRF token via query string so Laravel's CSRF middleware accepts the request.
            ckfinder: {
                uploadUrl: `${baseUploadUrl}?_token=${encodeURIComponent(csrfToken)}`
            },
            image: {
                toolbar: [
                    'imageStyle:inline',
                    'imageStyle:block',
                    'imageStyle:side',
                    '|',
                    'toggleImageCaption',
                    'imageTextAlternative'
                ]
            },
            table: {
                contentToolbar: [
                    'tableColumn',
                    'tableRow',
                    'mergeTableCells'
                ]
            },
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                ]
            }
        });

        ['#privacy_editor', '#terms_editor'].forEach((selector) => {
            const element = document.querySelector(selector);
            if (!element) {
                return;
            }

            ClassicEditor
                .create(element, getEditorConfig())
                .catch(error => {
                    console.error('Editor initialization error', error);
                });
        });
    </script>

    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('bg-[#9E2469]', 'text-white');
                el.classList.add('bg-[#F3E8EF]', 'text-[#91848C]');
            });

            document.getElementById(tabId).classList.remove('hidden');
            document.getElementById(tabId + '-tab').classList.remove('bg-[#F3E8EF]', 'text-[#91848C]');
            document.getElementById(tabId + '-tab').classList.add('bg-[#9E2469]', 'text-white');
        }

        const defaultTab = @json($activeTab ?? 'general');
        showTab(defaultTab || 'general');
    </script>

@endsection
