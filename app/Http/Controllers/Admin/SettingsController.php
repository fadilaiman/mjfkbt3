<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    use LogsAdminAction;

    public function index(): Response
    {
        return Inertia::render('Admin/Tetapan', [
            'settings' => [
                'mosque' => config('mjfkbt3.mosque'),
                'upload' => config('mjfkbt3.upload'),
                'jakim' => config('mjfkbt3.jakim'),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        // Placeholder for future settings update logic.
        // Settings can be stored in a database table or .env file.

        $this->logAction('updated_settings', null, null, 'Kemaskini tetapan sistem');

        return back()->with('success', 'Tetapan berjaya dikemaskini.');
    }
}
