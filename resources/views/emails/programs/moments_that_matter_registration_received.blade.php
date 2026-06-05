<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Moments That Matter Package Request Received</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6; margin: 0; padding: 0;">
    <div style="max-width: 640px; margin: 0 auto; padding: 24px;">
        <p style="margin: 0 0 12px;">Hi {{ $firstName }},</p>
        @foreach (\App\Support\MomentsThatMatterNotice::paragraphs() as $paragraph)
            <p style="margin: 0 0 12px;">{{ $paragraph }}</p>
        @endforeach
        <p style="margin: 16px 0 8px;">You can review your submission any time:</p>
        <p style="margin: 0;">
            <a href="{{ $detailUrl }}" target="_blank" style="color: #9E2469;">Open Patient Portal</a>
        </p>
    </div>
</body>
</html>
