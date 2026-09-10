<?php

namespace App\Jobs;

use App\Models\MailLog;
use App\Services\OrderDocumentService;
use App\Services\OrderAuditService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SendOrderInvoiceEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(
        public int $orderId,
        public int $mailLogId,
    ) {}

    public function handle(OrderDocumentService $documents, OrderAuditService $audit): void
    {
        $mailLog = MailLog::whereKey($this->mailLogId)->firstOrFail();

        if ($mailLog->status === 'sent') {
            return;
        }

        try {
            $documents->sendQueuedEmail($mailLog);
            $audit->record('invoice_email_sent', $mailLog->order, [
                'recipient' => $mailLog->recipient,
                'mail_log_id' => $mailLog->id,
            ]);
        } catch (Throwable $exception) {
            throw $exception;
        }
    }

    public function failed(?Throwable $exception): void
    {
        MailLog::whereKey($this->mailLogId)->update([
            'status' => 'failed',
            'error_message' => $exception?->getMessage() ?? 'Mail delivery failed after all retries.',
        ]);
    }
}