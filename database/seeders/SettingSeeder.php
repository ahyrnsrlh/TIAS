<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'company_name',
                'value' => 'PT. Telkom Indonesia (Persero) Tbk - Witel Lampung',
                'type' => 'string',
                'group' => 'company',
                'description' => 'Official organization name',
            ],
            [
                'key' => 'company_address',
                'value' => 'Jl. Majapahit No. 1, Enggal, Bandar Lampung, Lampung 35118',
                'type' => 'string',
                'group' => 'company',
                'description' => 'Office operational address',
            ],
            [
                'key' => 'company_npwp',
                'value' => '01.000.013.1-093.000',
                'type' => 'string',
                'group' => 'tax',
                'description' => 'Telkom Indonesia corporate NPWP',
            ],
            [
                'key' => 'tax_percentage',
                'value' => '11.00',
                'type' => 'number',
                'group' => 'tax',
                'description' => 'Standard PPN rate (11%)',
            ],
            [
                'key' => 'bank_name',
                'value' => 'Bank Mandiri (Cabang Bandar Lampung)',
                'type' => 'string',
                'group' => 'finance',
                'description' => 'Primary receiving bank',
            ],
            [
                'key' => 'bank_account_number',
                'value' => '114-00-0899887-1',
                'type' => 'string',
                'group' => 'finance',
                'description' => 'Corporate account number',
            ],
            [
                'key' => 'bank_account_name',
                'value' => 'PT TELKOM INDONESIA WITEL LAMPUNG',
                'type' => 'string',
                'group' => 'finance',
                'description' => 'Beneficiary account holder',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
