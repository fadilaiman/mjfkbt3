<?php

namespace App\Jobs;

use App\Services\JakimPrayerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncPrayerTimes implements ShouldQueue
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
    public function handle(JakimPrayerService $service): void
    {
        try {
            $prayerTimes = $service->fetchMonthlyPrayerTimes();

            if (!empty($prayerTimes)) {
                $count = $service->syncToDatabase($prayerTimes);
                Log::info('SyncPrayerTimes job completed', ['records_synced' => $count]);
            } else {
                Log::warning('SyncPrayerTimes job: No prayer time data returned from API');
            }
        } catch (\Exception $e) {
            Log::error('SyncPrayerTimes job failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
