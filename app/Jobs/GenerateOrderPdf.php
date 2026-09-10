<?php

namespace App\Jobs;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use App\Services\OrderAuditService;

class GenerateOrderPdf implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $orderId
    ) {}

    public function handle(): void
    {
        $order = Order::with([
            'customer',
            'employee.user',
            'items.product',
        ])->findOrFail($this->orderId);

        $path = app(\App\Services\OrderDocumentService::class)->pdf($order);
        app(OrderAuditService::class)->record('invoice_pdf_generated', $order, ['path' => $path]);
    }
}