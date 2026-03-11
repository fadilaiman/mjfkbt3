<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PrayerTime extends Model
{
    protected $table = 'prayer_times';

    protected $fillable = [
        'date',
        'zone_code',
        'subuh',
        'syuruk',
        'zohor',
        'asar',
        'maghrib',
        'isyak',
        'hijri_date',
        'fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'fetched_at' => 'datetime',
        ];
    }

    public function scopeForToday(Builder $query): Builder
    {
        return $query->whereDate('date', now()->toDateString());
    }

    public function scopeForZone(Builder $query, string $zoneCode = 'SGR01'): Builder
    {
        return $query->where('zone_code', $zoneCode);
    }
}
