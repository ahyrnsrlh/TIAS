<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        $logs = [
            [
                'uuid' => (string) Str::uuid(),
                'user_id' => $admin?->id,
                'action' => 'PAYMENT_RECEIVED',
                'subject_type' => 'App\Models\InvoiceReceipt',
                'subject_id' => 1,
                'description' => 'Confirmed payment receipt RCP/TLK-LPG/2026/07/0001 for Rp 145.000.000 (Dinas Kominfo Lampung)',
                'ip_address' => '180.250.12.44',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'properties' => [
                    'invoice_number' => 'INV/TLK-LPG/2026/07/0001',
                    'amount' => 145000000,
                    'method' => 'Bank Transfer (Mandiri)',
                ],
                'created_at' => Carbon::parse('2026-07-24 14:15:00'),
            ],
            [
                'uuid' => (string) Str::uuid(),
                'user_id' => $admin?->id,
                'action' => 'TAX_INVOICE_APPROVED',
                'subject_type' => 'App\Models\TaxInvoice',
                'subject_id' => 1,
                'description' => 'Approved e-Faktur 010.000-26.00000001 with PPN 11% (Rp 14.369.369)',
                'ip_address' => '180.250.12.44',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'properties' => [
                    'tax_number' => '010.000-26.00000001',
                    'ppn' => 14369369.37,
                ],
                'created_at' => Carbon::parse('2026-07-24 11:30:00'),
            ],
            [
                'uuid' => (string) Str::uuid(),
                'user_id' => $admin?->id,
                'action' => 'INVOICE_GENERATED',
                'subject_type' => 'App\Models\InvoiceHeader',
                'subject_id' => 2,
                'description' => 'Drafted new enterprise invoice INV/TLK-LPG/2026/07/0002 for PT Great Giant Pineapple',
                'ip_address' => '180.250.12.44',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'properties' => [
                    'invoice_number' => 'INV/TLK-LPG/2026/07/0002',
                    'service' => 'Metro Ethernet 500Mbps',
                ],
                'created_at' => Carbon::parse('2026-07-23 09:45:00'),
            ],
            [
                'uuid' => (string) Str::uuid(),
                'user_id' => $admin?->id,
                'action' => 'CUSTOMER_ONBOARDED',
                'subject_type' => 'App\Models\Customer',
                'subject_id' => 5,
                'description' => 'Onboarded enterprise client PT Bukit Asam Tbk (Pelabuhan Tarahan)',
                'ip_address' => '180.250.12.44',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'properties' => [
                    'customer_code' => 'CUST-TLK-005',
                    'company' => 'PT Bukit Asam Tbk',
                ],
                'created_at' => Carbon::parse('2026-07-20 16:20:00'),
            ],
        ];

        foreach ($logs as $log) {
            ActivityLog::create($log);
        }
    }
}
