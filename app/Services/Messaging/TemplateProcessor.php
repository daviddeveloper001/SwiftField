<?php

declare(strict_types=1);

namespace App\Services\Messaging;

use App\Constants\TemplateConstants;
use App\Models\Booking;

/**
 * Motor de plantillas aislado que reemplaza las etiquetas permitidas
 * de TemplateConstants con datos reales del Booking.
 *
 * Recibe texto plano neutro (sin formato de canal) y devuelve
 * texto plano listo para ser formateado por cada driver de canal.
 */
class TemplateProcessor
{
    /**
     * Procesa una plantilla reemplazando las etiquetas permitidas
     * con los datos reales de un Booking.
     *
     * @param string  $template Plantilla con etiquetas de TemplateConstants
     * @param Booking $booking  Booking con relaciones cargadas (customer, service, tenant)
     * @return string Mensaje procesado en texto plano
     */
    public function process(string $template, Booking $booking): string
    {
        $booking->loadMissing(['customer', 'service', 'tenant']);

        $tenant = $booking->tenant;
        $landingUrl = route('tenant.landing', ['slug' => $tenant->slug]);

        $replacements = [
            TemplateConstants::TAG_CLIENTE     => $booking->customer->name ?? '',
            TemplateConstants::TAG_SERVICIO    => $booking->service->name ?? '',
            TemplateConstants::TAG_LINK_AGENDA => $landingUrl,
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template
        );
    }
}
