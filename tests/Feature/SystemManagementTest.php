<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SystemManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    public function test_authenticated_admin_can_view_settings_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/settings');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Settings/Index')->has('settings'));
    }

    public function test_admin_can_update_system_settings(): void
    {
        $response = $this->actingAs($this->admin)->post('/settings', [
            'settings' => [
                'company_name' => 'PT. Telkom Indonesia Updated Name',
                'default_tax_rate' => '11.00',
                'invoice_number_prefix' => 'INV/TLK-LPG-UPDATED',
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('settings', [
            'key' => 'company_name',
            'value' => 'PT. Telkom Indonesia Updated Name',
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'SETTINGS_UPDATED',
        ]);
    }

    public function test_admin_can_trigger_smtp_test_connection_email(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->admin)->post('/settings/test-smtp', [
            'recipient' => 'test.admin@telkom.co.id',
        ]);

        $response->assertRedirect();
        Mail::assertSent(\App\Mail\TestSmtpMail::class, function ($mail) {
            return $mail->hasTo('test.admin@telkom.co.id');
        });

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'SMTP_TEST_SENT',
        ]);
    }

    public function test_authenticated_admin_can_view_activity_logs_page_with_filters(): void
    {
        ActivityLog::create([
            'user_id' => $this->admin->id,
            'action' => 'CUSTOM_TEST_ACTION',
            'description' => 'Test activity log description',
        ]);

        $response = $this->actingAs($this->admin)->get('/activity-logs?action=CUSTOM_TEST_ACTION');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('ActivityLogs/Index')
            ->has('logs')
            ->has('actions')
        );
    }
}
