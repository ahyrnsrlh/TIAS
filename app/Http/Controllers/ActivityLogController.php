<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    protected ActivityLogService $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    /**
     * Display system activity audit trail ledger.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $action = $request->input('action');
        $perPage = (int) $request->input('per_page', 15);

        $logs = $this->activityLogService->getPaginatedLogs($search, $action, $perPage);
        $actions = $this->activityLogService->getDistinctActions();

        return Inertia::render('ActivityLogs/Index', [
            'logs' => $logs,
            'actions' => $actions,
            'filters' => [
                'search' => $search,
                'action' => $action,
                'per_page' => $perPage,
            ],
        ]);
    }
}
