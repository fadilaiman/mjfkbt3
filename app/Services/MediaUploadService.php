<?php

namespace App\Services;

use App\Models\MediaFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaUploadService
{
    /**
     * Allowed MIME types mapped by file type category.
     */
    protected array $allowedMimes = [
        'pdf' => [
            'application/pdf',
        ],
        'image' => [
            'image/jpeg',
            'image/png',
            'image/webp',
        ],
    ];

    /**
     * Allowed file extensions mapped by type category.
     */
    protected array $allowedExtensions = [
        'pdf' => ['pdf'],
        'image' => ['jpg', 'jpeg', 'png', 'webp'],
    ];

    /**
     * Upload a file and create a MediaFile record.
     */
    public function upload(UploadedFile $file, string $type): MediaFile
    {
        // Validate type
        if (!in_array($type, ['pdf', 'image'])) {
            throw new \InvalidArgumentException("Invalid upload type: {$type}. Must be 'pdf' or 'image'.");
        }

        // Validate file size
        $maxSizeMb = config('mjfkbt3.upload.max_size_mb', 10);
        $maxSizeBytes = $maxSizeMb * 1024 * 1024;

        if ($file->getSize() > $maxSizeBytes) {
            throw new \InvalidArgumentException("File size exceeds maximum allowed size of {$maxSizeMb}MB.");
        }

        // Validate MIME type
        $mimeType = $file->getMimeType();
        $allowedMimes = $this->allowedMimes[$type] ?? [];

        if (!in_array($mimeType, $allowedMimes)) {
            throw new \InvalidArgumentException(
                "Invalid MIME type '{$mimeType}' for type '{$type}'. Allowed: " . implode(', ', $allowedMimes)
            );
        }

        // Validate extension (double validation)
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = $this->allowedExtensions[$type] ?? [];

        if (!in_array($extension, $allowedExtensions)) {
            throw new \InvalidArgumentException(
                "Invalid file extension '.{$extension}' for type '{$type}'. Allowed: " . implode(', ', $allowedExtensions)
            );
        }

        // Generate UUID-based filename
        $storedName = Str::uuid() . '.' . $extension;
        $directory = $type === 'pdf' ? 'uploads/documents' : 'uploads/images';
        $path = $directory . '/' . $storedName;

        // Store the file
        Storage::disk('public')->putFileAs($directory, $file, $storedName);

        // Create DB record
        $mediaFile = MediaFile::create([
            'original_name' => $file->getClientOriginalName(),
            'stored_name' => $storedName,
            'path' => $path,
            'disk' => 'public',
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
            'type' => $type,
            'uploaded_by' => auth('admin')->id() ?? 0,
        ]);

        Log::info('File uploaded successfully', [
            'media_file_id' => $mediaFile->id,
            'original_name' => $file->getClientOriginalName(),
            'type' => $type,
        ]);

        return $mediaFile;
    }

    /**
     * Delete a media file from storage and database.
     */
    public function delete(MediaFile $mediaFile): bool
    {
        try {
            // Delete file from storage
            if (Storage::disk($mediaFile->disk)->exists($mediaFile->path)) {
                Storage::disk($mediaFile->disk)->delete($mediaFile->path);
            }

            // Delete DB record
            $mediaFile->delete();

            Log::info('File deleted successfully', [
                'media_file_id' => $mediaFile->id,
                'original_name' => $mediaFile->original_name,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete media file', [
                'media_file_id' => $mediaFile->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get uploaded files, optionally filtered by type.
     */
    public function getUploadsByType(?string $type = null): Collection
    {
        $query = MediaFile::query();

        if ($type !== null) {
            $query->where('type', $type);
        }

        return $query->orderByDesc('created_at')->get();
    }
}
