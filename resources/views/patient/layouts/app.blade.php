<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

@php
    $patientUnreadNotifications = collect();
    if (auth()->check()) {
        $patientUnreadNotifications = auth()->user()
            ->notifications()
            ->unread()
            ->latest()
            ->take(5)
            ->get();
    }

    $patientNotificationPayloads = $patientUnreadNotifications
        ->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'link_url' => $notification->link_url,
                'mark_url' => route('patient.notifications.read', $notification),
                'created_at' => optional($notification->created_at)->format('d M Y, h:i A'),
            ];
        })
        ->values()
        ->toArray();
@endphp

<body class="bg-[#FFF8FC] font-sans flex min-h-screen">
    @include('patient.partials.sidebar')
    <div class="flex-1 flex flex-col">
        @include('patient.partials.topbar')
        <main class="flex-1 p-6">
            @include('partials.flash')
            @yield('content')
        </main>
        @include('patient.partials.footer')
    </div>
    @if ($patientUnreadNotifications->isNotEmpty())
        <div id="patient-notification-modal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 id="patient-notification-title"
                            class="text-xl font-semibold text-[#213430] app-main">Notification</h3>
                        <p id="patient-notification-time" class="text-xs text-[#91848C] mt-1 app-text"></p>
                    </div>
                    <button type="button" id="patient-notification-close"
                        class="text-[#91848C] hover:text-[#213430] transition" aria-label="Dismiss notification">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <p id="patient-notification-message" class="text-sm text-[#213430] leading-relaxed app-text"></p>
                <div class="flex flex-col sm:flex-row sm:justify-end gap-2 pt-2">
                    <button type="button" id="patient-notification-dismiss"
                        class="inline-flex justify-center items-center px-4 py-2 border border-[#DCCFD8] rounded-md text-sm text-[#91848C] hover:bg-[#F3E8EF] transition app-text">
                        Dismiss
                    </button>
                    <a id="patient-notification-view"
                        class="inline-flex justify-center items-center px-4 py-2 bg-[#DB69A2] text-white rounded-md text-sm font-semibold hover:bg-[#c95791] transition app-text"
                        href="#" target="_self">
                        View Details
                    </a>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                (function() {
                    const notifications = @json($patientNotificationPayloads);

                    if (!notifications.length) {
                        return;
                    }

                    const modal = document.getElementById('patient-notification-modal');
                    const titleEl = document.getElementById('patient-notification-title');
                    const messageEl = document.getElementById('patient-notification-message');
                    const timeEl = document.getElementById('patient-notification-time');
                    const viewBtn = document.getElementById('patient-notification-view');
                    const dismissBtn = document.getElementById('patient-notification-dismiss');
                    const closeBtn = document.getElementById('patient-notification-close');
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    let index = 0;

                    function showNotification() {
                        if (index >= notifications.length) {
                            modal.classList.add('hidden');
                            return;
                        }

                        const notif = notifications[index];
                        titleEl.textContent = notif.title || 'Notification';
                        messageEl.textContent = notif.message || '';
                        timeEl.textContent = notif.created_at ? `Received on ${notif.created_at}` : '';

                        if (notif.link_url) {
                            viewBtn.href = notif.link_url;
                            viewBtn.classList.remove('hidden');
                        } else {
                            viewBtn.href = '#';
                            viewBtn.classList.add('hidden');
                        }

                        modal.classList.remove('hidden');
                    }

                    function markRead(callback) {
                        const notif = notifications[index];
                        fetch(notif.mark_url, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json',
                                },
                            })
                            .catch(() => {})
                            .finally(() => {
                                index += 1;
                                if (typeof callback === 'function') {
                                    callback(notif);
                                } else {
                                    showNotification();
                                }
                            });
                    }

                    dismissBtn.addEventListener('click', () => {
                        modal.classList.add('hidden');
                        markRead(() => showNotification());
                    });

                    closeBtn.addEventListener('click', () => {
                        modal.classList.add('hidden');
                        markRead(() => showNotification());
                    });

                    viewBtn.addEventListener('click', (event) => {
                        const destination = event.currentTarget.getAttribute('href');
                        if (!destination || destination === '#') {
                            event.preventDefault();
                            markRead(() => showNotification());
                            return;
                        }

                        event.preventDefault();
                        modal.classList.add('hidden');
                        window.location.href = destination;
                        markRead();
                    });

                    modal.addEventListener('click', (event) => {
                        if (event.target === modal) {
                            modal.classList.add('hidden');
                            markRead(() => showNotification());
                        }
                    });

                    showNotification();
                })();
            </script>
        @endpush
    @endif
    @stack('scripts')
</body>

</html>
