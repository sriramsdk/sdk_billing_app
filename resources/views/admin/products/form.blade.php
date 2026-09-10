@if($errors->any())
    <div class="mb-6 rounded-xl bg-red-50 p-4 text-sm text-red-700">
        <ul class="list-disc space-y-1 pl-5">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif
<div class="space-y-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="mb-2 block text-sm font-semibold">Product name</label>
            <input name="name" value="{{ old('name', $product->name ?? '') }}" required class="w-full rounded-xl border border-slate-300 px-4 py-3">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">SKU</label>
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-500">{{ $product->sku ?? 'Generated automatically' }}</div>
        </div>
        <div class="md:col-span-2">
            <label class="mb-2 block text-sm font-semibold">Description</label>
            <textarea name="description" rows="4" class="w-full rounded-xl border border-slate-300 px-4 py-3">{{ old('description', $product->description ?? '') }}</textarea>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Selling price</label>
            <input type="number" name="selling_price" value="{{ old('selling_price', $product->selling_price ?? '') }}" min="0" step="0.01" required class="w-full rounded-xl border border-slate-300 px-4 py-3">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Tax rate (%)</label>
            <input type="number" name="tax_rate" value="{{ old('tax_rate', $product->tax_rate ?? 0) }}" min="0" max="100" step="0.01" required class="w-full rounded-xl border border-slate-300 px-4 py-3">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold">Low stock threshold</label>
            <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 10) }}" min="0" required class="w-full rounded-xl border border-slate-300 px-4 py-3">
        </div>
        <label class="flex items-center gap-3 pt-8 text-sm font-semibold">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-blue-600">
            Product is active
        </label>
    </div>
    <div class="flex justify-end gap-3 border-t pt-5">
        <a href="{{ route('admin.products.index') }}" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</a>
        <button class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700">Save Product</button>
    </div>
</div>