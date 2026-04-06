<?php
use App\Models\Tenant;
use App\Presenters\LandingPresenter;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenant = Tenant::where('name', 'manicura de uñas')->first();

if (!$tenant) {
    echo "Tenant no encontrado.\n";
    exit;
}

echo "=== DIAGNÓSTICO DEL PRESENTER ===\n";
echo "Tenant ID: {$tenant->id}\n";

$presenter = new LandingPresenter($tenant);
$result = $presenter->getConfig();

if ($result->failed()) {
    echo "Error en getConfig: " . $result->error . "\n";
    exit;
}

$config = $result->data;
echo "\n--- Configuración DTO ---\n";
print_r($config);

echo "\n--- Renderizando Secciones ---\n";
$html = $presenter->renderSections($config);
echo "Longitud del HTML generado: " . strlen($html) . " caracteres\n";
echo "Contenido HTML:\n";
echo $html . "\n";

echo "=================================\n";
