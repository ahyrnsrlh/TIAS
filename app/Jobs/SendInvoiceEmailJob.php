<?php

namespace App\Jobs;

use App\Mail\SendInvoiceMail;
use App\Models\ActivityLog;
use App\Models\InvoiceHeader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class SendInvoiceEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public InvoiceHeader $invoice;
    public ?int $userId;
    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(InvoiceHeader $invoice, ?int $userId = null)
    {
        $this->invoice = $invoice;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        $this->invoice->load(['customer', 'items']);
        $recipient = $this->invoice->customer->email;

        Mail::to($recipient)->send(new SendInvoiceMail($this->invoice));

        // Update invoice status to 'sent'
        $this->invoice->update(['status' => 'sent']);

        ActivityLog::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $this->userId,
            'action' => 'EMAIL_SENT',
            'subject_type' => InvoiceHeader::class,
            'subject_id' => $this->invoice->id,
            'description' => "Invoice {$this->invoice->invoice_number} emailed successfully to {$recipient}",
            'properties' => [
                'recipient' => $recipient,
                'attempts' => $this->attempts(),
            ],
        ]);
    }

    public function failed(Throwable $exception): void
    {
        ActivityLog::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $this->userId,
            'action' => 'EMAIL_FAILED',
            'subject_type' => InvoiceHeader::class,
            'subject_id' => $this->invoice->id,
            'description' => "Failed emailing invoice {$this->invoice->invoice_number} to {$this->invoice->customer?->email}: " . $exception->getMessage(),
            'properties' => [
                'error' => $exception->getMessage(),
                'attempts' => $this->attempts(),
            ],
        ]);
    }
}
