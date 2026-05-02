<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvailabilityBlock extends Model
{
    protected $fillable = [
        'tenant_id',
        'start_time',
        'end_time',
        'reason',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function tenant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
