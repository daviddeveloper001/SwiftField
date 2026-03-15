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
                'landing_config' => [
                    'template' => 'nature',
                    'headline' => 'Protege tu hogar con fumigación profesional',
                    'subheadline' => 'Bienvenido a Ambientaplus JA',
                    'description' => 'Más de 10 años eliminando plagas y cuidando tus espacios. Servicio garantizado a domicilio en toda la ciudad.',
                    'html_template' => <<<'BLADE'
                        <div class="nature-hero">
                            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                                    <div>
                                        <span class="inline-block px-4 py-1 rounded-full bg-white/20 text-white text-sm font-medium mb-6 backdrop-blur-sm">
                                            🌿 {{ $subheadline }}
                                        </span>
                                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6">
                                            {{ $headline }}
                                        </h1>
                                        <p class="text-lg text-white/80 mb-8 max-w-lg">
                                            {{ $description }}
                                        </p>
                                        <a href="#booking-section"
                                            class="inline-flex items-center px-8 py-4 bg-white text-primary font-bold rounded-full shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300">
                                            Agendar Ahora
                                            <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="hidden lg:flex justify-center">
                                        <div class="nature-hero-illustration">
                                            <svg viewBox="0 0 200 200" class="w-64 h-64 text-white/20">
                                                <circle cx="100" cy="100" r="90" fill="currentColor"/>
                                                <text x="100" y="90" text-anchor="middle" fill="white" font-size="48">🛡️</text>
                                                <text x="100" y="130" text-anchor="middle" fill="white" font-size="14" font-weight="bold">Protección Total</text>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="nature-hero-wave">
                                <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                                    <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
                                </svg>
                            </div>
                        </div>

                        @if (!empty($features))
                            <section class="py-16 bg-white">
                                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                                    <h2 class="text-3xl font-bold text-gray-900 text-center mb-4">¿Por qué elegirnos?</h2>
                                    <p class="text-gray-500 text-center mb-12 max-w-2xl mx-auto">Nuestro compromiso es ofrecerte el mejor servicio con garantía total.</p>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                        @foreach ($features as $index => $feature)
                                            <div class="nature-feature-card group">
                                                <div class="nature-feature-icon">
                                                    <span class="text-2xl">
                                                        @switch($feature['icon'] ?? 'star')
                                                            @case('clock') ⏱️ @break
                                                            @case('shield') 🛡️ @break
                                                            @case('phone') 📱 @break
                                                            @case('check') ✅ @break
                                                            @case('map') 📍 @break
                                                            @default ⭐
                                                        @endswitch
                                                    </span>
                                                </div>
                                                <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-primary transition-colors">{{ $feature['title'] }}</h3>
                                                <p class="text-gray-500 text-sm leading-relaxed">{{ $feature['description'] }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </section>
                        @endif

                        <section id="booking-section" class="py-16 bg-gray-50">
                            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                                <div class="text-center mb-10">
                                    <h2 class="text-3xl font-bold text-gray-900">Agenda tu servicio</h2>
                                    <p class="text-gray-500 mt-2">Completa el formulario y te contactaremos por WhatsApp</p>
                                </div>
                                <livewire:booking-form :tenantId="$tenant->id" />
                            </div>
                        </section>
                        BLADE,
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
        // ... (rest of ambientaplus services)
        // ... (clientes, bookings)

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
                    'template' => 'elegance',
                    'headline' => 'Haz de tu evento algo inolvidable',
                    'subheadline' => 'Bienvenido a Decoraciones Pro',
                    'description' => 'Transformamos espacios con decoraciones únicas para bodas, cumpleaños, baby showers y eventos corporativos.',
                    'html_template' => <<<'BLADE'
                        <div class="elegance-hero">
                            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28 text-center">
                                <div class="elegance-hero-badge">
                                    ✨ {{ $subheadline }}
                                </div>
                                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight mb-6">
                                    {!! str_replace(' ', ' <span class="text-primary">', $headline) . '</span>' !!}
                                </h1>
                                <p class="text-lg text-gray-500 mb-10 max-w-2xl mx-auto">
                                    {{ $description }}
                                </p>
                                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                    <a href="#booking-section"
                                        class="inline-flex items-center justify-center px-8 py-4 bg-primary text-white font-bold rounded-full shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300">
                                        Reservar mi Evento
                                        <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                        </svg>
                                    </a>
                                    <a href="https://wa.me/{{ $tenant->whatsapp_number }}" target="_blank"
                                        class="inline-flex items-center justify-center px-8 py-4 bg-white text-gray-700 font-bold rounded-full border-2 border-gray-200 hover:border-primary hover:text-primary transition-all duration-300">
                                        <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                                        </svg>
                                        WhatsApp
                                    </a>
                                </div>
                            </div>
                        </div>

                        @if (!empty($features))
                            <section class="py-20 bg-white">
                                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-0">
                                        @foreach ($features as $index => $feature)
                                            <div class="elegance-feature-card {{ $index === 1 ? 'elegance-feature-card-highlighted' : '' }}">
                                                <div class="elegance-feature-number">0{{ $index + 1 }}</div>
                                                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $feature['title'] }}</h3>
                                                <p class="text-gray-500 leading-relaxed">{{ $feature['description'] }}</p>
                                                <div class="elegance-feature-icon">
                                                    @switch($feature['icon'] ?? 'star')
                                                        @case('star') ✨ @break
                                                        @case('map') 🚚 @break
                                                        @case('check') 🎁 @break
                                                        @case('clock') ⏰ @break
                                                        @case('shield') 💎 @break
                                                        @case('phone') 💬 @break
                                                        @default ✨
                                                    @endswitch
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </section>
                        @endif

                        <section class="py-16 elegance-cta-section">
                            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                                <h2 class="text-3xl font-bold text-white mb-4">¿Lista para tu evento soñado?</h2>
                                <p class="text-white/80 mb-8">Cuéntanos los detalles y crearemos algo mágico para ti</p>
                            </div>
                        </section>

                        <section id="booking-section" class="py-16 bg-gray-50">
                            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                                <div class="text-center mb-10">
                                    <h2 class="text-3xl font-bold text-gray-900">Reserva tu servicio</h2>
                                    <p class="text-gray-500 mt-2">Completa los detalles y diseñaremos algo especial para ti</p>
                                </div>
                                <livewire:booking-form :tenantId="$tenant->id" />
                            </div>
                        </section>
                        BLADE,
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
