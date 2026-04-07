<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Staff sign in — {{ config('app.name', 'Pink Me') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', system-ui, sans-serif;
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-[#FDF2F8] via-[#FFF8FC] to-[#F3E8EF] flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <p class="text-sm font-semibold uppercase tracking-wider text-[#9E2469]">Pink Me</p>
            <h1 class="mt-2 text-2xl font-semibold text-[#213430]">Staff sign in</h1>
            <p class="mt-2 text-sm text-[#6C5B68]">For administrators, case managers, and finance users. Use your work
                email and password.</p>
        </div>

        <div class="rounded-2xl border border-[#E9DCE7] bg-white shadow-lg shadow-[#9E2469]/10 p-8">
            <form method="POST" action="{{ route('login.staff.submit') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-[#213430] mb-1.5">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                        autofocus
                        class="w-full rounded-xl border border-[#DCCFD8] px-4 py-3 text-sm text-[#213430] placeholder:text-[#91848C] outline-none transition focus:border-[#9E2469] focus:ring-2 focus:ring-[#9E2469]/20 @error('email') border-red-400 @enderror">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-[#213430] mb-1.5">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="w-full rounded-xl border border-[#DCCFD8] px-4 py-3 text-sm text-[#213430] outline-none transition focus:border-[#9E2469] focus:ring-2 focus:ring-[#9E2469]/20 @error('password') border-red-400 @enderror">
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between gap-3">
                    <label class="inline-flex items-center gap-2 text-sm text-[#6C5B68] cursor-pointer">
                        <input type="checkbox" name="remember" value="1"
                            class="rounded border-[#DCCFD8] text-[#9E2469] focus:ring-[#9E2469]"
                            {{ old('remember') ? 'checked' : '' }}>
                        Remember me
                    </label>
                    <a href="{{ route('password.request') }}"
                        class="text-sm font-medium text-[#9E2469] hover:underline">Forgot password?</a>
                </div>

                <button type="submit"
                    class="w-full rounded-xl bg-[#9E2469] px-4 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-[#B52D75] focus:outline-none focus:ring-2 focus:ring-[#9E2469] focus:ring-offset-2">
                    Sign in
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-sm text-[#6C5B68]">
            Patients and sponsors:
            <a href="{{ route('register', ['tab' => 'login']) }}" class="font-medium text-[#9E2469] hover:underline">Main
                login &amp; registration</a>
        </p>
    </div>
</body>

</html>
