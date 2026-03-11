<?php

namespace App\Jobs;

use App\Services\YouTubeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncYoutubeVideos implements ShouldQueue
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
    public function handle(YouTubeService $service): void
    {
        try {
            $videos = $service->fetchLatestVideos();

            if (!empty($videos)) {
                $count = $service->syncVideosToDatabase($videos);
                Log::info('SyncYoutubeVideos job completed', ['records_synced' => $count]);
            } else {
                Log::warning('SyncYoutubeVideos job: No videos returned from API');
            }
        } catch (\Exception $e) {
            Log::error('SyncYoutubeVideos job failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
