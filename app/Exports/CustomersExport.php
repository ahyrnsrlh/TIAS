<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Customer::orderBy('company_name')->get();
    }

    public function headings(): array
    {
        return [
            'Customer Number',
            'Company Name',
            'Contact Name',
            'Email',
            'Phone',
            'NPWP',
            'Virtual Account',
            'Address',
            'City',
            'Status',
            'Created Date',
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->customer_code,
            $customer->company_name,
            $customer->name,
            $customer->email,
            $customer->phone,
            $customer->npwp,
            $customer->virtual_account,
            $customer->address,
            $customer->city,
            strtoupper($customer->status),
            $customer->created_at ? $customer->created_at->format('Y-m-d H:i') : '',
        ];
    }
}
