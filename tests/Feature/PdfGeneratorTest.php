<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\InvoiceHeader;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PdfGeneratorTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected InvoiceHeader $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $customer = Customer::create([
            'customer_code' => 'CUST-PDF-001',
            'name' => 'PDF Test Client',
            'company_name' => 'PT PDF Enterprise',
            'email' => 'pdf@enterprise.com',
            'npwp' => '01.222.333.4-321.000',
            'virtual_account' => '9881140000000088',
            'address' => 'Jl. Wolter Monginsidi Bandar Lampung',
        ]);

        $this->invoice = InvoiceHeader::create([
            'invoice_number' => 'INV/PDF/2026/07/0001',
            'customer_id' => $customer->id,
            'user_id' => $this->admin->id,
            'issue_date' => now(),
            'due_date' => now()->addDays(14),
            'subtotal' => 100000000,
            'tax_percentage' => 11.00,
            'tax_amount' => 11000000,
            'total_amount' => 111000000,
            'status' => 'sent',
        ]);
    }

    public function test_admin_can_generate_invoice_pdf(): void
    {
        $response = $this->actingAs($this->admin)->get("/invoices/{$this->invoice->id}/pdf");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_can_stream_invoice_pdf(): void
    {
        $response = $this->actingAs($this->admin)->get("/invoices/{$this->invoice->id}/stream");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_can_generate_billing_detail_pdf(): void
    {
        $response = $this->actingAs($this->admin)->get("/invoices/{$this->invoice->id}/billing-detail/pdf");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_can_generate_official_receipt_pdf(): void
    {
        $response = $this->actingAs($this->admin)->get("/invoices/{$this->invoice->id}/receipt/pdf");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_can_generate_tax_invoice_pdf(): void
    {
        $response = $this->actingAs($this->admin)->get("/invoices/{$this->invoice->id}/tax-invoice/pdf");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
