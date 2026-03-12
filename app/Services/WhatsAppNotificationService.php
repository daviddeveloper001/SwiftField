<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Traits\FormatCustomValues;

class WhatsAppNotificationService
{
    use FormatCustomValues;

    /**
     * Generate the WhatsApp URL based on a Booking instance.
     *
     * @param Booking $booking
     * @return string
     */
    public function generateBookingUrl(Booking $booking): string
    {
        // Require eager-loaded relations to prevent N+1 issues
        $booking->loadMissing(['tenant', 'service', 'customer']);

        $tenant = $booking->tenant;
        $service = $booking->service;
        $customer = $booking->customer;

        $phone = $tenant->whatsapp_config['phone'] ?? '';
        
        // Strip any non-numeric characters for valid wa.me links
        $phone = preg_replace('/[^0-9]/', '', $phone);

        $scheduledAt = $booking->scheduled_at 
            ? $booking->scheduled_at->format('d M Y - h:i A') 
            : 'Por confirmar';

        $message = "Hola, he generado un nuevo requerimiento para el servicio de *{$service->name}*.\n\n";
        
        $message .= "🗓️ *Agendamiento:*\n";
        $message .= "{$scheduledAt}\n\n";

        $message .= "👤 *Mis Datos:*\n";
        $message .= "- Nombre: {$customer->name}\n";
        $message .= "- Teléfono: {$customer->phone}\n";
        
        if (!empty($booking->custom_values)) {
            $message .= "\n📝 *Detalles Adicionales:*\n";
            $message .= $this->formatCustomValuesToString((array) $booking->custom_values) . "\n";
        }

        if ($booking->lat && $booking->lng) {
            $message .= "\n📍 *Ubicación del Servicio:*\n";
            $message .= "https://www.google.com/maps?q={$booking->lat},{$booking->lng}";
        }

        return "https://wa.me/{$phone}?text=" . urlencode(trim($message));
    }
}
