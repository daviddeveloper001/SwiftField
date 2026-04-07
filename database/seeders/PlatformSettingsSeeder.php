<?php

namespace Database\Seeders;

use App\Models\TenantSetting;
use Illuminate\Database\Seeder;

class PlatformSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TenantSetting::updateOrCreate(
            ['tenant_id' => null, 'key' => 'platform_payment_methods'],
            [
                'value' => [
                    'nequi_number' => '300 000 0000',
                    'bancolombia_account' => 'Ahorros 123-456789-00',
                    'account_holder' => 'SwiftField Team',
                    'activation_message' => '⚠️ Una vez realizado el pago, sube el comprobante abajo. Tu servicio será activado en menos de 2 horas.'
                ]
            ]
        );
    }
}
