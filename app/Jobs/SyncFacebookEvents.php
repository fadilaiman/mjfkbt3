<?php

namespace App\Jobs;

use App\Services\FacebookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncFacebookEvents implements ShouldQueue
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
    public function handle(FacebookService $service): void
    {
        // Facebook Events sync now handled manually by admin.
        // Events are added via Admin > Aktiviti > Tambah Baru.
        // This job is kept for future API integration if needed.
        Log::info('SyncFacebookEvents: No-op. Events managed manually by admin.');
    }
}
