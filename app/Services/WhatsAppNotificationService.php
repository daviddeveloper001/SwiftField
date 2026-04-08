<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Traits\FormatCustomValues;

class WhatsAppNotificationService
{
    use FormatCustomValues;

    /**
     * Sanitiza el número de teléfono para enlaces de WhatsApp.
     */
    private function sanitizePhone(?string $phone): string
    {
        if (!$phone) return '';
        // Elimina espacios, guiones, signos '+' y cualquier otro caracter no numérico
        return preg_replace('/[^0-9]/', '', $phone);
    }

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

        // Recuperar configuración de WhatsApp desde el modelo Tenant (que usa settings)
        $config = $tenant->whatsapp_config;
        $phone = $this->sanitizePhone($config['phone'] ?? null);
        
        // Fallback al número de soporte si no hay número de tenant
        if (empty($phone)) {
            $phone = $this->sanitizePhone(config('app.support_whatsapp', '573000000000'));
        }

        $isQuote = $booking->scheduled_at === null;
        $typeLabel = $isQuote ? 'SOLICITUD DE COTIZACIÓN' : 'NUEVA RESERVA DE CITA';

        $scheduledAt = !$isQuote 
            ? $booking->scheduled_at->format('d M Y - h:i A') 
            : 'Por confirmar';

        $message = "✨ *{$typeLabel}* ✨\n\n";
        $message .= "Hola *{$tenant->name}*, he generado un nuevo requerimiento a través de SwiftField.\n\n";
        
        $message .= "🛠️ *Servicio:* {$service->name}\n";
        $message .= "📅 *Fecha/Hora:* {$scheduledAt}\n\n";

        $message .= "👤 *Datos del Cliente:*\n";
        $message .= "- *Nombre:* {$customer->name}\n";
        $message .= "- *Teléfono:* {$customer->phone}\n";
        
        if (!empty($booking->custom_values)) {
            $message .= "\n📝 *Detalles Adicionales:*\n";
            $message .= $this->formatCustomValuesToString((array) $booking->custom_values) . "\n";
        }

        if ($booking->lat && $booking->lng) {
            $message .= "\n📍 *Ubicación del Servicio:*\n";
            $message .= "https://www.google.com/maps?q={$booking->lat},{$booking->lng}";
        }

        $message .= "\n\n_Enviado desde el portal de reservas de {$tenant->name}_";

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
