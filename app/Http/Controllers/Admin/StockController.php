<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockRequest;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request): View
    {
        $stocks = Stock::with('product')
            ->when($request->search, fn ($query, $search) => $query->whereHas('product', fn ($product) => $product->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%")))
            ->when($request->status === 'low', fn ($query) => $query->whereHas('product', fn ($product) => $product->whereColumn('stocks.quantity', '<=', 'products.low_stock_threshold')))
            ->when($request->status === 'out', fn ($query) => $query->where('quantity', 0))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.stocks.index', compact('stocks'));
    }

    public function create(): View
    {
        $products = Product::whereDoesntHave('stock')->orderBy('name')->get();

        return view('admin.stocks.create', compact('products'));
    }

    public function store(StockRequest $request): RedirectResponse
    {
        Stock::create($request->validated());

        return redirect()->route('admin.stocks.index')
            ->with('success', 'Stock record was created.');
    }

    public function edit(Stock $stock): View
    {
        $products = Product::whereKey($stock->product_id)
            ->orWhereDoesntHave('stock')
            ->orderBy('name')
            ->get();

        return view('admin.stocks.edit', compact('stock', 'products'));
    }

    public function update(StockRequest $request, Stock $stock): RedirectResponse
    {
        $stock->update($request->validated());

        return redirect()->route('admin.stocks.index')
            ->with('success', 'Stock record was updated.');
    }

    public function destroy(Stock $stock): RedirectResponse
    {
        $stock->delete();

        return redirect()->route('admin.stocks.index')
            ->with('success', 'Stock record was deleted.');
    }
}