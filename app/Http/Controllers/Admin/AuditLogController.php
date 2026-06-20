<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::with('user')
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Security/AuditLogs', [
            'logs' => $logs
        ]);
    }
}
