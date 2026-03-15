{{-- Template: Elegance — Diseño para servicios de decoración y eventos --}}

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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3">
                    </path>
                </svg>
            </a>
            <a href="https://wa.me/{{ $tenant->whatsapp_number }}" target="_blank"
                class="inline-flex items-center justify-center px-8 py-4 bg-white text-gray-700 font-bold rounded-full border-2 border-gray-200 hover:border-primary hover:text-primary transition-all duration-300">
                <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z" />
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
                                @case('star')
                                    ✨
                                @break

                                @case('map')
                                    🚚
                                @break

                                @case('check')
                                    🎁
                                @break

                                @case('clock')
                                    ⏰
                                @break

                                @case('shield')
                                    💎
                                @break

                                @case('phone')
                                    💬
                                @break

                                @default
                                    ✨
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
