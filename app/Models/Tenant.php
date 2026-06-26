<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Deivy\SaasCore\Traits\HasSaaSManagement;

use App\Traits\HasTenantSettings;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends ModelBase
{
    use LogsActivity, HasTenantSettings, HasSaaSManagement;

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
        'address',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscription_status' => \App\Enums\SubscriptionStatus::class,
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    protected $appends = [
        'location',
    ];

    public function getLocationAttribute(): array
    {
        return [
            'lat' => $this->latitude ? (float) $this->latitude : null,
            'lng' => $this->longitude ? (float) $this->longitude : null,
        ];
    }

    public function setLocationAttribute(?array $location): void
    {
        if (is_array($location)) {
            $this->latitude = isset($location['lat']) ? (float) $location['lat'] : null;
            $this->longitude = isset($location['lng']) ? (float) $location['lng'] : null;
        }
    }
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
    public function getWhatsappConfigAttribute(): array
    {
        // El prompt sugiere que podría estar dentro de branding_config, 
        // pero lo ideal es su propia key 'whatsapp_config'. 
        // Intentamos ambas por seguridad arquitectónica.
        $config = $this->getSetting('whatsapp_config');
        
        if (!$config) {
            $branding = $this->getSetting('branding_config', []);
            $config = $branding['whatsapp_config'] ?? [
                'phone' => '',
                'templates' => [],
            ];
        }

        return $config;
    }

    public function getWhatsappNumberAttribute(): ?string
    {
        $config = $this->whatsapp_config;
        $phone = $config['phone'] ?? $config['number'] ?? null;
        
        if (empty($phone)) return $phone;
        return str_starts_with($phone, '57') ? substr($phone, 2) : $phone;
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
        static::creating(function ($tenant) {
            if (empty($tenant->uuid)) {
                $tenant->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function getDurationAttribute(): ?int
    {
        return $this->getSetting('default_service_duration', null);
    }
}
