<?php

use App\Models\Service;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Activando servicios existentes...\n";

$count = Service::where('is_active', false)->update(['is_active' => true]);

echo "Se activaron {$count} servicios.\n";
