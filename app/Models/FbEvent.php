<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FbEvent extends Model
{
    protected $table = 'fb_events';

    protected $fillable = [
        'fb_event_id',
        'name',
        'description',
        'start_time',
        'end_time',
        'place',
        'cover_url',
        'attending_count',
        'is_hidden',
        'fb_url',
        'fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'fetched_at' => 'datetime',
            'is_hidden' => 'boolean',
            'attending_count' => 'integer',
        ];
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_hidden', false);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('start_time', '>=', now());
    }
}
