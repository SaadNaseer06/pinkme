<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your {{ $roleLabel }} account</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6; margin: 0; padding: 0;">
    <div style="max-width: 640px; margin: 0 auto; padding: 24px;">
        <h2 style="margin: 0 0 16px; color: #9E2469;">Welcome to {{ $appName }}</h2>
        <p style="margin: 0 0 12px;">Hi {{ $recipientName }},</p>
        <p style="margin: 0 0 16px;">An administrator has created your <strong>{{ $roleLabel }}</strong> account. Use the credentials below to sign in:</p>

        <div style="background: #f9f5fb; border: 1px solid #eadff0; border-radius: 8px; padding: 16px; margin: 0 0 16px;">
            <p style="margin: 0 0 8px;"><strong>Email:</strong> {{ $email }}</p>
            <p style="margin: 0;"><strong>Temporary password:</strong> {{ $password }}</p>
        </div>

        <p style="margin: 0 0 8px;"><strong>Staff login link</strong> (admin, case manager, and finance — not the patient register page):</p>
        <p style="margin: 0 0 16px;">
            <a href="{{ $loginUrl }}" target="_blank" rel="noopener" style="color: #9E2469; font-weight: 600; word-break: break-all;">{{ $loginUrl }}</a>
        </p>

        <p style="margin: 0; font-size: 14px; color: #6b7280;">For security, please change your password after you log in.</p>
    </div>
</body>
</html>
