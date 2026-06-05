<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Ready for Processing</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6; margin: 0; padding: 0;">
    <div style="max-width: 640px; margin: 0 auto; padding: 24px;">
        <h2 style="margin: 0 0 16px; color: #9E2469;">Payment ready for processing</h2>
        <p style="margin: 0 0 12px;">A case manager has approved an application and payment can now be processed.</p>

        <div style="background: #f9f5fb; border: 1px solid #eadff0; border-radius: 8px; padding: 16px; margin: 0 0 20px;">
            <p style="margin: 0 0 8px;"><strong>Patient name:</strong> {{ $patientName }}</p>
            <p style="margin: 0 0 8px;"><strong>Application ID:</strong> {{ $applicationReference }}</p>
            <p style="margin: 0 0 8px;"><strong>Program:</strong> {{ $programTitle }}</p>
            @if($approvedAmount !== null)
                <p style="margin: 0 0 8px;"><strong>Approved amount (from patient selection):</strong> ${{ number_format($approvedAmount, 2) }}</p>
            @else
                <p style="margin: 0 0 8px;"><strong>Approved amount:</strong> See application details in the dashboard</p>
            @endif
            <p style="margin: 0;"><strong>Case manager:</strong> {{ $caseManagerName }}</p>
        </div>

        <p style="margin: 0 0 16px;">Review the case, complete payment via your approved method, then upload proof of bill payments in the finance dashboard.</p>

        <p style="margin: 0;">
            <a href="{{ $dashboardUrl }}" style="display: inline-block; background: #9E2469; color: #fff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">View in dashboard</a>
        </p>

        <p style="margin: 24px 0 0; font-size: 12px; color: #6b7280;">{{ config('app.brand_name', 'PINK "ME"®') }} — Finance team</p>
    </div>
</body>
</html>
