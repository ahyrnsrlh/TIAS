<?php

namespace Tests\Feature;

use App\Http\Resources\InvoiceResource;
use App\Jobs\UpdateOverdueInvoicesJob;
use App\Models\Customer;
use App\Models\InvoiceHeader;
use App\Models\User;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    public function test_repository_contracts_resolve_from_service_container(): void
    {
        $customerRepo = app(CustomerRepositoryInterface::class);
        $invoiceRepo = app(InvoiceRepositoryInterface::class);

        $this->assertInstanceOf(CustomerRepositoryInterface::class, $customerRepo);
        $this->assertInstanceOf(InvoiceRepositoryInterface::class, $invoiceRepo);
    }

    public function test_dashboard_service_caches_metrics(): void
    {
        Cache::forget('tias_dashboard_metrics');

        $dashboardService = app(DashboardService::class);
        $data = $dashboardService->getDashboardData();

        $this->assertArrayHasKey('widgets', $data);
        $this->assertTrue(Cache::has('tias_dashboard_metrics'));
    }

    public function test_update_overdue_invoices_job_marks_expired_invoices(): void
    {
        $customer = Customer::create([
            'customer_code' => 'CUST-OVERDUE-01',
            'company_name' => 'PT Expired Client',
            'name' => 'Contact Person',
            'email' => 'expired@client.com',
            'address' => 'Bandar Lampung',
        ]);

        $expiredInvoice = InvoiceHeader::create([
            'invoice_number' => 'INV/TEST/EXPIRED/001',
            'customer_id' => $customer->id,
            'issue_date' => now()->subDays(30),
            'due_date' => now()->subDays(1),
            'total_amount' => 15000000,
            'status' => 'generated',
        ]);

        $job = new UpdateOverdueInvoicesJob();
        $job->handle();

        $this->assertDatabaseHas('invoice_headers', [
            'id' => $expiredInvoice->id,
            'status' => 'overdue',
        ]);
    }

    public function test_invoice_api_resource_formats_json_structure(): void
    {
        $customer = Customer::create([
            'customer_code' => 'CUST-API-01',
            'company_name' => 'PT API Client',
            'name' => 'API Contact',
            'email' => 'api@client.com',
            'address' => 'Bandar Lampung',
        ]);

        $invoice = InvoiceHeader::create([
            'invoice_number' => 'INV/TEST/API/001',
            'customer_id' => $customer->id,
            'issue_date' => now(),
            'due_date' => now()->addDays(14),
            'subtotal' => 10000000,
            'tax_percentage' => 11.00,
            'tax_amount' => 1100000,
            'total_amount' => 11100000,
            'status' => 'generated',
        ]);

        $invoice->load(['customer', 'items']);
        $resource = new InvoiceResource($invoice);
        $array = $resource->response()->getData(true);

        $this->assertEquals('INV/TEST/API/001', $array['data']['invoice_number']);
        $this->assertEquals(11100000, $array['data']['total_amount']);
    }
}
