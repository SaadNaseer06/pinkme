<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $lines['heading'] }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6; margin: 0; padding: 0;">
    <div style="max-width: 640px; margin: 0 auto; padding: 24px;">
        <h2 style="margin: 0 0 16px; color: #9E2469;">{{ $lines['heading'] }}</h2>
        <p style="margin: 0 0 12px;">{{ $lines['lead'] }}</p>

        <div style="background: #f9f5fb; border: 1px solid #eadff0; border-radius: 8px; padding: 16px; margin: 16px 0;">
            <p style="margin: 0 0 8px;"><strong>Invoice:</strong> {{ $invoiceNumber }}</p>
            <p style="margin: 0 0 8px;"><strong>Amount:</strong> ${{ $amount }}</p>
            <p style="margin: 0;"><strong>Recorded payment method:</strong> {{ $paymentMethod }}</p>
        </div>

        <p style="margin: 0; font-size: 12px; color: #6b7280;">{{ config('app.brand_name', 'PINK "ME"®') }}</p>
    </div>
</body>
</html>
