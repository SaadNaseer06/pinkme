<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your bill has been paid</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6; margin: 0; padding: 0;">
    <div style="max-width: 640px; margin: 0 auto; padding: 24px;">
        <p style="margin: 0 0 12px;">Hi {{ $firstName }},</p>
        <p style="margin: 0 0 12px;">Your bill has been paid through the {{ $brandName }} financial assistance program.</p>
        <div style="background: #f9f5fb; border: 1px solid #eadff0; border-radius: 8px; padding: 16px; margin: 0 0 16px;">
            <p style="margin: 0 0 8px;"><strong>Status:</strong> Bill(s) Paid</p>
            <p style="margin: 0;"><strong>Note:</strong> Please allow a few days for the payment to reflect on your account. Questions? Message us in the
                <a href="{{ $portalUrl }}" style="color: #9E2469;">patient portal</a>.
            </p>
        </div>
        <p style="margin: 0;">With Care,<br>{{ $brandName }} Team</p>
    </div>
</body>
</html>
