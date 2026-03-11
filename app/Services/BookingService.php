<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\BookingDTO;
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

    /**
     * Process a booking creation end-to-end.
     *
     * @param BookingDTO $dto
     * @return \App\Models\Booking
     * @throws Exception
     */
    public function createBooking(BookingDTO $dto): \App\Models\Booking
    {
        return DB::transaction(function () use ($dto) {
            // 1. Resolve or Create Customer based on Tenant and Phone combination
            $customerPhone = $dto->customer_data['phone'] ?? null;
            
            if (!$customerPhone) {
                throw new Exception("Customer phone is required to create a booking.");
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

            // 2. Validate custom_values against the Service configuration
            $service = Service::where('tenant_id', $dto->tenant_id)
                ->findOrFail($dto->service_id);

            $this->validateCustomValues($dto->custom_values, $service);

            // 3. Delegate Persistence
            return $this->bookingRepository->store([
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

    /**
     * @param array $customValues
     * @param Service $service
     * @return void
     * @throws Exception
     */
    protected function validateCustomValues(array $customValues, Service $service): void
    {
        // TODO: In the future, decode $service->field_definitions and match keys against $customValues.
        // If strict validation fails based on definitions, throw an exception.
    }
}
