<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Webinar Registration Confirmed</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6; margin: 0; padding: 0;">
    <div style="max-width: 640px; margin: 0 auto; padding: 24px;">
        <h2 style="margin: 0 0 16px; color: #d12978;">Your webinar spot is confirmed</h2>
        <p style="margin: 0 0 12px;">Hi {{ $recipientName }},</p>
        <p style="margin: 0 0 12px;">Thanks for registering. Here are your webinar details:</p>

        <div style="background: #f9f5fb; border: 1px solid #eadff0; border-radius: 8px; padding: 16px; margin: 0 0 16px;">
            <p style="margin: 0 0 8px;"><strong>Title:</strong> {{ $webinar->title }}</p>
            @if($scheduledAt)
                <p style="margin: 0 0 8px;"><strong>When:</strong> {{ $scheduledAt }}</p>
            @endif
            @if($webinar->presenter)
                <p style="margin: 0 0 8px;"><strong>Presenter:</strong> {{ $webinar->presenter }}</p>
            @endif
            @if($joinUrl)
                <p style="margin: 0 0 8px;"><strong>Join link:</strong> <a href="{{ $joinUrl }}" target="_blank" style="color: #d12978;">{{ $joinUrl }}</a></p>
            @endif
            @if($webinar->description)
                <p style="margin: 0;">{{ $webinar->description }}</p>
            @endif
        </div>

        <p style="margin: 0;">See you there!</p>
    </div>
</body>
</html>
