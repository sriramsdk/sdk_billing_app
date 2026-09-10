@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">

    <div>
        <p class="text-sm text-slate-500">
            Welcome back, {{ auth()->user()->name }}
        </p>

        <h2 class="mt-1 text-3xl font-black text-slate-900">
            Store overview
        </h2>
    </div>

</div>


<div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">

    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-500">
                Today's Sales
            </span>

            <span class="rounded-xl bg-blue-50 px-3 py-2 text-blue-600">
                ₹
            </span>
        </div>

        <div class="mt-5 text-3xl font-black">
            ₹{{ number_format($todaySales, 2) }}
        </div>

        <div class="mt-2 text-xs text-emerald-600">
            Today's revenue
        </div>
    </div>


    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-500">
                Today's Orders
            </span>

            <span class="rounded-xl bg-violet-50 px-3 py-2 text-violet-600">
                #
            </span>
        </div>

        <div class="mt-5 text-3xl font-black">
            {{ $todayOrders }}
        </div>

        <div class="mt-2 text-xs text-slate-500">
            Completed transactions
        </div>
    </div>


    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-500">
                Customers
            </span>

            <span class="rounded-xl bg-emerald-50 px-3 py-2 text-emerald-600">
                ●
            </span>
        </div>

        <div class="mt-5 text-3xl font-black">
            {{ $customers }}
        </div>

        <div class="mt-2 text-xs text-slate-500">
            Active customers
        </div>
    </div>


    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-500">
                Low Stock
            </span>

            <span class="rounded-xl bg-red-50 px-3 py-2 text-red-600">
                !
            </span>
        </div>

        <div class="mt-5 text-3xl font-black">
            {{ $lowStockProducts }}
        </div>

        <div class="mt-2 text-xs text-red-500">
            Products need attention
        </div>
    </div>

</div>


<div class="mt-6 grid gap-6 xl:grid-cols-3">

    <div class="xl:col-span-2 rounded-2xl bg-white
                shadow-sm ring-1 ring-slate-200">

        <div class="flex items-center justify-between border-b
                    px-6 py-5">

            <div>
                <h3 class="font-bold text-slate-900">
                    Recent Orders
                </h3>

                <p class="text-xs text-slate-500">
                    Latest billing activity
                </p>
            </div>

            <a href="{{ route('admin.orders') }}"
               class="text-sm font-semibold text-blue-600">
                View all →
            </a>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full text-left text-sm">

                <thead class="bg-slate-50 text-xs uppercase
                              tracking-wider text-slate-500">

                <tr>
                    <th class="px-6 py-4">Invoice</th>
                    <th class="px-6 py-4">Customer</th>
                    <th class="px-6 py-4">Employee</th>
                    <th class="px-6 py-4">Amount</th>
                    <th class="px-6 py-4">Status</th>
                </tr>

                </thead>

                <tbody class="divide-y">

                @forelse($recentOrders as $order)

                    <tr class="hover:bg-slate-50">

                        <td class="px-6 py-4 font-bold">
                            {{ $order->invoice_number }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $order->customer?->name ?? 'Walk-in' }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $order->employee?->user?->name }}
                        </td>

                        <td class="px-6 py-4 font-semibold">
                            ₹{{ number_format($order->grand_total, 2) }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="rounded-full bg-emerald-50
                                         px-3 py-1 text-xs font-semibold
                                         text-emerald-700">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5"
                            class="px-6 py-10 text-center text-slate-400">
                            No orders yet.
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>


    <div class="rounded-2xl bg-slate-950 p-6 text-white">

        <div class="text-sm text-slate-400">
            MT Billing
        </div>

        <h3 class="mt-2 text-2xl font-black">
            Store health
        </h3>

        <div class="mt-8 space-y-5">

            <div>
                <div class="mb-2 flex justify-between text-sm">
                    <span>Inventory</span>
                    <span class="text-emerald-400">{{ $lowStockProducts === 0 ? 'Healthy' : 'Review' }}</span>
                </div>

                <div class="h-2 rounded-full bg-white/10">
                    <div class="h-2 rounded-full {{ $lowStockProducts === 0 ? 'w-full bg-emerald-400' : 'w-3/4 bg-amber-400' }}"></div>
                </div>
            </div>

            <div>
                <div class="mb-2 flex justify-between text-sm">
                    <span>Orders</span>
                    <span class="text-blue-400">{{ $todayOrders > 0 ? 'Active' : 'Idle' }}</span>
                </div>

                <div class="h-2 rounded-full bg-white/10">
                    <div class="h-2 rounded-full {{ $todayOrders > 0 ? 'w-3/4 bg-blue-400' : 'w-1/4 bg-slate-500' }}"></div>
                </div>
            </div>

            <div>
                <div class="mb-2 flex justify-between text-sm">
                    <span>System</span>
                    <span class="{{ $systemHealth['status'] === 'Healthy' ? 'text-emerald-400' : ($systemHealth['status'] === 'Busy' ? 'text-amber-400' : 'text-red-400') }}">{{ $systemHealth['status'] }}</span>
                </div>

                <div class="h-2 rounded-full bg-white/10">
                    <div class="h-2 rounded-full {{ $systemHealth['status'] === 'Healthy' ? 'w-full bg-emerald-400' : ($systemHealth['status'] === 'Busy' ? 'w-3/4 bg-amber-400' : 'w-1/3 bg-red-400') }}"></div>
                </div>
            </div>

        </div>

    </div>

</div>

@endsection