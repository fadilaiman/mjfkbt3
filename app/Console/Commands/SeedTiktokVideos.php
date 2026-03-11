<?php

namespace App\Console\Commands;

use App\Models\TiktokVideo;
use App\Services\TikTokService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SeedTiktokVideos extends Command
{
    protected $signature = 'tiktok:seed {startUrl? : Optional TikTok video URL to start from}';
    protected $description = 'Seed TikTok videos interactively — opens a visible browser so you can solve the CAPTCHA';

    public function __construct(private TikTokService $tiktokService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $script   = base_path('scripts/seed-tiktok.mjs');
        $startUrl = $this->argument('startUrl') ?? '';

        if (!file_exists($script)) {
            $this->error("Seed script not found: {$script}");
            return self::FAILURE;
        }

        $this->info('Opening TikTok in a visible browser window...');
        $this->line('  → Solve the CAPTCHA when it appears');
        $this->line('  → The browser will close automatically after collecting URLs');
        $this->newLine();

        $cmd = 'node ' . escapeshellarg($script);
        if ($startUrl) {
            $cmd .= ' ' . escapeshellarg($startUrl);
        }

        // Run interactively — stderr goes to terminal, stdout is captured
        $descriptors = [
            0 => ['pipe', 'r'],            // stdin
            1 => ['pipe', 'w'],            // stdout (JSON output)
            2 => STDERR,                   // stderr goes directly to terminal
        ];

        $process = proc_open($cmd, $descriptors, $pipes, base_path());

        if (!is_resource($process)) {
            $this->error('Failed to launch Node.js script.');
            return self::FAILURE;
        }

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        proc_close($process);

        $urls = json_decode(trim($stdout), true);

        if (!is_array($urls) || empty($urls)) {
            $this->warn('No video URLs collected.');
            return self::SUCCESS;
        }

        $this->info('Found ' . count($urls) . ' video URLs. Fetching oEmbed data...');
        $this->newLine();

        $added   = 0;
        $skipped = 0;
        $failed  = 0;

        foreach ($urls as $url) {
            $url = strtok($url, '?');

            if (TiktokVideo::where('tiktok_url', $url)->exists()) {
                $skipped++;
                continue;
            }

            $oembed = $this->tiktokService->fetchOEmbed($url);

            if ($oembed) {
                TiktokVideo::create([
                    'tiktok_url'        => $url,
                    'title'             => $oembed['title'] ?? null,
                    'thumbnail_url'     => $oembed['thumbnail_url'] ?? null,
                    'oembed_html'       => $oembed['html'] ?? null,
                    'author_name'       => $oembed['author_name'] ?? null,
                    'oembed_fetched_at' => now(),
                    'sort_order'        => 0,
                    'is_hidden'         => false,
                ]);
                $added++;
                $this->line('  <fg=green>+</> ' . ($oembed['title'] ?? $url));
            } else {
                $failed++;
                $this->line('  <fg=red>✗</> oEmbed failed: ' . $url);
            }
        }

        $this->newLine();
        $this->info("Done — {$added} added, {$skipped} already existed, {$failed} failed.");
        Log::info('tiktok:seed completed', compact('added', 'skipped', 'failed'));

        return self::SUCCESS;
    }
}
