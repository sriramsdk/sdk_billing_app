@extends('layouts.admin')

@section('title', 'Stock')
@section('page-title', 'Stock')

@section('content')
<div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
    <div>
        <p class="text-sm text-slate-500">Inventory management</p>
        <h2 class="mt-1 text-3xl font-black text-slate-900">Stock</h2>
    </div>
    <a href="{{ route('admin.stocks.create') }}" class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-700">+ Add Stock Record</a>
</div>
@include('admin.partials.flash')
<form method="GET" class="mb-6 flex flex-col gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:flex-row"><input name="search" value="{{ request('search') }}" placeholder="Product name or SKU" class="flex-1 rounded-xl border border-slate-300 px-4 py-3"><select name="status" class="rounded-xl border border-slate-300 bg-white px-4 py-3"><option value="">All stock</option><option value="low" @selected(request('status') === 'low')>Low stock</option><option value="out" @selected(request('status') === 'out')>Out of stock</option></select><button class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white">Search</button><a href="{{ route('admin.stocks.index') }}" class="rounded-xl border border-slate-200 px-5 py-3 text-center text-sm font-semibold text-slate-600">Reset</a></form>
<div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[700px] text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-6 py-4">Product</th><th class="px-6 py-4">Available</th><th class="px-6 py-4">Reserved</th><th class="px-6 py-4">Status</th><th class="px-6 py-4 text-right">Actions</th></tr></thead>
            <tbody class="divide-y">
                @forelse($stocks as $stock)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4"><div class="font-bold">{{ $stock->product->name }}</div><div class="mt-1 text-xs text-slate-500">{{ $stock->product->sku }}</div></td>
                        <td class="px-6 py-4 font-semibold">{{ $stock->quantity }}</td>
                        <td class="px-6 py-4">{{ $stock->reserved_quantity }}</td>
                        <td class="px-6 py-4"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $stock->isOutOfStock() ? 'bg-red-50 text-red-700' : ($stock->isLowStock() ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700') }}">{{ $stock->isOutOfStock() ? 'Out of stock' : ($stock->isLowStock() ? 'Low stock' : 'Healthy') }}</span></td>
                        <td class="px-6 py-4 text-right"><a href="{{ route('admin.stocks.edit', $stock) }}" class="font-semibold text-blue-600 hover:text-blue-800">Edit</a><form method="POST" action="{{ route('admin.stocks.destroy', $stock) }}" class="ml-4 inline" data-swal-confirm data-swal-title="Delete stock record?" data-swal-text="This action cannot be undone." data-swal-confirm-text="Yes, delete">@csrf @method('DELETE')<button class="font-semibold text-red-600 hover:text-red-800">Delete</button></form></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-slate-400">No stock records have been added yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($stocks->hasPages())<div class="border-t px-6 py-4">{{ $stocks->links() }}</div>@endif
</div>
@endsection