<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvoicesTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'customer_code',
            'company_name',
            'email',
            'invoice_number',
            'issue_date',
            'due_date',
            'service_name',
            'service_code',
            'description',
            'quantity',
            'unit_price',
            'tax_percentage',
        ];
    }

    public function array(): array
    {
        return [
            [
                'customer_code' => 'CUST-TLK-001',
                'company_name' => 'Dinas Kominfo Provinsi Lampung',
                'email' => 'billing@diskominfo.lampungprov.go.id',
                'invoice_number' => 'INV/TLK-LPG/' . date('Y/m') . '/9001',
                'issue_date' => date('Y-m-d'),
                'due_date' => date('Y-m-d', strtotime('+14 days')),
                'service_name' => 'Astinet Enterprise 1Gbps Dedicated',
                'service_code' => 'AST-1G-DEDICATED',
                'description' => 'Dedicated Bandwidth Internet Link',
                'quantity' => 1,
                'unit_price' => 130630630.63,
                'tax_percentage' => 11,
            ],
            [
                'customer_code' => 'CUST-TLK-002',
                'company_name' => 'PT Great Giant Pineapple (GGP)',
                'email' => 'finance@ggp-co.com',
                'invoice_number' => 'INV/TLK-LPG/' . date('Y/m') . '/9002',
                'issue_date' => date('Y-m-d'),
                'due_date' => date('Y-m-d', strtotime('+14 days')),
                'service_name' => 'Metro Ethernet 500Mbps',
                'service_code' => 'METRO-E-500M',
                'description' => 'Dedicated Fiber Network Interconnect',
                'quantity' => 1,
                'unit_price' => 88738738.74,
                'tax_percentage' => 11,
            ]
        ];
    }
}
