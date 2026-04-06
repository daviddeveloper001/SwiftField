<section id="services-{{ $order }}" class="py-24 relative z-10 fade-up">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-slate-900 sm:text-4xl tracking-tight">Nuestros Servicios</h2>
            <p class="mt-4 text-slate-600 max-w-2xl mx-auto">Seleccione el servicio que mejor se adapte a sus necesidades y agende su cita en segundos.</p>
            <div class="mt-4 h-1 w-12 bg-primary rounded-full mx-auto"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($services as $service)
                <div class="backdrop-blur-md bg-white/60 border border-white/20 rounded-[2.5rem] p-8 shadow-[0_20px_50px_rgba(0,0,0,0.05)] hover:shadow-[0_20px_50px_rgba(0,0,0,0.1)] transition-all hover:-translate-y-2 group flex flex-col h-full">
                    <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center text-primary mb-6 group-hover:bg-primary group-hover:text-white transition-all duration-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>

                    <h3 class="text-2xl font-bold text-slate-900 mb-3 tracking-tight">{{ $service->name }}</h3>
                    <p class="text-slate-600 leading-relaxed mb-8 flex-grow line-clamp-3">{{ $service->description }}</p>

                    <div class="flex items-center justify-between mt-auto pt-6 border-t border-slate-100">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Inversión</span>
                            <span class="text-3xl font-extrabold text-primary">${{ number_format($service->price, 0, ',', '.') }}</span>
                        </div>
                        <button 
                           type="button"
                           onclick="Livewire.dispatch('service-selected', { id: {{ $service->id }} })"
                           class="bg-slate-900 text-white px-6 py-3 rounded-xl font-bold hover:bg-primary transition-all shadow-lg hover:shadow-primary/30 cursor-pointer active:scale-95">
                            Reservar
                        </button>
                        </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center">
                    <div class="bg-primary/10 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 text-primary">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900">No hay servicios disponibles</h3>
                    <p class="text-slate-500 mt-2">Pronto estaremos publicando nuevos servicios para usted.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
