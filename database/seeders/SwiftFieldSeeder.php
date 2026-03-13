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
        // 0. Ensure a test user exists
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        // 1. Tenant: Ambientaplus JA
        $tenant1 = Tenant::updateOrCreate(
            ['slug' => 'ambientaplus'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Ambientaplus JA',
                'branding_config' => [
                    'primary_color' => '#10b981', // Green
                ],
                'landing_config' => [
                    'headline' => 'Protege tu hogar con fumigación profesional',
                    'subheadline' => 'Bienvenido a Ambientaplus JA',
                    'description' => 'Más de 10 años eliminando plagas y cuidando tus espacios. Servicio garantizado a domicilio en toda la ciudad.',
                    'features' => [
                        ['icon' => 'clock', 'title' => 'Respuesta en 24h', 'description' => 'Agendamos tu servicio en menos de un día hábil.'],
                        ['icon' => 'shield', 'title' => 'Garantía Total', 'description' => 'Si la plaga regresa, nosotros también. Sin costo adicional.'],
                        ['icon' => 'phone', 'title' => 'Soporte por WhatsApp', 'description' => 'Atención directa y personalizada por WhatsApp.'],
                    ],
                ],
                'is_active' => true,
            ]
        );

        // 2. Servicios para Ambientaplus
        $service1 = Service::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'slug' => 'fumigacion'],
            [
                'name' => 'Fumigación',
                'price' => 150000,
                'field_definitions' => [
                    [
                        'name' => 'tipo_plaga',
                        'label' => 'Tipo de Plaga',
                        'type' => 'select',
                        'options' => ['Cucarachas', 'Roedores', 'Hormigas', 'Termitas'],
                        'required' => true,
                    ],
                    [
                        'name' => 'm2',
                        'label' => 'M2 del Área',
                        'type' => 'number',
                        'required' => true,
                    ],
                ],
            ]
        );

        $service2 = Service::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'slug' => 'lavado-muebles'],
            [
                'name' => 'Lavado de Muebles',
                'price' => 80000,
                'field_definitions' => [
                    [
                        'name' => 'cantidad_puestos',
                        'label' => 'Cantidad de puestos',
                        'type' => 'number',
                        'required' => true,
                    ],
                ],
            ]
        );

        $service3 = Service::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'slug' => 'mantenimiento-jardin'],
            [
                'name' => 'Mantenimiento de Jardín',
                'price' => 120000,
                'field_definitions' => [],
            ]
        );

        // 3. Clientes para Ambientaplus
        $clientes = [
            ['name' => 'Juan Pérez', 'phone' => '3101234567', 'email' => 'juan@example.com'],
            ['name' => 'María Rodríguez', 'phone' => '3119876543', 'email' => 'maria@example.com'],
            ['name' => 'Carlos López', 'phone' => '3004561234', 'email' => 'carlos@example.com'],
            ['name' => 'Ana Martínez', 'phone' => '3207894561', 'email' => 'ana@example.com'],
            ['name' => 'Luis Gómez', 'phone' => '3151239876', 'email' => 'luis@example.com'],
        ];

        $customerModels = [];
        foreach ($clientes as $cliente) {
            $customerModels[] = Customer::updateOrCreate(
                ['tenant_id' => $tenant1->id, 'phone' => $cliente['phone']],
                [
                    'name' => $cliente['name'],
                    'email' => $cliente['email'],
                ]
            );
        }

        // 4. Bookings para Ambientaplus (10 registros)
        $statuses = BookingStatus::cases();
        $services = [$service1, $service2, $service3];

        for ($i = 0; $i < 10; $i++) {
            $service = $services[array_rand($services)];
            $customer = $customerModels[array_rand($customerModels)];
            
            // Unas para hoy, otras futuras
            $date = $i < 3 ? now() : now()->addDays(rand(1, 30));
            
            Booking::create([
                'uuid' => (string) Str::uuid(),
                'tenant_id' => $tenant1->id,
                'service_id' => $service->id,
                'customer_id' => $customer->id,
                'status' => $statuses[array_rand($statuses)],
                'scheduled_at' => $date->setHour(rand(8, 17))->setMinute(0)->setSecond(0),
                'custom_values' => $this->generateCustomValues($service),
                'internal_notes' => 'Nota de prueba ' . ($i + 1),
            ]);
        }

        // 5. Tenant 2: Decoraciones Pro
        $tenant2 = Tenant::updateOrCreate(
            ['slug' => 'decoraciones-pro'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Decoraciones Pro',
                'branding_config' => [
                    'primary_color' => '#ec4899', // Pink
                ],
                'landing_config' => [
                    'headline' => 'Haz de tu evento algo inolvidable',
                    'subheadline' => 'Bienvenido a Decoraciones Pro',
                    'description' => 'Transformamos espacios con decoraciones únicas para bodas, cumpleaños, baby showers y eventos corporativos.',
                    'features' => [
                        ['icon' => 'star', 'title' => 'Diseños Exclusivos', 'description' => 'Cada evento es único. Creamos diseños personalizados para ti.'],
                        ['icon' => 'map', 'title' => 'Servicio a Domicilio', 'description' => 'Montamos y desmontamos en la ubicación de tu evento.'],
                        ['icon' => 'check', 'title' => 'Todo Incluido', 'description' => 'Globos, flores, manteles y más. Un solo proveedor para todo.'],
                    ],
                ],
                'is_active' => true,
            ]
        );

        Service::updateOrCreate(
            ['tenant_id' => $tenant2->id, 'slug' => 'arcos-globos'],
            [
                'name' => 'Arcos de Globos',
                'price' => 250000,
                'field_definitions' => [
                    [
                        'name' => 'color_globos',
                        'label' => 'Color de Globos',
                        'type' => 'text',
                    ],
                ],
            ]
        );

        // 6. Vincular usuario a tenants
        $user->tenants()->syncWithoutDetaching([$tenant1->id, $tenant2->id]);
    }

    private function generateCustomValues(Service $service): array
    {
        $values = [];
        if ($service->slug === 'fumigacion') {
            $values['tipo_plaga'] = 'Cucarachas';
            $values['m2'] = rand(50, 200);
        } elseif ($service->slug === 'lavado-muebles') {
            $values['cantidad_puestos'] = rand(2, 6);
        }
        return $values;
    }
}
