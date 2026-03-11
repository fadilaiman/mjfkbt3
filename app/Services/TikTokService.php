<?php

namespace App\Services;

use App\Models\TiktokVideo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TikTokService
{
    /**
     * Fetch oEmbed data for a TikTok video URL.
     */
    public function fetchOEmbed(string $tiktokUrl): ?array
    {
        try {
            $response = Http::timeout(10)->get('https://www.tiktok.com/oembed', [
                'url' => $tiktokUrl,
            ]);

            if ($response->failed()) {
                Log::error('TikTok oEmbed fetch failed', [
                    'url' => $tiktokUrl,
                    'status' => $response->status(),
                ]);
                return null;
            }

            $data = $response->json();

            if (!$data || !isset($data['html'])) {
                Log::warning('TikTok oEmbed response missing html', [
                    'url' => $tiktokUrl,
                ]);
                return null;
            }

            return [
                'title' => $data['title'] ?? null,
                'thumbnail_url' => $data['thumbnail_url'] ?? null,
                'html' => $data['html'] ?? null,
                'author_name' => $data['author_name'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('TikTok oEmbed exception', [
                'url' => $tiktokUrl,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Refresh oEmbed data for all TikTok videos in the database.
     */
    public function refreshAllOEmbeds(): int
    {
        $videos = TiktokVideo::all();
        $count = 0;

        foreach ($videos as $video) {
            try {
                $oembedData = $this->fetchOEmbed($video->tiktok_url);

                if ($oembedData) {
                    $video->update([
                        'title' => $oembedData['title'],
                        'thumbnail_url' => $oembedData['thumbnail_url'],
                        'oembed_html' => $oembedData['html'],
                        'author_name' => $oembedData['author_name'],
                        'oembed_fetched_at' => now(),
                    ]);

                    $count++;
                }
            } catch (\Exception $e) {
                Log::error('Failed to refresh TikTok oEmbed', [
                    'tiktok_url' => $video->tiktok_url,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('TikTok oEmbeds refreshed', ['count' => $count]);

        return $count;
    }

    /**
     * Get visible TikTok videos from DB ordered by sort_order.
     */
    public function getVisibleVideos(): Collection
    {
        return TiktokVideo::where('is_hidden', false)
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();
    }
}
