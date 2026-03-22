<div class="flex min-h-screen bg-slate-50">
    <!-- Left Column: Brand Panel -->
    <div class="hidden lg:flex lg:w-1/2 bg-slate-900 text-white flex-col justify-between p-12 relative overflow-hidden">
        <!-- Abstract Decoration -->
        <div class="absolute top-0 right-0 -tr-translate-y-12 translate-x-1/3 w-96 h-96 bg-indigo-600 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
        <div class="absolute bottom-0 left-0 translate-y-1/3 -translate-x-1/4 w-72 h-72 bg-fuchsia-600 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>

        <div class="relative z-10">
            <div class="text-4xl font-extrabold tracking-tight mb-2">
                SwiftField<span class="text-indigo-500">.</span>
            </div>
            <p class="text-slate-400 text-sm font-semibold tracking-widest uppercase">Ecosistema de Gestión</p>
        </div>

        <div class="space-y-10 relative z-10">
            <h2 class="text-4xl font-bold leading-tight">
                El sistema operativo<br/>para escalar tu negocio.
            </h2>
            
            <ul class="space-y-6 text-slate-300">
                <li class="flex items-start gap-4">
                    <div class="mt-1 bg-white/10 p-2 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-white">Gestiona tu agenda</h4>
                        <p class="text-sm text-slate-400 leading-relaxed">Organiza citas, reservas y a tu equipo sin esfuerzo desde un solo lugar.</p>
                    </div>
                </li>
                <li class="flex items-start gap-4">
                    <div class="mt-1 bg-white/10 p-2 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-white">Automatiza recordatorios</h4>
                        <p class="text-sm text-slate-400 leading-relaxed">Evita inasistencias enviando alertas por correo y WhatsApp a tus clientes.</p>
                    </div>
                </li>
                <li class="flex items-start gap-4">
                    <div class="mt-1 bg-white/10 p-2 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-white">Reportes en tiempo real</h4>
                        <p class="text-sm text-slate-400 leading-relaxed">Métricas claras para tomar decisiones inteligentes y medir tu crecimiento.</p>
                    </div>
                </li>
            </ul>
        </div>

        <div class="text-slate-500 text-sm font-medium relative z-10">
            &copy; {{ date('Y') }} SwiftField. Todos los derechos reservados.
        </div>
    </div>

    <!-- Right Column: Registration Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 xl:p-20 overflow-y-auto">
        <div class="w-full max-w-md">
            
            <!-- Mobile Logo -->
            <div class="lg:hidden text-center mb-10">
                <div class="text-3xl font-extrabold tracking-tight text-slate-900">
                    SwiftField<span class="text-indigo-600">.</span>
                </div>
            </div>

            <h2 class="text-3xl font-bold text-slate-900 mb-2">Crea tu cuenta en SwiftField</h2>
            <p class="text-slate-500 mb-8 font-medium">Empieza tu prueba gratuita de 7 días.</p>

            <form wire:submit.prevent="register" class="space-y-6">
                
                <!-- Business Name -->
                <div>
                    <label for="business_name" class="block text-sm font-bold text-slate-700 mb-1.5">Nombre del Negocio</label>
                    <input type="text" id="business_name" wire:model.live="business_name" placeholder="Ej: Peluquería Estilos"
                        class="w-full p-3.5 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('business_name') border-red-500 ring-red-500 @enderror">
                    @error('business_name') <span class="text-red-500 text-xs mt-1.5 font-semibold block">{{ $message }}</span> @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-bold text-slate-700 mb-1.5">Tu Enlace Personalizado</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-4 text-slate-400 text-sm font-medium pointer-events-none">
                            swiftfield.com/
                        </span>
                        <input type="text" id="slug" wire:model.blur="slug" placeholder="peluqueria-estilos"
                            class="w-full p-3.5 pl-32 bg-slate-50 border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-700 transition-colors @error('slug') border-red-500 ring-red-500 bg-white @enderror">
                    </div>
                    @error('slug') <span class="text-red-500 text-xs mt-1.5 font-semibold block">{{ $message }}</span> @enderror
                    <p class="text-xs text-slate-400 mt-1.5">Este será tu acceso directo para que los clientes reserven.</p>
                </div>

                <hr class="border-slate-200">

                <!-- Owner Name -->
                <div>
                    <label for="owner_name" class="block text-sm font-bold text-slate-700 mb-1.5">Nombre Completo</label>
                    <input type="text" id="owner_name" wire:model.blur="owner_name" placeholder="Juan Pérez"
                        class="w-full p-3.5 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('owner_name') border-red-500 ring-red-500 @enderror">
                    @error('owner_name') <span class="text-red-500 text-xs mt-1.5 font-semibold block">{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-bold text-slate-700 mb-1.5">Correo Electrónico</label>
                    <input type="email" id="email" wire:model.blur="email" placeholder="tucorreo@ejemplo.com"
                        class="w-full p-3.5 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('email') border-red-500 ring-red-500 @enderror">
                    @error('email') <span class="text-red-500 text-xs mt-1.5 font-semibold block">{{ $message }}</span> @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700 mb-1.5">Contraseña</label>
                    <input type="password" id="password" wire:model.blur="password" placeholder="••••••••"
                        class="w-full p-3.5 bg-white border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('password') border-red-500 ring-red-500 @enderror">
                    @error('password') <span class="text-red-500 text-xs mt-1.5 font-semibold block">{{ $message }}</span> @enderror
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-md text-base font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 transition-all transform hover:-translate-y-0.5" wire:loading.class="opacity-75 cursor-not-allowed">
                        <span wire:loading.remove wire:target="register">Crear Negocio</span>
                        <span wire:loading wire:target="register" class="flex items-center gap-2">
                            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Construyendo tu ecosistema...
                        </span>
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center bg-slate-100 rounded-xl p-4 border border-slate-200">
                <p class="text-sm text-slate-600 font-medium">
                    ¿Ya tienes cuenta? 
                    <a href="/admin/login" class="font-bold text-indigo-600 hover:text-indigo-800 transition-colors ml-1">Inicia sesión aquí</a>
                </p>
            </div>
            
        </div>
    </div>
</div>
