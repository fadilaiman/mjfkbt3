<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use Inertia\Inertia;
use Inertia\Response;

class AdminLogController extends Controller
{
    use LogsAdminAction;

    public function index(): Response
    {
        $logs = AdminLog::with('adminUser')
            ->orderByDesc('created_at')
            ->paginate(50);

        return Inertia::render('Admin/Log/Index', [
            'logs' => $logs,
        ]);
    }
}
