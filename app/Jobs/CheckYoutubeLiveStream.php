<?php

namespace App\Jobs;

use App\Services\YouTubeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckYoutubeLiveStream implements ShouldQueue
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
        if (!config('mjfkbt3.youtube.api_key')) {
            return;
        }

        try {
            $liveData = $service->checkLiveStream();
            $service->updateLiveStatus($liveData);

            if ($liveData) {
                Log::info('CheckYoutubeLiveStream: Live stream detected', [
                    'video_id' => $liveData['video_id'] ?? null,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('CheckYoutubeLiveStream job failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
