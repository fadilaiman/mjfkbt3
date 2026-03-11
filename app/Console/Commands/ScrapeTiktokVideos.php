<?php

namespace App\Console\Commands;

use App\Models\TiktokVideo;
use App\Services\TikTokService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScrapeTiktokVideos extends Command
{
    protected $signature = 'tiktok:scrape {--max=20 : Maximum number of videos to scrape}';
    protected $description = 'Scrape TikTok profile for videos using Playwright headless browser';

    public function __construct(private TikTokService $tiktokService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $profileUrl = config('mjfkbt3.tiktok.profile_url', 'https://www.tiktok.com/@mjfk.batu03');
        $max        = (int) $this->option('max');
        $script     = base_path('scripts/scrape-tiktok.mjs');

        if (!file_exists($script)) {
            $this->error("Scraper script not found: {$script}");
            return self::FAILURE;
        }

        $this->info("Scraping TikTok: {$profileUrl}");

        $redirect = PHP_OS_FAMILY === 'Windows' ? '2>NUL' : '2>/dev/null';
        $cmd      = 'node ' . escapeshellarg($script) . ' ' . escapeshellarg($profileUrl) . ' ' . $max;
        $output   = shell_exec($cmd . ' ' . $redirect);

        if (!$output) {
            $this->error('Playwright scraper returned no output. Check Node.js is installed and Playwright is set up.');
            return self::FAILURE;
        }

        $urls = json_decode(trim($output), true);

        if (!is_array($urls)) {
            $this->error("Could not parse scraper output: {$output}");
            return self::FAILURE;
        }

        if (empty($urls)) {
            $this->warn('No video URLs found. TikTok may have changed their page structure.');
            return self::SUCCESS;
        }

        $this->info('Found ' . count($urls) . ' video URLs. Fetching oEmbed data...');

        $added   = 0;
        $skipped = 0;

        foreach ($urls as $url) {
            // Normalise URL — strip query params
            $url = strtok($url, '?');

            if (TiktokVideo::where('tiktok_url', $url)->exists()) {
                $skipped++;
                continue;
            }

            $oembed = $this->tiktokService->fetchOEmbed($url);

            if ($oembed) {
                TiktokVideo::create([
                    'tiktok_url'       => $url,
                    'title'            => $oembed['title'] ?? null,
                    'thumbnail_url'    => $oembed['thumbnail_url'] ?? null,
                    'oembed_html'      => $oembed['html'] ?? null,
                    'author_name'      => $oembed['author_name'] ?? null,
                    'oembed_fetched_at'=> now(),
                    'sort_order'       => 0,
                    'is_hidden'        => false,
                ]);

                $added++;
                $this->line('  + ' . ($oembed['title'] ?? $url));
            } else {
                $this->warn('  ✗ oEmbed failed: ' . $url);
            }
        }

        $this->info("Done — {$added} added, {$skipped} already existed.");
        Log::info('tiktok:scrape completed', ['added' => $added, 'skipped' => $skipped, 'found' => count($urls)]);

        return self::SUCCESS;
    }
}
