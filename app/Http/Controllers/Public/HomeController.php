<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\FbPost;
use App\Models\PrayerTime;
use App\Models\TiktokVideo;
use App\Models\WhatsappContact;
use App\Models\YoutubeLiveStatus;
use App\Models\YoutubeVideo;
use App\Services\JakimPrayerService;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __construct(
        protected JakimPrayerService $jakimPrayerService,
    ) {}

    public function index(): Response
    {
        $zone = config('mjfkbt3.jakim.zone', 'SGR01');

        return Inertia::render('Public/Home', [
            'liveStatus' => YoutubeLiveStatus::orderByDesc('checked_at')->first(),
            'latestVideo' => YoutubeVideo::visible()->orderByDesc('published_at')->first(),
            'prayerTimes' => PrayerTime::forToday()->forZone($zone)->first(),
            'activePrayer' => $this->jakimPrayerService->getActivePrayer(),
            'donations' => Donation::active()->orderBy('sort_order')->get(),
            'latestVideos' => YoutubeVideo::visible()
                ->orderByDesc('published_at')
                ->limit(8)
                ->get(),
            'tiktokVideos' => TiktokVideo::visible()->get(),
            'fbPosts' => FbPost::visible()
                ->orderByDesc('published_at')
                ->limit(6)
                ->get(),
            'whatsappContacts' => WhatsappContact::active()->get(),
            'mosque' => config('mjfkbt3.mosque'),
        ]);
    }
}
