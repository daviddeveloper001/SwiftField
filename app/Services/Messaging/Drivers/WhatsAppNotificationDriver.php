<?php

declare(strict_types=1);

namespace App\Services\Messaging\Drivers;

use App\Services\Messaging\Contracts\NotificationDriverInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Driver de WhatsApp que implementa la interfaz de notificación.
 *
 * Responsable de:
 * 1. Traducir el texto plano a formato WhatsApp (negritas, etc.)
 * 2. Enviar el mensaje a través de la API de WhatsApp (wa.me link / API directa)
 */
class WhatsAppNotificationDriver implements NotificationDriverInterface
{
    /**
     * Envía un mensaje de WhatsApp al destinatario.
     *
     * En esta implementación, genera la URL de wa.me con el mensaje formateado
     * y la registra para procesamiento (Job / webhook / API call).
     *
     * @param string $to      Número de teléfono del destinatario (formato internacional sin +)
     * @param string $message Mensaje en texto plano
     */
    public function send(string $to, string $message): void
    {
        $formattedMessage = $this->applyWhatsAppFormatting($message);

        $sanitizedPhone = preg_replace('/[^0-9]/', '', $to);

        // Construir la URL de WhatsApp
        $url = "https://wa.me/{$sanitizedPhone}?text=" . urlencode(trim($formattedMessage));

        Log::info('[WhatsAppNotificationDriver] Reminder dispatched', [
            'to'  => $sanitizedPhone,
            'url' => $url,
        ]);

        // TODO: Integrar con API de WhatsApp Business cuando esté disponible.
        // Por ahora se registra la URL para despacho manual o via webhook.
    }

    /**
     * Aplica formato de WhatsApp al mensaje en texto plano.
     *
     * Convierte patrones semánticos a sintaxis de WhatsApp:
     * - Líneas que terminan en ':' se vuelven negritas (encabezados)
     * - El saludo inicial (primera línea) se enfatiza
     */
    private function applyWhatsAppFormatting(string $message): string
    {
        $lines = explode("\n", $message);
        $formatted = [];

        foreach ($lines as $index => $line) {
            $trimmed = trim($line);

            // La primera línea no vacía se convierte en encabezado con emoji
            if ($index === 0 && !empty($trimmed)) {
                $formatted[] = "🔔 *{$trimmed}*";
                continue;
            }

            $formatted[] = $line;
        }

        return implode("\n", $formatted);
    }
}
