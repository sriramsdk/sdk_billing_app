<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\OrderAuditService;
use App\Services\OrderDocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $employee = auth()->user()->employee;

        $orders = Order::with([
                'customer',
                'items.product',
            ])
            ->where('employee_id', $employee->id)

            ->when(
                $request->search,
                function ($query, $search) {
                    $query->where(function ($searchQuery) use ($search) {
                        $searchQuery->where('invoice_number', 'like', "%{$search}%")
                            ->orWhereHas('customer', function ($customerQuery) use ($search) {
                                $customerQuery->where('name', 'like', "%{$search}%");
                            });
                    });
                }
            )

            ->when(
                $request->status,
                fn ($query, $status) =>
                    $query->where('status', $status)
            )

            ->latest()

            ->paginate(15)

            ->withQueryString();

        return view(
            'employee.orders.index',
            compact('orders')
        );
    }

    public function show(Order $order, OrderAuditService $audit): View
    {
        $employee = auth()->user()->employee;

        abort_unless(
            $order->employee_id === $employee->id,
            403
        );

        $order->load([
            'customer',
            'employee.user',
            'items.product',
            'mailLogs',
            'auditLogs.user',
        ]);
        $audit->record('order_viewed_by_employee', $order);

        return view(
            'employee.orders.show',
            compact('order')
        );
    }

    public function download(Order $order, OrderDocumentService $documents, OrderAuditService $audit): BinaryFileResponse
    {
        $this->authorizeOrder($order);
        $path = $documents->pdf($order);
        $audit->record('invoice_downloaded', $order, ['path' => $path]);

        return response()->download(storage_path('app/public/' . $path), $order->invoice_number . '.pdf');
    }

    public function print(Order $order, OrderAuditService $audit): View
    {
        $this->authorizeOrder($order);
        $order->load(['customer', 'employee.user', 'items.product']);
        $audit->record('invoice_print_viewed', $order);

        return view('pdf.invoice', compact('order'));
    }

    public function email(Request $request, Order $order, OrderDocumentService $documents, OrderAuditService $audit): RedirectResponse
    {
        $this->authorizeOrder($order);
        $data = $request->validate(['email' => ['required', 'email']]);
        $mailLog = $documents->email($order, $data['email']);
        $audit->record('invoice_email_' . $mailLog->status, $order, ['recipient' => $data['email'], 'mail_log_id' => $mailLog->id, 'error' => $mailLog->error_message]);

        return back()->with('success', 'Invoice email queued for SMTP delivery.');
    }

    private function authorizeOrder(Order $order): void
    {
        abort_unless($order->employee_id === auth()->user()->employee?->id, 403);
    }
}