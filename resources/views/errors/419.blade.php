@php
    $sessionExpiredRole = optional(auth()->user())->role?->name;
    $loginUrl = in_array($sessionExpiredRole, ['admin', 'casemanager', 'finance'], true)
        ? route('login.staff')
        : route('login');
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="0;url={{ $loginUrl }}">
    <title>Redirecting… — {{ config('app.name', 'Pink Me') }}</title>
    <script>
        window.location.replace(@json($loginUrl));
    </script>
</head>

<body style="font-family:system-ui,sans-serif;background:#fdf2f8;color:#213430;margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1.5rem;text-align:center;">
    <p style="max-width:24rem;font-size:0.95rem;line-height:1.5;">
        Your session expired or this page is out of date. Redirecting to sign in…
        <br><br>
        <a href="{{ $loginUrl }}" style="color:#9e2469;font-weight:600;">Continue to sign in</a>
    </p>
</body>

</html>
