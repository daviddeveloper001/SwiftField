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
        'duration_minutes',
        'field_definitions',
        'is_active',
        'description',
        'requires_quote',
        'quote_label',
    ];

    protected $casts = [
        'field_definitions' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'requires_quote' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
