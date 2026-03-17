<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Booking;
use App\Models\Tenant;
use App\Models\Service;
use App\Models\Customer;
use App\Services\WhatsAppNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WhatsAppNotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_inbound_url_generates_correct_link()
    {
        $tenant = Tenant::factory()->create([
            'name' => 'Test Business',
            'whatsapp_config' => ['phone' => '1234567890']
        ]);

        $service = Service::factory()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Test Service'
        ]);

        $customer = Customer::factory()->create([
            'tenant_id' => $tenant->id,
            'name' => 'John Doe',
            'phone' => '+573001112233'
        ]);

        $booking = Booking::factory()->create([
            'tenant_id' => $tenant->id,
            'service_id' => $service->id,
            'customer_id' => $customer->id,
            'scheduled_at' => '2026-03-20 10:00:00',
            'lat' => 4.6097,
            'lng' => -74.0817,
            'custom_values' => ['Mascota' => 'Si']
        ]);

        $serviceNotify = new WhatsAppNotificationService();
        $url = $serviceNotify->getInboundUrl($booking);

        $this->assertStringContainsString('wa.me/1234567890', $url);
        $this->assertStringContainsString(urlencode('Test Service'), $url);
        $this->assertStringContainsString(urlencode('John Doe'), $url);
        $this->assertStringContainsString(urlencode('20 Mar 2026 - 10:00 AM'), $url);
        $this->assertStringContainsString(urlencode('https://www.google.com/maps?q=4.6097,-74.0817'), $url);
    }

    public function test_get_confirmation_url_generates_correct_link()
    {
        $tenant = Tenant::factory()->create([
            'name' => 'Test Business'
        ]);

        $service = Service::factory()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Haircut'
        ]);

        $customer = Customer::factory()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Jane Smith',
            'phone' => '573112223344'
        ]);

        $booking = Booking::factory()->create([
            'tenant_id' => $tenant->id,
            'service_id' => $service->id,
            'customer_id' => $customer->id,
            'scheduled_at' => '2026-03-22 15:30:00',
            'lat' => 6.2442,
            'lng' => -75.5812
        ]);

        $serviceNotify = new WhatsAppNotificationService();
        $url = $serviceNotify->getConfirmationUrl($booking);

        $this->assertStringContainsString('wa.me/573112223344', $url);
        $this->assertStringContainsString(urlencode('Jane Smith'), $url);
        $this->assertStringContainsString(urlencode('Test Business'), $url);
        $this->assertStringContainsString(urlencode('Haircut'), $url);
        $this->assertStringContainsString(urlencode('22 Mar 2026 - 03:30 PM'), $url);
        $this->assertStringContainsString(urlencode('https://www.google.com/maps?q=6.2442,-75.5812'), $url);
    }

    public function test_get_reminder_url_generates_correct_link()
    {
        $tenant = Tenant::factory()->create([
            'name' => 'Fast Cleaners'
        ]);

        $service = Service::factory()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Home Cleaning'
        ]);

        $customer = Customer::factory()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Bob Builder',
            'phone' => '123456789'
        ]);

        $booking = Booking::factory()->create([
            'tenant_id' => $tenant->id,
            'service_id' => $service->id,
            'customer_id' => $customer->id,
            'scheduled_at' => now()->startOfDay()->addHours(14), // 2:00 PM
        ]);

        $serviceNotify = new WhatsAppNotificationService();
        $url = $serviceNotify->getReminderUrl($booking);

        $this->assertStringContainsString('wa.me/123456789', $url);
        $this->assertStringContainsString(urlencode('Bob Builder'), $url);
        $this->assertStringContainsString(urlencode('Home Cleaning'), $url);
        $this->assertStringContainsString(urlencode('02:00 PM'), $url);
    }
}
