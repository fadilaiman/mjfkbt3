<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Announcement;
use App\Models\Donation;
use App\Models\Event;
use App\Models\FbPost;
use App\Models\TiktokVideo;
use App\Models\YoutubeVideo;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    use LogsAdminAction;

    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'announcement_count' => Announcement::published()->count(),
                'event_count' => Event::published()->upcoming()->count(),
                'donation_active' => Donation::active()->count(),
                'youtube_video_count' => YoutubeVideo::visible()->count(),
                'fb_post_count' => FbPost::visible()->count(),
                'tiktok_video_count' => TiktokVideo::visible()->count(),
            ],
            'recent_logs' => AdminLog::with('adminUser')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(),
        ]);
    }
}
