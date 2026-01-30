<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New message</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6; margin: 0; padding: 0;">
    <div style="max-width: 640px; margin: 0 auto; padding: 24px;">
        <h2 style="margin: 0 0 16px; color: #d12978;">You've got a new message</h2>
        <p style="margin: 0 0 12px;">Hi {{ $recipientName }},</p>
        <p style="margin: 0 0 12px;">You have a new message from <strong>{{ $senderName }}</strong>.</p>
        <div style="background: #f9f5fb; border: 1px solid #eadff0; border-radius: 8px; padding: 16px; margin: 0 0 16px;">
            <p style="margin: 0; white-space: pre-wrap;">{{ $content }}</p>
        </div>
        <p style="margin: 0 0 8px;">Reply in the chat:</p>
        <p style="margin: 0;">
            <a href="{{ $chatUrl }}" target="_blank" style="color: #d12978;">Open chat</a>
        </p>
    </div>
</body>
</html>
