<?php

namespace Tests\Feature;

use App\Exports\InvoicesTemplateExport;
use App\Models\Customer;
use App\Models\InvoiceHeader;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class BulkInvoiceImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_download_invoice_import_template(): void
    {
        Excel::fake();
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get('/invoices/template');

        $response->assertStatus(200);
        Excel::assertDownloaded('Telkom_Bulk_Billing_Template.xlsx');
    }

    public function test_admin_can_bulk_import_invoices_with_duplicate_detection(): void
    {
        $admin = User::factory()->create();
        $customer = Customer::create([
            'customer_code' => 'CUST-TLK-999',
            'company_name' => 'PT Existing Import Client',
            'name' => 'Contact Person Name',
            'email' => 'import@test.com',
            'address' => 'Bandar Lampung',
        ]);

        // Pre-create an existing invoice to test duplicate detection
        InvoiceHeader::create([
            'invoice_number' => 'INV/TLK-LPG/2026/07/DUP-001',
            'customer_id' => $customer->id,
            'issue_date' => now(),
            'due_date' => now()->addDays(14),
            'total_amount' => 5000000,
            'status' => 'generated',
        ]);

        // Create dummy CSV for import
        $csvContent = "customer_code,company_name,email,invoice_number,issue_date,due_date,service_name,quantity,unit_price\n" .
            "CUST-TLK-999,PT Existing Import Client,import@test.com,INV/TLK-LPG/2026/07/DUP-001,2026-07-25,2026-08-08,Astinet 1Gbps,1,10000000\n" .
            "CUST-TLK-888,PT New Import Client,new@test.com,INV/TLK-LPG/2026/07/NEW-002,2026-07-25,2026-08-08,Metro Ethernet 500M,1,5000000\n";

        $file = UploadedFile::fake()->createWithContent('bulk_billing.csv', $csvContent);

        $response = $this->actingAs($admin)->post('/invoices/import', [
            'file' => $file,
        ]);

        $response->assertRedirect();
        
        // Assert new invoice was created
        $this->assertDatabaseHas('invoice_headers', [
            'invoice_number' => 'INV/TLK-LPG/2026/07/NEW-002',
        ]);

        // Assert customer record auto-created
        $this->assertDatabaseHas('customers', [
            'customer_code' => 'CUST-TLK-888',
        ]);
    }
}
