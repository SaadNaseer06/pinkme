<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>A Patient Bill(s) Paid By Finance &amp; Grant Team</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6; margin: 0; padding: 0;">
    <div style="max-width: 640px; margin: 0 auto; padding: 24px;">
        <h2 style="margin: 0 0 16px; color: #9E2469;">A Patient Bill(s) Paid By Finance &amp; Grant Team</h2>
        <p style="margin: 0 0 12px;">Finance &amp; Grant Team has paid the patient bill for <strong>{{ $applicantName }}</strong> – {{ $programTitle }}. Invoice #{{ $invoice->invoice_number }} (${{ number_format($invoice->amount, 2) }}).</p>

        <div style="background: #f9f5fb; border: 1px solid #eadff0; border-radius: 8px; padding: 16px; margin: 0 0 16px;">
            @if ($invoice->file_path)
                <p style="margin: 0;"><strong>Invoice PDF</strong> is attached to this email.</p>
            @endif
        </div>

        <p style="margin: 0 0 8px;">View full details:</p>
        <p style="margin: 0;">
            <a href="{{ $detailUrl }}" target="_blank" style="color: #9E2469;">View registration</a>
        </p>
    </div>
</body>
</html>
