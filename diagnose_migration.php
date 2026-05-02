<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Availability;
use App\Models\Booking;
use App\Models\Tenant;
use Carbon\Carbon;

echo "--- TENANTS ---\n";
foreach (Tenant::all() as $tenant) {
    echo "ID: {$tenant->id}, Name: {$tenant->name}, Slug: {$tenant->slug}\n";
}

echo "\n--- AVAILABILITIES ---\n";
$availabilities = Availability::all();
foreach ($availabilities as $a) {
    echo "Tenant: {$a->tenant_id}, Day: {$a->day_of_week}, Open: " . ($a->is_open ? 'YES' : 'NO') . ", Start: {$a->start_time->format('H:i')}, End: {$a->end_time->format('H:i')}\n";
}

echo "\n--- RECENT BOOKINGS (Last 5) ---\n";
$bookings = Booking::latest()->take(5)->get();
foreach ($bookings as $b) {
    $dayOfWeek = Carbon::parse($b->scheduled_at)->dayOfWeek;
    echo "ID: {$b->id}, Tenant: {$b->tenant_id}, Scheduled: {$b->scheduled_at}, Day: {$dayOfWeek}, Status: {$b->status->value}\n";
}
