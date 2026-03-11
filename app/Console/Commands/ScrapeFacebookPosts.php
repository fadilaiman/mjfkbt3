<?php

namespace App\Console\Commands;

use App\Models\FbPost;
use App\Services\FacebookService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScrapeFacebookPosts extends Command
{
    protected $signature = 'facebook:scrape
        {--wait=25   : Seconds to wait in the browser before auto-scraping}
        {--max=40    : Maximum number of posts to scrape}';

    protected $description = 'Open a real browser, scrape the Facebook page for posts, and import them (one-time initial import)';

    public function __construct(private FacebookService $facebookService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $script = base_path('scripts/scrape-facebook.mjs');

        if (! file_exists($script)) {
            $this->error("Scraper script not found: {$script}");
            return self::FAILURE;
        }

        $pageUrl  = config('mjfkbt3.facebook.page_url', 'https://www.facebook.com/mjfkbt3');
        $wait     = (int) $this->option('wait');
        $max      = (int) $this->option('max');
        $tmpFile  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'fb_scrape_' . uniqid() . '.json';

        $this->info("Opening browser for: {$pageUrl}");
        $this->line("  • Close any login popup or CAPTCHA in the browser window.");
        $this->line("  • The script will auto-scrape after {$wait}s.");
        $this->newLine();

        $cmd = sprintf(
            'node %s %s %d %d %s',
            escapeshellarg($script),
            escapeshellarg($pageUrl),
            $wait,
            $max,
            escapeshellarg($tmpFile)
        );

        // passthru shows the Node.js output in real-time (countdown, scroll progress, etc.)
        passthru($cmd, $exitCode);

        $this->newLine();

        if (! file_exists($tmpFile)) {
            $this->error('Scraper did not produce output file. Check Node.js and Playwright are installed.');
            return self::FAILURE;
        }

        $json = file_get_contents($tmpFile);
        @unlink($tmpFile);

        $urls = json_decode($json, true);

        if (! is_array($urls)) {
            $this->error("Could not parse scraper output.");
            return self::FAILURE;
        }

        if (empty($urls)) {
            $this->warn('No post URLs found. Try increasing --wait to give yourself more time.');
            return self::SUCCESS;
        }

        $this->info('Importing ' . count($urls) . ' posts...');
        $this->newLine();

        $added   = 0;
        $skipped = 0;

        foreach ($urls as $i => $url) {
            $fbPostId = 'url_' . md5($url);

            if (FbPost::where('fb_post_id', $fbPostId)->exists()) {
                $skipped++;
                continue;
            }

            try {
                FbPost::create([
                    'fb_post_id'    => $fbPostId,
                    'permalink_url' => $url,
                    'embed_html'    => $this->facebookService->generatePostEmbed($url),
                    'post_type'     => 'status',
                    // Space posts out so they sort correctly (first found = newest)
                    'published_at'  => now()->subMinutes($i * 2),
                    'fetched_at'    => now(),
                    'is_hidden'     => false,
                ]);
                $added++;
                $this->line("  + {$url}");
            } catch (\Exception $e) {
                $this->warn("  ✗ Failed: {$url} — {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Done — {$added} added, {$skipped} already existed.");

        Log::info('facebook:scrape completed', [
            'added'   => $added,
            'skipped' => $skipped,
            'found'   => count($urls),
        ]);

        return self::SUCCESS;
    }
}
