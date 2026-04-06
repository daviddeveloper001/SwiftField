<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = Schema::getColumnListing('tenants');
echo "Columnas en tabla 'tenants':\n";
print_r($columns);

$settings_count = DB::table('tenant_settings')->count();
echo "\nTotal de registros en 'tenant_settings': {$settings_count}\n";
