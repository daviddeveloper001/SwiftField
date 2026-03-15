<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends ModelBase
{
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
    ];

    protected $casts = [
        'branding_config' => 'array',
        'landing_config' => 'array',
        'whatsapp_config' => 'array',
        'is_active' => 'boolean',
    ];
    public function getWhatsappNumberAttribute(): ?string
    {
        return $this->whatsapp_config['phone'] ?? $this->whatsapp_config['number'] ?? null;
    }
}
