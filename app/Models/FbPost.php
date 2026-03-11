<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FbPost extends Model
{
    protected $table = 'fb_posts';

    protected $fillable = [
        'fb_post_id',
        'message',
        'story',
        'full_picture',
        'permalink_url',
        'embed_html',
        'post_type',
        'published_at',
        'is_hidden',
        'fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'fetched_at' => 'datetime',
            'is_hidden' => 'boolean',
        ];
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_hidden', false);
    }
}
