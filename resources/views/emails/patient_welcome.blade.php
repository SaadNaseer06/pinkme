<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6; margin: 0; padding: 0;">
    <div style="max-width: 640px; margin: 0 auto; padding: 24px;">
        <h2 style="margin: 0 0 16px; color: #9E2469;">Welcome to {{ $appName }}</h2>
        <p style="margin: 0 0 12px;">Hi {{ $recipientName }},</p>
        <p style="margin: 0 0 12px;">Thank you for registering. Your patient account is ready.</p>
        @if(!empty($viaGoogle))
            <p style="margin: 0 0 12px;">You can sign in anytime using <strong>Continue with Google</strong> on the login page with the same Google account you used to register.</p>
        @else
            <p style="margin: 0 0 12px;">You can sign in anytime with the email and password you used to register.</p>
        @endif
        <p style="margin: 0 0 8px;">
            <a href="{{ $loginUrl }}" style="color: #9E2469; font-weight: 600;">Go to login</a>
        </p>
        <p style="margin: 24px 0 0; font-size: 14px; color: #6b7280;">If you did not create this account, you can ignore this email.</p>
    </div>
</body>
</html>
