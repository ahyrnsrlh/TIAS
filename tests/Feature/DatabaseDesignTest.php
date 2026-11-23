<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\InvoiceHeader;
use App\Models\InvoiceItem;
use App\Models\InvoiceReceipt;
use App\Models\Setting;
use App\Models\TaxInvoice;
use App\Models\User;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseDesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_model_auto_generates_uuid_and_supports_soft_deletes(): void
    {
        $customer = Customer::create([
            'customer_code' => 'CUST-TEST-001',
            'name' => 'Test Client',
            'company_name' => 'PT Test Indonesia',
            'email' => 'client@test.co.id',
            'address' => 'Jl. Test No. 1, Bandar Lampung',
        ]);

        $this->assertNotNull($customer->uuid);
        $this->assertDatabaseHas('customers', ['customer_code' => 'CUST-TEST-001']);

        $customer->delete();
        $this->assertSoftDeleted($customer);
    }

    public function test_invoice_header_and_item_relationships(): void
    {
        $customer = Customer::create([
            'customer_code' => 'CUST-TEST-002',
            'name' => 'Gov Client',
            'company_name' => 'Dinas Test',
            'email' => 'gov@test.go.id',
            'address' => 'Bandar Lampung',
        ]);

        $invoice = InvoiceHeader::create([
            'invoice_number' => 'INV/TEST/001',
            'customer_id' => $customer->id,
            'issue_date' => now(),
            'due_date' => now()->addDays(14),
            'total_amount' => 1000000,
            'status' => 'draft',
        ]);

        $item = InvoiceItem::create([
            'invoice_header_id' => $invoice->id,
            'service_name' => 'Astinet 1Gbps',
            'quantity' => 1,
            'unit_price' => 1000000,
            'amount' => 1000000,
        ]);

        $this->assertEquals($customer->id, $invoice->customer->id);
        $this->assertCount(1, $invoice->items);
        $this->assertEquals('Astinet 1Gbps', $invoice->items->first()->service_name);
    }

    public function test_customer_repository_and_invoice_repository_bindings(): void
    {
        $customerRepo = app(CustomerRepositoryInterface::class);
        $invoiceRepo = app(InvoiceRepositoryInterface::class);

        $this->assertInstanceOf(\App\Repositories\Eloquent\CustomerRepository::class, $customerRepo);
        $this->assertInstanceOf(\App\Repositories\Eloquent\InvoiceRepository::class, $invoiceRepo);
    }

    public function test_settings_helper_methods(): void
    {
        Setting::set('test_key', 'Telkom Lampung', 'string', 'company', 'Test setting');

        $value = Setting::get('test_key');
        $this->assertEquals('Telkom Lampung', $value);
    }
}
