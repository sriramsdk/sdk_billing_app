@extends('layouts.admin')

@section('title', 'Products')
@section('page-title', 'Products')

@section('content')
<div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
    <div>
        <p class="text-sm text-slate-500">Catalog management</p>
        <h2 class="mt-1 text-3xl font-black text-slate-900">Products</h2>
    </div>
    <a href="{{ route('admin.products.create') }}" class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-700">
        + Add Product
    </a>
</div>

@include('admin.partials.flash')
<form method="GET" class="mb-6 flex flex-col gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 lg:flex-row"><input name="search" value="{{ request('search') }}" placeholder="Product name or SKU" class="flex-1 rounded-xl border border-slate-300 px-4 py-3"><select name="status" class="rounded-xl border border-slate-300 bg-white px-4 py-3"><option value="">All statuses</option><option value="active" @selected(request('status') === 'active')>Active</option><option value="inactive" @selected(request('status') === 'inactive')>Inactive</option></select><select name="stock_status" class="rounded-xl border border-slate-300 bg-white px-4 py-3"><option value="">All stock</option><option value="low" @selected(request('stock_status') === 'low')>Low stock</option><option value="out" @selected(request('stock_status') === 'out')>Out of stock</option></select><button class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white">Search</button><a href="{{ route('admin.products.index') }}" class="rounded-xl border border-slate-200 px-5 py-3 text-center text-sm font-semibold text-slate-600">Reset</a></form>

<div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[850px] text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-6 py-4">Product</th>
                    <th class="px-6 py-4">SKU</th>
                    <th class="px-6 py-4">Price</th>
                    <th class="px-6 py-4">Stock</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($products as $product)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900">{{ $product->name }}</div>
                            <div class="mt-1 max-w-xs truncate text-xs text-slate-500">{{ $product->description ?: 'No description' }}</div>
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-slate-600">{{ $product->sku }}</td>
                        <td class="px-6 py-4 font-semibold">₹{{ number_format((float) $product->selling_price, 2) }}</td>
                        <td class="px-6 py-4">
                            @php($quantity = $product->stock?->quantity)
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $quantity === null ? 'bg-slate-100 text-slate-600' : ($product->isLowStock() ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700') }}">
                                {{ $quantity === null ? 'Not set' : $quantity }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $product->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.products.edit', $product) }}" class="font-semibold text-blue-600 hover:text-blue-800">Edit</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="ml-4 inline" data-swal-confirm data-swal-title="Delete product?" data-swal-text="This action cannot be undone." data-swal-confirm-text="Yes, delete">
                                @csrf
                                @method('DELETE')
                                <button class="font-semibold text-red-600 hover:text-red-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400">No products have been added yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
        <div class="border-t px-6 py-4">{{ $products->links() }}</div>
    @endif
</div>
@endsection