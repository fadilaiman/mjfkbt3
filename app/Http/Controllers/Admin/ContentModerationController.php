<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTiktokVideoRequest;
use App\Models\FbPost;
use App\Models\TiktokVideo;
use App\Models\YoutubeVideo;
use App\Services\FacebookService;
use App\Services\TikTokService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContentModerationController extends Controller
{
    use LogsAdminAction;

    public function __construct(
        protected TikTokService $tiktokService,
        protected FacebookService $facebookService,
    ) {}

    public function youtube(): Response
    {
        $videos = YoutubeVideo::orderByDesc('published_at')
            ->paginate(15);

        return Inertia::render('Admin/Kandungan/Youtube', [
            'videos' => $videos,
        ]);
    }

    public function facebook(): Response
    {
        $posts = FbPost::orderByDesc('published_at')->get();

        return Inertia::render('Admin/Kandungan/Facebook', [
            'posts' => $posts,
            'pageUrl' => $this->facebookService->getPageUrl(),
        ]);
    }

    public function storeFacebook(Request $request): RedirectResponse
    {
        $request->validate([
            'facebook_url' => ['required', 'url', 'max:1000'],
        ]);

        $post = $this->facebookService->addPostByUrl($request->facebook_url);

        $this->logAction('created_fb_post', 'FbPost', $post->id, "Tambah post Facebook: {$request->facebook_url}");

        return back()->with('success', 'Post Facebook berjaya ditambah.');
    }

    public function destroyFacebook(int $id): RedirectResponse
    {
        $post = FbPost::findOrFail($id);
        $url = $post->permalink_url;
        $post->delete();

        $this->logAction('deleted_fb_post', 'FbPost', $id, "Padam post Facebook: {$url}");

        return back()->with('success', 'Post Facebook berjaya dipadam.');
    }

    public function tiktok(): Response
    {
        $videos = TiktokVideo::orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Admin/Kandungan/Tiktok', [
            'videos' => $videos,
        ]);
    }

    public function toggle(string $type, int $id): RedirectResponse
    {
        $modelMap = [
            'youtube_videos' => YoutubeVideo::class,
            'fb_posts' => FbPost::class,
            'tiktok_videos' => TiktokVideo::class,
        ];

        if (! isset($modelMap[$type])) {
            return back()->with('error', 'Jenis kandungan tidak sah.');
        }

        $modelClass = $modelMap[$type];
        $item = $modelClass::findOrFail($id);

        $item->update([
            'is_hidden' => ! $item->is_hidden,
        ]);

        $status = $item->is_hidden ? 'disembunyikan' : 'dipaparkan';

        $this->logAction('toggled_content', $type, $id, "Kandungan {$type} #{$id} {$status}");

        return back()->with('success', "Kandungan berjaya {$status}.");
    }

    public function storeTiktok(StoreTiktokVideoRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $oembedData = $this->tiktokService->fetchOEmbed($data['tiktok_url']);

        if ($oembedData) {
            $data['thumbnail_url'] = $oembedData['thumbnail_url'] ?? null;
            $data['oembed_html'] = $oembedData['html'] ?? null;
            $data['author_name'] = $oembedData['author_name'] ?? null;
            $data['title'] = $data['title'] ?? ($oembedData['title'] ?? null);
            $data['oembed_fetched_at'] = now();
        }

        $video = TiktokVideo::create($data);

        $this->logAction('created_tiktok', 'TiktokVideo', $video->id, "Tambah video TikTok: {$data['tiktok_url']}");

        return back()->with('success', 'Video TikTok berjaya ditambah.');
    }

    public function destroyTiktok(int $id): RedirectResponse
    {
        $video = TiktokVideo::findOrFail($id);
        $url = $video->tiktok_url;

        $video->delete();

        $this->logAction('deleted_tiktok', 'TiktokVideo', $id, "Padam video TikTok: {$url}");

        return back()->with('success', 'Video TikTok berjaya dipadam.');
    }
}
