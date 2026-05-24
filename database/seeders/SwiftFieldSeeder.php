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
            ['email' => 'admin@swiftfield.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('Prueba123'),
                'is_super_admin' => true,
            ]
        );

        // 0.1 Ensure a tenant for Super Admin exists
        $superAdminTenant = Tenant::updateOrCreate(
            ['slug' => 'swiftfield-admin'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'SwiftField Admin',
                'is_active' => true,
                'subscription_status' => \App\Enums\SubscriptionStatus::Active,
            ]
        );

        // Associate Super Admin with their tenant
        $superAdmin->tenants()->syncWithoutDetaching([$superAdminTenant->id]);

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

        // 1.1 User for Ambientaplus
        $ambientaplusUser = User::updateOrCreate(
            ['email' => 'ambientaplus@swiftfield.com'],
            [
                'name' => 'Ambientaplus Admin',
                'password' => bcrypt('Prueba123'),
                'is_super_admin' => false,
            ]
        );

        // Associate Ambientaplus user with their tenant
        $ambientaplusUser->tenants()->syncWithoutDetaching([$tenant1->id]);

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

        // 6. Seed default availabilities for all tenants
        foreach ([$superAdminTenant, $tenant1, $tenant2] as $tenant) {
            foreach (\App\Enums\DayOfWeek::cases() as $day) {
                \App\Models\Availability::updateOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'day_of_week' => $day->value,
                    ],
                    [
                        'is_open' => $day->isOpenByDefault(),
                        'ranges' => $day->isOpenByDefault() ? [['start_time' => '08:00', 'end_time' => '18:00']] : null,
                    ]
                );
            }
        }
    }
}

