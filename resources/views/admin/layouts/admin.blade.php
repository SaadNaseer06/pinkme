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
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    <style>
        .toast-msg {
            min-width: 250px;
            max-width: 400px;
            background: #fff;
            color: #22223B;
            border-left: 6px solid #db69a2;
            box-shadow: 0 4px 24px rgba(50, 17, 40, 0.10);
            padding: 18px 16px 16px 16px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            animation: fadeInUp 0.25s;
        }

        .toast-msg.toast-success {
            border-color: #20b354;
        }

        .toast-msg.toast-error {
            border-color: #d8000c;
        }

        .toast-msg .toast-close {
            background: none;
            border: none;
            color: #888;
            font-size: 1.3em;
            cursor: pointer;
            position: absolute;
            top: 6px;
            right: 8px;
            line-height: 1;
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-[#FFF8FC] font-sans flex min-h-screen">
    @include('admin.partials.sidebar')
    <div class="flex-1 flex flex-col">
        @include('admin.partials.topbar')
        <main class="flex-1 p-6">
            @include('admin.partials.flash')
            @yield('content')
        </main>
        @include('admin.partials.footer')
    </div>
    @stack('scripts')
</body>

</html>

