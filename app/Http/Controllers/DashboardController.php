<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display the enterprise dashboard for PT. Telkom Witel Lampung.
     */
    public function index(): Response
    {
        $data = $this->dashboardService->getDashboardData();

        return Inertia::render('Dashboard', [
            'widgets' => $data['widgets'],
            'witelInfo' => [
                'name' => 'PT. Telkom Indonesia Witel Lampung',
                'witel' => 'Witel Lampung',
                'location' => 'Bandar Lampung',
                'system_version' => 'TIAS v1.0 Enterprise',
            ],
        ]);
    }
}
