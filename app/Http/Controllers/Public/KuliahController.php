<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\YoutubeVideo;
use Inertia\Inertia;
use Inertia\Response;

class KuliahController extends Controller
{
    public function index(): Response
    {
        $videos = YoutubeVideo::visible()
            ->orderByDesc('published_at')
            ->paginate(12);

        return Inertia::render('Public/Kuliah', [
            'videos' => $videos,
        ]);
    }
}
