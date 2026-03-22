<?php

namespace App\Support;

class DefaultTenantLayout
{
    public static function get(): string
    {
        return <<<'BLADE'
<div class="min-h-screen bg-slate-50 font-sans">
    <!-- Navbar -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-md">
                    {{ substr($tenant->name, 0, 1) }}
                </div>
                <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $tenant->name }}</h1>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-24">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            <!-- Left Column: Info & Value Prop -->
            <div class="lg:col-span-5 lg:sticky lg:top-32 space-y-8">
                <div>
                    <span class="inline-block py-1.5 px-3 rounded-full bg-indigo-50 text-indigo-600 text-sm font-semibold mb-6 border border-indigo-100 uppercase tracking-widest">
                        Reserva Online
                    </span>
                    <h2 class="text-4xl sm:text-5xl font-extrabold text-slate-900 leading-tight mb-6">
                        {{ $headline ?? 'Agenda tu cita en segundos' }}
                    </h2>
                    <p class="text-lg text-slate-600 leading-relaxed">
                        {{ $description ?? 'Bienvenido a nuestro portal de reservas. Selecciona el servicio que necesitas y elige el horario que mejor se adapte a ti.' }}
                    </p>
                </div>
                
                <div class="pt-6 border-t border-slate-200">
                    <div class="flex items-center gap-4 text-slate-700">
                        <div class="bg-slate-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900">Confirmación Inmediata</h4>
                            <p class="text-sm text-slate-500">Recibe los detalles al instante en tu correo.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Booking Form Component -->
            <div class="lg:col-span-7 bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] ring-1 ring-slate-100 p-6 sm:p-10 relative overflow-hidden">
                <!-- Decorative background elements -->
                <div class="absolute top-0 right-0 -translate-y-12 translate-x-12 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-50 z-0"></div>
                
                <div class="relative z-10">
                    <h3 class="text-2xl font-bold text-slate-900 mb-8 border-b border-slate-100 pb-4">
                        1. Selecciona tu servicio
                    </h3>
                    
                    <!-- Formulario Automático Dynamic Livewire -->
                    <livewire:booking-form :tenantId="$tenant->id" />
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="font-medium text-slate-300">{{ $tenant->name }} &copy; {{ date('Y') }}</p>
            <p class="text-sm">
                Página impulsada por <span class="text-indigo-400 font-semibold tracking-wide">SwiftField</span>
            </p>
        </div>
    </footer>
</div>
BLADE;
    }
}
