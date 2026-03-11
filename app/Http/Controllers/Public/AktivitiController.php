<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\FbEvent;
use Inertia\Inertia;
use Inertia\Response;

class AktivitiController extends Controller
{
    public function index(): Response
    {
        $events = Event::published()
            ->orderByDesc('start_datetime')
            ->paginate(12);

        $fbEvents = FbEvent::visible()
            ->upcoming()
            ->orderBy('start_time')
            ->get();

        return Inertia::render('Public/Aktiviti', [
            'events' => $events,
            'fbEvents' => $fbEvents,
        ]);
    }
}
