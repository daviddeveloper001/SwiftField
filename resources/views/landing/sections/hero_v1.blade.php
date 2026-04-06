{{-- resources/views/landing/sections/hero_v1.blade.php --}}
<section class="bg-white py-20 px-4 text-center">
    <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
        {{ $data['title'] ?? $tenant->name }}
    </h1>
    <p class="text-xl text-gray-600 mb-10 max-w-2xl mx-auto">
        {{ $data['subtitle'] ?? 'Agenda tu servicio en pocos clics.' }}
    </p>
    <a href="#booking"
        class="inline-block bg-[var(--primary-color)] text-white px-8 py-4 rounded-lg font-bold shadow-lg hover:opacity-90 transition">
        Agendar Ahora
    </a>
</section>
