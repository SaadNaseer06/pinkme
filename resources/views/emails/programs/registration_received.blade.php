<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Financial Assistance Program Application Received</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6; margin: 0; padding: 0;">
    <div style="max-width: 640px; margin: 0 auto; padding: 24px;">
        <p style="margin: 0 0 12px;">Hi {{ $firstName }},</p>
        <p style="margin: 0 0 12px;">Thank you for submitting your financial assistance application to {{ $brandName }}. Your information has been received.</p>
        <div style="background: #f9f5fb; border: 1px solid #eadff0; border-radius: 8px; padding: 16px; margin: 0 0 16px;">
            <p style="margin: 0 0 8px;"><strong>Status:</strong> Received</p>
            <p style="margin: 0;"><strong>Note:</strong> We will be in contact with you through the Patient Portal, where you can access our chat support feature to communicate with the {{ $brandName }} team. This allows us to stay connected and support you through the process.</p>
        </div>
        <p style="margin: 0 0 8px;">You can review your submission any time:</p>
        <p style="margin: 0;">
            <a href="{{ $detailUrl }}" target="_blank" style="color: #9E2469;">Open Patient Portal</a>
        </p>
        <p style="margin: 24px 0 0;">Thank You!<br>{{ $brandName }} Team</p>
    </div>
</body>
</html>
