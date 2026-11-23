<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\InvoiceHeader;
use App\Models\InvoiceItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InvoicesImport implements ToCollection, WithHeadingRow
{
    protected ?int $userId;
    protected array $stats = [
        'imported' => 0,
        'duplicates_skipped' => 0,
        'skipped' => 0,
        'total_volume' => 0,
    ];

    public function __construct(?int $userId = null)
    {
        $this->userId = $userId;
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $serviceName = $row['service_name'] ?? $row['nama_layanan'] ?? null;
                $unitPrice = (float) ($row['unit_price'] ?? $row['harga_satuan'] ?? 0);

                if (!$serviceName || $unitPrice <= 0) {
                    $this->stats['skipped']++;
                    continue;
                }

                // 1. Resolve Customer
                $customerCode = $row['customer_code'] ?? $row['kode_pelanggan'] ?? null;
                $companyName = $row['company_name'] ?? $row['nama_perusahaan'] ?? null;
                $email = $row['email'] ?? null;

                $customer = null;
                if ($customerCode) {
                    $customer = Customer::where('customer_code', $customerCode)->first();
                }
                if (!$customer && $email) {
                    $customer = Customer::where('email', $email)->first();
                }
                if (!$customer && $companyName) {
                    $customer = Customer::where('company_name', 'like', "%{$companyName}%")->first();
                }

                if (!$customer) {
                    $code = $customerCode ?: 'CUST-TLK-' . strtoupper(Str::random(6));
                    $comp = $companyName ?: 'Client Enterprise ' . Str::random(4);
                    $customer = Customer::create([
                        'uuid' => (string) Str::uuid(),
                        'customer_code' => $code,
                        'company_name' => $comp,
                        'name' => $comp,
                        'email' => $email ?: 'billing@' . Str::slug($comp) . '.co.id',
                        'virtual_account' => '988114' . str_pad(Customer::max('id') + 1, 10, '0', STR_PAD_LEFT),
                        'address' => 'Bandar Lampung',
                        'city' => 'Bandar Lampung',
                        'status' => 'active',
                        'created_by' => $this->userId,
                    ]);
                }

                // 2. Duplicate Check
                $invNumber = $row['invoice_number'] ?? $row['nomor_invoice'] ?? null;
                $issueDate = $row['issue_date'] ?? $row['tanggal_terbit'] ?? date('Y-m-d');
                $dueDate = $row['due_date'] ?? $row['jatuh_tempo'] ?? date('Y-m-d', strtotime('+14 days'));

                if ($invNumber) {
                    $exists = InvoiceHeader::where('invoice_number', $invNumber)->exists();
                    if ($exists) {
                        $this->stats['duplicates_skipped']++;
                        continue;
                    }
                } else {
                    $exists = InvoiceHeader::where('customer_id', $customer->id)
                        ->whereDate('issue_date', $issueDate)
                        ->exists();
                    if ($exists) {
                        $this->stats['duplicates_skipped']++;
                        continue;
                    }
                    $seq = InvoiceHeader::count() + 1;
                    $invNumber = "INV/TLK-LPG/" . date('Y/m') . "/" . str_pad($seq, 4, '0', STR_PAD_LEFT);
                }

                // 3. Amount & Tax Calculations
                $qty = (float) ($row['quantity'] ?? $row['jumlah'] ?? 1);
                $subtotal = $qty * $unitPrice;
                $taxPercentage = (float) ($row['tax_percentage'] ?? 11.00);
                $taxAmount = ($subtotal * $taxPercentage) / 100;
                $totalAmount = $subtotal + $taxAmount;

                // 4. Create Invoice Header
                $header = InvoiceHeader::create([
                    'uuid' => (string) Str::uuid(),
                    'invoice_number' => $invNumber,
                    'customer_id' => $customer->id,
                    'user_id' => $this->userId,
                    'issue_date' => $issueDate,
                    'due_date' => $dueDate,
                    'subtotal' => $subtotal,
                    'tax_percentage' => $taxPercentage,
                    'tax_amount' => $taxAmount,
                    'discount_amount' => 0,
                    'total_amount' => $totalAmount,
                    'status' => 'generated',
                    'payment_terms' => 'Net 14 Days',
                    'notes' => 'Generated via Bulk Excel Billing Import',
                ]);

                // 5. Create Invoice Item
                InvoiceItem::create([
                    'uuid' => (string) Str::uuid(),
                    'invoice_header_id' => $header->id,
                    'service_name' => $serviceName,
                    'service_code' => $row['service_code'] ?? $row['kode_layanan'] ?? null,
                    'description' => $row['description'] ?? $row['keterangan'] ?? null,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'amount' => $subtotal,
                ]);

                $this->stats['imported']++;
                $this->stats['total_volume'] += $totalAmount;
            }
        });
    }

    public function getStats(): array
    {
        return $this->stats;
    }
}
