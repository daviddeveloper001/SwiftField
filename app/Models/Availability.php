<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    protected $fillable = [
        'tenant_id',
        'day_of_week',
        'is_open',
        'ranges',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'is_open' => 'boolean',
        'ranges' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
