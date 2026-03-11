<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TiktokVideo extends Model
{
    protected $fillable = [
        'tiktok_url',
        'title',
        'thumbnail_url',
        'oembed_html',
        'author_name',
        'sort_order',
        'is_hidden',
        'oembed_fetched_at',
    ];

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_hidden', false)->orderBy('sort_order');
    }

    protected function casts(): array
    {
        return [
            'is_hidden' => 'boolean',
            'sort_order' => 'integer',
            'oembed_fetched_at' => 'datetime',
        ];
    }
}
