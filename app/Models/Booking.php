<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\BookingStatus;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Booking extends ModelBase
{
    use LogsActivity;

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
        'status' => BookingStatus::class,
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
