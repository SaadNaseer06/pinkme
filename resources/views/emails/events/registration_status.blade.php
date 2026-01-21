<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Registration Update</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6; margin: 0; padding: 0;">
    <div style="max-width: 640px; margin: 0 auto; padding: 24px;">
        <h2 style="margin: 0 0 16px; color: #d12978;">Event registration {{ strtolower($statusLabel) }}</h2>
        <p style="margin: 0 0 12px;">Hi {{ $recipientName }},</p>
        <p style="margin: 0 0 12px;">Your registration for <strong>{{ $eventTitle }}</strong> has been {{ strtolower($statusLabel) }}.</p>

        <div style="background: #f9f5fb; border: 1px solid #eadff0; border-radius: 8px; padding: 16px; margin: 0 0 16px;">
            <p style="margin: 0 0 8px;"><strong>Status:</strong> {{ $statusLabel }}</p>
            @if($eventDate)
                <p style="margin: 0 0 8px;"><strong>Date:</strong> {{ $eventDate }}</p>
            @endif
            @if($amount)
                <p style="margin: 0;"><strong>Amount:</strong> ${{ number_format($amount, 2) }}</p>
            @endif
        </div>
    </div>
</body>
</html>
