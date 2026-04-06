<?php

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenants = DB::table('tenants')->get();

echo "--- DIAGNÓSTICO DE MIGRACIÓN ---\n";
foreach ($tenants as $tenant) {
    echo "Tenant ID: {$tenant->id}\n";
    echo " - Branding Config (RAW): " . ($tenant->branding_config ? 'CON DATOS' : 'VACÍO') . "\n";
    echo " - Landing Config (RAW): " . ($tenant->landing_config ? 'CON DATOS' : 'VACÍO') . "\n";
    echo " - WhatsApp Config (RAW): " . ($tenant->whatsapp_config ? 'CON DATOS' : 'VACÍO') . "\n";
}

$settings = DB::table('tenant_settings')->count();
echo "Total registros en tenant_settings: {$settings}\n";
echo "-------------------------------\n";
