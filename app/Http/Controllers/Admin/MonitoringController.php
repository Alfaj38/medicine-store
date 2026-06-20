<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $dbStatus = 'down';
        try {
            if (DB::connection()->getPdo()) {
                $dbStatus = 'healthy';
            }
        } catch (\Exception $e) {}

        return Inertia::render('Admin/Monitoring/Index', [
            'metrics' => [
                'db_status'      => $dbStatus,
                'queue_jobs'     => DB::table('jobs')->count(),
                'failed_jobs'    => DB::table('failed_jobs')->count(),
                'cache_status'   => Cache::put('_health', 'ok', 1) ? 'healthy' : 'down',
                'disk_free_gb'   => round(disk_free_space('/') / 1e9, 1),
            ]
        ]);
    }
}
