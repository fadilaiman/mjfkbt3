<?php

namespace App\Services;

use App\Models\FbPost;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FacebookService
{
    protected string $graphApiVersion = 'v19.0';

    /**
     * Generate the Facebook Page Plugin iframe HTML for embedding the full page feed.
     * No API token required — works for any public Facebook Page.
     */
    public function getPagePluginEmbed(
        ?string $pageUrl = null,
        int $width = 500,
        int $height = 600,
        string $tabs = 'timeline'
    ): string {
        $pageUrl = $pageUrl ?? config('mjfkbt3.facebook.page_url', 'https://www.facebook.com/mjfkbt3');
        $encodedUrl = urlencode($pageUrl);

        return <<<HTML
<iframe
    src="https://www.facebook.com/plugins/page.php?href={$encodedUrl}&tabs={$tabs}&width={$width}&height={$height}&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=false"
    width="{$width}"
    height="{$height}"
    style="border:none;overflow:hidden"
    scrolling="no"
    frameborder="0"
    allowfullscreen="true"
    allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share">
</iframe>
HTML;
    }

    /**
     * Generate a Facebook post embed iframe from a public post URL.
     * No API token required — works for any public post.
     *
     * Supported URL formats:
     * - https://www.facebook.com/mjfkbt3/posts/12345
     * - https://www.facebook.com/permalink.php?story_fbid=...
     * - https://fb.watch/...
     */
    public function generatePostEmbed(string $postUrl, int $width = 500): string
    {
        $encodedUrl = urlencode($postUrl);

        return <<<HTML
<iframe
    src="https://www.facebook.com/plugins/post.php?href={$encodedUrl}&width={$width}&show_text=true"
    width="{$width}"
    height="auto"
    style="border:none;overflow:hidden;min-height:400px"
    scrolling="no"
    frameborder="0"
    allowfullscreen="true"
    allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share">
</iframe>
HTML;
    }

    /**
     * Add a Facebook post by URL (admin-input approach, like TikTok).
     * Stores the URL and auto-generates the embed iframe.
     */
    public function addPostByUrl(string $url): FbPost
    {
        $embedHtml = $this->generatePostEmbed($url);

        // Generate a stable unique ID from the URL
        $fbPostId = 'url_' . md5($url);

        return FbPost::updateOrCreate(
            ['fb_post_id' => $fbPostId],
            [
                'permalink_url' => $url,
                'embed_html' => $embedHtml,
                'post_type' => $this->guessPostType($url),
                'published_at' => now(),
                'fetched_at' => now(),
            ]
        );
    }

    /**
     * Refresh embed HTML for all manually-added posts.
     * Since embed HTML is generated from URL (not fetched), this just regenerates it.
     */
    public function refreshAllEmbeds(): int
    {
        $posts = FbPost::whereNotNull('permalink_url')->get();
        $count = 0;

        foreach ($posts as $post) {
            try {
                $post->update([
                    'embed_html' => $this->generatePostEmbed($post->permalink_url),
                    'fetched_at' => now(),
                ]);
                $count++;
            } catch (\Exception $e) {
                Log::error('FacebookService: Failed to refresh embed', [
                    'post_id' => $post->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $count;
    }

    /**
     * Delete a post from the database.
     */
    public function deletePost(FbPost $post): bool
    {
        return $post->delete();
    }

    /**
     * Guess post type from URL pattern.
     */
    protected function guessPostType(string $url): string
    {
        if (Str::contains($url, ['/videos/', 'fb.watch', '/video/'])) {
            return 'video';
        }
        if (Str::contains($url, ['/photos/', '/photo/'])) {
            return 'photo';
        }
        return 'status';
    }

    // -----------------------------------------------------------------------
    // Graph API (optional — requires FACEBOOK_APP_ID + FACEBOOK_APP_SECRET)
    // No page owner login needed. Any Facebook Developer App works.
    // -----------------------------------------------------------------------

    /**
     * True when Graph API credentials are configured in .env.
     */
    public function hasGraphApiCredentials(): bool
    {
        return filled(config('mjfkbt3.facebook.app_id'))
            && filled(config('mjfkbt3.facebook.app_secret'));
    }

    /**
     * Build an App Access Token from App ID + Secret.
     * This token never expires and does NOT require the page owner's login.
     * It can read any public Facebook Page's posts.
     */
    protected function appAccessToken(): string
    {
        $id     = config('mjfkbt3.facebook.app_id');
        $secret = config('mjfkbt3.facebook.app_secret');

        return "{$id}|{$secret}";
    }

    /**
     * Fetch new public posts from the Facebook Page via Graph API and save them.
     * Returns ['new' => int, 'found' => int] or ['new' => 0, 'error' => string].
     *
     * Requires FACEBOOK_APP_ID + FACEBOOK_APP_SECRET in .env.
     * No page owner login needed — App Token can read any public page.
     */
    public function syncViaGraphApi(int $limit = 25): array
    {
        $slug  = config('mjfkbt3.facebook.page_slug', 'mjfkbt3');
        $token = $this->appAccessToken();
        $ver   = $this->graphApiVersion;

        try {
            $response = Http::timeout(30)->get(
                "https://graph.facebook.com/{$ver}/{$slug}/posts",
                [
                    'fields'       => 'id,message,story,full_picture,permalink_url,created_time,attachments{media_type}',
                    'limit'        => $limit,
                    'access_token' => $token,
                ]
            );

            if (! $response->successful()) {
                $body = $response->json();
                $msg  = $body['error']['message'] ?? ('HTTP ' . $response->status());
                Log::warning('FacebookService: Graph API error', ['msg' => $msg]);

                return ['new' => 0, 'found' => 0, 'error' => $msg];
            }

            $data  = $response->json('data', []);
            $found = count($data);
            $saved = 0;

            foreach ($data as $post) {
                $postId = $post['id'] ?? null;
                if (! $postId) {
                    continue;
                }

                // Skip if already stored
                if (FbPost::where('fb_post_id', $postId)->exists()) {
                    continue;
                }

                // Detect type from attachments
                $mediaType = $post['attachments']['data'][0]['media_type'] ?? 'status';
                $type = match ($mediaType) {
                    'video'   => 'video',
                    'photo'   => 'photo',
                    'link'    => 'link',
                    default   => 'status',
                };

                // Skip videos — handled separately
                if ($type === 'video') {
                    continue;
                }

                $permalink = $post['permalink_url'] ?? null;
                if (! $permalink) {
                    // Build permalink from post ID: {page-id}_{post-id}
                    $parts     = explode('_', $postId, 2);
                    $postNum   = $parts[1] ?? $postId;
                    $permalink = "https://www.facebook.com/{$slug}/posts/{$postNum}";
                }

                try {
                    FbPost::create([
                        'fb_post_id'    => $postId,
                        'message'       => Str::limit($post['message'] ?? $post['story'] ?? null, 500),
                        'story'         => $post['story'] ?? null,
                        'full_picture'  => $post['full_picture'] ?? null,
                        'permalink_url' => $permalink,
                        'embed_html'    => $this->generatePostEmbed($permalink),
                        'post_type'     => $type,
                        'published_at'  => Carbon::parse($post['created_time']),
                        'fetched_at'    => now(),
                        'is_hidden'     => false,
                    ]);
                    $saved++;
                } catch (\Exception $e) {
                    Log::error('FacebookService: Failed to save Graph API post', [
                        'post_id' => $postId,
                        'error'   => $e->getMessage(),
                    ]);
                }
            }

            Log::info('FacebookService: syncViaGraphApi complete', [
                'found' => $found,
                'new'   => $saved,
            ]);

            return ['new' => $saved, 'found' => $found];

        } catch (\Exception $e) {
            Log::error('FacebookService: syncViaGraphApi exception', ['error' => $e->getMessage()]);

            return ['new' => 0, 'found' => 0, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get the Facebook page URL from config.
     */
    public function getPageUrl(): string
    {
        return config('mjfkbt3.facebook.page_url', 'https://www.facebook.com/mjfkbt3');
    }

    /**
     * Get latest visible posts from DB.
     */
    public function getLatestPosts(int $limit = 6): Collection
    {
        return FbPost::where('is_hidden', false)
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }
}
