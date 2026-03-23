<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Tenant extends ModelBase
{
    use LogsActivity;

    protected $table = 'tenants';

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'domain',
        'branding_config',
        'landing_config',
        'whatsapp_config',
        'is_active',
        'subscription_status',
        'trial_ends_at',
        'subscription_ends_at',
        'payment_proof',
    ];

    protected $casts = [
        'branding_config' => 'array',
        'landing_config' => 'array',
        'whatsapp_config' => 'array',
        'is_active' => 'boolean',
        'subscription_status' => \App\Enums\SubscriptionStatus::class,
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];

    public function getWhatsappNumberAttribute(): ?string
    {
        return $this->whatsapp_config['phone'] ?? $this->whatsapp_config['number'] ?? null;
    }

    public function hasValidSubscription(): bool
    {
        if ($this->subscription_status === \App\Enums\SubscriptionStatus::Active) {
            return $this->subscription_ends_at && $this->subscription_ends_at->isFuture();
        }

        if ($this->subscription_status === \App\Enums\SubscriptionStatus::Trial) {
            return $this->trial_ends_at && $this->trial_ends_at->isFuture();
        }

        return false;
    }

    public function isTrial(): bool
    {
        return $this->subscription_status === \App\Enums\SubscriptionStatus::Trial;
    }

    public function isExpired(): bool
    {
        return !$this->hasValidSubscription();
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
        static::saving(function ($tenant) {
            $config = $tenant->whatsapp_config;
            if (is_array($config) && isset($config['phone'])) {
                $clean = preg_replace('/[^0-9]/', '', $config['phone']);
                if (!empty($clean) && !str_starts_with($clean, '57')) {
                    $clean = '57' . $clean;
                }
                $config['phone'] = $clean;
                $tenant->whatsapp_config = $config;
            }
        });
    }
}
