<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_service_returns_all_7_enterprise_widgets(): void
    {
        /** @var DashboardService $service */
        $service = app(DashboardService::class);
        $summary = $service->getDashboardSummary();

        $this->assertArrayHasKey('widgets', $summary);
        $widgets = $summary['widgets'];

        $this->assertArrayHasKey('total_customer', $widgets);
        $this->assertArrayHasKey('total_invoice', $widgets);
        $this->assertArrayHasKey('paid_invoice', $widgets);
        $this->assertArrayHasKey('pending_invoice', $widgets);
        $this->assertArrayHasKey('monthly_revenue', $widgets);
        $this->assertArrayHasKey('latest_invoice', $widgets);
        $this->assertArrayHasKey('activity_timeline', $widgets);
    }

    public function test_authenticated_user_can_access_dashboard_with_inertia_widget_props(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('widgets.total_customer')
            ->has('widgets.total_invoice')
            ->has('widgets.paid_invoice')
            ->has('widgets.pending_invoice')
            ->has('widgets.monthly_revenue')
            ->has('widgets.latest_invoice')
            ->has('widgets.activity_timeline')
            ->has('witelInfo')
        );
    }
}
