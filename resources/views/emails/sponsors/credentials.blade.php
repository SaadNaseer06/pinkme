<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sponsor Account Credentials</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6; margin: 0; padding: 0;">
    <div style="max-width: 640px; margin: 0 auto; padding: 24px;">
        <h2 style="margin: 0 0 16px; color: #9E2469;">Welcome to PINK ME</h2>
        <p style="margin: 0 0 12px;">Hi {{ $recipientName }},</p>
        <p style="margin: 0 0 16px;">An admin has created your sponsor account. Here are your login credentials:</p>

        <div style="background: #f9f5fb; border: 1px solid #eadff0; border-radius: 8px; padding: 16px; margin: 0 0 16px;">
            <p style="margin: 0 0 8px;"><strong>Email:</strong> {{ $email }}</p>
            <p style="margin: 0;"><strong>Password:</strong> {{ $password }}</p>
        </div>

        <p style="margin: 0 0 12px;">Login here:</p>
        <p style="margin: 0 0 16px;">
            <a href="{{ $loginUrl }}" target="_blank" style="color: #9E2469;">{{ $loginUrl }}</a>
        </p>

        <p style="margin: 0;">Please change your password after signing in.</p>
    </div>
</body>
</html>
