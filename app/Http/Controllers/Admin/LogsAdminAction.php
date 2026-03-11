<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminLog;

trait LogsAdminAction
{
    protected function logAction(string $action, ?string $modelType = null, ?int $modelId = null, ?string $description = null): void
    {
        AdminLog::create([
            'admin_user_id' => auth('admin')->id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }
}
