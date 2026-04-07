<?php

namespace App\Services;

use App\Models\TenantSetting;
use Illuminate\Support\Facades\Log;

class PlatformSettings
{
    protected const KEY = 'platform_payment_methods';

    /**
     * Get platform payment methods from global settings.
     *
     * @param string|null $subKey
     * @return mixed
     */
    public static function get(?string $subKey = null)
    {
        try {
            $setting = TenantSetting::whereNull('tenant_id')
                ->where('key', self::KEY)
                ->first();

            $defaults = config('app.platform_defaults', [
                'nequi_number' => '300 000 0000',
                'bancolombia_account' => '123-456789-00',
                'account_holder' => 'SwiftField Team',
                'activation_message' => '⚠️ Una vez realizado el pago, sube el comprobante abajo. Tu servicio será activado en menos de 2 horas.'
            ]);

            $values = $setting ? ($setting->value ?? []) : $defaults;

            if ($subKey) {
                return $values[$subKey] ?? ($defaults[$subKey] ?? null);
            }

            return $values;
        } catch (\Exception $e) {
            Log::error('Error fetching PlatformSettings: ' . $e->getMessage());
            return config("app.platform_defaults.{$subKey}", 'Información de pago no disponible temporalmente');
        }
    }
}
