{{-- Template: Nature — Diseño para servicios de fumigación y limpieza --}}

<div class="nature-hero">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span
                    class="inline-block px-4 py-1 rounded-full bg-white/20 text-white text-sm font-medium mb-6 backdrop-blur-sm">
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>
            <div class="hidden lg:flex justify-center">
                <div class="nature-hero-illustration">
                    <svg viewBox="0 0 200 200" class="w-64 h-64 text-white/20">
                        <circle cx="100" cy="100" r="90" fill="currentColor" />
                        <text x="100" y="90" text-anchor="middle" fill="white" font-size="48">🛡️</text>
                        <text x="100" y="130" text-anchor="middle" fill="white" font-size="14"
                            font-weight="bold">Protección Total</text>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    <div class="nature-hero-wave">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path
                d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z"
                fill="white" />
        </svg>
    </div>
</div>

@if (!empty($features))
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 text-center mb-4">¿Por qué elegirnos?</h2>
            <p class="text-gray-500 text-center mb-12 max-w-2xl mx-auto">Nuestro compromiso es ofrecerte el mejor
                servicio con garantía total.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach ($features as $index => $feature)
                    <div class="nature-feature-card group">
                        <div class="nature-feature-icon">
                            <span class="text-2xl">
                                @switch($feature['icon'] ?? 'star')
                                    @case('clock')
                                        ⏱️
                                    @break

                                    @case('shield')
                                        🛡️
                                    @break

                                    @case('phone')
                                        📱
                                    @break

                                    @case('check')
                                        ✅
                                    @break

                                    @case('map')
                                        📍
                                    @break

                                    @default
                                        ⭐
                                @endswitch
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-primary transition-colors">
                            {{ $feature['title'] }}</h3>
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
