<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\InvoiceHeader;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    protected CustomerRepositoryInterface $customerRepository;
    protected InvoiceRepositoryInterface $invoiceRepository;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        InvoiceRepositoryInterface $invoiceRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    public function getDashboardData(): array
    {
        return Cache::remember('tias_dashboard_metrics', 3600, function () {
            $totalCustomers = $this->customerRepository->getTotalCount();
            $totalInvoices = InvoiceHeader::count();
            $paidInvoices = InvoiceHeader::where('status', 'paid')->count();
            $pendingInvoices = InvoiceHeader::whereIn('status', ['draft', 'generated', 'sent'])->count();
            $monthlyRevenue = (float) InvoiceHeader::where('status', 'paid')->sum('total_amount');

            $latestInvoicesRaw = InvoiceHeader::with(['customer', 'items'])
                ->latest()
                ->take(5)
                ->get();

            $latestInvoices = $latestInvoicesRaw->map(function ($inv) {
                $item = $inv->items ? $inv->items->first() : null;
                return [
                    'id' => $inv->invoice_number,
                    'db_id' => $inv->id,
                    'client_name' => $inv->customer?->company_name ?? 'B2B Client',
                    'client_code' => $inv->customer?->customer_code ?? 'CUST-000',
                    'service' => $item ? $item->service_name : 'Telecommunication Services',
                    'amount' => 'Rp ' . number_format($inv->total_amount, 0, ',', '.'),
                    'due_date' => $inv->due_date ? (is_string($inv->due_date) ? $inv->due_date : $inv->due_date->format('Y-m-d')) : date('Y-m-d'),
                    'date' => $inv->issue_date ? (is_string($inv->issue_date) ? $inv->issue_date : $inv->issue_date->format('Y-m-d')) : date('Y-m-d'),
                    'status' => ucfirst($inv->status ?? 'Draft'),
                ];
            })->toArray();

            $activityRaw = ActivityLog::with('user')
                ->latest()
                ->take(6)
                ->get();

            $activityTimeline = $activityRaw->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action ?? 'SYSTEM',
                    'description' => $log->description ?? 'Activity event logged',
                    'time_ago' => $log->created_at ? $log->created_at->diffForHumans() : 'Just now',
                    'user' => $log->user?->name ?? 'System',
                    'ip' => $log->ip_address ?? '127.0.0.1',
                ];
            })->toArray();

            $monthlyChart = [
                ['month' => 'Jan', 'revenue' => 3200, 'formatted' => 'Rp 3.2B'],
                ['month' => 'Feb', 'revenue' => 4100, 'formatted' => 'Rp 4.1B'],
                ['month' => 'Mar', 'revenue' => 3800, 'formatted' => 'Rp 3.8B'],
                ['month' => 'Apr', 'revenue' => 4500, 'formatted' => 'Rp 4.5B'],
                ['month' => 'May', 'revenue' => 4800, 'formatted' => 'Rp 4.8B'],
                ['month' => 'Jun', 'revenue' => 4200, 'formatted' => 'Rp 4.2B'],
                ['month' => 'Jul', 'revenue' => 5000, 'formatted' => 'Rp 5.0B'],
            ];

            $widgets = [
                'total_customer' => [
                    'title' => 'Total B2B Customers',
                    'value' => number_format($totalCustomers),
                    'subvalue' => 'Active Enterprise Accounts',
                    'change' => '+12.5%',
                ],
                'total_invoice' => [
                    'title' => 'Total Invoices',
                    'value' => number_format($totalInvoices),
                    'subvalue' => 'Generated Billing Records',
                    'change' => '+8.2%',
                ],
                'paid_invoice' => [
                    'title' => 'Paid Invoices',
                    'value' => number_format($paidInvoices),
                    'subvalue' => 'Settled Payments',
                    'change' => '+15.4%',
                ],
                'pending_invoice' => [
                    'title' => 'Pending Invoices',
                    'value' => number_format($pendingInvoices),
                    'subvalue' => 'Awaiting Settlement',
                    'change' => '-3.1%',
                ],
                'monthly_revenue' => [
                    'title' => 'Monthly Revenue',
                    'value' => 'Rp ' . number_format($monthlyRevenue, 0, ',', '.'),
                    'year' => date('Y'),
                    'target_completion' => '94.2%',
                    'target_monthly' => 'Rp 5.0B',
                    'chart' => $monthlyChart,
                ],
                'latest_invoice' => $latestInvoices,
                'activity_timeline' => $activityTimeline,
            ];

            return [
                'widgets' => $widgets,
                'latest_invoices' => $latestInvoices,
                'activity_timeline' => $activityTimeline,
            ];
        });
    }

    public function getDashboardSummary(): array
    {
        return $this->getDashboardData();
    }

    public static function clearDashboardCache(): void
    {
        Cache::forget('tias_dashboard_metrics');
    }
}
