<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Throwable;

class SystemHealthService
{
    public function check(): array
    {
        $queryMilliseconds = null;
        $databaseHealthy = false;

        try {
            $startedAt = hrtime(true);
            DB::select('select 1');
            $queryMilliseconds = round((hrtime(true) - $startedAt) / 1_000_000, 2);
            $databaseHealthy = true;
        } catch (Throwable) {
            // The health panel should render even when the database is unavailable.
        }

        $queuedJobs = 0;
        $failedJobs = 0;

        if ($databaseHealthy) {
            try {
                $queuedJobs = (int) DB::table('jobs')->count();
                $failedJobs = (int) DB::table('failed_jobs')->count();
            } catch (Throwable) {
                $databaseHealthy = false;
            }
        }

        $queryHealthy = $queryMilliseconds !== null && $queryMilliseconds < 5000;
        $healthy = $databaseHealthy && $queryHealthy && $queuedJobs === 0;
        $busy = $databaseHealthy && $queryHealthy && $queuedJobs > 0;

        return [
            'database_healthy' => $databaseHealthy,
            'query_ms' => $queryMilliseconds,
            'query_healthy' => $queryHealthy,
            'query_limit_ms' => 5000,
            'queued_jobs' => $queuedJobs,
            'failed_jobs' => $failedJobs,
            'status' => $healthy ? 'Healthy' : ($busy ? 'Busy' : 'Attention'),
            'status_class' => $healthy
                ? 'bg-emerald-50 text-emerald-700'
                : ($busy ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700'),
        ];
    }
}
