<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>{{ $bodyLine }}</title></head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6;">
<div style="max-width: 640px; margin: 0 auto; padding: 24px;">
    <p style="margin: 0 0 12px;">Hello,</p>
    <p style="margin: 0 0 12px;">{{ $bodyLine }}</p>
    <p style="margin: 0 0 12px;"><strong>Applicant:</strong> {{ $applicant }}<br><strong>Program:</strong> {{ $programTitle }}</p>
    <p style="margin: 0;"><a href="{{ $detailUrl }}" style="color: #d12978;">{{ $linkLabel ?? 'View application' }}</a></p>
</div>
</body>
</html>
