<?php

namespace App\Jobs;

use App\Services\FacebookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncFacebookPosts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Sync Facebook posts.
     *
     * If FACEBOOK_APP_ID + FACEBOOK_APP_SECRET are set in .env, this auto-fetches
     * new public posts via the Graph API (App Access Token — no page owner login needed).
     * Otherwise, it just refreshes embed HTML for manually-added posts.
     */
    public function handle(FacebookService $service): void
    {
        try {
            if ($service->hasGraphApiCredentials()) {
                $result = $service->syncViaGraphApi(limit: 25);
                Log::info('SyncFacebookPosts: Graph API sync complete', $result);
            }

            $refreshed = $service->refreshAllEmbeds();
            Log::info('SyncFacebookPosts: Embeds refreshed', ['count' => $refreshed]);
        } catch (\Exception $e) {
            Log::error('SyncFacebookPosts job failed', ['error' => $e->getMessage()]);
        }
    }
}
