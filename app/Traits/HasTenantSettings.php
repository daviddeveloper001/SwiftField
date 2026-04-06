<?php

namespace App\Traits;

use App\Models\TenantSetting;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasTenantSettings
{
    /**
     * Relationship to settings.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(TenantSetting::class);
    }

    /**
     * Get a setting by key with a fallback value.
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        // Internal caching for current instance to avoid DB hits
        $settings = $this->relations['settings'] ?? $this->settings;

        $setting = $settings->firstWhere('key', $key);

        if ($setting === null) {
            return $this->getSettingFallback($key, $default);
        }

        return $setting->value;
    }

    /**
     * Set or update a setting.
     */
    public function setSetting(string $key, mixed $value): TenantSetting
    {
        return $this->settings()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Fallback values for critical keys.
     */
    protected function getSettingFallback(string $key, mixed $default = null): mixed
    {
        $fallbacks = [
            'booking_slot_size' => 15, // Default 15 minutes grid
            'default_service_duration' => 60, // Default 1 hour
            'buffer_time_between_bookings' => 0, // No buffer by default
        ];

        return $fallbacks[$key] ?? $default;
    }
}
