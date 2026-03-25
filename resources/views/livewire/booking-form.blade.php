<div class="max-w-2xl mx-auto p-4 sm:p-6 bg-white rounded-lg shadow-md">
    <!-- Stepper Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between text-xs sm:text-base">
            <div class="flex items-center text-primary">
                <span
                    class="rounded-full h-6 w-6 sm:h-8 sm:w-8 flex flex-shrink-0 items-center justify-center border-2 {{ $step >= 1 ? 'stepper-active font-bold' : 'border-gray-300 text-gray-400' }}">1</span>
                <span
                    class="ml-1 sm:ml-2 whitespace-nowrap {{ $step >= 1 ? 'font-semibold' : 'text-gray-400' }}">Servicio</span>
            </div>
            <div class="flex-1 border-t-2 {{ $step >= 2 ? 'stepper-line-active' : 'border-gray-200' }} mx-2 sm:mx-4">
            </div>
            <div class="flex items-center {{ $step >= 2 ? 'text-primary' : 'text-gray-400' }}">
                <span
                    class="rounded-full h-6 w-6 sm:h-8 sm:w-8 flex flex-shrink-0 items-center justify-center border-2 {{ $step >= 2 ? 'stepper-active font-bold' : 'border-gray-300' }}">2</span>
                <span class="ml-1 sm:ml-2 whitespace-nowrap {{ $step >= 2 ? 'font-semibold' : '' }}">Detalles</span>
            </div>
            <div class="flex-1 border-t-2 {{ $step >= 3 ? 'stepper-line-active' : 'border-gray-200' }} mx-2 sm:mx-4">
            </div>
            <div class="flex items-center {{ $step >= 3 ? 'text-primary' : 'text-gray-400' }}">
                <span
                    class="rounded-full h-6 w-6 sm:h-8 sm:w-8 flex flex-shrink-0 items-center justify-center border-2 {{ $step >= 3 ? 'stepper-active font-bold' : 'border-gray-300' }}">3</span>
                <span class="ml-1 sm:ml-2 whitespace-nowrap {{ $step >= 3 ? 'font-semibold' : '' }}">Contacto</span>
            </div>
        </div>
    </div>

    <!-- Step 1: Select Service -->
    @if ($step === 1)
        <div>
            <h2 class="text-xl font-bold mb-4 text-gray-800">Selecciona el Servicio</h2>
            <div class="space-y-4">
                <x-ui.label for="service_id" value="Servicios Disponibles" />
                <x-ui.select wire:model.live="service_id" id="service_id" :error="$errors->has('service_id')">
                    <option value="">-- Elige un servicio --</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}">{{ $service->name }} -
                            ${{ number_format($service->price, 2) }}</option>
                    @endforeach
                </x-ui.select>
                @error('service_id')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mt-8 flex justify-end">
                <x-ui.button wire:click="nextStep" type="button"
                    class="bg-primary hover:brightness-90 transition-all">Siguiente &rarr;</x-ui.button>
            </div>
        </div>
    @endif

    <!-- Step 2: Dynamic Form -->
    @if ($step === 2)
        <div>
            <h2 class="text-xl font-bold mb-4 text-gray-800">Detalles de {{ $selectedService->name }}</h2>
            <div class="space-y-4">
                @if (empty($fieldDefinitions))
                    <p class="text-gray-500 italic">No hay detalles adicionales requeridos para este servicio.</p>
                @else
                    @foreach ($fieldDefinitions as $field)
                        @php
                            $key = $field['key'] ?? $field['name'];
                            $type = $field['type'] ?? 'text';
                        @endphp
                        <div>
                            <x-ui.label :for="'custom_' . $key" :value="$field['label'] ?? ucfirst($key)" />

                            @if ($type === 'select' && isset($field['options']))
                                <x-ui.select wire:model="custom_values.{{ $key }}" :id="'custom_' . $key"
                                    :error="$errors->has('custom_values.' . $key)">
                                    <option value="">-- Selecciona --</option>
                                    @foreach ($field['options'] as $opt)
                                        <option value="{{ is_array($opt) ? $opt['value'] : $opt }}">
                                            {{ is_array($opt) ? $opt['label'] : $opt }}</option>
                                    @endforeach
                                </x-ui.select>
                            @else
                                <x-ui.input type="{{ $type }}" wire:model="custom_values.{{ $key }}"
                                    :id="'custom_' . $key" :error="$errors->has('custom_values.' . $key)" />
                            @endif

                            @error('custom_values.' . $key)
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="mt-8 flex justify-between">
                <x-ui.button wire:click="previousStep" type="button"
                    class="bg-gray-500 hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-700">&larr;
                    Volver</x-ui.button>
                <x-ui.button wire:click="nextStep" type="button"
                    class="bg-primary hover:brightness-90 transition-all">Siguiente &rarr;</x-ui.button>
            </div>
        </div>
    @endif

    <!-- Step 3: Contact & Location -->
    @if ($step === 3)
        <div x-data="{
            gettingLocation: false,
            locationError: null,
            getLocation() {
                this.gettingLocation = true;
                this.locationError = null;
                if (!navigator.geolocation) {
                    this.locationError = 'Geolocalización no es soportada por tu navegador.';
                    this.gettingLocation = false;
                    return;
                }
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        $wire.set('lat', position.coords.latitude);
                        $wire.set('lng', position.coords.longitude);
                        this.gettingLocation = false;
                    },
                    (error) => {
                        this.locationError = 'No se pudo obtener la ubicación. Por favor acepta los permisos.';
                        this.gettingLocation = false;
                    }
                );
            }
        }">

            <h2 class="text-xl font-bold mb-4 text-gray-800">Casi listos...</h2>

            <div class="space-y-4">
                <!-- Name -->
                <div>
                    <x-ui.label for="customer_name" value="Tu Nombre" />
                    <x-ui.input type="text" wire:model="customer_name" id="customer_name"
                        placeholder="Ej. Juan Pérez" :error="$errors->has('customer_name')" />
                    @error('customer_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <x-ui.label for="customer_phone" value="Número de Celular" />
                    <div class="relative mt-1 flex rounded-md shadow-sm">
                        <span class="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-50 px-3 text-gray-500 sm:text-sm font-semibold">
                            +57
                        </span>
                        <input x-data x-mask="999 999 9999" type="tel" wire:model.live="customer_phone" id="customer_phone"
                            class="flex-1 block w-full rounded-none rounded-r-md focus:border-primary focus:ring-primary sm:text-sm {{ $errors->has('customer_phone') ? 'border-red-500' : 'border-gray-300' }}"
                            placeholder="310 123 4567" />
                    </div>
                    @error('customer_phone')
                        <span class="block mt-1 text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Date & Time (Conditional) -->
                @if(!$selectedService->requires_quote)
                    <div class="space-y-4">
                        <div>
                            <x-ui.label for="selectedDate" value="1. Selecciona el Día" />
                            <x-ui.input type="date" wire:model.live="selectedDate" id="selectedDate" min="{{ date('Y-m-d') }}" :error="$errors->has('selectedDate')" wire:loading.attr="disabled" />
                            @error('selectedDate')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <x-ui.label value="2. Selecciona la Hora" />
                            <div class="mt-2 relative">
                                <!-- Loading Overlay -->
                                <div wire:loading wire:target="selectedDate, service_id" class="absolute inset-0 bg-white/50 z-10 flex items-center justify-center">
                                    <svg class="animate-spin h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>

                                @if(empty($availableSlots))
                                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-md text-yellow-700 text-sm italic">
                                        No hay citas disponibles para este día con la duración seleccionada, intenta con otro.
                                    </div>
                                @else
                                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                        @foreach($availableSlots as $slot)
                                            <button 
                                                type="button"
                                                wire:click="selectTime('{{ $slot }}')"
                                                wire:loading.attr="disabled"
                                                class="py-2 px-1 text-sm font-medium rounded-md border transition-all duration-200 
                                                {{ $selectedTime === $slot 
                                                    ? 'bg-primary text-white border-primary shadow-sm' 
                                                    : 'bg-white text-gray-700 border-gray-300 hover:border-primary hover:text-primary' }}
                                                disabled:opacity-50 disabled:cursor-not-allowed"
                                            >
                                                {{ $slot }}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            @error('selectedTime')
                                <span class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @else
                    <!-- Quote Field -->
                    <div>
                        <x-ui.label for="quote_text" :value="$selectedService->quote_label ?? 'Detalles de la solicitud'" />
                        <textarea wire:model="quote_text" id="quote_text" rows="3"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary {{ $errors->has('quote_text') ? 'border-red-500' : '' }}"
                            placeholder="Escribe aquí los detalles..."></textarea>
                        @error('quote_text')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                <!-- Location block with AlpineJS -->
                <div class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-md">
                    <h3 class="font-medium text-gray-900 mb-2">📍 Tu Ubicación</h3>
                    <p class="text-sm text-gray-600 mb-3">Necesitamos tu ubicación para que el profesional pueda llegar
                        a ti.</p>

                    <button type="button" @click="getLocation"
                        class="inline-flex items-center px-4 py-2 bg-primary hover:brightness-90 text-white text-sm font-medium rounded-md shadow-sm transition">
                        <svg x-show="gettingLocation" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-text="gettingLocation ? 'Obteniendo...' : 'Capturar Mi Ubicación'">Capturar Mi
                            Ubicación</span>
                    </button>

                    <div x-show="locationError" x-text="locationError" class="mt-2 text-sm text-red-600"
                        style="display: none;"></div>

                    @if ($lat && $lng)
                        <div class="mt-3 text-sm text-green-600 font-medium flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            ¡Ubicación registrada exitosamente!
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-8 flex justify-between">
                <x-ui.button wire:click="previousStep" type="button"
                    class="bg-gray-500 hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-700">&larr;
                    Volver</x-ui.button>
                <x-ui.button wire:click="submit" type="button"
                    class="bg-primary hover:brightness-90 transition-all">{{ $selectedService->requires_quote ? 'Solicitar Presupuesto' : 'Completar Reserva' }}</x-ui.button>
            </div>
        </div>
    @endif
</div>
