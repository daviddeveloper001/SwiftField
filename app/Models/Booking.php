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
        'completed_at',
        'lat',
        'lng',
        'location',
        'custom_values',
        'internal_notes',
        'is_auto_confirmed',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'lat' => 'float',
        'lng' => 'float',
        'custom_values' => 'array',
        'status' => BookingStatus::class,
        'is_auto_confirmed' => 'boolean',
    ];

    public function getLocationAttribute(): array
    {
        return [
            'lat' => $this->lat ? (float) $this->lat : null,
            'lng' => $this->lng ? (float) $this->lng : null,
        ];
    }

    public function setLocationAttribute(?array $location): void
    {
        if (is_array($location)) {
            $this->lat = isset($location['lat']) ? (float) $location['lat'] : null;
            $this->lng = isset($location['lng']) ? (float) $location['lng'] : null;
        }
    }

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

    public function getCustomerPhoneAttribute(): ?string
    {
        return $this->customer?->phone;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function booted()
    {
        static::creating(function ($booking) {
            if (empty($booking->uuid)) {
                $booking->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });

        static::updating(function ($booking) {
            if ($booking->isDirty('status')) {
                if ($booking->status === BookingStatus::Completed) {
                    $booking->completed_at = now();
                } elseif ($booking->getOriginal('status') === BookingStatus::Completed) {
                    $booking->completed_at = null;
                }
            }
        });
    }
}

