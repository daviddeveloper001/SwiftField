<?php

declare(strict_types=1);

namespace App\DTOs;

use Carbon\CarbonImmutable;

readonly class BookingDTO
{
    public function __construct(
        public int $tenant_id,
        public int $service_id,
        public array $customer_data,
        public ?CarbonImmutable $scheduled_at = null,
        public ?float $lat = null,
        public ?float $lng = null,
        public array $custom_values = [],
        public ?string $internal_notes = null,
        public string $status = 'pending'
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            tenant_id: (int) $data['tenant_id'],
            service_id: (int) $data['service_id'],
            customer_data: (array) ($data['customer_data'] ?? []),
            scheduled_at: isset($data['scheduled_at']) ? CarbonImmutable::parse($data['scheduled_at']) : null,
            lat: isset($data['lat']) ? (float) $data['lat'] : null,
            lng: isset($data['lng']) ? (float) $data['lng'] : null,
            custom_values: (array) ($data['custom_values'] ?? []),
            internal_notes: $data['internal_notes'] ?? null,
            status: $data['status'] ?? 'pending'
        );
    }
}