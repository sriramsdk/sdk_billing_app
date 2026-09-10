<?php

namespace App\Http\Controllers\Employee;

use App\Actions\Orders\CreateOrderAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Customer;
use App\Models\Product;

class BillingController extends Controller
{
    public function create()
    {
        $employeeId = auth()->user()->employee->id;

        $customers = Customer::where('is_active', true)
            ->withCount(['orders as attended_orders_count' => fn ($query) => $query->where('employee_id', $employeeId)])
            ->orderByDesc('attended_orders_count')
            ->latest()
            ->get();

        $products = Product::with('stock')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'employee.billing.create',
            compact('customers', 'products')
        );
    }

    public function store(
        StoreOrderRequest $request,
        CreateOrderAction $createOrderAction
    ) {
        $employee = auth()->user()->employee;

        $order = $createOrderAction->execute(
            employeeId: $employee->id,
            customerId: $request->customer_id,
            customerName: $request->customer_name,
            customerEmail: $request->customer_email,
            items: $request->items,
            amountPaid: (float) $request->amount_paid
        );

        return redirect()
            ->route('employee.orders.show', $order)
            ->with(
                'success',
                "Bill {$order->invoice_number} created successfully."
            );
    }
}