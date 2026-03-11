<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('sync:facebook', function () {
    $service = app(\App\Services\FacebookService::class);

    if ($service->hasGraphApiCredentials()) {
        $this->info('Syncing via Facebook Graph API...');
        $result = $service->syncViaGraphApi(limit: 25);
        if (isset($result['error'])) {
            $this->error("Graph API error: {$result['error']}");
        } else {
            $this->info("Found: {$result['found']} posts — New: {$result['new']} saved.");
        }
    } else {
        $this->warn('FACEBOOK_APP_ID / FACEBOOK_APP_SECRET not set — skipping auto-sync.');
        $this->line('Set them in .env to enable automatic post fetching.');
    }

    $refreshed = $service->refreshAllEmbeds();
    $this->info("Embeds refreshed: {$refreshed}");
})->purpose('Sync Facebook posts via Graph API (requires FACEBOOK_APP_ID + FACEBOOK_APP_SECRET in .env)');

/*
|--------------------------------------------------------------------------
| Scheduled Jobs
|--------------------------------------------------------------------------
|
| Jadual kerja latar belakang untuk sync data dari API luaran.
| Pastikan cron server dikonfigurasi:
| * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
|
*/

Artisan::command('sync:youtube', function () {
    $service = app(\App\Services\YouTubeService::class);
    $videos = $service->fetchLatestVideos();
    if (empty($videos)) {
        $this->warn('No videos found from YouTube channel page.');
        return;
    }
    $count = $service->syncVideosToDatabase($videos);
    $this->info("Synced {$count} videos.");
})->purpose('Sync latest videos from YouTube channel page (no API key required)');

Schedule::job(new \App\Jobs\SyncPrayerTimes())->dailyAt('00:01')->timezone('Asia/Kuala_Lumpur');
Schedule::job(new \App\Jobs\CheckYoutubeLiveStream())->everyFiveMinutes();
Schedule::job(new \App\Jobs\SyncYoutubeVideos())->everySixHours();
Schedule::job(new \App\Jobs\SyncFacebookPosts())->everyThirtyMinutes();
Schedule::job(new \App\Jobs\SyncFacebookEvents())->hourly();
Schedule::job(new \App\Jobs\RefreshTikTokOEmbed())->daily();
Schedule::command('tiktok:scrape --max=20')->dailyAt('03:00')->timezone('Asia/Kuala_Lumpur');
