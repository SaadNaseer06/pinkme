<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Finance: Budget Allocated</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6; margin: 0; padding: 0;">
    <div style="max-width: 640px; margin: 0 auto; padding: 24px;">
        <h2 style="margin: 0 0 16px; color: #9E2469;">Finance Has Allocated Budget</h2>
        <p style="margin: 0 0 12px;">Finance has allocated budget for the following registration:</p>

        <div style="background: #f9f5fb; border: 1px solid #eadff0; border-radius: 8px; padding: 16px; margin: 0 0 16px;">
            <p style="margin: 0 0 8px;"><strong>Applicant:</strong> {{ $applicantName }}</p>
            <p style="margin: 0 0 8px;"><strong>Program:</strong> {{ $programTitle }}</p>
            <p style="margin: 0 0 8px;"><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
            <p style="margin: 0 0 8px;"><strong>Amount:</strong> ${{ number_format($invoice->amount, 2) }}</p>
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
