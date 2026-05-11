<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\User;
use App\Enums\BookingStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SwiftFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 0. Ensure a Super Admin exists
        $superAdmin = User::updateOrCreate(
            ['email' => config('swiftfield.super_admin.email')],
            [
                'name' => config('swiftfield.super_admin.name'),
                'password' => bcrypt(config('swiftfield.super_admin.password')),
                'is_super_admin' => true,
            ]
        );

        // 0.1 Ensure a test user exists for tenant testing
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'is_super_admin' => false,
            ]
        );

        // 1. Tenant: Ambientaplus JA
        $tenant1 = Tenant::updateOrCreate(
            ['slug' => 'ambientaplus'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Ambientaplus JA',
                'is_active' => true,
                'subscription_status' => \App\Enums\SubscriptionStatus::Trial,
                'trial_ends_at' => now()->addDays(7),
            ]
        );

        // 2. Tenant 2: Decoraciones Pro
        $tenant2 = Tenant::updateOrCreate(
            ['slug' => 'decoraciones-pro'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Decoraciones Pro',
                'is_active' => true,
                'subscription_status' => \App\Enums\SubscriptionStatus::Trial,
                'trial_ends_at' => now()->addDays(7),
            ]
        );

        // 5.1 Fumigación (Ambientaplus)
        Service::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'slug' => 'fumigacion'],
            [
                'name' => 'Fumigación Residencial',
                'price' => 80000,
                'is_active' => true,
                'description' => 'Servicio básico de fumigación para hogares y apartamentos.',
                'field_definitions' => [
                    ['name' => 'tipo_plaga', 'label' => '¿Qué plaga detectó?', 'type' => 'text'],
                    ['name' => 'm2', 'label' => 'Metros cuadrados aprox.', 'type' => 'number'],
                ],
            ]
        );
        
    }
}
