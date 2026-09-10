<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\MailLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $employees = collect(range(1, 10))->map(function (int $number): Employee {
            $user = User::firstOrCreate(
                ['email' => "sample.employee{$number}@mtbilling.test"],
                [
                    'name' => "Sample Employee {$number}",
                    'password' => Hash::make('Employee@12345'),
                    'role' => 'employee',
                    'is_active' => true,
                ]
            );

            return Employee::firstOrCreate(
                ['employee_code' => sprintf('SMP%03d', $number)],
                [
                    'user_id' => $user->id,
                    'phone' => '900000' . str_pad((string) $number, 4, '0', STR_PAD_LEFT),
                    'department' => 'Sales',
                    'designation' => 'Billing Executive',
                    'is_active' => true,
                ]
            );
        });

        $customers = collect(range(1, 10))->map(fn (int $number): Customer => Customer::firstOrCreate(
            ['email' => "sample.customer{$number}@example.com"],
            [
                'name' => "Sample Customer {$number}",
                'phone' => '800000' . str_pad((string) $number, 4, '0', STR_PAD_LEFT),
                'address' => "Sample Street {$number}, Bengaluru",
                'is_active' => true,
            ]
        ));

        $products = collect(range(1, 10))->map(function (int $number): Product {
            return Product::firstOrCreate(
                ['sku' => sprintf('SAMPLE-%03d', $number)],
                [
                    'name' => "Sample Product {$number}",
                    'description' => 'Sample inventory item for development and demonstration.',
                    'selling_price' => 100 + ($number * 25),
                    'tax_rate' => $number % 2 === 0 ? 18 : 12,
                    'low_stock_threshold' => 10,
                    'is_active' => true,
                ]
            );
        });

        $products->each(function (Product $product, int $index): void {
            Stock::firstOrCreate(
                ['product_id' => $product->id],
                [
                    'quantity' => $index === 0 ? 5 : 40 + ($index * 5),
                    'reserved_quantity' => 0,
                ]
            );
        });

        $orders = collect(range(1, 10))->map(function (int $number) use ($employees, $customers, $products): Order {
            $product = $products[$number - 1];
            $quantity = $number % 3 + 1;
            $subtotal = (float) $product->selling_price * $quantity;
            $taxAmount = $subtotal * ((float) $product->tax_rate / 100);
            $grandTotal = $subtotal + $taxAmount;
            $invoiceNumber = sprintf('SAMPLE-INV-%03d', $number);

            return Order::firstOrCreate(
                ['invoice_number' => $invoiceNumber],
                [
                    'employee_id' => $employees[$number - 1]->id,
                    'customer_id' => $customers[$number - 1]->id,
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'grand_total' => $grandTotal,
                    'amount_paid' => $grandTotal,
                    'balance_amount' => 0,
                    'payment_status' => 'paid',
                    'status' => 'completed',
                    'billed_at' => now()->subDays($number),
                ]
            );
        });

        $orders->each(function (Order $order, int $index) use ($products, $customers, $employees): void {
            $product = $products[$index];
            $quantity = $index % 3 + 1;
            $lineSubtotal = (float) $product->selling_price * $quantity;
            $lineTax = $lineSubtotal * ((float) $product->tax_rate / 100);

            OrderItem::firstOrCreate(
                [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                ],
                [
                    'quantity' => $quantity,
                    'unit_price' => $product->selling_price,
                    'tax_rate' => $product->tax_rate,
                    'tax_amount' => $lineTax,
                    'line_total' => $lineSubtotal + $lineTax,
                ]
            );

            AuditLog::firstOrCreate(
                [
                    'action' => 'sample_order_created',
                    'auditable_type' => Order::class,
                    'auditable_id' => $order->id,
                ],
                [
                    'user_id' => $employees[$index]->user_id,
                    'module' => 'orders',
                    'new_values' => [
                        'invoice_number' => $order->invoice_number,
                        'source' => 'SampleDataSeeder',
                    ],
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'SampleDataSeeder',
                ]
            );

            MailLog::firstOrCreate(
                [
                    'order_id' => $order->id,
                    'recipient' => $customers[$index]->email,
                    'subject' => 'Invoice ' . $order->invoice_number . ' from MT Billing',
                ],
                [
                    'user_id' => $employees[$index]->user_id,
                    'status' => 'sent',
                    'message_id' => '<sample-' . $order->id . '@mtbilling.test>',
                    'sent_at' => now()->subDays($index),
                ]
            );
        });
    }
}
