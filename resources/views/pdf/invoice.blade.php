<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number ?? '' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #213430; font-size: 11px; line-height: 1.5; margin: 0; padding: 30px; }
        .header {
            background: #9E2469;
            color: #fff;
            padding: 24px 28px;
            margin: -30px -30px 28px -30px;
        }
        .brand { font-size: 24px; font-weight: bold; letter-spacing: 0.5px; }
        .tagline { font-size: 9px; opacity: 0.9; margin-top: 4px; }
        .invoice-badge { text-align: right; }
        .invoice-badge .label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; }
        .invoice-badge .number { font-size: 20px; font-weight: bold; margin-top: 2px; }

        .info-grid { margin-bottom: 24px; }
        .info-grid table { width: 100%; }
        .info-grid td { padding: 4px 0; vertical-align: top; }
        .info-grid td:first-child { width: 140px; color: #91848C; font-size: 10px; }
        .applicant-name { font-size: 14px; font-weight: bold; color: #213430; }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 24px 0;
            border: 1px solid #E5D2DE;
        }
        .details-table th {
            background: #F3E8EF;
            color: #213430;
            font-weight: 600;
            padding: 12px 16px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #E5D2DE;
        }
        .details-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #F0E5EB;
        }
        .details-table tr:last-child td { border-bottom: none; }
        .amount-row { background: #FDF8FB; }
        .amount-value { font-size: 20px; font-weight: bold; color: #9E2469; }
        .status-paid { color: #20B354; font-weight: 600; }

        .total-box {
            margin-top: 24px;
            padding: 20px 24px;
            background: #F3E8EF;
            border: 1px solid #E5D2DE;
        }
        .total-box .label { font-size: 10px; color: #91848C; margin-bottom: 4px; }
        .total-box .value { font-size: 24px; font-weight: bold; color: #9E2469; }
        .total-box .status { background: #20B354; color: #fff; padding: 8px 16px; font-size: 11px; font-weight: bold; }

        .footer {
            margin-top: 32px;
            padding-top: 16px;
            border-top: 1px solid #E5D2DE;
            font-size: 9px;
            color: #91848C;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <div class="brand">PINK "ME"</div>
                    <div class="tagline">A NON-PROFIT BREAST CANCER ORGANIZATION</div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <div class="invoice-badge">
                        <div class="label">Invoice</div>
                        <div class="number">{{ $invoice->invoice_number ?? '—' }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="info-grid">
        <table>
            <tr>
                <td>Applicant</td>
                <td><span class="applicant-name">{{ $registration->full_name ?? '—' }}</span></td>
            </tr>
            <tr>
                <td>Program</td>
                <td>{{ $registration->program->title ?? '—' }}</td>
            </tr>
            <tr>
                <td>Issue Date</td>
                <td>{{ $invoice->issue_date?->format('M d, Y') ?? '—' }}</td>
            </tr>
        </table>
    </div>

    <table class="details-table">
        <tr>
            <th>Payment Purpose</th>
            <td>{{ $invoice->payment_purpose }}</td>
        </tr>
        <tr>
            <th>Payment Method</th>
            <td>{{ $invoice->payment_method }}</td>
        </tr>
        <tr class="amount-row">
            <th>Amount</th>
            <td><span class="amount-value">${{ number_format($invoice->amount, 2) }}</span></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><span class="status-paid">{{ $invoice->status }}</span></td>
        </tr>
        @if (!empty($invoice->notes))
        <tr>
            <th>Notes</th>
            <td>{{ $invoice->notes }}</td>
        </tr>
        @endif
    </table>

    <div class="total-box">
        <table style="width: 100%;">
            <tr>
                <td>
                    <div class="label">Total Amount Due</div>
                    <div class="value">${{ number_format($invoice->amount, 2) }}</div>
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <span class="status">{{ $invoice->status }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>This is an official invoice from {{ config('app.name') }}. Please retain for your records.</p>
        <p style="margin-top: 6px;">Thank you for your support.</p>
    </div>
</body>
</html>
