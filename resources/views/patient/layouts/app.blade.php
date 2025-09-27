<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Patient')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        pink: {
                            light: "#F9F4F8",
                            DEFAULT: "#E35A9B",
                            dark: "#D1478A",
                        },
                    },
                    fontFamily: {
                        sans: ["Poppins", "ui-sans-serif", "system-ui"],
                    },
                },
            },
        };
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">
    @vite('resources/css/patient.css')
    @stack('head')
</head>

<body class="bg-[#FFF8FC] font-sans flex min-h-screen">
    @include('patient.partials.sidebar')
    <div class="flex-1 flex flex-col">
        @include('patient.partials.topbar')
        <main class="flex-1 p-6">
            @yield('content')
        </main>
        @include('patient.partials.footer')
    </div>
    @stack('scripts')
</body>

</html>
