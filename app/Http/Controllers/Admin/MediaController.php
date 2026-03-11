<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UploadMediaRequest;
use App\Models\MediaFile;
use App\Services\MediaUploadService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MediaController extends Controller
{
    use LogsAdminAction;

    public function __construct(
        protected MediaUploadService $mediaUploadService,
    ) {}

    public function index(): Response
    {
        $mediaFiles = MediaFile::orderByDesc('created_at')
            ->paginate(20);

        return Inertia::render('Admin/Fail/Index', [
            'mediaFiles' => $mediaFiles,
        ]);
    }

    public function store(UploadMediaRequest $request): RedirectResponse
    {
        $mediaFile = $this->mediaUploadService->upload(
            $request->file('file'),
            auth('admin')->id()
        );

        $this->logAction('uploaded_media', 'MediaFile', $mediaFile->id, "Muat naik fail: {$mediaFile->original_name}");

        return back()->with('success', 'Fail berjaya dimuat naik.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $mediaFile = MediaFile::findOrFail($id);
        $originalName = $mediaFile->original_name;

        $this->mediaUploadService->delete($mediaFile);

        $this->logAction('deleted_media', 'MediaFile', $id, "Padam fail: {$originalName}");

        return back()->with('success', 'Fail berjaya dipadam.');
    }
}
