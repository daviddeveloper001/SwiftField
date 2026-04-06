<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use App\Traits\HasTenantSettings;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends ModelBase
{
    use LogsActivity, HasTenantSettings;

    protected $table = 'tenants';

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

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
 * Accessor para Branding Centralizado (Colores, Logo)
 */
public function getBrandingConfigAttribute(): array
{
    return $this->getSetting('branding_config', [
        'primary_color' => '#3b82f6',
        'secondary_color' => '#1e40af',
        'logo_url' => null,
    ]);
}

public function getLandingConfigAttribute(): array
{
    return $this->getSetting('landing_config', [
        'theme_id' => 'default',
        'sections' => [
            ['type' => 'hero', 'order' => 1, 'content' => ['title' => 'Bienvenido a ' . $this->name]],
            ['type' => 'services', 'order' => 2, 'content' => []],
        ],
    ]);
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
