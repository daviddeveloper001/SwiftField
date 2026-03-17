<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\Service;
use App\Models\Customer;
use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'tenant_id' => Tenant::factory(),
            'service_id' => Service::factory(),
            'customer_id' => Customer::factory(),
            'status' => BookingStatus::Pending,
            'scheduled_at' => now()->addDay(),
            'lat' => fake()->latitude(),
            'lng' => fake()->longitude(),
            'custom_values' => [],
        ];
    }
}
