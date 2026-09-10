<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;

class DashboardController extends Controller
{
    public function index()
    {
        $todaySales = Order::whereDate('created_at', today())
            ->sum('grand_total');

        $todayOrders = Order::whereDate('created_at', today())
            ->count();

        $customers = Customer::where('is_active', true)->count();

        $lowStockProducts = Product::with('stock')
            ->whereHas('stock', function ($query) {
                $query->whereColumn(
                    'quantity',
                    '<=',
                    'products.low_stock_threshold'
                );
            })
            ->count();

        $recentOrders = Order::with([
                'customer',
                'employee.user'
            ])
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact(
            'todaySales',
            'todayOrders',
            'customers',
            'lowStockProducts',
            'recentOrders'
        ));
    }
}