<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaFile extends Model
{
    public $timestamps = false;

    protected $table = 'media_files';

    protected $fillable = [
        'original_name',
        'stored_name',
        'path',
        'disk',
        'mime_type',
        'file_size',
        'type',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'uploaded_by');
    }
}
