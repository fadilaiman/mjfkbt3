<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $table = 'donations';

    protected $fillable = [
        'name',
        'description',
        'target_amount',
        'collected_amount',
        'contributor_count',
        'whatsapp_number',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'collected_amount' => 'decimal:2',
            'contributor_count' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
