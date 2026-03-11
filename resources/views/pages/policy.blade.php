<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | {{ config('app.name', 'Pink Me') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#FFF6FB',
                            100: '#FDE8F2',
                            200: '#F5D0E8',
                            300: '#E9A8D4',
                            400: '#D472B6',
                            500: '#9E2469',
                            600: '#8a1f5a',
                            700: '#7D1D54',
                        },
                        slate: {
                            900: '#213430',
                            600: '#6C5B68',
                        }
                    },
                    fontFamily: {
                        sans: ['Poppins', 'ui-sans-serif', 'system-ui'],
                    },
                },
            },
        };
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link rel="icon" href="{{ asset('public/images/favicon.png') }}" type="image/x-icon">
    <style>
        .policy-content h1,
        .policy-content h2,
        .policy-content h3,
        .policy-content h4 {
            color: #213430;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .policy-content p {
            color: #4C3F4C;
            line-height: 1.75;
            margin-bottom: 1rem;
        }

        .policy-content ul,
        .policy-content ol {
            margin-left: 1.5rem;
            padding-left: 1.5rem;
            color: #4C3F4C;
        }

        .policy-content ul {
            list-style-type: disc;
        }

        .policy-content ol {
            list-style-type: decimal;
        }

        .policy-content li {
            margin-bottom: 0.5rem;
        }
    </style>
</head>

<body class="bg-primary-50 font-sans min-h-screen">
    <div class="relative min-h-screen flex flex-col">
        <div class="absolute inset-0 pointer-events-none"
            style="background: radial-gradient(circle at top right, rgba(255, 110, 182, 0.18), transparent 55%), radial-gradient(circle at bottom left, rgba(219, 105, 162, 0.22), transparent 50%);">
        </div>

        <header class="relative z-10">
            <div class="bg-gradient-to-r from-primary-500 via-primary-500/90 to-primary-400 text-white">
                <div class="max-w-6xl mx-auto px-5 py-6 md:py-8 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('public/images/pink_me_logo.png') }}" alt="{{ config('app.name', 'Pink Me') }}"
                            class="h-10 w-auto">
                        {{-- <span class="text-lg font-semibold tracking-wide uppercase">{{ config('app.name', 'Pink Me') }}</span> --}}
                    </div>
                    <div class="hidden md:flex items-center gap-4 text-sm">
                        <a href="{{ route('policy.privacy') }}"
                            class="hover:text-primary-50 transition {{ $title === 'Privacy Policy' ? 'font-semibold underline' : '' }}">Privacy</a>
                        <a href="{{ route('policy.terms') }}"
                            class="hover:text-primary-50 transition {{ $title === 'Terms & Conditions' ? 'font-semibold underline' : '' }}">Terms</a>
                        <a href="/"
                            class="inline-flex items-center gap-2 bg-white text-primary-600 font-medium px-4 py-2 rounded-full shadow-sm hover:bg-primary-100 transition">
                            <span>Back to Dashboard</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="max-w-6xl mx-auto px-5 pb-16 md:pb-24">
                    <div class="max-w-3xl">
                        <p class="text-sm md:text-base uppercase tracking-[0.3em] text-white/70 mb-4">Legal</p>
                        <h1 class="text-3xl md:text-4xl font-semibold leading-tight">{{ $title }}</h1>
                        <p class="mt-4 text-base md:text-lg text-white/80 max-w-2xl">
                            Transparency matters. Review our latest {{ strtolower($title) }} to understand how we protect
                            your data and uphold responsible usage across the Pink Me platform.
                        </p>
                        @if ($lastUpdated)
                            <p class="mt-6 inline-flex items-center gap-2 text-sm font-medium bg-white/15 backdrop-blur px-4 py-2 rounded-full">
                                <span class="inline-block w-2 h-2 rounded-full bg-white/80"></span>
                                Last updated {{ $lastUpdated->format('F j, Y') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <main class="relative z-10 flex-1 pb-12 md:pb-16">
            <div class="max-w-4xl mx-auto px-5 -mt-16 md:-mt-20">
                <div class="bg-white shadow-xl shadow-primary-500/10 border border-white/60 rounded-3xl p-6 md:p-10 policy-content">
                    {!! $content !!}
                </div>
            </div>
        </main>

        <footer class="relative z-10 mt-auto">
            <div class="max-w-6xl mx-auto px-5 py-8 text-sm text-slate-600 flex flex-col md:flex-row items-center justify-between gap-3">
                <div>&copy; {{ now()->year }} <a href="https://www.pink-me.org/" target="_blank" rel="noopener noreferrer" class="hover:text-primary-600 transition">Pink Me</a>. All rights reserved.</div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('policy.privacy') }}" class="hover:text-primary-600 transition">Privacy Policy</a>
                    <a href="{{ route('policy.terms') }}" class="hover:text-primary-600 transition">Terms &amp; Conditions</a>
                    {{-- <a href="{{ route('login') }}" class="hover:text-primary-600 transition">Sign In</a> --}}
                </div>
            </div>
        </footer>
    </div>
</body>

</html>
