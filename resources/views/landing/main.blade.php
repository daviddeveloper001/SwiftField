<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tenant->name }} | SwiftField</title>
    
    <!-- Carga segura de fuentes para evitar conflictos con @ -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800&display=swap');
        
        :root {
            --primary-color: {{ $tenant->branding_config['primary_color'] ?? '#3b82f6' }};
            --secondary-color: {{ $tenant->branding_config['secondary_color'] ?? '#1e40af' }};
            --primary-light: {{ ($tenant->branding_config['primary_color'] ?? '#3b82f6') }}15;
        }

        body { font-family: 'Inter', sans-serif; }
    </style>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/css/landing.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-slate-900 antialiased bg-premium-gradient min-h-screen relative">
    <div class="fixed inset-0 bg-grid-pattern opacity-[0.03] pointer-events-none"></div>

    <!-- Header Profesional -->
    <header class="fixed top-4 inset-x-4 z-50">
        <nav class="max-w-7xl mx-auto backdrop-blur-lg bg-white/70 border border-white/20 rounded-2xl px-6 py-3 shadow-xl flex items-center justify-between">
            <div class="flex items-center space-x-3">
                @if($tenant->branding_config['logo_url'])
                    <img src="{{ Storage::url($tenant->branding_config['logo_url']) }}" alt="{{ $tenant->name }}" class="h-10 w-auto rounded-lg object-contain">
                @else
                    <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-white font-black text-xl shadow-lg shadow-primary/20">
                        {{ strtoupper(substr($tenant->name, 0, 1)) }}
                    </div>
                @endif
                <span class="font-extrabold tracking-tight text-slate-900 hidden sm:block">{{ $tenant->name }}</span>
            </div>

            <div class="flex items-center space-x-2">
                <a href="#booking" class="bg-primary text-white px-5 py-2 rounded-xl text-sm font-bold shadow-lg shadow-primary/25 hover:scale-105 transition-all">
                    Agendar Cita
                </a>
            </div>
        </nav>
    </header>

    <div id="landing-engine" class="relative z-10" x-data="{ 
        init() {
            // Respaldo de seguridad: si IntersectionObserver falla, mostramos todo
            setTimeout(() => { document.querySelectorAll('.fade-up').forEach(el => el.classList.add('visible')); }, 100);
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) { entry.target.classList.add('visible'); }
                });
            }, { threshold: 0.05 });
            document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));
        }
    }">
        {!! $presenter->renderSections($config) !!}

        <!-- Zona de Reserva Automática -->
        <section id="booking" class="py-24 relative z-10 fade-up">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-slate-900 sm:text-4xl tracking-tight">Agenda tu Cita</h2>
                    <p class="mt-4 text-slate-600">Completa los detalles a continuación para confirmar tu reserva.</p>
                </div>
                <livewire:booking-form :tenantId="$tenant->id" />
            </div>
        </section>
    </div>

    <footer class="bg-slate-50 border-t border-slate-200 py-12 text-center relative z-10">
        <p class="text-slate-500 text-sm">© {{ date('Y') }} {{ $tenant->name }}. SwiftField Premium.</p>
    </footer>

    @livewireScripts
</body>
</html>
