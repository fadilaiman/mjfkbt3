<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class YoutubeLiveStatus extends Model
{
    protected $table = 'youtube_live_status';

    protected $fillable = [
        'is_live',
        'video_id',
        'title',
        'concurrent_viewers',
        'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'is_live' => 'boolean',
            'concurrent_viewers' => 'integer',
            'checked_at' => 'datetime',
        ];
    }

    public function scopeLatestCheck(Builder $query): Builder
    {
        return $query->orderByDesc('checked_at');
    }
}
