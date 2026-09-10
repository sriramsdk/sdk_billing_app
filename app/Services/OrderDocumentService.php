<?php

namespace App\Services;

use App\Models\MailLog;
use App\Models\Order;
use App\Jobs\SendOrderInvoiceEmail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Throwable;

class OrderDocumentService
{
    public function pdf(Order $order): string
    {
        if ($order->pdf_path
            && str_starts_with($order->pdf_path, 'invoices/v2/')
            && Storage::disk('public')->exists($order->pdf_path)) {
            return $order->pdf_path;
        }

        $order->loadMissing(['customer', 'employee.user', 'items.product']);
        $pdf = Pdf::loadView('pdf.invoice', compact('order'));
        $path = 'invoices/v2/' . $order->invoice_number . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());
        $order->update(['pdf_path' => $path]);

        return $path;
    }

    public function email(Order $order, string $recipient, ?int $userId = null): MailLog
    {
        $mailLog = MailLog::create([
            'order_id' => $order->id,
            'user_id' => $userId ?? auth()->id(),
            'recipient' => $recipient,
            'subject' => 'Invoice ' . $order->invoice_number . ' from MT Billing',
            'status' => 'queued',
        ]);

        SendOrderInvoiceEmail::dispatch($order->id, $mailLog->id)->onQueue('mail');

        return $mailLog->fresh();
    }

    public function sendQueuedEmail(MailLog $mailLog): MailLog
    {
        $order = $mailLog->order()->with(['customer', 'items.product'])->firstOrFail();
        $path = $order->pdf_path ?: $this->pdf($order);
        $absolutePath = Storage::disk('public')->path($path);

        Mail::send('emails.invoice', ['order' => $order], function ($message) use ($mailLog, $order, $absolutePath): void {
            $message->to($mailLog->recipient)
                ->subject($mailLog->subject)
                ->attach($absolutePath, ['as' => $order->invoice_number . '.pdf', 'mime' => 'application/pdf']);
        });

        return tap($mailLog)->update([
            'status' => 'sent',
            'sent_at' => now(),
            'error_message' => null,
        ]);
    }
}