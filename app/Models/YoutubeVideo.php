<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class YoutubeVideo extends Model
{
    protected $table = 'youtube_videos';

    protected $fillable = [
        'video_id',
        'title',
        'description',
        'thumbnail_url',
        'published_at',
        'duration',
        'view_count',
        'like_count',
        'is_live',
        'is_hidden',
        'fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'fetched_at' => 'datetime',
            'is_live' => 'boolean',
            'is_hidden' => 'boolean',
            'view_count' => 'integer',
            'like_count' => 'integer',
        ];
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_hidden', false);
    }

    public function scopeLive(Builder $query): Builder
    {
        return $query->where('is_live', true);
    }
}
