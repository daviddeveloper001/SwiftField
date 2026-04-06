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
            --primary-color: {{ $config->primary_color ?? '#3b82f6' }};
            --secondary-color: {{ $config->secondary_color ?? '#1e40af' }};
            --primary-light: {{ ($config->primary_color ?? '#3b82f6') }}15;
        }

        body { font-family: 'Inter', sans-serif; }
        .bg-grid-pattern { background-image: radial-gradient(circle, #000 1px, transparent 1px); background-size: 30px 30px; }
        .bg-premium-gradient { background: radial-gradient(circle at 0% 0%, var(--primary-light) 0%, transparent 50%), radial-gradient(circle at 100% 100%, var(--primary-light) 0%, transparent 50%); }
        
        /* Animación Fade Up - Modificada para ser visible si JS falla o Alpine tarda */
        .fade-up { opacity: 0; transform: translateY(20px); transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
        .fade-up.visible { opacity: 1; transform: translateY(0); }
        
        /* Fallback: Si no hay JS, mostrar todo inmediatamente */
        noscript .fade-up { opacity: 1; transform: none; }
    </style>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-slate-900 antialiased bg-premium-gradient min-h-screen relative">
    <div class="fixed inset-0 bg-grid-pattern opacity-[0.03] pointer-events-none"></div>

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
