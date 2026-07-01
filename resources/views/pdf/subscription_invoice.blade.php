<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; font-size: 14px; margin: 0; padding: 20px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #10b981; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #10b981; font-size: 28px; }
        .header p { margin: 5px 0 0; color: #666; font-size: 14px; }
        .details { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .details td { padding: 5px; vertical-align: top; }
        .details .title { font-weight: bold; color: #555; width: 40%; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table.items th, table.items td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        table.items th { background-color: #f8f9fa; font-weight: bold; color: #333; }
        .total { font-size: 18px; font-weight: bold; color: #10b981; }
        .footer { margin-top: 40px; text-align: center; color: #777; font-size: 12px; border-top: 1px solid #eee; padding-top: 20px; }
        .status { padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; text-transform: uppercase; }
        .status.success, .status.completed { background: #d1fae5; color: #065f46; }
        .status.pending { background: #fef3c7; color: #92400e; }
        .status.failed { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div style="text-align: right; margin-bottom: 20px;">
            <h1 style="color: #10b981; margin: 0;">INVOICE</h1>
            <p style="margin: 5px 0 0; color: #555;">#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>

        <table style="width: 100%; margin-bottom: 30px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <h3 style="margin-top: 0; color: #333;">Billed To:</h3>
                    <strong>{{ $company->name }}</strong><br>
                    Email: {{ $company->email }}<br>
                    Phone: {{ $company->phone }}<br>
                    Address: {{ $company->address ?? 'N/A' }}
                </td>
                <td style="width: 50%; vertical-align: top; text-align: right;">
                    <h3 style="margin-top: 0; color: #333;">Payment Details:</h3>
                    Date: {{ $payment->created_at->format('M d, Y H:i A') }}<br>
                    Gateway: <span style="text-transform: uppercase;">{{ $payment->gateway }}</span><br>
                    TrxID: {{ $payment->transaction_id ?? 'N/A' }}<br>
                    Sender No: {{ $payment->sender_account_no ?? 'N/A' }}<br>
                    Status: <span class="status {{ $payment->status }}">{{ $payment->status }}</span>
                </td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        Software Subscription Payment<br>
                        <small style="color: #666;">Manual payment verification</small>
                    </td>
                    <td style="text-align: right;">BDT {{ number_format($payment->amount, 2) }}</td>
                </tr>
                <tr>
                    <td style="text-align: right; padding-top: 20px;"><strong>Total:</strong></td>
                    <td style="text-align: right; padding-top: 20px;" class="total">BDT {{ number_format($payment->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        @if($payment->status === 'failed' && $payment->rejection_reason)
        <div style="margin-top: 20px; padding: 15px; background: #fee2e2; border-left: 4px solid #ef4444;">
            <strong>Rejection Reason:</strong> {{ $payment->rejection_reason }}
        </div>
        @endif

        <div class="footer">
            Thank you for your business!<br>
            If you have any questions about this invoice, please contact support.
        </div>
    </div>
</body>
</html>
