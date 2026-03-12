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
use Illuminate\Support\Facades\Http;
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

        $donations = Donation::active()->orderBy('sort_order')->get();

        // Fetch live collected amount from ToyyibPay query endpoint
        $queryUrl = config('mjfkbt3.toyyibpay.query_url');
        if ($queryUrl && $donations->isNotEmpty()) {
            try {
                $response = Http::timeout(5)->get($queryUrl);
                if ($response->successful()) {
                    $amount = $response->json('billpaymentAmountNett');
                    if ($amount !== null) {
                        $donations[0]->collected_amount = $amount;
                    }
                }
            } catch (\Exception $e) {
                // fall back to DB value silently
            }
        }

        return Inertia::render('Public/Home', [
            'liveStatus' => YoutubeLiveStatus::orderByDesc('checked_at')->first(),
            'latestVideo' => YoutubeVideo::visible()->orderByDesc('published_at')->first(),
            'prayerTimes' => PrayerTime::forToday()->forZone($zone)->first(),
            'activePrayer' => $this->jakimPrayerService->getActivePrayer(),
            'donations' => $donations,
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
