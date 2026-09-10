@extends(($isAdmin ?? false) ? 'layouts.admin' : 'layouts.employee')

@section('title', $order->invoice_number)
@section('page-title', 'Order Details')

@section('content')

<div class="space-y-6">

    @if(session('success'))

        <div class="rounded-xl bg-emerald-50 px-5 py-4
                    text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>

    @endif


    <div class="flex flex-col justify-between gap-5 md:flex-row md:items-center">

        <div>

            <a
                href="{{ route($isAdmin ?? false ? 'admin.orders' : 'employee.orders') }}"
                class="text-sm font-semibold text-slate-500
                       hover:text-blue-600"
            >
                ← Back to Orders
            </a>

            <div class="mt-3 flex items-center gap-3">

                <h2 class="text-3xl font-black">
                    {{ $order->invoice_number }}
                </h2>

                <span
                    class="rounded-full bg-emerald-50
                           px-3 py-1 text-xs font-bold
                           text-emerald-700"
                >
                    {{ strtoupper($order->payment_status) }}
                </span>

            </div>

            <p class="mt-2 text-sm text-slate-500">
                Created
                {{ $order->created_at->format('d M Y, h:i A') }}
            </p>

        </div>


        <div class="flex flex-wrap gap-3">

            <a
            href="{{ route($isAdmin ?? false ? 'admin.orders.print' : 'employee.orders.print', $order) }}"
            target="_blank"
                data-loading-link
                class="rounded-xl border border-slate-200
                       bg-white px-5 py-3 text-sm font-bold
                       hover:bg-slate-50"
            >
                🖨 Print Bill
            </a>

            <a href="{{ route($isAdmin ?? false ? 'admin.orders.download' : 'employee.orders.download', $order) }}" target="_blank" rel="noopener" class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold hover:bg-slate-50">↓ Download PDF</a>

            @if($order->customer?->email)
                <form method="POST" action="{{ route($isAdmin ?? false ? 'admin.orders.email' : 'employee.orders.email', $order) }}" data-loading data-swal-confirm data-swal-title="Email this invoice?" data-swal-text="The bill will be sent to {{ $order->customer->email }}." data-swal-icon="question" data-swal-confirm-text="Send invoice">
                    @csrf
                    <input type="hidden" name="email" value="{{ $order->customer->email }}">
                    <button class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white">✉ Email Bill</button>
                </form>
            @endif

        </div>

    </div>


    <div class="grid gap-6 lg:grid-cols-3">

        <!-- Customer -->
        <div class="rounded-2xl bg-white p-6
                    shadow-sm ring-1 ring-slate-200">

            <p class="text-xs font-bold uppercase
                      tracking-wider text-slate-400">
                Customer
            </p>

            <h3 class="mt-4 text-xl font-black">

                {{ $order->customer?->name ?? 'Walk-in Customer' }}

            </h3>

            @if($order->customer)

                @if($order->customer->email)

                    <p class="mt-3 text-sm text-slate-500">
                        {{ $order->customer->email }}
                    </p>

                @endif

                @if($order->customer->phone)

                    <p class="mt-1 text-sm text-slate-500">
                        {{ $order->customer->phone }}
                    </p>

                @endif

                @if($order->customer->address)

                    <p class="mt-4 text-sm leading-6 text-slate-500">
                        {{ $order->customer->address }}
                    </p>

                @endif

            @endif

        </div>


        <!-- Employee -->
        <div class="rounded-2xl bg-white p-6
                    shadow-sm ring-1 ring-slate-200">

            <p class="text-xs font-bold uppercase
                      tracking-wider text-slate-400">
                Billed By
            </p>

            <h3 class="mt-4 text-xl font-black">
                {{ $order->employee?->user?->name }}
            </h3>

            <p class="mt-2 text-sm text-slate-500">
                {{ $order->employee?->employee_code }}
            </p>

        </div>


        <!-- Payment -->
        <div class="rounded-2xl bg-slate-950 p-6 text-white">

            <p class="text-xs font-bold uppercase
                      tracking-wider text-slate-500">
                Payment
            </p>

            <div class="mt-5 space-y-3">

                <div class="flex justify-between text-sm">

                    <span class="text-slate-400">
                        Subtotal
                    </span>

                    <span>
                        ₹{{ number_format($order->subtotal, 2) }}
                    </span>

                </div>

                <div class="flex justify-between text-sm">

                    <span class="text-slate-400">
                        Tax
                    </span>

                    <span>
                        ₹{{ number_format($order->tax_amount, 2) }}
                    </span>

                </div>

                <div class="border-t border-white/10 pt-4">

                    <div class="flex justify-between">

                        <span class="font-bold">
                            Grand Total
                        </span>

                        <span class="text-2xl font-black">
                            ₹{{ number_format($order->grand_total, 2) }}
                        </span>

                    </div>

                </div>

                <div class="flex justify-between text-sm">

                    <span class="text-slate-400">
                        Amount Paid
                    </span>

                    <span>
                        ₹{{ number_format($order->amount_paid, 2) }}
                    </span>

                </div>

                <div class="flex justify-between text-sm">

                    <span class="text-slate-400">
                        Change
                    </span>

                    <span class="font-bold text-emerald-400">
                        ₹{{ number_format($order->balance_amount, 2) }}
                    </span>

                </div>

            </div>

        </div>

    </div>


    <!-- Items -->
    <div class="overflow-hidden rounded-2xl bg-white
                shadow-sm ring-1 ring-slate-200">

        <div class="border-b px-6 py-5">

            <h3 class="font-bold">
                Products
            </h3>

            <p class="mt-1 text-xs text-slate-400">
                {{ $order->items->count() }} line items
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-left">

                <thead class="bg-slate-50 text-xs
                              uppercase tracking-wider
                              text-slate-500">

                <tr>

                    <th class="px-6 py-4">
                        Product
                    </th>

                    <th class="px-6 py-4">
                        Qty
                    </th>

                    <th class="px-6 py-4">
                        Unit Price
                    </th>

                    <th class="px-6 py-4">
                        Tax
                    </th>

                    <th class="px-6 py-4 text-right">
                        Total
                    </th>

                </tr>

                </thead>

                <tbody class="divide-y">

                @foreach($order->items as $item)

                    <tr>

                        <td class="px-6 py-5">

                            <div class="font-bold">
                                {{ $item->product->name }}
                            </div>

                            <div class="mt-1 text-xs text-slate-400">
                                SKU: {{ $item->product->sku }}
                            </div>

                        </td>

                        <td class="px-6 py-5">
                            {{ $item->quantity }}
                        </td>

                        <td class="px-6 py-5">
                            ₹{{ number_format($item->unit_price, 2) }}
                        </td>

                        <td class="px-6 py-5">
                            {{ number_format($item->tax_rate, 2) }}%
                        </td>

                        <td class="px-6 py-5 text-right font-black">
                            ₹{{ number_format($item->line_total, 2) }}
                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>

