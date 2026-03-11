<?php

use Livewire\Volt\Component;
use App\Models\Service;
use App\Models\Tenant;
use App\Services\BookingService;
use App\DTOs\BookingDTO;
use Illuminate\Support\Collection;

new class extends Component {
    public int $step = 1;
    public int $tenantId;
    public Collection $services;

    // Form Data
    public ?int $service_id = null;
    public string $customer_name = '';
    public string $customer_phone = '';
    public ?float $lat = null;
    public ?float $lng = null;
    public array $custom_values = [];

    // Computed / Auxiliary
    public ?Service $selectedService = null;
    public array $fieldDefinitions = [];

    public function mount(int $tenantId)
    {
        $this->tenantId = $tenantId;
        $this->services = Service::where('tenant_id', $tenantId)->where('is_active', true)->get();
    }

    public function updatedServiceId($value)
    {
        if ($value) {
            $this->selectedService = $this->services->firstWhere('id', $value);
            // Decode field definitions if present, fallback to empty array
            $this->fieldDefinitions = is_string($this->selectedService->field_definitions) ? json_decode($this->selectedService->field_definitions, true) ?? [] : $this->selectedService->field_definitions ?? [];

            // Re-initialize custom values if service changes
            $this->custom_values = [];
            foreach ($this->fieldDefinitions as $field) {
                $this->custom_values[$field['key'] ?? $field['name']] = ''; // default empty
            }
        } else {
            $this->selectedService = null;
            $this->fieldDefinitions = [];
            $this->custom_values = [];
        }
    }

    public function nextStep()
    {
        if ($this->step === 1) {
            $this->validate(['service_id' => 'required|exists:services,id']);
        } elseif ($this->step === 2) {
            // Validate dynamically generated fields
            $rules = [];
            $messages = [];
            foreach ($this->fieldDefinitions as $field) {
                $key = $field['key'] ?? $field['name'];
                if (isset($field['required']) && $field['required']) {
                    $rules["custom_values.{$key}"] = 'required';
                    $messages["custom_values.{$key}.required"] = "El campo {$field['label']} es requerido.";
                }
            }
            if (!empty($rules)) {
                $this->validate($rules, $messages);
            }
        }

        $this->step++;
    }

    public function previousStep()
    {
        $this->step--;
    }

    public function submit(BookingService $bookingService)
    {
        $this->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
        ]);

        $dto = BookingDTO::fromArray([
            'tenant_id' => $this->tenantId,
            'service_id' => $this->service_id,
            'customer_data' => [
                'name' => $this->customer_name,
                'phone' => $this->customer_phone,
            ],
            'scheduled_at' => now(), // Assume 'now' if not strictly required in the form yet
            'lat' => $this->lat,
            'lng' => $this->lng,
            'custom_values' => $this->custom_values,
        ]);

        $booking = $bookingService->createBooking($dto);

        // Redirect to WhatsApp
        $tenant = Tenant::find($this->tenantId);
        $phone = $tenant->whatsapp_number ?? '';

        $message = "Hola, me gustaría agendar un servicio de {$this->selectedService->name}.\n\n";
        $message .= "*Mis Datos:*\n";
        $message .= "Nombre: {$this->customer_name}\n";
        $message .= "Teléfono: {$this->customer_phone}\n\n";

        if (!empty($this->custom_values)) {
            $message .= "*Detalles:*\n";
            foreach ($this->custom_values as $key => $val) {
                $message .= "- {$key}: {$val}\n";
            }
        }

        if ($this->lat && $this->lng) {
            $message .= "\n*Ubicación:*\nhttps://maps.google.com/?q={$this->lat},{$this->lng}";
        }

        $url = "https://wa.me/{$phone}?text=" . urlencode($message);

        return redirect()->away($url);
    }
}; ?>

<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <!-- Stepper Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center text-indigo-600">
                <span
                    class="rounded-full h-8 w-8 flex items-center justify-center border-2 {{ $step >= 1 ? 'border-indigo-600 bg-indigo-100 font-bold' : 'border-gray-300 text-gray-400' }}">1</span>
                <span class="ml-2 {{ $step >= 1 ? 'font-semibold' : 'text-gray-400' }}">Servicio</span>
            </div>
            <div class="flex-1 border-t-2 {{ $step >= 2 ? 'border-indigo-600' : 'border-gray-200' }} mx-4"></div>
            <div class="flex items-center {{ $step >= 2 ? 'text-indigo-600' : 'text-gray-400' }}">
                <span
                    class="rounded-full h-8 w-8 flex items-center justify-center border-2 {{ $step >= 2 ? 'border-indigo-600 bg-indigo-100 font-bold' : 'border-gray-300' }}">2</span>
                <span class="ml-2 {{ $step >= 2 ? 'font-semibold' : '' }}">Detalles</span>
            </div>
            <div class="flex-1 border-t-2 {{ $step >= 3 ? 'border-indigo-600' : 'border-gray-200' }} mx-4"></div>
            <div class="flex items-center {{ $step >= 3 ? 'text-indigo-600' : 'text-gray-400' }}">
                <span
                    class="rounded-full h-8 w-8 flex items-center justify-center border-2 {{ $step >= 3 ? 'border-indigo-600 bg-indigo-100 font-bold' : 'border-gray-300' }}">3</span>
                <span class="ml-2 {{ $step >= 3 ? 'font-semibold' : '' }}">Contacto</span>
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
                <x-ui.button wire:click="nextStep" type="button">Siguiente &rarr;</x-ui.button>
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
                <x-ui.button wire:click="nextStep" type="button">Siguiente &rarr;</x-ui.button>
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
                    <x-ui.label for="customer_phone" value="Número de Teléfono" />
                    <x-ui.input type="text" wire:model="customer_phone" id="customer_phone"
                        placeholder="Ej. +573001234567" :error="$errors->has('customer_phone')" />
                    @error('customer_phone')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Location block with AlpineJS -->
                <div class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-md">
                    <h3 class="font-medium text-gray-900 mb-2">📍 Tu Ubicación</h3>
                    <p class="text-sm text-gray-600 mb-3">Necesitamos tu ubicación para que el profesional pueda llegar
                        a ti.</p>

                    <button type="button" @click="getLocation"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm transition">
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
                    class="bg-green-600 hover:bg-green-700 border-green-700">Completar Reserva</x-ui.button>
            </div>
        </div>
    @endif
</div>
