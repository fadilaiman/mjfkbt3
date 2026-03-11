<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminLog extends Model
{
    public $timestamps = false;

    protected $table = 'admin_logs';

    protected $fillable = [
        'admin_user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'model_id' => 'integer',
        ];
    }

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class);
    }
}
