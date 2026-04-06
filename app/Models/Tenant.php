<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use App\Traits\HasTenantSettings;

class Tenant extends ModelBase
{
    use LogsActivity, HasTenantSettings;

    protected $table = 'tenants';

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'domain',
        'is_active',
        'subscription_status',
        'trial_ends_at',
        'subscription_ends_at',
        'payment_proof',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscription_status' => \App\Enums\SubscriptionStatus::class,
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];

    /**
     * Accessors para mantener compatibilidad con el código que aún usa $tenant->config_name
     */
    public function getWhatsappConfigAttribute(): ?array
    {
        return $this->getSetting('whatsapp_config', []);
    }

    public function getBrandingConfigAttribute(): ?array
    {
        return $this->getSetting('branding_config', []);
    }

    public function getLandingConfigAttribute(): ?array
    {
        return $this->getSetting('landing_config', []);
    }

    public function getWhatsappNumberAttribute(): ?string
    {
        $config = $this->whatsapp_config;
        $phone = $config['phone'] ?? $config['number'] ?? null;
        
        if (empty($phone)) return $phone;
        return str_starts_with($phone, '57') ? substr($phone, 2) : $phone;
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
        // Limpiamos la lógica legacy del booted ya que no hay columnas que sincronizar
        // Una vez que corras la migración destructiva, este modelo estará perfectamente alineado
    }

    public function getDurationAttribute(): ?int
    {
        return $this->getSetting('default_service_duration', null);
    }
}
