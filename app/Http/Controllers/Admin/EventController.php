<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEventRequest;
use App\Http\Requests\Admin\UpdateEventRequest;
use App\Models\Event;
use App\Services\MediaUploadService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    use LogsAdminAction;

    public function __construct(
        protected MediaUploadService $mediaUploadService,
    ) {}

    public function index(): Response
    {
        $events = Event::orderByDesc('start_datetime')
            ->paginate(15);

        return Inertia::render('Admin/Aktiviti/Index', [
            'events' => $events,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Aktiviti/Create');
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['source'] = 'manual';
        $data['created_by'] = auth('admin')->id();

        if ($request->hasFile('cover_image')) {
            $mediaFile = $this->mediaUploadService->upload(
                $request->file('cover_image'),
                auth('admin')->id()
            );
            $data['cover_image_path'] = $mediaFile->path;
        }

        unset($data['cover_image']);

        $event = Event::create($data);

        $this->logAction('created_event', 'Event', $event->id, "Cipta aktiviti: {$event->title}");

        return redirect()->route('admin.aktiviti.index')
            ->with('success', 'Aktiviti berjaya dicipta.');
    }

    public function edit(int $id): Response
    {
        $event = Event::findOrFail($id);

        return Inertia::render('Admin/Aktiviti/Edit', [
            'event' => $event,
        ]);
    }

    public function update(UpdateEventRequest $request, int $id): RedirectResponse
    {
        $event = Event::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            $mediaFile = $this->mediaUploadService->upload(
                $request->file('cover_image'),
                auth('admin')->id()
            );
            $data['cover_image_path'] = $mediaFile->path;
        }

        unset($data['cover_image']);

        $event->update($data);

        $this->logAction('updated_event', 'Event', $event->id, "Kemaskini aktiviti: {$event->title}");

        return redirect()->route('admin.aktiviti.index')
            ->with('success', 'Aktiviti berjaya dikemaskini.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $event = Event::findOrFail($id);
        $title = $event->title;

        $event->delete();

        $this->logAction('deleted_event', 'Event', $id, "Padam aktiviti: {$title}");

        return redirect()->route('admin.aktiviti.index')
            ->with('success', 'Aktiviti berjaya dipadam.');
    }
}
