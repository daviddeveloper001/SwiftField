<?php

declare(strict_types=1);

namespace App\Constants;

/**
 * Define de forma estricta las etiquetas (placeholders) permitidas
 * en las plantillas de mensajes de recordatorio de re-agendamiento.
 *
 * Cualquier etiqueta fuera de esta lista será ignorada por el TemplateProcessor.
 */
final class TemplateConstants
{
    /** Nombre del cliente */
    public const TAG_CLIENTE = '{cliente}';

    /** Nombre del servicio */
    public const TAG_SERVICIO = '{servicio}';

    /** Enlace de agendamiento del tenant */
    public const TAG_LINK_AGENDA = '{link_agenda}';

    /**
     * Devuelve la lista completa de etiquetas permitidas.
     *
     * @return string[]
     */
    public static function allowedTags(): array
    {
        return [
            self::TAG_CLIENTE,
            self::TAG_SERVICIO,
            self::TAG_LINK_AGENDA,
        ];
    }

    /**
     * Genera una cadena descriptiva con las etiquetas disponibles
     * para mostrar como helper text en la UI de Filament.
     */
    public static function helperText(): string
    {
        return 'Etiquetas disponibles: '
            . self::TAG_CLIENTE . ' (nombre del cliente), '
            . self::TAG_SERVICIO . ' (nombre del servicio), '
            . self::TAG_LINK_AGENDA . ' (enlace de agendamiento).';
    }
}
