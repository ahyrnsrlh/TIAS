<?php

namespace App\Imports;

use App\Models\Customer;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomersImport implements ToModel, WithHeadingRow
{
    protected ?int $userId;

    public function __construct(?int $userId = null)
    {
        $this->userId = $userId;
    }

    public function model(array $row)
    {
        $companyName = $row['company_name'] ?? $row['nama_perusahaan'] ?? null;
        $name = $row['contact_name'] ?? $row['nama_kontak'] ?? $companyName;
        $email = $row['email'] ?? null;

        if (!$companyName || !$email) {
            return null;
        }

        $code = $row['customer_number'] ?? $row['kode_pelanggan'] ?? 'CUST-TLK-' . strtoupper(Str::random(6));

        return Customer::updateOrCreate(
            ['email' => $email],
            [
                'uuid' => (string) Str::uuid(),
                'customer_code' => $code,
                'company_name' => $companyName,
                'name' => $name ?: $companyName,
                'phone' => $row['phone'] ?? $row['telepon'] ?? null,
                'npwp' => $row['npwp'] ?? null,
                'virtual_account' => $row['virtual_account'] ?? $row['va'] ?? null,
                'address' => $row['address'] ?? $row['alamat'] ?? 'Bandar Lampung',
                'city' => $row['city'] ?? $row['kota'] ?? 'Bandar Lampung',
                'status' => 'active',
                'created_by' => $this->userId,
            ]
        );
    }
}
