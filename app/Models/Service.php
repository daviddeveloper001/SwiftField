<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends ModelBase
{
    protected $table = 'services';

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'price',
        'field_definitions',
        'is_active',
        'description',
    ];

    protected $casts = [
        'field_definitions' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
