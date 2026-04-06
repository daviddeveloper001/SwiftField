<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Traits\FormatCustomValues;

class WhatsAppNotificationService
{
    use FormatCustomValues;

    /**
     * Generate the WhatsApp URL for the inbound message (Client to Owner).
     * Used by the booking form to send the request to the proprietor.
     *
     * @param Booking $booking
     * @return string
     */
    public function getBookingSubmissionUrl(Booking $booking): string
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

    /**
     * Generate a direct chat link with the customer.
     *
     * @param Booking $booking
     * @return string
     */
    public function getInboundUrl(Booking $booking): string
    {
        $booking->loadMissing(['customer']);
        $customerPhone = preg_replace('/[^0-9]/', '', $booking->customer->phone);

        return "https://wa.me/{$customerPhone}";
    }

    /**
     * Generate the WhatsApp URL for the confirmation message (Owner to Client).
     *
     * @param Booking $booking
     * @return string
     */
    public function getConfirmationUrl(Booking $booking): string
    {
        $booking->loadMissing(['tenant', 'service', 'customer']);

        $tenant = $booking->tenant;
        $service = $booking->service;
        $customer = $booking->customer;

        $customerPhone = preg_replace('/[^0-9]/', '', $customer->phone);
        $scheduledAt = $booking->scheduled_at->format('d M Y - h:i A');

        $message = "¡Hola {$customer->name}! 👋 Soy de {$tenant->name}. Te confirmo que tu servicio de {$service->name} para el día {$scheduledAt}, duración del servicio {$tenant->duration} minutos, ha sido CONFIRMADO. ¡Nos vemos pronto!";

        if ($booking->lat && $booking->lng) {
            $message .= "\n\n📍 Ubicación registrada:\n";
            $message .= "https://www.google.com/maps?q={$booking->lat},{$booking->lng}";
        }

        return "https://wa.me/{$customerPhone}?text=" . urlencode(trim($message));
    }

    /**
     * Generate the WhatsApp URL for a reminder message.
     *
     * @param Booking $booking
     * @return string
     */
    public function getReminderUrl(Booking $booking): string
    {
        $booking->loadMissing(['tenant', 'service', 'customer']);

        $tenant = $booking->tenant;
        $service = $booking->service;
        $customer = $booking->customer;

        $customerPhone = preg_replace('/[^0-9]/', '', $customer->phone);
        $time = $booking->scheduled_at->format('h:i A');

        $message = "Hola {$customer->name}, te recordamos tu cita de {$service->name} hoy a las {$time}. ¡Estamos listos para atenderte!";

        return "https://wa.me/{$customerPhone}?text=" . urlencode(trim($message));
    }
}
