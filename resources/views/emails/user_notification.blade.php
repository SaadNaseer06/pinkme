<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notification</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6; margin: 0; padding: 0;">
    <div style="max-width: 640px; margin: 0 auto; padding: 24px;">
        <h2 style="margin: 0 0 16px; color: #9E2469;">{{ $title }}</h2>
        <p style="margin: 0 0 12px;">Hi {{ $recipientName }},</p>
        <p style="margin: 0 0 12px;">{{ $message }}</p>
        @if(!empty($linkUrl))
            <p style="margin: 0 0 8px;">View details:</p>
            <p style="margin: 0;">
                <a href="{{ $linkUrl }}" target="_blank" style="color: #9E2469;">{{ $linkUrl }}</a>
            </p>
        @endif
    </div>
</body>
</html>