<section class="overflow-hidden mt-5 rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
    <div class="border-b px-6 py-5">
        <h3 class="font-bold">Order activity</h3>
        <p class="mt-1 text-xs text-slate-400">Audit events and invoice email history</p>
    </div>
    <div class="grid divide-y lg:grid-cols-2 lg:divide-x lg:divide-y-0">
        <div class="p-6">
            <h4 class="text-sm font-bold uppercase tracking-wider text-slate-500">Audit timeline</h4>
            <div class="mt-5 space-y-4">
                @forelse($order->auditLogs->sortByDesc('created_at') as $log)
                    <div class="border-l-2 border-blue-200 pl-4">
                        <div class="font-semibold text-slate-800">{{ ucwords(str_replace('_', ' ', $log->action)) }}</div>
                        <div class="mt-1 text-xs text-slate-500">{{ $log->user?->name ?? 'System' }} · {{ $log->created_at->timezone(config('app.timezone'))->format('d M Y, h:i A') }}</div>
                    </div>
                @empty
                    <div class="text-sm text-slate-500">No audit events recorded.</div>
                @endforelse
            </div>
        </div>
        <div class="p-6">
            <h4 class="text-sm font-bold uppercase tracking-wider text-slate-500">Email history</h4>
            <div class="mt-5 space-y-4">
                @forelse($order->mailLogs->sortByDesc(fn ($mail) => $mail->sent_at ?? $mail->created_at) as $mail)
                    <div class="border-l-2 {{ $mail->status === 'sent' ? 'border-emerald-300' : ($mail->status === 'failed' ? 'border-red-300' : 'border-amber-300') }} pl-4">
                        <div class="font-semibold text-slate-800">{{ ucfirst($mail->status) }} · {{ $mail->recipient }}</div>
                        <div class="mt-1 text-xs text-slate-500">{{ $mail->subject }} · {{ ($mail->sent_at ?? $mail->created_at)->timezone(config('app.timezone'))->format('d M Y, h:i A') }}</div>
                        @if($mail->error_message)<div class="mt-1 text-xs text-red-600">{{ $mail->error_message }}</div>@endif
                    </div>
                @empty
                    <div class="text-sm text-slate-500">No email attempts recorded.</div>
                @endforelse
            </div>
        </div>
    </div>
</section>

@endsection