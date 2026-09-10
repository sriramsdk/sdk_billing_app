<?php

namespace App\Actions\Orders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Jobs\SendOrderInvoiceEmail;
use App\Models\MailLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\OrderAuditService;

class CreateOrderAction
{
    public function execute(
        int $employeeId,
        ?int $customerId,
        ?string $customerName,
        ?string $customerEmail,
        array $items,
        float $amountPaid,
    ): Order {
        return DB::transaction(function () use ($employeeId, $customerId, $customerName, $customerEmail, $items, $amountPaid): Order {
            $customer = $this->resolveCustomer($customerId, $customerName, $customerEmail);
            $subtotal = 0;
            $taxAmount = 0;
            $lineItems = [];

            foreach ($items as $item) {
                $product = Product::with('stock')->lockForUpdate()->findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];
                $available = $product->stock?->quantity ?? 0;

                if (! $product->is_active || ! $product->stock || $quantity > $available) {
                    throw ValidationException::withMessages([
                        'items' => "{$product->name} does not have enough stock available.",
                    ]);
                }

                $unitPrice = (float) $product->selling_price;
                $lineSubtotal = $unitPrice * $quantity;
                $lineTax = $lineSubtotal * ((float) $product->tax_rate / 100);
                $subtotal += $lineSubtotal;
                $taxAmount += $lineTax;
                $lineItems[] = compact('product', 'quantity', 'unitPrice', 'lineSubtotal', 'lineTax');
            }

            $grandTotal = $subtotal + $taxAmount;
            if ($amountPaid < $grandTotal) {
                throw ValidationException::withMessages([
                    'amount_paid' => 'Amount received is less than the grand total.',
                ]);
            }

            $order = Order::create([
                'invoice_number' => 'INV-' . now()->format('YmdHis') . '-' . random_int(100, 999),
                'employee_id' => $employeeId,
                'customer_id' => $customer?->id,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'amount_paid' => $amountPaid,
                'balance_amount' => max($amountPaid - $grandTotal, 0),
                'payment_status' => 'paid',
                'status' => 'completed',
                'billed_at' => now(),
            ]);

            app(OrderAuditService::class)->record('order_created', $order, [
                'invoice_number' => $order->invoice_number,
                'customer_id' => $customer?->id,
                'employee_id' => $employeeId,
                'grand_total' => $grandTotal,
            ]);

            foreach ($lineItems as $line) {
                $order->items()->create([
                    'product_id' => $line['product']->id,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unitPrice'],
                    'tax_amount' => $line['lineTax'],
                    'tax_rate' => $line['product']->tax_rate,
                    'line_total' => $line['lineSubtotal'] + $line['lineTax'],
                ]);

                $line['product']->stock->decrement('quantity', $line['quantity']);
            }

            if ($customer?->email) {
                $mailLog = MailLog::create([
                    'order_id' => $order->id,
                    'user_id' => auth()->id(),
                    'recipient' => $customer->email,
                    'subject' => 'Invoice ' . $order->invoice_number . ' from MT Billing',
                    'status' => 'queued',
                ]);

                SendOrderInvoiceEmail::dispatch($order->id, $mailLog->id)
                    ->onQueue('mail')
                    ->afterCommit();
            }

            return $order;
        });
    }

    private function resolveCustomer(?int $customerId, ?string $customerName, ?string $customerEmail): ?Customer
    {
        if ($customerId) {
            return Customer::whereKey($customerId)->where('is_active', true)->firstOrFail();
        }

        if (! $customerEmail) {
            return null;
        }

        $customer = Customer::where('email', strtolower(trim($customerEmail)))->first();

        if ($customer && ! $customer->is_active) {
            throw ValidationException::withMessages([
                'customer_email' => 'This customer is inactive. Activate the customer before billing.',
            ]);
        }

        return $customer ?: Customer::create([
            'name' => $customerName ?: 'Customer',
            'email' => strtolower(trim($customerEmail)),
            'is_active' => true,
        ]);
    }
}