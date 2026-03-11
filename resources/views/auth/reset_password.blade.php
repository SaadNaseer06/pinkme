<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PINK "ME" - Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ["Poppins", "sans-serif"],
                    },
                    colors: {
                        primary: {
                            500: '#ec4899',
                        },
                    },
                },
            },
        };
    </script>
</head>

<body class="min-h-screen bg-gradient-to-br from-pink-100 via-white to-purple-100 font-sans">
    <div class="flex min-h-screen items-center justify-center px-4">
        <div
            class="w-full max-w-md rounded-3xl bg-white/80 p-10 shadow-xl backdrop-blur-lg border border-white/40 space-y-8">
            <div class="space-y-2 text-center">
                <span class="inline-flex items-center rounded-full bg-primary-500/10 px-3 py-1 text-sm font-medium text-primary-500">
                    Reset Password
                </span>
                <h1 class="text-2xl font-semibold text-gray-800">Choose a new password</h1>
                <p class="text-sm text-gray-500">
                    Create a strong password to secure your account.
                </p>
            </div>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="space-y-2">
                    <label for="email" class="text-sm font-medium text-gray-600">Email address</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autofocus
                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-700 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200" />
                    @error('email')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="password" class="text-sm font-medium text-gray-600">New password</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-700 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200" />
                    @error('password')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation" class="text-sm font-medium text-gray-600">Confirm password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-700 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200" />
                </div>

                <button type="submit"
                    class="w-full rounded-xl bg-gradient-to-r from-primary-500 to-pink-400 px-4 py-3 text-white font-semibold shadow-lg transition hover:from-pink-500 hover:to-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                    Update password
                </button>
            </form>

            <div class="text-center text-sm text-gray-500">
                Remembered your password?
                <a href="{{ route('login') }}" class="font-medium text-primary-500 hover:text-primary-600">
                    Back to sign in
                </a>
            </div>
        </div>
    </div>
</body>

</html>

