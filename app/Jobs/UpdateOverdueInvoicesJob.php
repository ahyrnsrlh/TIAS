<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\InvoiceHeader;
use App\Services\DashboardService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class UpdateOverdueInvoicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $overdueInvoices = InvoiceHeader::whereIn('status', ['generated', 'sent'])
            ->whereDate('due_date', '<', now())
            ->get();

        $count = 0;
        foreach ($overdueInvoices as $invoice) {
            $invoice->update(['status' => 'overdue']);
            $count++;
        }

        if ($count > 0) {
            DashboardService::clearDashboardCache();

            ActivityLog::create([
                'uuid' => (string) Str::uuid(),
                'action' => 'INVOICES_OVERDUE_MARKED',
                'subject_type' => InvoiceHeader::class,
                'description' => "Scheduled job marked {$count} invoices as overdue based on due_date expiration",
                'properties' => ['count' => $count],
            ]);
        }
    }
}
