<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderAuditService;
use App\Services\OrderDocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::with(['customer', 'employee.user'])
            ->withCount('items')
            ->when($request->search, function ($query, $search): void {
                $query->where(function ($searchQuery) use ($search): void {
                    $searchQuery->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($customer) => $customer->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('employee.user', fn ($user) => $user->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['customer', 'employee.user', 'items.product', 'auditLogs.user', 'mailLogs.user']);
        app(OrderAuditService::class)->record('order_viewed_by_admin', $order);

        return view('employee.orders.show', ['order' => $order, 'isAdmin' => true]);
    }

    public function download(Order $order, OrderDocumentService $documents, OrderAuditService $audit): BinaryFileResponse
    {
        $path = $documents->pdf($order);
        $audit->record('invoice_downloaded', $order, ['path' => $path]);

        return response()->download(storage_path('app/public/' . $path), $order->invoice_number . '.pdf');
    }

    public function print(Order $order, OrderAuditService $audit): View
    {
        $order->load(['customer', 'employee.user', 'items.product']);
        $audit->record('invoice_print_viewed', $order);

        return view('pdf.invoice', compact('order'));
    }

    public function email(Request $request, Order $order, OrderDocumentService $documents, OrderAuditService $audit): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);
        $mailLog = $documents->email($order, $data['email']);
        $audit->record('invoice_email_' . $mailLog->status, $order, [
            'recipient' => $data['email'],
            'mail_log_id' => $mailLog->id,
            'error' => $mailLog->error_message,
        ]);

        return back()->with('success', 'Invoice email queued for SMTP delivery.');
    }
}