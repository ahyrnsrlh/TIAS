<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\InvoiceHeader;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_admin_can_view_invoices_index_page(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get('/invoices');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Invoices/Index')->has('invoices'));
    }

    public function test_admin_can_view_create_invoice_page(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get('/invoices/create');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Invoices/Create')->has('customers'));
    }

    public function test_admin_can_create_invoice_with_items_and_tax_calculation(): void
    {
        $admin = User::factory()->create();
        $customer = Customer::create([
            'customer_code' => 'CUST-INV-001',
            'name' => 'Test Client',
            'company_name' => 'PT Invoice Test',
            'email' => 'client@invtest.com',
            'address' => 'Bandar Lampung',
        ]);

        $response = $this->actingAs($admin)->post('/invoices', [
            'customer_id' => $customer->id,
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(14)->format('Y-m-d'),
            'status' => 'generated',
            'tax_percentage' => 11.00,
            'discount_amount' => 0,
            'items' => [
                [
                    'service_name' => 'Astinet 1Gbps Dedicated',
                    'service_code' => 'AST-1G',
                    'description' => 'Dedicated Bandwidth',
                    'quantity' => 1,
                    'unit_price' => 100000000,
                    'amount' => 100000000,
                ]
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('invoice_headers', [
            'customer_id' => $customer->id,
            'status' => 'generated',
            'subtotal' => 100000000,
            'tax_amount' => 11000000,
            'total_amount' => 111000000,
        ]);
        $this->assertDatabaseHas('invoice_items', [
            'service_name' => 'Astinet 1Gbps Dedicated',
            'amount' => 100000000,
        ]);
    }

    public function test_admin_can_view_invoice_show_page(): void
    {
        $admin = User::factory()->create();
        $customer = Customer::create([
            'customer_code' => 'CUST-INV-002',
            'name' => 'Show Client',
            'company_name' => 'PT Show Test',
            'email' => 'show@invtest.com',
            'address' => 'Bandar Lampung',
        ]);

        $invoice = InvoiceHeader::create([
            'invoice_number' => 'INV/TEST/SHOW/001',
            'customer_id' => $customer->id,
            'issue_date' => now(),
            'due_date' => now()->addDays(14),
            'total_amount' => 5000000,
            'status' => 'sent',
        ]);

        $response = $this->actingAs($admin)->get("/invoices/{$invoice->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Invoices/Show')->has('invoice'));
    }

    public function test_admin_can_soft_delete_invoice(): void
    {
        $admin = User::factory()->create();
        $customer = Customer::create([
            'customer_code' => 'CUST-INV-003',
            'name' => 'Delete Client',
            'company_name' => 'PT Delete Test',
            'email' => 'delete@invtest.com',
            'address' => 'Bandar Lampung',
        ]);

        $invoice = InvoiceHeader::create([
            'invoice_number' => 'INV/TEST/DEL/001',
            'customer_id' => $customer->id,
            'issue_date' => now(),
            'due_date' => now()->addDays(14),
            'total_amount' => 5000000,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($admin)->delete("/invoices/{$invoice->id}");

        $response->assertRedirect();
        $this->assertSoftDeleted($invoice);
    }
}
