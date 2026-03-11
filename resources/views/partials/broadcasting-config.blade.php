{{-- Runtime config for subdirectory: Pusher/Echo + axios base URL --}}
@php
    $pusher = config('broadcasting.connections.pusher', []);
    $key = $pusher['key'] ?? null;
    $options = $pusher['options'] ?? [];
@endphp
@if($key)
<script>
window.PUSHER_CONFIG = {
    key: @json($key),
    cluster: @json($options['cluster'] ?? 'mt1'),
    wsHost: 'ws-' + {!! json_encode($options['cluster'] ?? 'mt1') !!} + '.pusher.com',
    wsPort: 80,
    wssPort: 443,
    forceTLS: true,
    enabledTransports: ['ws', 'wss'],
    authEndpoint: @json(url('/broadcasting/auth')),
};
</script>
@endif
<meta name="app-url" content="{{ rtrim(config('app.url'), '/') }}">
