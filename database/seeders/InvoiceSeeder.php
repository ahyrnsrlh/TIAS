<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\InvoiceHeader;
use App\Models\InvoiceItem;
use App\Models\InvoiceReceipt;
use App\Models\TaxInvoice;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        $diskominfo = Customer::where('customer_code', 'CUST-TLK-001')->first();
        $ggp = Customer::where('customer_code', 'CUST-TLK-002')->first();
        $ptba = Customer::where('customer_code', 'CUST-TLK-005')->first();

        if (!$diskominfo || !$admin) {
            return;
        }

        // Invoice 1 - Paid (Diskominfo)
        $inv1 = InvoiceHeader::updateOrCreate(
            ['invoice_number' => 'INV/TLK-LPG/2026/07/0001'],
            [
                'uuid' => (string) Str::uuid(),
                'customer_id' => $diskominfo->id,
                'user_id' => $admin->id,
                'period_start' => '2026-07-01',
                'period_end' => '2026-07-31',
                'issue_date' => '2026-07-01',
                'due_date' => '2026-07-15',
                'subtotal' => 130630630.63,
                'tax_percentage' => 11.00,
                'tax_amount' => 14369369.37,
                'discount_amount' => 0,
                'total_amount' => 145000000.00,
                'status' => 'paid',
                'payment_terms' => 'Net 14 Days',
                'notes' => 'Astinet Enterprise Dedicated High Speed Bandwidth 1Gbps',
            ]
        );

        InvoiceItem::updateOrCreate(
            ['invoice_header_id' => $inv1->id, 'service_name' => 'Astinet Enterprise 1Gbps Dedicated'],
            [
                'uuid' => (string) Str::uuid(),
                'service_code' => 'AST-1G-DEDICATED',
                'description' => 'Dedicated Internet Service 1Gbps for Provincial Government Center',
                'quantity' => 1,
                'unit_price' => 130630630.63,
                'amount' => 130630630.63,
            ]
        );

        InvoiceReceipt::updateOrCreate(
            ['receipt_number' => 'RCP/TLK-LPG/2026/07/0001'],
            [
                'uuid' => (string) Str::uuid(),
                'invoice_header_id' => $inv1->id,
                'user_id' => $admin->id,
                'payment_date' => Carbon::parse('2026-07-10 10:30:00'),
                'amount_paid' => 145000000.00,
                'payment_method' => 'bank_transfer',
                'reference_number' => 'TRF-MANDIRI-9988221',
                'notes' => 'Full payment confirmed by Treasury Unit Witel Lampung',
            ]
        );

        TaxInvoice::updateOrCreate(
            ['tax_invoice_number' => '010.000-26.00000001'],
            [
                'uuid' => (string) Str::uuid(),
                'invoice_header_id' => $inv1->id,
                'tax_period' => '07-2026',
                'dpp_amount' => 130630630.63,
                'ppn_amount' => 14369369.37,
                'status' => 'approved',
                'qr_code_url' => 'https://efaktur.pajak.go.id/verify/0100002600000001',
            ]
        );

        // Invoice 2 - Pending (GGP)
        if ($ggp) {
            $inv2 = InvoiceHeader::updateOrCreate(
                ['invoice_number' => 'INV/TLK-LPG/2026/07/0002'],
                [
                    'uuid' => (string) Str::uuid(),
                    'customer_id' => $ggp->id,
                    'user_id' => $admin->id,
                    'period_start' => '2026-07-01',
                    'period_end' => '2026-07-31',
                    'issue_date' => '2026-07-05',
                    'due_date' => '2026-08-05',
                    'subtotal' => 88738738.74,
                    'tax_percentage' => 11.00,
                    'tax_amount' => 9761261.26,
                    'discount_amount' => 0,
                    'total_amount' => 98500000.00,
                    'status' => 'sent',
                    'payment_terms' => 'Net 30 Days',
                    'notes' => 'Metro Ethernet 500Mbps & Managed SD-WAN Site Link',
                ]
            );

            InvoiceItem::updateOrCreate(
                ['invoice_header_id' => $inv2->id, 'service_name' => 'Metro Ethernet 500Mbps'],
                [
                    'uuid' => (string) Str::uuid(),
                    'service_code' => 'METRO-E-500M',
                    'description' => 'Inter-plant Fiber Connection Central Factory - Port',
                    'quantity' => 1,
                    'unit_price' => 88738738.74,
                    'amount' => 88738738.74,
                ]
            );

            TaxInvoice::updateOrCreate(
                ['tax_invoice_number' => '010.000-26.00000002'],
                [
                    'uuid' => (string) Str::uuid(),
                    'invoice_header_id' => $inv2->id,
                    'tax_period' => '07-2026',
                    'dpp_amount' => 88738738.74,
                    'ppn_amount' => 9761261.26,
                    'status' => 'draft',
                ]
            );
        }

        // Invoice 3 - Overdue (PTBA)
        if ($ptba) {
            $inv3 = InvoiceHeader::updateOrCreate(
                ['invoice_number' => 'INV/TLK-LPG/2026/06/0099'],
                [
                    'uuid' => (string) Str::uuid(),
                    'customer_id' => $ptba->id,
                    'user_id' => $admin->id,
                    'period_start' => '2026-06-01',
                    'period_end' => '2026-06-30',
                    'issue_date' => '2026-06-02',
                    'due_date' => '2026-06-16',
                    'subtotal' => 279279279.28,
                    'tax_percentage' => 11.00,
                    'tax_amount' => 30720720.72,
                    'discount_amount' => 0,
                    'total_amount' => 310000000.00,
                    'status' => 'overdue',
                    'payment_terms' => 'Net 14 Days',
                    'notes' => 'Private LTE Infrastructure & Enterprise Gateway Tarahan Port',
                ]
            );

            InvoiceItem::updateOrCreate(
                ['invoice_header_id' => $inv3->id, 'service_name' => 'Private LTE Port Network'],
                [
                    'uuid' => (string) Str::uuid(),
                    'service_code' => 'PLTE-ENTERPRISE',
                    'description' => 'Private 5G/LTE Network Operations Port Node',
                    'quantity' => 1,
                    'unit_price' => 279279279.28,
                    'amount' => 279279279.28,
                ]
            );
        }
    }
}
