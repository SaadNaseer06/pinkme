<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PINK "ME" - Authentication</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Tailwind Config for Poppins -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ["Poppins", "sans-serif"],
                    },
                    colors: {
                        primary: {
                            50: '#fdf2f8',
                            100: '#fce7f3',
                            200: '#fbcfe8',
                            300: '#f9a8d4',
                            400: '#f472b6',
                            500: '#ec4899',
                            600: '#db2777',
                            700: '#be185d',
                            800: '#9d174d',
                            900: '#831843',
                        }
                    }
                },
            },
        };
    </script>

    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />

    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />

    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .form-input {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .form-input:focus {
            border-color: #DB69A2;
            box-shadow: 0 0 0 3px rgba(219, 105, 162, 0.1);
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(90deg, #DB69A2 0%, #FE6EB6 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            color: #fff;
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(219, 39, 119, 0.3);
            background: #FE6EB6;
            color: #fff;
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .tab-btn {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .tab-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .tab-btn:hover::before {
            left: 100%;
        }

        .floating-label {
            position: absolute;
            pointer-events: none;
            left: 1rem;
            top: 1rem;
            transition: 0.2s ease all;
            color: rgb(55 65 81 / var(--tw-text-opacity, 1));
        }

        .form-input:focus+.floating-label,
        .form-input:not(:placeholder-shown)+.floating-label {
            top: 0.25rem;
            font-size: 0.75rem;
            color: rgb(55 65 81 / var(--tw-text-opacity, 1));
        }

        /* Mobile-specific improvements */
        @media (max-width: 1023px) {
            .tab-form-img {
                display: none !important;
            }

            .tab-form {
                width: 100% !important;
            }

            .tab-logo {
                height: 70px !important;
            }

            .tab-bg {
                background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%) !important;
            }

            .tab-shadow {
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
                padding: 1.5rem !important;
                margin-top: 1rem !important;
            }

            .mobile-container {
                padding: 0.75rem !important;
            }

            .mobile-form-section {
                padding: 1.25rem 1rem !important;
            }

            .mobile-hero {
                min-height: 180px !important;
                border-radius: 20px !important;
                margin-bottom: 1.5rem !important;
            }

            .mobile-toggle {
                margin-bottom: 1.5rem !important;
                padding: 0.4rem !important;
            }

            .mobile-tab-btn {
                padding: 0.75rem 0.5rem !important;
                font-size: 0.875rem !important;
            }

            .mobile-h1 {
                font-size: 1.5rem !important;
                line-height: 1.3 !important;
                margin-bottom: 0.5rem !important;
            }

            .mobile-h2 {
                font-size: 0.875rem !important;
                line-height: 1.4 !important;
            }

            .mobile-h3 {
                font-size: 0.875rem !important;
            }

            .mobile-input {
                padding: 0.875rem !important;
                font-size: 0.875rem !important;
            }

            .mobile-btn {
                padding: 0.875rem !important;
                font-size: 1rem !important;
            }

            .mobile-grid {
                gap: 0.75rem !important;
            }

            .mobile-space-y {
                gap: 1.25rem !important;
            }

            .mobile-checkbox {
                font-size: 0.75rem !important;
            }

            .mobile-hero__copy {
                padding: 1.5rem !important;
            }

            .mobile-hero-title {
                font-size: 1.25rem !important;
                line-height: 1.4 !important;
            }

            .mobile-hero-subtitle {
                font-size: 0.75rem !important;
                line-height: 1.4 !important;
            }

            .mobile-hero-badge {
                font-size: 0.7rem !important;
                padding: 0.4rem 0.75rem !important;
                margin-bottom: 0.75rem !important;
            }
        }

        @media (max-width: 640px) {
            .mobile-container {
                padding: 0.5rem !important;
            }

            .mobile-form-section {
                padding: 1rem 0.75rem !important;
            }

            .mobile-h1 {
                font-size: 1.375rem !important;
            }

            .mobile-hero {
                min-height: 160px !important;
                border-radius: 16px !important;
            }

            .mobile-hero__copy {
                padding: 1.25rem !important;
            }

            .mobile-hero-title {
                font-size: 1.125rem !important;
            }
        }

        @media (min-width: 1024px) and (max-width: 1668px) {
            .tab-shadow {
                margin-top: 2rem !important;
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-in {
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(-100%);
            }

            to {
                transform: translateX(0);
            }
        }

        .nav-tab {
            position: relative;
            overflow: hidden;
        }

        .nav-tab::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .nav-tab:hover::before {
            left: 100%;
        }

        .nav-tab.active {
            background: #fff;
            color: #DB69A2;
            border-color: #DB69A2;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            background: #DB69A2;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100%;
            padding: 0 0.5rem;
        }

        .sidebar-divider {
            position: absolute;
            right: 0;
            top: 0;
            width: 2px;
            height: 100%;
            background: rgba(219, 105, 162, 0.15);
            box-shadow: 2px 0 8px 0 rgba(219, 105, 162, 0.04);
            z-index: 30;
        }

        .sidebar-tab {
            width: 140px;
            margin: 1rem 0;
            padding: 1rem 0;
            border-radius: 9999px;
            font-size: 1.25rem;
            font-weight: 600;
            color: #fff;
            background: rgba(219, 105, 162, 0.12);
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.25s;
            outline: none;
            box-shadow: 0 2px 8px 0 rgba(219, 105, 162, 0.04);
        }

        .sidebar-tab.active,
        .sidebar-tab:focus {
            background: #fff;
            color: #DB69A2;
            border-color: #DB69A2;
            box-shadow: 0 4px 16px 0 rgba(219, 105, 162, 0.10);
        }

        .sidebar-tab:not(.active):hover {
            background: rgba(219, 105, 162, 0.22);
            color: #fff;
        }

        .mobile-toggle {
            display: flex;
            gap: 0.75rem;
            background: #F3E8EF;
            padding: 0.5rem;
            border-radius: 9999px;
            box-shadow: 0 14px 32px rgba(219, 105, 162, 0.18);
            backdrop-filter: blur(18px);
        }

        .mobile-tab-btn {
            flex: 1;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            font-weight: 600;
            padding: 0.85rem 1rem;
            border-radius: 9999px;
            border: 2px solid transparent;
            background: rgba(255, 255, 255, 0.55);
            color: #91848C;
            transition: all 0.25s ease;
            font-size: 0.95rem;
        }

        .mobile-tab-btn.active {
            background: linear-gradient(90deg, #DB69A2 0%, #FE6EB6 100%);
            color: #fff;
            border-color: rgba(255, 255, 255, 0.25);
            box-shadow: 0 16px 32px rgba(219, 105, 162, 0.35);
        }

        .mobile-hero {
            position: relative;
            min-height: 200px;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 26px 42px rgba(219, 105, 162, 0.22);
        }

        .mobile-hero img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .mobile-hero::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(219, 105, 162, 0.85) 0%, rgba(254, 110, 182, 0.6) 100%);
        }

        .mobile-hero__copy {
            position: relative;
            z-index: 10;
            color: #fff;
        }

        .auth-shell {
            background: rgba(255, 255, 255, 0.65);
            border-radius: 32px;
            padding: 1.75rem;
            box-shadow: 0 35px 70px rgba(66, 28, 49, 0.12);
        }

        @media (max-width: 1023px) {
            .auth-shell {
                padding: 1.5rem 1.25rem 2rem;
                border-radius: 24px;
                box-shadow: 0 24px 60px rgba(66, 28, 49, 0.15);
            }
        }

        .form-wrapper {
            width: 100%;
            max-width: 520px;
        }

        .tab-btn.bg-primary-600 {
            background: #DB69A2 !important;
            color: #fff !important;
        }

        .tab-btn.text-gray-600 {
            background: #F3E8EF !important;
            color: #91848C !important;
        }

        .tab-btn.bg-primary-600:hover,
        .tab-btn.text-gray-600:hover {
            opacity: 0.5;
            color: #fff !important;
        }

        .nav-tab.active {
            background: #fff;
            color: #DB69A2;
            border-color: #DB69A2;
        }

        .nav-tab {
            border: 2px solid transparent;
        }

        .floating-label {
            color: rgb(55 65 81 / var(--tw-text-opacity, 1));
        }

        .form-checkbox:checked {
            accent-color: #DB69A2;
        }

        .sidebar-divider {
            background: rgba(219, 105, 162, 0.15);
            box-shadow: 2px 0 8px 0 rgba(219, 105, 162, 0.04);
        }

        body {
            background: linear-gradient(135deg, #FAF5F8 0%, #F3E8EF 100%);
        }
    </style>
</head>

<body
    class="font-sans bg-gradient-to-br from-pink-50 via-white to-purple-50 min-h-screen flex items-center justify-center p-4 mobile-container">
    <div class="container mx-auto max-w-6xl px-2 sm:px-4">
        <div class="glass-effect rounded-[32px] shadow-2xl overflow-hidden animate-fade-in">
            <div class="flex flex-col md:flex-row min-h-[720px]">
                <!-- Left Panel with Dynamic Background and Tabs -->
                <div class="w-1/3 relative sidebar tab-form-img">
                    <!-- Background image -->
                    <img id="signup-bg" src="{{ asset('public/images/Patient Signup.png') }}" alt="Signup Background"
                        class="absolute inset-0 w-full h-full object-cover z-0" />
                    <img id="login-bg" src="{{ asset('public/images/Patient Login.png') }}" alt="Login Background"
                        class="absolute inset-0 w-full h-full object-cover z-0 hidden" />
                    <div class="sidebar-divider"></div>
                    <div class="relative z-20 flex flex-col h-full justify-center items-center space-y-8">
                        <button id="sidebar-signup" class="sidebar-tab" onclick="toggleForm('signup')">SIGN UP</button>
                        <button id="sidebar-login" class="sidebar-tab" onclick="toggleForm('login')">LOGIN</button>
                    </div>
                </div>

                <!-- Right Panel - Forms -->
                <div class="w-full md:w-2/3 p-4 md:p-12 flex items-center justify-center tab-form bg-white/40 md:bg-transparent"
                    id="form-container">
                    <!-- Mobile hero -->
                    <div class="w-full md:hidden">
                        <div class="mobile-hero mb-6">
                            <img id="mobile-signup-bg" src="{{ asset('public/images/Patient Signup.png') }}"
                                alt="Sign up illustration">
                            <img id="mobile-login-bg" src="{{ asset('public/images/Patient Login.png') }}"
                                alt="Login illustration" class="hidden">
                            <div class="mobile-hero__copy px-6 py-8">
                                <p id="mobile-hero-badge"
                                    class="inline-flex items-center px-4 py-1.5 rounded-full bg-white/20 text-sm uppercase tracking-wide mb-3 mobile-hero-badge">
                                    Join the community
                                </p>
                                <h2 id="mobile-hero-title"
                                    class="text-2xl font-semibold leading-snug mobile-hero-title">
                                    Start your healing journey with Pink "ME"
                                </h2>
                                <p id="mobile-hero-subtitle" class="text-sm mt-2 text-white/80 mobile-hero-subtitle">
                                    Sign up to get personalised support, funding opportunities, and caring professionals
                                    in your corner.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile toggle buttons -->
                    <div class="w-full mb-8 md:hidden">
                        <div class="mobile-toggle">
                            <button id="mobile-signup" type="button" class="mobile-tab-btn"
                                onclick="toggleForm('signup')">Sign Up</button>
                            <button id="mobile-login" type="button" class="mobile-tab-btn"
                                onclick="toggleForm('login')">Login</button>
                        </div>
                    </div>

                    <!-- Signup Form -->
                    <div id="signup-form" class="form-wrapper auth-shell animate-fade-in mobile-form-section">
                        <div class="text-center mb-8">
                            <div class="flex justify-center mb-4">
                                <img src="{{ asset('public/images/logo.png') }}" alt="Logo" class="h-16 tab-logo" />
                            </div>
                            <h2 class="text-3xl font-bold text-gray-800 mb-2 mobile-h1">
                                Welcome to PINK "ME"
                            </h2>
                            <p class="text-gray-600 text-lg mobile-h2">Create your account to get started</p>
                        </div>

                        <!-- Role Selection Tabs -->
                        <div class="flex mb-8 bg-gray-100 rounded-xl p-1">
                            <button onclick="showTab('personal')"
                                class="tab-btn flex-1 py-3 px-4 font-medium text-center rounded-lg transition-all duration-300 mobile-h2 bg-primary-600 text-white">
                                Patient
                            </button>
                            <button onclick="showTab('company')"
                                class="tab-btn flex-1 py-3 px-4 font-medium text-center rounded-lg transition-all duration-300 mobile-h2 text-gray-600 hover:text-gray-800">
                                Sponsor
                            </button>
                        </div>

                        <!-- Tab Contents -->
                        <div id="tabContents">
                            <!-- Patient Signup Form -->
                            <div id="personal" class="tab-content animate-fade-in">
                                <form method="POST" action="{{ route('register') }}" class="space-y-6 mobile-space-y">
                                    @csrf
                                    <input type="hidden" name="role_id" value="2" />
                                    <div class="relative">
                                        <input type="text" name="full_name" placeholder=" "
                                            value="{{ old('full_name') }}" required
                                            class="form-input w-full px-4 py-4 rounded-xl bg-gray-50 outline-none mobile-h3 mobile-input" />
                                        <label class="floating-label">Full Name</label>
                                        @error('full_name')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mobile-grid">
                                        <div class="relative">
                                            <input type="email" name="email" placeholder=" "
                                                value="{{ old('email') }}" required
                                                class="form-input w-full px-4 py-4 rounded-xl bg-gray-50 outline-none mobile-h3 mobile-input" />
                                            <label class="floating-label">Email Address</label>
                                            @error('email')
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="relative">
                                            <input type="tel" name="phone" placeholder=" "
                                                value="{{ old('phone') }}" required
                                                class="form-input w-full px-4 py-4 rounded-xl bg-gray-50 outline-none mobile-h3 mobile-input" />
                                            <label class="floating-label">Phone Number</label>
                                            @error('phone')
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mobile-grid">
                                        <div class="relative">
                                            <input type="date" name="date_of_birth"
                                                value="{{ old('date_of_birth') }}"
                                                class="form-input w-full px-4 py-4 rounded-xl bg-gray-50 text-gray-700 outline-none mobile-h3 mobile-input" />
                                            <label class="floating-label">Date of Birth</label>
                                            @error('date_of_birth')
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="relative">
                                            <select name="gender"
                                                class="form-input appearance-none w-full px-4 py-4 rounded-xl bg-gray-50 text-gray-700 outline-none pr-10 mobile-h3 mobile-input">
                                                <option value="">Select Gender</option>
                                                <option value="female" @selected(old('gender') == 'female')>Female</option>
                                                <option value="male" @selected(old('gender') == 'male')>Male</option>
                                                <option value="other" @selected(old('gender') == 'other')>Other</option>
                                            </select>
                                            <i
                                                class="fas fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                            @error('gender')
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="relative">
                                        <input type="password" name="password" placeholder=" " required
                                            class="password-input form-input w-full px-4 py-4 rounded-xl bg-gray-50 outline-none mobile-h3 mobile-input" />
                                        <label class="floating-label">Password</label>
                                        <button type="button"
                                            class="absolute right-3 top-4 text-gray-400 hover:text-gray-600 toggle-password">
                                            <i class="far fa-eye"></i>
                                        </button>
                                        @error('password')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="relative">
                                        <input type="password" name="password_confirmation" placeholder=" " required
                                            class="password-input form-input w-full px-4 py-4 rounded-xl bg-gray-50 outline-none mobile-h3 mobile-input" />
                                        <label class="floating-label">Confirm Password</label>
                                        <button type="button"
                                            class="absolute right-3 top-4 text-gray-400 hover:text-gray-600 toggle-password">
                                            <i class="far fa-eye"></i>
                                        </button>
                                    </div>

                                    <div class="flex items-start space-x-3">
                                        <input type="checkbox" name="terms" required
                                            class="mt-1 h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500" />
                                        <label class="text-sm text-gray-600 mobile-checkbox">
                                            I agree to the
                                            <a href="{{ route('policy.privacy') }}"
                                                class="text-primary-600 hover:text-primary-700 underline font-medium"
                                                target="_blank">Terms
                                                of Service</a> and
                                            <a href="{{ route('policy.terms') }}"
                                                class="text-primary-600 hover:text-primary-700 underline font-medium"
                                                target="_blank">Privacy
                                                Policy</a>
                                        </label>
                                    </div>
                                    @error('terms')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror

                                    <button type="submit"
                                        class="btn-primary w-full text-lg text-white py-4 rounded-xl font-semibold transition-all duration-300 mobile-btn">
                                        Create Account
                                    </button>
                                </form>
                            </div>

                            <!-- Sponsor Signup Form -->
                            <div id="company" class="tab-content hidden animate-fade-in">
                                <!-- Individual Sponsor Form -->
                                <form method="POST" action="{{ route('register') }}"
                                    class="space-y-6 mobile-space-y">
                                    @csrf
                                    <input type="hidden" name="role_id" value="3" />
                                    <input type="hidden" name="sponsor_mode" value="individual" />


                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mobile-grid">
                                        <div class="relative">
                                            <select name="sponsor_mode" onchange="toggleSponsorType(this)"
                                                class="sponsor-type-select form-input appearance-none w-full px-4 py-4 rounded-xl bg-gray-50 text-gray-700 outline-none pr-10 mobile-h3 mobile-input">
                                                <option value="individual">Individual Sponsor</option>
                                                <option value="company">Company Sponsor</option>
                                            </select>
                                            <i
                                                class="fas fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                        </div>
                                        <div class="relative">
                                            <input type="text" name="full_name" placeholder=" "
                                                value="{{ old('full_name') }}" required
                                                class="form-input w-full px-4 py-4 rounded-xl bg-gray-50 outline-none mobile-h3 mobile-input" />
                                            <label class="floating-label">Full Name</label>
                                            @error('full_name')
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mobile-grid">
                                        <div class="relative">
                                            <input type="email" name="email" placeholder=" "
                                                value="{{ old('email') }}" required
                                                class="form-input w-full px-4 py-4 rounded-xl bg-gray-50 outline-none mobile-h3 mobile-input" />
                                            <label class="floating-label">Email Address</label>
                                        </div>
                                        <div class="relative">
                                            <input type="tel" name="phone" placeholder=" "
                                                value="{{ old('phone') }}" required
                                                class="form-input w-full px-4 py-4 rounded-xl bg-gray-50 outline-none mobile-h3 mobile-input" />
                                            <label class="floating-label">Phone Number</label>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mobile-grid">
                                        <div class="relative">
                                            <select name="gender"
                                                class="form-input appearance-none w-full px-4 py-4 rounded-xl bg-gray-50 text-gray-700 outline-none pr-10 mobile-h3 mobile-input">
                                                <option value="">Select Gender</option>
                                                <option value="female" @selected(old('gender') == 'female')>Female</option>
                                                <option value="male" @selected(old('gender') == 'male')>Male</option>
                                                <option value="other" @selected(old('gender') == 'other')>Other</option>
                                            </select>
                                            <i
                                                class="fas fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                        </div>
                                        <div class="relative">
                                            <select name="sponsor_type"
                                                class="form-input appearance-none w-full px-4 py-4 rounded-xl bg-gray-50 text-gray-700 outline-none pr-10 mobile-h3 mobile-input">
                                                <option value="">Sponsor Type</option>
                                                <option value="funding" @selected(old('sponsor_type') == 'funding')>Funding</option>
                                                <option value="donation" @selected(old('sponsor_type') == 'donation')>Donation</option>
                                            </select>
                                            <i
                                                class="fas fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                        </div>
                                    </div>

                                    <div class="relative">
                                        <input type="password" name="password" placeholder=" " required
                                            class="password-input form-input w-full px-4 py-4 rounded-xl bg-gray-50 outline-none mobile-h3 mobile-input" />
                                        <label class="floating-label">Password</label>
                                        <button type="button"
                                            class="absolute right-3 top-4 text-gray-400 hover:text-gray-600 toggle-password">
                                            <i class="far fa-eye"></i>
                                        </button>
                                    </div>

                                    <div class="relative">
                                        <input type="password" name="password_confirmation" placeholder=" " required
                                            class="password-input form-input w-full px-4 py-4 rounded-xl bg-gray-50 outline-none mobile-h3 mobile-input" />
                                        <label class="floating-label">Confirm Password</label>
                                        <button type="button"
                                            class="absolute right-3 top-4 text-gray-400 hover:text-gray-600 toggle-password">
                                            <i class="far fa-eye"></i>
                                        </button>
                                    </div>

                                    <div class="flex items-start space-x-3">
                                        <input type="checkbox" name="terms" required
                                            class="mt-1 h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500" />
                                        <label class="text-sm text-gray-600 mobile-checkbox">
                                            I agree to the
                                            <a href="{{ route('policy.terms') }}"
                                                class="text-primary-600 hover:text-primary-700 underline font-medium">Terms
                                                of Service</a> and
                                            <a href="{{ route('policy.privacy') }}"
                                                class="text-primary-600 hover:text-primary-700 underline font-medium">Privacy
                                                Policy</a>
                                        </label>
                                    </div>

                                    <button type="submit"
                                        class="btn-primary w-full text-lg text-white py-4 rounded-xl font-semibold transition-all duration-300 mobile-btn">
                                        Create Sponsor Account
                                    </button>
                                </form>

                                <!-- Company Sponsor Form -->
                                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data"
                                    class="space-y-6 mobile-space-y hidden" id="companySponsorForm">
                                    @csrf
                                    <input type="hidden" name="role_id" value="3" />
                                    <input type="hidden" name="sponsor_mode" value="company" />

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mobile-grid">
                                        <div class="relative">
                                            <select name="sponsor_mode" onchange="toggleSponsorType(this)"
                                                class="sponsor-type-select form-input appearance-none w-full px-4 py-4 rounded-xl bg-gray-50 text-gray-700 outline-none pr-10 mobile-h3 mobile-input">
                                                <option value="individual">Individual Sponsor</option>
                                                <option value="company" selected>Company Sponsor</option>
                                            </select>
                                            <i
                                                class="fas fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                        </div>
                                        <div class="relative">
                                            <input type="text" name="company_name" placeholder=" "
                                                value="{{ old('company_name') }}" required
                                                class="form-input w-full px-4 py-4 rounded-xl bg-gray-50 outline-none mobile-h3 mobile-input" />
                                            <label class="floating-label">Company Name</label>
                                            @error('company_name')
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mobile-grid">
                                        <div class="relative">
                                            <input type="email" name="company_email" placeholder=" "
                                                value="{{ old('company_email') }}" required
                                                class="form-input w-full px-4 py-4 rounded-xl bg-gray-50 outline-none mobile-h3 mobile-input" />
                                            <label class="floating-label">Company Email</label>
                                            @error('company_email')
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="relative">
                                            <input type="tel" name="company_phone" placeholder=" "
                                                value="{{ old('company_phone') }}" required
                                                class="form-input w-full px-4 py-4 rounded-xl bg-gray-50 outline-none mobile-h3 mobile-input" />
                                            <label class="floating-label">Company Phone</label>
                                            @error('company_phone')
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mobile-grid">
                                        <div class="relative">
                                            <select name="company_type"
                                                class="form-input appearance-none w-full px-4 py-4 rounded-xl bg-gray-50 text-gray-700 outline-none pr-10 mobile-h3 mobile-input"
                                                required>
                                                <option value="">Company Type</option>
                                                <option value="private" @selected(old('company_type') == 'private')>Private Limited
                                                </option>
                                                <option value="public" @selected(old('company_type') == 'public')>Public Limited
                                                </option>
                                                <option value="ngo" @selected(old('company_type') == 'ngo')>Non-Profit
                                                    Organization</option>
                                            </select>
                                            <i
                                                class="fas fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                            @error('company_type')
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="relative">
                                            <input type="text" name="registration_number" placeholder=" "
                                                value="{{ old('registration_number') }}" required
                                                class="form-input w-full px-4 py-4 rounded-xl bg-gray-50 outline-none mobile-h3 mobile-input" />
                                            <label class="floating-label">Registration Number</label>
                                            @error('registration_number')
                                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="relative">
                                        <input type="file" name="logo" accept="image/*" id="logoFileInput"
                                            class="opacity-0 absolute inset-0 z-50 cursor-pointer" />
                                        <div
                                            class="flex items-center w-full font-light rounded-xl bg-gray-50 text-gray-500 pointer-events-none border-2 border-dashed border-gray-300 hover:border-primary-400 transition-colors">
                                            <div class="bg-gray-100 px-4 py-4 rounded-l-xl text-gray-600 mobile-h3">
                                                <i class="fas fa-upload mr-2"></i>Upload Logo
                                            </div>
                                            <div class="ml-3 truncate" id="fileNameDisplay">No file chosen</div>
                                        </div>
                                        @error('logo')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="relative">
                                        <input type="password" name="password" placeholder=" " required
                                            class="password-input form-input w-full px-4 py-4 rounded-xl bg-gray-50 outline-none mobile-h3 mobile-input" />
                                        <label class="floating-label">Password</label>
                                        <button type="button"
                                            class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 toggle-password">
                                            <i class="far fa-eye"></i>
                                        </button>
                                        @error('password')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="relative">
                                        <input type="password" name="password_confirmation" placeholder=" " required
                                            class="password-input form-input w-full px-4 py-4 rounded-xl bg-gray-50 outline-none mobile-h3 mobile-input" />
                                        <label class="floating-label">Confirm Password</label>
                                        <button type="button"
                                            class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 toggle-password">
                                            <i class="far fa-eye"></i>
                                        </button>
                                    </div>

                                    <div class="flex items-start space-x-3">
                                        <input type="checkbox" name="terms" required
                                            class="mt-1 h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500" />
                                        <label class="text-sm text-gray-600 mobile-checkbox">
                                            I agree to the
                                            <a href="#"
                                                class="text-primary-600 hover:text-primary-700 underline font-medium">Terms
                                                of Service</a> and
                                            <a href="#"
                                                class="text-primary-600 hover:text-primary-700 underline font-medium">Privacy
                                                Policy</a>
                                        </label>
                                    </div>
                                    @error('terms')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror

                                    <button type="submit"
                                        class="btn-primary w-full text-lg text-white py-4 rounded-xl font-semibold transition-all duration-300 mobile-btn">
                                        Create Company Account
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Login Form -->
                    <div id="login-form" class="hidden form-wrapper auth-shell animate-fade-in mobile-form-section">
                        <div class="text-center mb-8">
                            <div class="flex justify-center mb-4">
                                <img src="{{ asset('public/images/logo.png') }}" alt="Logo"
                                    class="h-16 tab-logo" />
                            </div>
                            <h2 class="text-3xl font-bold text-gray-800 mb-2 mobile-h1">
                                Welcome Back
                            </h2>
                            <p class="text-gray-600 text-lg mobile-h2">
                                Sign in to your account
                            </p>
                        </div>

                        <form method="POST" action="{{ route('login') }}" class="space-y-6 mobile-space-y">
                            @csrf

                            @if (session('success'))
                                <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800 text-center">
                                    {{ session('success') }}
                                </div>
                            @endif
                            <div class="relative">
                                <input type="text" name="login" placeholder=" " value="{{ old('login') }}"
                                    required
                                    class="form-input w-full px-4 py-4 rounded-xl bg-gray-50 outline-none mobile-h3 mobile-input" />
                                <label class="floating-label">Email or Phone</label>
                                @error('login')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="relative">
                                <input id="login-password" type="password" name="password" placeholder=" " required
                                    class="form-input w-full px-4 py-4 rounded-xl bg-gray-50 outline-none mobile-h3 mobile-input" />
                                <label class="floating-label">Password</label>
                                <button type="button"
                                    class="absolute right-3 top-4 text-gray-400 hover:text-gray-600"
                                    onclick="togglePassword('login-password', this)">
                                    <i class="far fa-eye"></i>
                                </button>
                                @error('password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-between items-center">
                                <label class="flex items-center">
                                    <input type="checkbox" name="remember"
                                        class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                                        {{ old('remember') ? 'checked' : '' }} />
                                    <span class="ml-2 text-sm text-gray-600 mobile-checkbox">Remember me</span>
                                </label>
                                <a href="#" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                    Forgot password?
                                </a>
                            </div>

                            <button type="submit"
                                class="btn-primary w-full text-lg text-white py-4 rounded-xl font-semibold transition-all duration-300 mobile-btn">
                                Sign In
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId, iconElement) {
            const input = document.getElementById(fieldId);
            const icon = iconElement.querySelector("i");

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        function toggleForm(formType) {
            const signupForm = document.getElementById("signup-form");
            const loginForm = document.getElementById("login-form");
            const signupBg = document.getElementById("signup-bg");
            const loginBg = document.getElementById("login-bg");
            const sidebarSignup = document.getElementById("sidebar-signup");
            const sidebarLogin = document.getElementById("sidebar-login");
            const mobileSignup = document.getElementById("mobile-signup");
            const mobileLogin = document.getElementById("mobile-login");
            const mobileSignupBg = document.getElementById("mobile-signup-bg");
            const mobileLoginBg = document.getElementById("mobile-login-bg");
            const mobileHeroTitle = document.getElementById("mobile-hero-title");
            const mobileHeroSubtitle = document.getElementById("mobile-hero-subtitle");
            const mobileHeroBadge = document.getElementById("mobile-hero-badge");

            const heroCopy = {
                signup: {
                    title: 'Start your healing journey with Pink "ME"',
                    subtitle: 'Sign up to get personalised support, funding opportunities, and caring professionals in your corner.',
                    badge: 'Join the community',
                },
                login: {
                    title: 'Welcome back, we saved your space',
                    subtitle: 'Log in to manage applications, connect with sponsors, and keep your progress on track.',
                    badge: 'Glad to see you',
                },
            };

            if (formType === "signup") {
                signupForm.classList.remove("hidden");
                loginForm.classList.add("hidden");
                signupBg.classList.remove("hidden");
                loginBg.classList.add("hidden");
                sidebarSignup?.classList.add("active");
                sidebarLogin?.classList.remove("active");
                mobileSignup?.classList.add("active");
                mobileLogin?.classList.remove("active");
                mobileSignupBg?.classList.remove("hidden");
                mobileLoginBg?.classList.add("hidden");
            } else {
                signupForm.classList.add("hidden");
                loginForm.classList.remove("hidden");
                signupBg.classList.add("hidden");
                loginBg.classList.remove("hidden");
                sidebarLogin?.classList.add("active");
                sidebarSignup?.classList.remove("active");
                mobileLogin?.classList.add("active");
                mobileSignup?.classList.remove("active");
                mobileLoginBg?.classList.remove("hidden");
                mobileSignupBg?.classList.add("hidden");
            }

            if (mobileHeroTitle && mobileHeroSubtitle && mobileHeroBadge) {
                const copy = heroCopy[formType] ?? heroCopy.signup;
                mobileHeroTitle.textContent = copy.title;
                mobileHeroSubtitle.textContent = copy.subtitle;
                mobileHeroBadge.textContent = copy.badge;
            }
        }

        function showTab(tabId) {
            const tabs = document.querySelectorAll(".tab-content");
            tabs.forEach((tab) => tab.classList.add("hidden"));
            const buttons = document.querySelectorAll(".tab-btn");
            buttons.forEach((btn) => {
                btn.classList.remove("bg-primary-600", "text-white");
                btn.classList.add("text-gray-600", "hover:text-gray-800");
            });
            document.getElementById(tabId).classList.remove("hidden");
            const activeBtn = document.querySelector(
                `[onclick="showTab('${tabId}')"]`
            );
            activeBtn.classList.add("bg-primary-600", "text-white");
            activeBtn.classList.remove("text-gray-600", "hover:text-gray-800");
        }

        function toggleSponsorType(selectElement) {
            const type = selectElement.value;
            const individualForm = document.querySelector('#company form:not([enctype])');
            const companyForm = document.getElementById('companySponsorForm');

            if (individualForm) individualForm.classList.add('hidden');
            if (companyForm) companyForm.classList.add('hidden');

            if (type === 'individual' && individualForm) {
                individualForm.classList.remove('hidden');
            } else if (type === 'company' && companyForm) {
                companyForm.classList.remove('hidden');
            }

            const allSelects = document.querySelectorAll('.sponsor-type-select');
            allSelects.forEach((select) => {
                if (select !== selectElement) {
                    select.value = type;
                }
            });
        }

        document.addEventListener("DOMContentLoaded", () => {
            const firstSelect = document.querySelector(".sponsor-type-select");
            if (firstSelect) {
                toggleSponsorType(firstSelect);
            }

            const urlParams = new URLSearchParams(window.location.search);
            const initialTab = 'signup';
            const tab = urlParams.get('tab') || initialTab;
            toggleForm(tab);
        });

        document.querySelectorAll('.toggle-password').forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const parent = this.parentElement;
                const input = parent.querySelector('.password-input');
                const icon = this.querySelector('i');

                if (input && input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else if (input) {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        const logoInput = document.getElementById('logoFileInput');
        if (logoInput) {
            logoInput.addEventListener('change', function(e) {
                const fileName = e.target.files[0]?.name || 'No file chosen';
                const displayElement = document.getElementById('fileNameDisplay');
                if (displayElement) {
                    displayElement.textContent = fileName;
                }
            });
        }
    </script>
</body>

</html>
