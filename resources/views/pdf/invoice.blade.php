<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->invoice_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f1f5f9; color: #1e293b; font-family: DejaVu Sans, sans-serif; }
        .sheet { width: 210mm; min-height: 297mm; margin: 24px auto; padding: 42px; background: #fff; }
        .top { display: flex; justify-content: space-between; border-bottom: 4px solid #2563eb; padding-bottom: 28px; }
        .brand { font-size: 28px; font-weight: 900; color: #0f172a; } .brand span { color: #2563eb; }
        .muted { color: #64748b; font-size: 12px; } .invoice { text-align: right; }
        .invoice h1 { margin: 0 0 8px; font-size: 26px; } .section { margin-top: 28px; }
        .grid { display: flex; gap: 48px; } .grid > div { flex: 1; }
        .label { margin-bottom: 8px; color: #64748b; font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
        .value { font-size: 14px; font-weight: 700; } table { width: 100%; border-collapse: collapse; margin-top: 24px; }
        th { padding: 12px 10px; background: #eff6ff; color: #475569; font-size: 10px; text-align: left; text-transform: uppercase; }
        td { padding: 14px 10px; border-bottom: 1px solid #e2e8f0; font-size: 12px; } .right { text-align: right; }
        .totals { width: 300px; margin: 26px 0 0 auto; } .total-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 13px; }
        .grand { border-top: 2px solid #0f172a; margin-top: 6px; padding-top: 14px; font-size: 20px; font-weight: 900; }
        .footer { margin-top: 70px; border-top: 1px solid #e2e8f0; padding-top: 16px; text-align: center; }
        @media print { body { background: #fff; } .sheet { width: auto; min-height: auto; margin: 0; padding: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
<div class="sheet">
    <div class="top"><div><div class="brand">MT <span>Billing</span></div><div class="muted">Store billing and operations</div></div><div class="invoice"><h1>INVOICE</h1><div class="value">{{ $order->invoice_number }}</div><div class="muted">{{ $order->created_at->format('d M Y, h:i A') }}</div></div></div>
    <div class="section grid"><div><div class="label">Bill To</div><div class="value">{{ $order->customer?->name ?? 'Walk-in Customer' }}</div><div class="muted">{{ $order->customer?->email }}</div><div class="muted">{{ $order->customer?->phone }}</div></div><div><div class="label">Billed By</div><div class="value">{{ $order->employee?->user?->name }}</div><div class="muted">{{ $order->employee?->employee_code }}</div></div></div>
    <table><thead><tr><th>Product</th><th>SKU</th><th>Qty</th><th>Unit Price</th><th class="right">Line Total</th></tr></thead><tbody>@foreach($order->items as $item)<tr><td>{{ $item->product->name }}</td><td>{{ $item->product->sku }}</td><td>{{ $item->quantity }}</td><td>₹{{ number_format($item->unit_price, 2) }}</td><td class="right">₹{{ number_format($item->line_total, 2) }}</td></tr>@endforeach</tbody></table>
    <div class="totals"><div class="total-row"><span>Subtotal</span><span>₹{{ number_format($order->subtotal, 2) }}</span></div><div class="total-row"><span>Tax</span><span>₹{{ number_format($order->tax_amount, 2) }}</span></div><div class="total-row grand"><span>Total</span><span>₹{{ number_format($order->grand_total, 2) }}</span></div><div class="total-row"><span>Amount paid</span><span>₹{{ number_format($order->amount_paid, 2) }}</span></div></div>
    <div class="footer muted">Thank you for shopping with MT Billing.</div>
</div>
<script>window.addEventListener('load', () => window.print());</script>
</body>
</html>