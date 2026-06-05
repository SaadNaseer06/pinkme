<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff portal — {{ $brandName ?? config('app.brand_name', 'PINK "ME"®') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', system-ui, sans-serif;
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-[#FDF2F8] via-[#FFF8FC] to-[#F3E8EF] flex items-center justify-center p-4">
    <div class="w-full max-w-3xl">
        <div class="text-center mb-10">
            <img src="{{ asset('public/images/logo.png') }}"
                alt="{{ $brandName ?? config('app.brand_name', 'PINK "ME"®') }}"
                class="mx-auto h-16 w-auto max-w-[200px] object-contain mb-4" />
            <h1 class="text-2xl font-semibold text-[#213430]">Staff portal</h1>
            <p class="mt-2 text-sm font-semibold tracking-wide text-[#6C5B68] max-w-lg mx-auto uppercase">
                {{ $staffAccessNotice ?? 'BOARD MEMBER ACCESS ONLY' }}
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <a href="{{ route('login.staff', ['portal' => 'admin']) }}"
                class="group rounded-2xl border border-[#E9DCE7] bg-white p-6 shadow-lg shadow-[#9E2469]/5 transition hover:border-[#9E2469] hover:shadow-md focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:ring-offset-2">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#F3E8EF] text-[#9E2469] group-hover:bg-[#9E2469] group-hover:text-white transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-[#213430]">Admin</h2>
                <p class="mt-2 text-sm text-[#6C5B68] leading-relaxed">
                    Full administration: applications, programs, settings, and team oversight.
                </p>
                <span class="mt-4 inline-flex items-center text-sm font-semibold text-[#9E2469] group-hover:underline">
                    Sign in as Admin
                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </span>
            </a>

            <a href="{{ route('login.staff', ['portal' => 'coordinator']) }}"
                class="group rounded-2xl border border-[#E9DCE7] bg-white p-6 shadow-lg shadow-[#9E2469]/5 transition hover:border-[#9E2469] hover:shadow-md focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:ring-offset-2">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#F3E8EF] text-[#9E2469] group-hover:bg-[#9E2469] group-hover:text-white transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-[#213430]">Patient Coordinator</h2>
                <p class="mt-2 text-sm text-[#6C5B68] leading-relaxed">
                    Review assigned applications, patient profiles, and program registrations.
                </p>
                <span class="mt-4 inline-flex items-center text-sm font-semibold text-[#9E2469] group-hover:underline">
                    Sign in as Coordinator
                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </span>
            </a>

            <a href="{{ route('login.staff', ['portal' => 'finance']) }}"
                class="group rounded-2xl border border-[#E9DCE7] bg-white p-6 shadow-lg shadow-[#9E2469]/5 transition hover:border-[#9E2469] hover:shadow-md focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:ring-offset-2">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#F3E8EF] text-[#9E2469] group-hover:bg-[#9E2469] group-hover:text-white transition">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-[#213430]">Finance &amp; Grant Manager</h2>
                <p class="mt-2 text-sm text-[#6C5B68] leading-relaxed">
                    Process payments, record bills paid, and manage the finance queue.
                </p>
                <span class="mt-4 inline-flex items-center text-sm font-semibold text-[#9E2469] group-hover:underline">
                    Sign in as Finance
                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </span>
            </a>
        </div>

        <p class="mt-8 text-center text-sm text-[#6C5B68]">
            Patients:
            <a href="{{ route('register', ['tab' => 'login']) }}" class="font-medium text-[#9E2469] hover:underline">Main
                login &amp; registration</a>
        </p>
    </div>
</body>

</html>