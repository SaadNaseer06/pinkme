<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Budget Allocated</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6; margin: 0; padding: 0;">
    <div style="max-width: 640px; margin: 0 auto; padding: 24px;">
        <h2 style="margin: 0 0 16px; color: #9E2469;">Budget Allocated for Your Registration</h2>
        <p style="margin: 0 0 12px;">Hi {{ $recipientName }},</p>
        <p style="margin: 0 0 12px;">Good news! Budget has been allocated for your registration for <strong>{{ $programTitle }}</strong>.</p>

        <div style="background: #f9f5fb; border: 1px solid #eadff0; border-radius: 8px; padding: 16px; margin: 0 0 16px;">
            <p style="margin: 0 0 8px;"><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
            <p style="margin: 0 0 8px;"><strong>Amount:</strong> ${{ number_format($invoice->amount, 2) }}</p>
            <p style="margin: 0 0 8px;"><strong>Payment Purpose:</strong> {{ $invoice->payment_purpose }}</p>
            @if ($invoice->file_path)
                <p style="margin: 0;"><strong>Invoice PDF</strong> is attached to this email for your records.</p>
            @endif
        </div>

        <p style="margin: 0;">If you have any questions, please contact us.</p>
    </div>
</body>
</html>
