<?php

namespace App\Services;

use App\Jobs\SendInvoiceEmailJob;
use App\Models\ActivityLog;
use App\Models\InvoiceHeader;
use Illuminate\Support\Str;

class EmailService
{
    public function sendInvoiceEmail(InvoiceHeader $invoice, ?int $userId = null): void
    {
        SendInvoiceEmailJob::dispatch($invoice, $userId);

        ActivityLog::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $userId,
            'action' => 'EMAIL_QUEUED',
            'subject_type' => InvoiceHeader::class,
            'subject_id' => $invoice->id,
            'description' => "Queued email dispatch for invoice {$invoice->invoice_number}",
        ]);
    }

    public function sendBulkInvoiceEmails(array $invoiceIds, ?int $userId = null): int
    {
        $invoices = InvoiceHeader::whereIn('id', $invoiceIds)->with('customer')->get();
        $queuedCount = 0;

        foreach ($invoices as $inv) {
            SendInvoiceEmailJob::dispatch($inv, $userId);
            $queuedCount++;
        }

        ActivityLog::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $userId,
            'action' => 'BULK_EMAILS_QUEUED',
            'subject_type' => InvoiceHeader::class,
            'description' => "Queued bulk email delivery for {$queuedCount} invoices",
        ]);

        return $queuedCount;
    }

    public function retryFailedEmail(InvoiceHeader $invoice, ?int $userId = null): void
    {
        SendInvoiceEmailJob::dispatch($invoice, $userId);

        ActivityLog::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $userId,
            'action' => 'EMAIL_RETRY_QUEUED',
            'subject_type' => InvoiceHeader::class,
            'subject_id' => $invoice->id,
            'description' => "Re-queued failed email dispatch for invoice {$invoice->invoice_number}",
        ]);
    }
}
