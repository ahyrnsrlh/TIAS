<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\InvoiceHeader;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\InvoicesImport;
use App\Exports\InvoicesTemplateExport;

class InvoiceService
{
    protected InvoiceRepositoryInterface $invoiceRepository;

    public function __construct(InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    public function getPaginatedInvoices(?string $search = null, ?string $status = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->invoiceRepository->getPaginated($search, $status, $perPage);
    }

    public function createInvoice(array $data, ?int $userId = null): InvoiceHeader
    {
        // Auto-generate Invoice Number if empty
        if (empty($data['invoice_number'])) {
            $year = date('Y');
            $month = date('m');
            $nextSeq = InvoiceHeader::count() + 1;
            $data['invoice_number'] = "INV/TLK-LPG/{$year}/{$month}/" . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
        }

        // Calculate item amounts & subtotal
        $subtotal = 0;
        $processedItems = [];

        foreach ($data['items'] as $item) {
            $qty = (float) ($item['quantity'] ?? 1);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $itemAmount = $qty * $unitPrice;
            $subtotal += $itemAmount;

            $processedItems[] = [
                'service_name' => $item['service_name'],
                'description' => $item['description'] ?? null,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
            ];
        }

        $taxPercentage = isset($data['tax_percentage']) ? (float) $data['tax_percentage'] : 11.00;
        $discountAmount = isset($data['discount_amount']) ? (float) $data['discount_amount'] : 0;
        $taxableAmount = max(0, $subtotal - $discountAmount);
        $taxAmount = ($taxableAmount * $taxPercentage) / 100;
        $totalAmount = $taxableAmount + $taxAmount;

        $headerData = [
            'invoice_number' => $data['invoice_number'],
            'customer_id' => $data['customer_id'],
            'user_id' => $userId,
            'period_start' => $data['period_start'] ?? null,
            'period_end' => $data['period_end'] ?? null,
            'issue_date' => $data['issue_date'],
            'due_date' => $data['due_date'],
            'subtotal' => $subtotal,
            'tax_percentage' => $taxPercentage,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'status' => $data['status'] ?? 'draft',
            'payment_terms' => $data['payment_terms'] ?? 'Net 14 Days',
            'notes' => $data['notes'] ?? null,
        ];

        $invoice = $this->invoiceRepository->create($headerData, $processedItems);

        DashboardService::clearDashboardCache();

        ActivityLog::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $userId,
            'action' => 'INVOICE_CREATED',
            'subject_type' => InvoiceHeader::class,
            'subject_id' => $invoice->id,
            'description' => "Generated invoice {$invoice->invoice_number} for customer ID {$invoice->customer_id} (Total: Rp " . number_format($invoice->total_amount, 0, ',', '.') . ")",
            'properties' => $invoice->toArray(),
        ]);

        return $invoice;
    }

    public function updateInvoice(int $id, array $data, ?int $userId = null): bool
    {
        $invoice = $this->invoiceRepository->findById($id);
        if (!$invoice) {
            return false;
        }

        $subtotal = 0;
        $processedItems = [];

        foreach ($data['items'] as $item) {
            $qty = (float) ($item['quantity'] ?? 1);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $itemAmount = $qty * $unitPrice;
            $subtotal += $itemAmount;

            $processedItems[] = [
                'service_name' => $item['service_name'],
                'description' => $item['description'] ?? null,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
            ];
        }

        $taxPercentage = isset($data['tax_percentage']) ? (float) $data['tax_percentage'] : 11.00;
        $discountAmount = isset($data['discount_amount']) ? (float) $data['discount_amount'] : 0;
        $taxableAmount = max(0, $subtotal - $discountAmount);
        $taxAmount = ($taxableAmount * $taxPercentage) / 100;
        $totalAmount = $taxableAmount + $taxAmount;

        $headerData = [
            'invoice_number' => $data['invoice_number'],
            'customer_id' => $data['customer_id'],
            'period_start' => $data['period_start'] ?? null,
            'period_end' => $data['period_end'] ?? null,
            'issue_date' => $data['issue_date'],
            'due_date' => $data['due_date'],
            'subtotal' => $subtotal,
            'tax_percentage' => $taxPercentage,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'status' => $data['status'] ?? 'draft',
            'payment_terms' => $data['payment_terms'] ?? 'Net 14 Days',
            'notes' => $data['notes'] ?? null,
        ];

        $updatedInvoice = $this->invoiceRepository->update($invoice, $headerData, $processedItems);
        DashboardService::clearDashboardCache();

        ActivityLog::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $userId,
            'action' => 'INVOICE_UPDATED',
            'subject_type' => InvoiceHeader::class,
            'subject_id' => $id,
            'description' => "Updated invoice {$data['invoice_number']} details and line items",
        ]);

        return (bool) $updatedInvoice;
    }

    public function deleteInvoice(int $id, ?int $userId = null): bool
    {
        $invoice = $this->invoiceRepository->findById($id);
        if (!$invoice) {
            return false;
        }

        $invNumber = $invoice->invoice_number;
        $deleted = $this->invoiceRepository->delete($invoice);
        DashboardService::clearDashboardCache();

        if ($deleted) {
            ActivityLog::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $userId,
                'action' => 'INVOICE_DELETED',
                'subject_type' => InvoiceHeader::class,
                'subject_id' => $id,
                'description' => "Soft-deleted invoice record {$invNumber}",
            ]);
        }

        return $deleted;
    }

    public function getInvoiceDetails(int $id): ?InvoiceHeader
    {
        return $this->invoiceRepository->findById($id);
    }

    public function importBulkInvoices($file, ?int $userId = null): array
    {
        $importer = new InvoicesImport($userId);
        Excel::import($importer, $file);

        $stats = $importer->getStats();
        DashboardService::clearDashboardCache();

        ActivityLog::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $userId,
            'action' => 'INVOICES_BULK_IMPORTED',
            'subject_type' => InvoiceHeader::class,
            'description' => "Bulk imported {$stats['imported']} invoices from Excel (Total Volume: Rp " . number_format($stats['total_volume'], 0, ',', '.') . ", Duplicates Skipped: {$stats['duplicates_skipped']})",
            'properties' => $stats,
        ]);

        return $stats;
    }

    public function downloadImportTemplate()
    {
        return Excel::download(new InvoicesTemplateExport, 'Telkom_Bulk_Billing_Template.xlsx');
    }
}
