<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $employeeId = auth()->user()->employee->id;
        $today = now()->startOfDay();
        $week = now()->copy()->startOfWeek();
        $month = now()->copy()->startOfMonth();

        $base = Order::where('employee_id', $employeeId)->where('status', 'completed');
        $todaySales = (clone $base)->where('created_at', '>=', $today)->sum('grand_total');
        $weeklySales = (clone $base)->where('created_at', '>=', $week)->sum('grand_total');
        $monthlySales = (clone $base)->where('created_at', '>=', $month)->sum('grand_total');
        $todayOrders = (clone $base)->where('created_at', '>=', $today)->count();
        $weeklyOrders = (clone $base)->where('created_at', '>=', $week)->count();
        $monthlyOrders = (clone $base)->where('created_at', '>=', $month)->count();
        $customersAttended = (clone $base)->where('created_at', '>=', $today)->whereNotNull('customer_id')->distinct('customer_id')->count('customer_id');
        $recentOrders = (clone $base)->with('customer')->latest()->limit(8)->get();

        return view('employee.dashboard', compact(
            'todaySales', 'weeklySales', 'monthlySales',
            'todayOrders', 'weeklyOrders', 'monthlyOrders',
            'customersAttended', 'recentOrders'
        ));
    }
}