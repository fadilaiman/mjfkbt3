<?php

namespace App\Services;

use App\Models\YoutubeLiveStatus;
use App\Models\YoutubeVideo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YouTubeService
{
    /**
     * Resolve channel ID from handle by scraping the YouTube channel page.
     * Cached for 30 days since channel IDs never change.
     */
    public function resolveChannelId(): ?string
    {
        $configId = config('mjfkbt3.youtube.channel_id');
        if ($configId) {
            return $configId;
        }

        $handle = config('mjfkbt3.youtube.channel_handle');
        if (!$handle) {
            Log::warning('YouTubeService: No channel handle or ID configured.');
            return null;
        }

        return Cache::remember('youtube_channel_id', now()->addDays(30), function () use ($handle) {
            return $this->scrapeChannelId($handle);
        });
    }

    /**
     * Scrape the YouTube channel page to extract the channel ID (UCxxxxxxxxx).
     */
    protected function scrapeChannelId(string $handle): ?string
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'])
                ->get("https://www.youtube.com/@{$handle}");

            if ($response->failed()) {
                Log::error('YouTubeService: Failed to fetch channel page for handle', ['handle' => $handle]);
                return null;
            }

            $body = $response->body();

            // Try multiple patterns for channel ID
            $patterns = [
                '/"externalId":"(UC[a-zA-Z0-9_-]{22})"/',
                '/"channelId":"(UC[a-zA-Z0-9_-]{22})"/',
                '/channel\/(UC[a-zA-Z0-9_-]{22})/',
            ];

            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $body, $matches)) {
                    Log::info('YouTubeService: Resolved channel ID', ['handle' => $handle, 'id' => $matches[1]]);
                    return $matches[1];
                }
            }

            Log::error('YouTubeService: Could not extract channel ID from page', ['handle' => $handle]);
            return null;
        } catch (\Exception $e) {
            Log::error('YouTubeService: scrapeChannelId exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Check if the channel has an active live stream by scraping the /live page.
     * No API key required.
     */
    public function checkLiveStream(): ?array
    {
        $handle = config('mjfkbt3.youtube.channel_handle');
        $channelId = $this->resolveChannelId();

        if (!$handle && !$channelId) {
            return null;
        }

        try {
            // Prefer handle-based URL as it's more reliable
            $liveUrl = $handle
                ? "https://www.youtube.com/@{$handle}/live"
                : "https://www.youtube.com/channel/{$channelId}/live";

            $response = Http::timeout(15)
                ->withOptions(['allow_redirects' => true])
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; bot)'])
                ->get($liveUrl);

            $body = $response->body();

            // Check if there's a live video ID on this page
            if (!preg_match('/"videoId":"([a-zA-Z0-9_-]{11})"/', $body, $matches)) {
                return null;
            }

            $videoId = $matches[1];

            // Verify it is actually live (not just a recent video)
            $isLive = str_contains($body, '"isLiveNow":true')
                || str_contains($body, '"isLive":true')
                || str_contains($body, '"liveBroadcastContent":"live"');

            if (!$isLive) {
                return null;
            }

            // Extract title if available
            $title = null;
            if (preg_match('/"title":\{"runs":\[\{"text":"([^"]+)"/', $body, $titleMatches)) {
                $title = $titleMatches[1];
            }

            return [
                'is_live' => true,
                'video_id' => $videoId,
                'title' => $title,
            ];
        } catch (\Exception $e) {
            Log::error('YouTubeService: checkLiveStream exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Update or create the live status record in the database.
     */
    public function updateLiveStatus(?array $liveData): void
    {
        $record = YoutubeLiveStatus::orderByDesc('id')->first();

        $payload = [
            'is_live' => $liveData['is_live'] ?? false,
            'video_id' => $liveData['video_id'] ?? null,
            'title' => $liveData['title'] ?? null,
            'concurrent_viewers' => 0,
            'checked_at' => now(),
        ];

        if ($record) {
            $record->update($payload);
        } else {
            YoutubeLiveStatus::create($payload);
        }
    }

    /**
     * Fetch latest videos by scraping the channel page ytInitialData.
     * Works for channels that only live-stream (RSS feed returns 404 for those).
     */
    public function fetchLatestVideos(int $maxResults = 15): array
    {
        $handle = config('mjfkbt3.youtube.channel_handle');
        if (!$handle) {
            Log::warning('YouTubeService: No channel handle configured.');
            return [];
        }

        try {
            $response = Http::timeout(20)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'])
                ->get("https://www.youtube.com/@{$handle}");

            if ($response->failed()) {
                Log::error('YouTubeService: Channel page fetch failed', ['status' => $response->status()]);
                return [];
            }

            return $this->parseChannelPage($response->body(), $maxResults);
        } catch (\Exception $e) {
            Log::error('YouTubeService: fetchLatestVideos exception', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Parse ytInitialData from a YouTube channel page to extract video items.
     */
    protected function parseChannelPage(string $html, int $limit = 15): array
    {
        if (!preg_match('/var ytInitialData = ({.+?});<\/script>/', $html, $m)) {
            Log::error('YouTubeService: ytInitialData not found in channel page');
            return [];
        }

        $data = json_decode($m[1], true);
        if (!$data) {
            Log::error('YouTubeService: Failed to decode ytInitialData JSON');
            return [];
        }

        $tabs = $data['contents']['twoColumnBrowseResultsRenderer']['tabs'] ?? [];
        $videos = [];

        foreach ($tabs as $tab) {
            $renderer = $tab['tabRenderer'] ?? null;
            if (!$renderer) {
                continue;
            }

            $items = $renderer['content']['richGridRenderer']['contents'] ?? [];
            if (empty($items)) {
                continue;
            }

            foreach ($items as $item) {
                if (count($videos) >= $limit) {
                    break;
                }

                $vid = $item['richItemRenderer']['content']['videoRenderer'] ?? null;
                if (!$vid || empty($vid['videoId'])) {
                    continue;
                }

                $videoId = $vid['videoId'];
                $title = $vid['title']['runs'][0]['text'] ?? '';
                $relativeTime = $vid['publishedTimeText']['simpleText'] ?? '';
                $duration = $vid['lengthText']['simpleText'] ?? null;

                // Parse view count from Malay locale ("20 tontonan", "1.2K tontonan")
                $viewText = $vid['viewCountText']['simpleText'] ?? '0';
                $viewCount = $this->parseViewCount($viewText);

                // Approximate published_at from relative Malay time string
                $publishedAt = $this->approximatePublishedAt($relativeTime);

                // Use clean hqdefault thumbnail URL
                $thumbnailUrl = "https://i.ytimg.com/vi/{$videoId}/hqdefault.jpg";

                $videos[] = [
                    'video_id' => $videoId,
                    'title' => $title,
                    'description' => '',
                    'thumbnail_url' => $thumbnailUrl,
                    'published_at' => $publishedAt,
                    'duration' => $duration,
                    'view_count' => $viewCount,
                    'like_count' => 0,
                    'is_live' => false,
                ];
            }

            if (!empty($videos)) {
                break; // Stop after first tab that has videos
            }
        }

        return $videos;
    }

    /**
     * Parse view count string from Malay/English YouTube locale.
     * e.g. "20 tontonan", "1.2K tontonan", "1.5K views"
     */
    protected function parseViewCount(string $text): int
    {
        // Remove non-numeric suffix words
        $text = preg_replace('/\s*(tontonan|views?|penonton).*/i', '', trim($text));
        $text = trim($text);

        if (str_contains(strtoupper($text), 'K')) {
            return (int) (floatval($text) * 1000);
        }
        if (str_contains(strtoupper($text), 'M')) {
            return (int) (floatval($text) * 1000000);
        }

        return (int) preg_replace('/[^0-9]/', '', $text);
    }

    /**
     * Approximate a Carbon timestamp from YouTube's relative Malay time strings.
     * e.g. "8 jam lalu" → now - 8 hours, "2 hari lalu" → now - 2 days
     */
    protected function approximatePublishedAt(string $relative): Carbon
    {
        if (empty($relative)) {
            return now();
        }

        $relative = mb_strtolower($relative);

        // Malay: "X jam lalu", "X minit lalu", "X hari lalu", "X minggu lalu", "X bulan lalu", "X tahun lalu"
        // English: "X hours ago", "X minutes ago", "X days ago", "X weeks ago", "X months ago", "X years ago"
        $map = [
            '/(\d+)\s*(jam|hour)/' => 'hours',
            '/(\d+)\s*(minit|minute)/' => 'minutes',
            '/(\d+)\s*(hari|day)/' => 'days',
            '/(\d+)\s*(minggu|week)/' => 'weeks',
            '/(\d+)\s*(bulan|month)/' => 'months',
            '/(\d+)\s*(tahun|year)/' => 'years',
        ];

        foreach ($map as $pattern => $unit) {
            if (preg_match($pattern, $relative, $m)) {
                return now()->sub($unit, (int) $m[1]);
            }
        }

        return now();
    }

    /**
     * Upsert videos into the youtube_videos table.
     */
    public function syncVideosToDatabase(array $videos): int
    {
        $count = 0;

        foreach ($videos as $video) {
            try {
                YoutubeVideo::updateOrCreate(
                    ['video_id' => $video['video_id']],
                    [
                        'title' => $video['title'],
                        'description' => $video['description'] ?? null,
                        'thumbnail_url' => $video['thumbnail_url'] ?? null,
                        'published_at' => $video['published_at'] ?? now(),
                        'duration' => $video['duration'] ?? null,
                        'view_count' => $video['view_count'] ?? 0,
                        'like_count' => $video['like_count'] ?? 0,
                        'is_live' => $video['is_live'] ?? false,
                        'fetched_at' => now(),
                    ]
                );
                $count++;
            } catch (\Exception $e) {
                Log::error('YouTubeService: Failed to sync video', [
                    'video_id' => $video['video_id'] ?? 'unknown',
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('YouTubeService: Videos synced', ['count' => $count]);
        return $count;
    }

    /**
     * Get the latest live status from DB.
     */
    public function getLiveStatus(): ?YoutubeLiveStatus
    {
        return YoutubeLiveStatus::orderByDesc('id')->first();
    }

    /**
     * Get latest visible videos from DB.
     */
    public function getLatestVideos(int $limit = 8): Collection
    {
        return YoutubeVideo::visible()->orderByDesc('published_at')->limit($limit)->get();
    }
}
