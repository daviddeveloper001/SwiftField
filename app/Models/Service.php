<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\BelongsToTenant;

class Service extends ModelBase
{
    use BelongsToTenant;

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
        'delivery_mode',
        'shipping_fee',
        'auto_confirm',
    ];

    protected $casts = [
        'field_definitions' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'requires_quote' => 'boolean',
        'shipping_fee' => 'decimal:2',
        'auto_confirm' => 'boolean',
    ];
}
