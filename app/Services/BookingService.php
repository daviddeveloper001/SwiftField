<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\BookingDTO;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use App\Repositories\BookingRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class BookingService
{
    public function __construct(
        protected BookingRepository $bookingRepository
    ) {
    }


    public function createBooking(BookingDTO $dto): Booking
    {
        return DB::transaction(function () use ($dto) {

            $customerPhone = $dto->customer_data['phone'] ?? null;
            
            if (!$customerPhone) {
                throw new Exception("Customer phone is required to create a booking.");
            }

            // Overbooking prevention lock
            $exists = Booking::where('tenant_id', $dto->tenant_id)
                ->where('scheduled_at', $dto->scheduled_at)
                ->lockForUpdate()
                ->exists();

            if ($exists) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'scheduled_at' => 'Lo sentimos, este horario acaba de ser reservado por otro cliente. Por favor elige otro.',
                ]);
            }

            $customer = Customer::firstOrCreate(
                [
                    'tenant_id' => $dto->tenant_id,
                    'phone' => $customerPhone,
                ],
                [
                    'name' => $dto->customer_data['name'] ?? 'Unknown',
                    'email' => $dto->customer_data['email'] ?? null,
                ]
            );

            $service = Service::where('tenant_id', $dto->tenant_id)
                ->findOrFail($dto->service_id);

            $this->validateCustomValues($dto->custom_values, $service);

            return $this->bookingRepository->create([
                'uuid' => (string) Str::uuid(),
                'tenant_id' => $dto->tenant_id,
                'service_id' => $dto->service_id,
                'customer_id' => $customer->id,
                'status' => $dto->status,
                'scheduled_at' => $dto->scheduled_at,
                'lat' => $dto->lat,
                'lng' => $dto->lng,
                'custom_values' => $dto->custom_values,
                'internal_notes' => $dto->internal_notes,
            ]);
        });
    }

    protected function validateCustomValues(array $customValues, Service $service): void
    {
    }
}
