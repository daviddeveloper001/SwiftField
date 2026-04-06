    {{-- resources/views/landing/sections/hero_v2.blade.php --}}
    <section class="grid md:grid-cols-2 items-center min-h-[60vh] bg-gray-50">
        <div class="p-8 md:p-20">
            <span class="text-[var(--primary-color)] font-bold tracking-widest uppercase text-sm">Bienvenido</span>
            <h1 class="text-5xl font-extrabold text-gray-900 mt-4 mb-6">
                {{ $data['title'] }}
            </h1>
            <p class="text-lg text-gray-600 mb-8">{{ $data['subtitle'] }}</p>
        </div>
        <div class="h-full min-h-[300px] bg-cover bg-center" style="background-image: url('{{ $tenant->logo_url }}')">
            {{-- Aquí podríamos usar una imagen de fondo dinámica del JSON --}}
        </div>
    </section>
