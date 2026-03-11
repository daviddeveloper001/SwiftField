<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends ModelBase
{
    protected $table = 'bookings';

    protected $fillable = [
        'uuid',
        'tenant_id',
        'service_id',
        'customer_id',
        'status',
        'scheduled_at',
        'lat',
        'lng',
        'custom_values',
        'internal_notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'lat' => 'float',
        'lng' => 'float',
        'custom_values' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
