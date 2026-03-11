<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAnnouncementRequest;
use App\Http\Requests\Admin\UpdateAnnouncementRequest;
use App\Models\Announcement;
use App\Services\MediaUploadService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    use LogsAdminAction;

    public function __construct(
        protected MediaUploadService $mediaUploadService,
    ) {}

    public function index(): Response
    {
        $announcements = Announcement::orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->paginate(15);

        return Inertia::render('Admin/Pengumuman/Index', [
            'announcements' => $announcements,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Pengumuman/Create');
    }

    public function store(StoreAnnouncementRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['created_by'] = auth('admin')->id();

        if ($request->hasFile('attachment')) {
            $mediaFile = $this->mediaUploadService->upload(
                $request->file('attachment'),
                auth('admin')->id()
            );
            $data['attachment_path'] = $mediaFile->path;
            $data['attachment_type'] = $mediaFile->type;
        }

        unset($data['attachment']);

        $announcement = Announcement::create($data);

        $this->logAction('created_announcement', 'Announcement', $announcement->id, "Cipta pengumuman: {$announcement->title}");

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berjaya dicipta.');
    }

    public function edit(int $id): Response
    {
        $announcement = Announcement::findOrFail($id);

        return Inertia::render('Admin/Pengumuman/Edit', [
            'announcement' => $announcement,
        ]);
    }

    public function update(UpdateAnnouncementRequest $request, int $id): RedirectResponse
    {
        $announcement = Announcement::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('attachment')) {
            $mediaFile = $this->mediaUploadService->upload(
                $request->file('attachment'),
                auth('admin')->id()
            );
            $data['attachment_path'] = $mediaFile->path;
            $data['attachment_type'] = $mediaFile->type;
        }

        unset($data['attachment']);

        $announcement->update($data);

        $this->logAction('updated_announcement', 'Announcement', $announcement->id, "Kemaskini pengumuman: {$announcement->title}");

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berjaya dikemaskini.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $announcement = Announcement::findOrFail($id);
        $title = $announcement->title;

        $announcement->delete();

        $this->logAction('deleted_announcement', 'Announcement', $id, "Padam pengumuman: {$title}");

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berjaya dipadam.');
    }
}
