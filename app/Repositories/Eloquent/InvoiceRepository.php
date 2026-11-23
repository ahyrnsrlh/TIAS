<?php

namespace App\Repositories\Eloquent;

use App\Models\InvoiceHeader;
use App\Models\InvoiceItem;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function getPaginated(?string $search = null, ?string $status = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = InvoiceHeader::with(['customer', 'items'])->latest();

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('company_name', 'like', "%{$search}%")
                         ->orWhere('customer_code', 'like', "%{$search}%");
                  });
            });
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?InvoiceHeader
    {
        return InvoiceHeader::with(['customer', 'items', 'receipts', 'taxInvoice'])->find($id);
    }

    public function findByInvoiceNumber(string $invoiceNumber): ?InvoiceHeader
    {
        return InvoiceHeader::where('invoice_number', $invoiceNumber)->first();
    }

    public function create(array $headerData, array $itemsData): InvoiceHeader
    {
        return DB::transaction(function () use ($headerData, $itemsData) {
            if (empty($headerData['uuid'])) {
                $headerData['uuid'] = (string) Str::uuid();
            }

            $invoice = InvoiceHeader::create($headerData);

            foreach ($itemsData as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                InvoiceItem::create([
                    'uuid' => (string) Str::uuid(),
                    'invoice_header_id' => $invoice->id,
                    'service_name' => $item['service_name'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'amount' => $subtotal,
                ]);
            }

            return $invoice->fresh(['customer', 'items']);
        });
    }

    public function update(InvoiceHeader $invoice, array $headerData, ?array $itemsData = null): InvoiceHeader
    {
        return DB::transaction(function () use ($invoice, $headerData, $itemsData) {
            $invoice->update($headerData);

            if ($itemsData !== null) {
                $invoice->items()->delete();
                foreach ($itemsData as $item) {
                    $subtotal = $item['quantity'] * $item['unit_price'];
                    InvoiceItem::create([
                        'uuid' => (string) Str::uuid(),
                        'invoice_header_id' => $invoice->id,
                        'service_name' => $item['service_name'],
                        'description' => $item['description'] ?? null,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'amount' => $subtotal,
                    ]);
                }
            }

            return $invoice->fresh(['customer', 'items']);
        });
    }

    public function delete(InvoiceHeader $invoice): bool
    {
        return (bool) $invoice->delete();
    }

    public function getOverduePendingInvoices(): Collection
    {
        return InvoiceHeader::whereIn('status', ['generated', 'sent'])
            ->whereDate('due_date', '<', now())
            ->get();
    }

    public function getDashboardMetrics(): array
    {
        return [
            'total_invoice' => InvoiceHeader::count(),
            'paid_invoice' => InvoiceHeader::where('status', 'paid')->count(),
            'pending_invoice' => InvoiceHeader::whereIn('status', ['draft', 'generated', 'sent'])->count(),
            'overdue_invoice' => InvoiceHeader::where('status', 'overdue')->count(),
            'monthly_revenue' => (float) InvoiceHeader::where('status', 'paid')->sum('total_amount'),
        ];
    }
}
