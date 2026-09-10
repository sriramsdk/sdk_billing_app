<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with('stock')
            ->when($request->search, fn ($query, $search) => $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            }))
            ->when($request->status, fn ($query, $status) => $query->where('is_active', $status === 'active'))
            ->when($request->stock_status === 'low', fn ($query) => $query->whereHas('stock', fn ($stock) => $stock->whereColumn('quantity', '<=', 'products.low_stock_threshold')))
            ->when($request->stock_status === 'out', fn ($query) => $query->whereHas('stock', fn ($stock) => $stock->where('quantity', 0)))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.products.create');
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = Product::create([
            ...$request->validated(),
            'sku' => $this->nextSku(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', "Product {$product->name} was created.");
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', "Product {$product->name} was updated.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->orderItems()->exists()) {
            return back()->with('error', 'Products used in orders cannot be deleted. Deactivate the product instead.');
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product was deleted.');
    }

    private function nextSku(): string
    {
        $number = ((int) Product::max('id')) + 1;

        do {
            $sku = sprintf('PRD-%05d', $number++);
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }
}