<?php

namespace Tests\Feature;

use App\Jobs\SendInvoiceEmailJob;
use App\Mail\SendInvoiceMail;
use App\Models\Customer;
use App\Models\InvoiceHeader;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class EmailAutomationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected InvoiceHeader $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $customer = Customer::create([
            'customer_code' => 'CUST-MAIL-001',
            'company_name' => 'PT Mail Enterprise',
            'name' => 'Mail Recipient',
            'email' => 'billing@mailenterprise.co.id',
            'address' => 'Bandar Lampung',
        ]);

        $this->invoice = InvoiceHeader::create([
            'invoice_number' => 'INV/MAIL/2026/07/0001',
            'customer_id' => $customer->id,
            'user_id' => $this->admin->id,
            'issue_date' => now(),
            'due_date' => now()->addDays(14),
            'subtotal' => 50000000,
            'tax_percentage' => 11.00,
            'tax_amount' => 5500000,
            'total_amount' => 55500000,
            'status' => 'generated',
        ]);
    }

    public function test_admin_can_queue_single_invoice_email(): void
    {
        Queue::fake();

        $response = $this->actingAs($this->admin)->post("/invoices/{$this->invoice->id}/send-email");

        $response->assertRedirect();
        Queue::assertPushed(SendInvoiceEmailJob::class, function ($job) {
            return $job->invoice->id === $this->invoice->id;
        });

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'EMAIL_QUEUED',
            'subject_id' => $this->invoice->id,
        ]);
    }

    public function test_admin_can_queue_bulk_invoice_emails(): void
    {
        Queue::fake();

        $invoice2 = InvoiceHeader::create([
            'invoice_number' => 'INV/MAIL/2026/07/0002',
            'customer_id' => $this->invoice->customer_id,
            'issue_date' => now(),
            'due_date' => now()->addDays(14),
            'total_amount' => 20000000,
            'status' => 'generated',
        ]);

        $response = $this->actingAs($this->admin)->post('/invoices/bulk-send-email', [
            'invoice_ids' => [$this->invoice->id, $invoice2->id],
        ]);

        $response->assertRedirect();
        Queue::assertPushed(SendInvoiceEmailJob::class, 2);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'BULK_EMAILS_QUEUED',
        ]);
    }

    public function test_send_invoice_email_job_executes_and_updates_status(): void
    {
        Mail::fake();

        $job = new SendInvoiceEmailJob($this->invoice, $this->admin->id);
        $job->handle();

        Mail::assertQueued(SendInvoiceMail::class, function ($mail) {
            return $mail->hasTo('billing@mailenterprise.co.id') &&
                $mail->invoice->id === $this->invoice->id;
        });

        $this->assertDatabaseHas('invoice_headers', [
            'id' => $this->invoice->id,
            'status' => 'sent',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'EMAIL_SENT',
            'subject_id' => $this->invoice->id,
        ]);
    }
}
