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
use App\Enums\BookingStatus;

class BookingForm extends Component
{
    public int $step = 1;
    public int $tenantId;
    public Collection $services;

    // Form Data
    public ?int $service_id = null;
    public string $customer_name = '';
    public string $customer_phone = '';
    public ?string $scheduled_at = null;
    public ?float $lat = null;
    public ?float $lng = null;
    public array $custom_values = [];
    public string $quote_text = '';

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
        // Sanitize phone before validation: remove spaces
        $this->customer_phone = str_replace(' ', '', $this->customer_phone);

        $isQuote = $this->selectedService->requires_quote;

        $rules = [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => ['required', 'string', 'regex:/^3[0-9]{9}$/'],
        ];

        $messages = [
            'customer_name.required' => 'Tu nombre es obligatorio.',
            'customer_name.max' => 'El nombre no puede exceder los 255 caracteres.',
            'customer_phone.required' => 'El número de teléfono es obligatorio.',
            'customer_phone.regex' => 'Número celular no válido (ej: 310 123 4567).',
        ];

        if ($isQuote) {
            $rules['quote_text'] = 'required|string';
            $messages['quote_text.required'] = "Por favor, especifica el detalle para '{$this->selectedService->quote_label}'.";
        } else {
            $rules['scheduled_at'] = 'required|after:now';
            $messages['scheduled_at.required'] = 'Debes seleccionar la fecha y hora de la cita.';
            $messages['scheduled_at.after'] = 'La reserva debe ser futura.';
        }

        $this->validate($rules, $messages);

        if (!$isQuote) {
            // Business hours validation
            $dt = new \DateTime($this->scheduled_at);
            $dayOfWeek = (int) $dt->format('w');
            $time = $dt->format('H:i');

            $availability = Availability::where('tenant_id', $this->tenantId)
                ->where('day_of_week', $dayOfWeek)
                ->first();

            if (!$availability || !$availability->is_open) {
                $this->addError('scheduled_at', 'El negocio no atiende ese día.');
                return;
            }

            if ($time < $availability->start_time?->format('H:i') || $time > $availability->end_time?->format('H:i')) {
                $this->addError('scheduled_at', 'El negocio no atiende en ese horario.');
                return;
            }
        }

        $customValues = $this->custom_values;
        if ($isQuote) {
            $customValues['quote_details'] = $this->quote_text;
        }

        $dto = BookingDTO::fromArray([
            'tenant_id' => $this->tenantId,
            'service_id' => $this->service_id,
            'customer_data' => [
                'name' => $this->customer_name,
                'phone' => '57' . preg_replace('/[^0-9]/', '', $this->customer_phone),
            ],
            'scheduled_at' => $isQuote ? null : $this->scheduled_at,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'custom_values' => $customValues,
            'status' => $isQuote ? BookingStatus::QuotationRequested->value : BookingStatus::Pending->value,
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
