<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Service;
use App\Models\Tenant;
use App\Services\BookingService;
use App\Services\WhatsAppNotificationService;
use App\DTOs\BookingDTO;
use Illuminate\Support\Collection;
use App\Models\Availability;

class BookingForm extends Component
{
    public int $step = 1;
    public int $tenantId;
    public Collection $services;

    // Form Data
    public ?int $service_id = null;
    public string $customer_name = '';
    public string $customer_phone = '';
    public string $scheduled_at = '';
    public ?float $lat = null;
    public ?float $lng = null;
    public array $custom_values = [];

    // Computed / Auxiliary
    public ?Service $selectedService = null;
    public array $fieldDefinitions = [];

    public function mount(int $tenantId)
    {
        $this->tenantId = $tenantId;
        $this->services = Service::where('tenant_id', $this->tenantId)->where('is_active', true)->get();
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
            $this->validate(
                ['service_id' => 'required|exists:services,id'],
                [
                    'service_id.required' => 'Por favor, selecciona un servicio para continuar.',
                    'service_id.exists' => 'El servicio seleccionado no es válido.'
                ]
            );
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
            'customer_phone' => ['required', 'string', 'regex:/^3[0-9]{9}$/'],
            'scheduled_at' => 'required|after:now',
        ], [
            'customer_name.required' => 'Tu nombre es obligatorio para completar la reserva.',
            'customer_name.max' => 'El nombre no puede exceder los 255 caracteres.',
            'customer_phone.required' => 'El número de teléfono es obligatorio.',
            'customer_phone.regex' => 'Por favor, ingresa un número celular válido de 10 dígitos (ej: 310 123 4567).',
            'scheduled_at.required' => 'Debes seleccionar la fecha y hora de la cita.',
            'scheduled_at.after' => 'La reserva debe programarse para una fecha u hora futura.',
        ]);

        // Validation against business hours
        $dt = new \DateTime($this->scheduled_at);
        $dayOfWeek = (int) $dt->format('w');
        $time = $dt->format('H:i');

        $availability = Availability::where('tenant_id', $this->tenantId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$availability || !$availability->is_open) {
            $this->addError('scheduled_at', 'Lo sentimos, el negocio no atiende en ese día.');
            return;
        }

        if ($time < $availability->start_time?->format('H:i') || $time > $availability->end_time?->format('H:i')) {
            $this->addError('scheduled_at', 'Lo sentimos, el negocio no atiende en ese horario.');
            return;
        }

        $dto = BookingDTO::fromArray([
            'tenant_id' => $this->tenantId,
            'service_id' => $this->service_id,
            'customer_data' => [
                'name' => $this->customer_name,
                'phone' => '57' . preg_replace('/[^0-9]/', '', $this->customer_phone),
            ],
            'scheduled_at' => $this->scheduled_at,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'custom_values' => $this->custom_values,
        ]);

        $booking = $bookingService->createBooking($dto);

        // Redirect to WhatsApp using the service
        $url = app(WhatsAppNotificationService::class)->getBookingSubmissionUrl($booking);

        return redirect()->away($url);
    }

    public function render()
    {
        return view('livewire.booking-form');
    }
}
