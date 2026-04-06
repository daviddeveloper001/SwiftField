<section id="hero-{{ $order }}" class="relative overflow-hidden pt-32 pb-24 md:pt-48 md:pb-36 fade-up">
    <!-- Círculos de Brillo de Fondo -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-4xl h-full pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-primary opacity-[0.08] blur-[120px] rounded-full"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-secondary opacity-[0.08] blur-[120px] rounded-full"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <div class="inline-flex items-center space-x-2 bg-primary/10 text-primary px-4 py-1.5 rounded-full text-sm font-bold mb-8 tracking-wide uppercase border border-primary/20">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
            </span>
            <span>Servicio Profesional Garantizado</span>
        </div>

        <h1 class="text-5xl md:text-7xl font-extrabold mb-8 tracking-tight text-slate-900">
            {{ $content['title'] ?? 'Nuestros Servicios de Campo' }}
        </h1>
        
        <p class="text-lg md:text-xl text-slate-600 mb-12 max-w-2xl mx-auto leading-relaxed">
            {{ $content['subtitle'] ?? 'Agende su cita de forma rápida y profesional con ' . $tenant->name . '. La solución líder en gestión de servicios.' }}
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4">
            <a href="#services" class="btn-shine bg-primary text-white px-10 py-4 rounded-2xl font-bold shadow-[0_10px_40px_-10px_var(--primary-color)] hover:scale-105 transition-all w-full sm:w-auto">
                Ver Servicios Disponibles
            </a>
            <a href="#about" class="bg-white border border-slate-200 text-slate-700 px-10 py-4 rounded-2xl font-bold hover:bg-slate-50 transition-all w-full sm:w-auto">
                Saber Más
            </a>
        </div>
    </div>
</section>
