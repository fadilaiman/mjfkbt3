<?php

namespace App\Jobs;

use App\Services\TikTokService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RefreshTikTokOEmbed implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(TikTokService $service): void
    {
        try {
            $count = $service->refreshAllOEmbeds();
            Log::info('RefreshTikTokOEmbed job completed', ['records_refreshed' => $count]);
        } catch (\Exception $e) {
            Log::error('RefreshTikTokOEmbed job failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
