<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        $customers = [
            [
                'customer_code' => 'CUST-TLK-001',
                'name' => 'Dinas Komunikasi & Informatika',
                'company_name' => 'Dinas Kominfo Provinsi Lampung',
                'email' => 'billing@diskominfo.lampungprov.go.id',
                'phone' => '0721-481234',
                'npwp' => '00.123.456.7-321.000',
                'virtual_account' => '9881140000000001',
                'address' => 'Jl. Wolter Monginsidi No. 69, Teluk Betung Utara, Bandar Lampung',
                'city' => 'Bandar Lampung',
                'postal_code' => '35215',
                'status' => 'active',
                'created_by' => $admin?->id,
            ],
            [
                'customer_code' => 'CUST-TLK-002',
                'name' => 'PT Great Giant Pineapple',
                'company_name' => 'PT Great Giant Pineapple (GGP)',
                'email' => 'finance@ggp-co.com',
                'phone' => '0725-752100',
                'npwp' => '01.345.678.9-322.000',
                'virtual_account' => '9881140000000002',
                'address' => 'Terbanggi Besar, Lampung Tengah, Lampung',
                'city' => 'Lampung Tengah',
                'postal_code' => '34163',
                'status' => 'active',
                'created_by' => $admin?->id,
            ],
            [
                'customer_code' => 'CUST-TLK-003',
                'name' => 'Universitas Lampung',
                'company_name' => 'Universitas Lampung (UNILA)',
                'email' => 'keuangan@unila.ac.id',
                'phone' => '0721-701609',
                'npwp' => '00.987.654.3-321.000',
                'virtual_account' => '9881140000000003',
                'address' => 'Jl. Prof. Dr. Sumantri Brojonegoro No. 1, Rajabasa, Bandar Lampung',
                'city' => 'Bandar Lampung',
                'postal_code' => '35145',
                'status' => 'active',
                'created_by' => $admin?->id,
            ],
            [
                'customer_code' => 'CUST-TLK-004',
                'name' => 'RSUD Dr. H. Abdul Moeloek',
                'company_name' => 'RSUD Dr. H. Abdul Moeloek Provinsi Lampung',
                'email' => 'procurement@rsudam.lampungprov.go.id',
                'phone' => '0721-703312',
                'npwp' => '00.456.789.0-321.000',
                'virtual_account' => '9881140000000004',
                'address' => 'Jl. Dr. Rivai No. 6, Penengahan, Bandar Lampung',
                'city' => 'Bandar Lampung',
                'postal_code' => '35112',
                'status' => 'active',
                'created_by' => $admin?->id,
            ],
            [
                'customer_code' => 'CUST-TLK-005',
                'name' => 'PT Bukit Asam Tbk',
                'company_name' => 'PT Bukit Asam Tbk (Pelabuhan Tarahan)',
                'email' => 'vendor.tarahan@ptba.co.id',
                'phone' => '0721-31555',
                'npwp' => '01.111.222.3-323.000',
                'virtual_account' => '9881140000000005',
                'address' => 'Jl. Soekarno-Hatta Km 15, Tarahan, Katibung, Lampung Selatan',
                'city' => 'Lampung Selatan',
                'postal_code' => '35452',
                'status' => 'active',
                'created_by' => $admin?->id,
            ],
        ];

        foreach ($customers as $c) {
            $c['uuid'] = (string) Str::uuid();
            Customer::updateOrCreate(['customer_code' => $c['customer_code']], $c);
        }
    }
}
