<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <title>@yield('title', 'Case Manager')</title>
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
    @vite('resources/css/custom.css')
    @stack('head')
</head>

<body class="bg-[#FFF8FC] font-sans flex min-h-screen">
    @include('case_manager.partials.sidebar')
    <div class="flex-1 flex flex-col">
        @include('case_manager.partials.topbar')
        <main class="flex-1 p-6">
            @include('partials.flash')
            @yield('content')
        </main>
        @include('case_manager.partials.footer')
    </div>
    @stack('scripts')
</body>

</html>
