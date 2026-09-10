<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $order->invoice_number }} | MT Billing</title>
</head>
<body style="margin:0;background:#eef3f8;color:#172033;font-family:Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef3f8;padding:32px 12px;">
    <tr><td align="center">
        <table role="presentation" width="640" cellspacing="0" cellpadding="0" style="width:100%;max-width:640px;background:#ffffff;border-radius:18px;overflow:hidden;box-shadow:0 8px 28px rgba(23,32,51,.10);">
            <tr><td style="height:7px;background:#2563eb;font-size:0;line-height:0;">&nbsp;</td></tr>
            <tr><td style="padding:30px 34px 24px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0"><tr>
                    <td><div style="font-size:27px;font-weight:800;color:#0f172a;">MT <span style="color:#2563eb;">Billing</span></div><div style="margin-top:5px;color:#64748b;font-size:12px;">Store billing and operations</div></td>
                    <td align="right"><div style="color:#64748b;font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;">Invoice</div><div style="margin-top:6px;color:#0f172a;font-size:16px;font-weight:800;">{{ $order->invoice_number }}</div><div style="margin-top:4px;color:#64748b;font-size:12px;">{{ $order->created_at->timezone(config('app.timezone'))->format('d M Y, h:i A') }}</div></td>
                </tr></table>
            </td></tr>
            <tr><td style="padding:0 34px;"><div style="height:1px;background:#e5eaf0;"></div></td></tr>
            <tr><td style="padding:24px 34px 10px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0"><tr>
                    <td valign="top" width="50%"><div style="color:#2563eb;font-size:10px;font-weight:800;letter-spacing:1px;text-transform:uppercase;">Billed to</div><div style="margin-top:8px;color:#172033;font-size:15px;font-weight:700;">{{ $order->customer?->name ?? 'Walk-in Customer' }}</div><div style="margin-top:5px;color:#64748b;font-size:12px;">{{ $order->customer?->email ?: 'No email provided' }}</div><div style="margin-top:3px;color:#64748b;font-size:12px;">{{ $order->customer?->phone ?: '' }}</div></td>
                    <td valign="top" width="50%"><div style="color:#2563eb;font-size:10px;font-weight:800;letter-spacing:1px;text-transform:uppercase;">Payment status</div><div style="margin-top:8px;display:inline-block;border-radius:999px;background:#dcfce7;color:#047857;padding:6px 11px;font-size:11px;font-weight:800;">{{ strtoupper($order->payment_status) }}</div><div style="margin-top:10px;color:#64748b;font-size:12px;">Billed by {{ $order->employee?->user?->name ?? 'MT Billing' }}</div></td>
                </tr></table>
            </td></tr>
            <tr><td style="padding:16px 34px 0;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;font-size:12px;">
                    <thead><tr style="background:#eff6ff;color:#385477;text-align:left;"><th style="padding:12px 10px;">Item</th><th style="padding:12px 10px;">Qty</th><th style="padding:12px 10px;text-align:right;">Unit price</th><th style="padding:12px 10px;text-align:right;">Total</th></tr></thead>
                    <tbody>@foreach($order->items as $item)<tr><td style="padding:13px 10px;border-bottom:1px solid #e8edf3;color:#172033;font-weight:700;">{{ $item->product->name }}<div style="margin-top:3px;color:#94a3b8;font-size:10px;font-weight:400;">SKU {{ $item->product->sku }} · Tax {{ number_format($item->tax_rate, 2) }}%</div></td><td style="padding:13px 10px;border-bottom:1px solid #e8edf3;color:#475569;">{{ $item->quantity }}</td><td style="padding:13px 10px;border-bottom:1px solid #e8edf3;text-align:right;color:#475569;">₹{{ number_format($item->unit_price, 2) }}</td><td style="padding:13px 10px;border-bottom:1px solid #e8edf3;text-align:right;color:#172033;font-weight:700;">₹{{ number_format($item->line_total, 2) }}</td></tr>@endforeach</tbody>
                </table>
            </td></tr>
            <tr><td style="padding:24px 34px 28px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0"><tr><td>&nbsp;</td><td width="245" style="width:245px;"><table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="font-size:13px;color:#64748b;"><tr><td style="padding:5px 0;">Subtotal</td><td align="right" style="padding:5px 0;color:#172033;">₹{{ number_format($order->subtotal, 2) }}</td></tr><tr><td style="padding:5px 0;">Tax</td><td align="right" style="padding:5px 0;color:#172033;">₹{{ number_format($order->tax_amount, 2) }}</td></tr><tr><td colspan="2" style="padding-top:10px;"><div style="height:1px;background:#172033;"></div></td></tr><tr><td style="padding:12px 0 5px;color:#172033;font-size:16px;font-weight:800;">Total</td><td align="right" style="padding:12px 0 5px;color:#2563eb;font-size:18px;font-weight:800;">₹{{ number_format($order->grand_total, 2) }}</td></tr><tr><td style="padding:5px 0;">Amount paid</td><td align="right" style="padding:5px 0;color:#047857;font-weight:700;">₹{{ number_format($order->amount_paid, 2) }}</td></tr></table></td></tr></table>
            </td></tr>
            <tr><td style="background:#0f172a;padding:22px 34px;color:#cbd5e1;"><div style="color:#ffffff;font-size:14px;font-weight:700;">Thank you for choosing MT Billing.</div><div style="margin-top:6px;font-size:11px;line-height:1.6;">This is an automated invoice email. Please keep this message for your records.</div></td></tr>
        </table>
    </td></tr>
</table>
</body>
</html>
