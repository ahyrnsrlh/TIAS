<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_admin_can_view_customers_page(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get('/customers');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Customers/Index')->has('customers'));
    }

    public function test_admin_can_create_new_customer(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->post('/customers', [
            'company_name' => 'PT Lampung Jaya Enterprise',
            'name' => 'Ahmad Subandi',
            'email' => 'ahmad@lampungjaya.com',
            'phone' => '0721-998877',
            'npwp' => '01.999.888.7-321.000',
            'virtual_account' => '9881140000000099',
            'address' => 'Jl. Raden Intan No. 45, Bandar Lampung',
            'city' => 'Bandar Lampung',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('customers', [
            'company_name' => 'PT Lampung Jaya Enterprise',
            'email' => 'ahmad@lampungjaya.com',
            'virtual_account' => '9881140000000099',
        ]);
    }

    public function test_admin_can_update_existing_customer(): void
    {
        $admin = User::factory()->create();
        $customer = Customer::create([
            'customer_code' => 'CUST-TLK-777',
            'company_name' => 'PT Initial Name',
            'name' => 'Budi',
            'email' => 'initial@company.com',
            'address' => 'Bandar Lampung',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->put("/customers/{$customer->id}", [
            'customer_code' => 'CUST-TLK-777',
            'company_name' => 'PT Updated Corporate Name',
            'name' => 'Budi Updated',
            'email' => 'initial@company.com',
            'address' => 'Jl. Updated Address No. 12',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'company_name' => 'PT Updated Corporate Name',
        ]);
    }

    public function test_admin_can_soft_delete_customer(): void
    {
        $admin = User::factory()->create();
        $customer = Customer::create([
            'customer_code' => 'CUST-TLK-888',
            'company_name' => 'PT Delete Test',
            'name' => 'Charlie',
            'email' => 'delete@test.com',
            'address' => 'Bandar Lampung',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->delete("/customers/{$customer->id}");

        $response->assertRedirect();
        $this->assertSoftDeleted($customer);
    }

    public function test_admin_can_export_customers_excel(): void
    {
        Excel::fake();
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get('/customers/export');

        $response->assertStatus(200);
        Excel::assertDownloaded('Telkom_Lampung_Customers_' . date('Ymd_His') . '.xlsx');
    }
}
