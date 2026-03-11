<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Inertia\Inertia;
use Inertia\Response;

class PengumumanController extends Controller
{
    public function index(): Response
    {
        $announcements = Announcement::published()
            ->active()
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->paginate(12);

        return Inertia::render('Public/Pengumuman', [
            'announcements' => $announcements,
        ]);
    }
}
