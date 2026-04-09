<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Availability;
use App\Models\Tenant;

echo "Initializing Availabilities...\n";

foreach (Tenant::all() as $tenant) {
    echo "Processing Tenant: {$tenant->name} (ID: {$tenant->id})\n";
    
    // Monday to Friday: 09:00 - 18:00
    foreach (range(1, 5) as $day) {
        Availability::updateOrCreate(
            ['tenant_id' => $tenant->id, 'day_of_week' => $day],
            ['start_time' => '09:00', 'end_time' => '18:00', 'is_open' => true]
        );
    }
    
    // Saturday: 09:00 - 13:00
    Availability::updateOrCreate(
        ['tenant_id' => $tenant->id, 'day_of_week' => 6],
        ['start_time' => '09:00', 'end_time' => '13:00', 'is_open' => true]
    );
    
    // Sunday: CLOSED
    Availability::updateOrCreate(
        ['tenant_id' => $tenant->id, 'day_of_week' => 0],
        ['start_time' => '00:00', 'end_time' => '00:00', 'is_open' => false]
    );
}

echo "Done!\n";
