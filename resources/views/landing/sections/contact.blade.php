<section id="contact-{{ $order }}" class="py-24 relative z-10 fade-up">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="backdrop-blur-md bg-white/60 border border-white/20 rounded-[2.5rem] p-12 shadow-2xl text-center">
            <h2 class="text-3xl font-bold mb-4">{{ $content['title'] ?? '¿Listo para agendar?' }}</h2>
            <p class="text-slate-600 mb-8 max-w-xl mx-auto">Contáctenos directamente o use nuestro sistema de reservas online para asegurar su espacio hoy mismo.</p>
            <div class="flex flex-col md:flex-row justify-center items-center space-y-4 md:space-y-0 md:space-x-6">
                <a href="https://wa.me/{{ $tenant->whatsapp_number }}" class="bg-[#25D366] text-white px-8 py-4 rounded-2xl font-bold flex items-center shadow-lg hover:scale-105 transition-all">
                    WhatsApp Directo
                </a>
                <a href="#booking" class="bg-primary text-white px-8 py-4 rounded-2xl font-bold shadow-lg hover:scale-105 transition-all">
                    Agendar Ahora
                </a>
            </div>
        </div>
    </div>
</section>
