<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    @vite('resources/css/app.css')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @vite('resources/css/custom.css')
    @stack('head')
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('public/images/favicon.png') }}" type="image/x-icon">
    {{-- Tailwind Config --}}
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
</head>

<body class="bg-[#FFF8FC] font-sans flex min-h-screen">
    @include('sponsor.partials.sidebar')
    <div class="flex-1 flex flex-col">
        @include('sponsor.partials.topbar')
        <main class="flex-1 p-6">
            @include('partials.flash')
            @yield('content')
        </main>
        @include('sponsor.partials.footer')
    </div>
    @stack('scripts')
</body>

</html>
