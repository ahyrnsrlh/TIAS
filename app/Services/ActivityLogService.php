<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityLogService
{
    public function getPaginatedLogs(?string $search = null, ?string $action = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = ActivityLog::with('user')->latest();

        if ($action) {
            $query->where('action', $action);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function getDistinctActions(): array
    {
        return ActivityLog::distinct()->pluck('action')->toArray();
    }
}
