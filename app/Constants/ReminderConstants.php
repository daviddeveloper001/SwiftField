<?php

declare(strict_types=1);

namespace App\Constants;

/**
 * Centraliza las unidades de tiempo válidas para el sistema de recordatorios
 * de re-agendamiento, eliminando magic strings en la capa de servicio y UI.
 */
final class ReminderConstants
{
    /** Unidad: días */
    public const UNIT_DAYS = 'days';

    /** Unidad: semanas */
    public const UNIT_WEEKS = 'weeks';

    /** Unidad: meses */
    public const UNIT_MONTHS = 'months';

    /**
     * Devuelve las opciones de unidad formateadas para Select de Filament.
     *
     * @return array<string, string>
     */
    public static function unitOptions(): array
    {
        return [
            self::UNIT_DAYS   => 'Días',
            self::UNIT_WEEKS  => 'Semanas',
            self::UNIT_MONTHS => 'Meses',
        ];
    }

    /**
     * Lista plana de unidades válidas para validación.
     *
     * @return string[]
     */
    public static function validUnits(): array
    {
        return [
            self::UNIT_DAYS,
            self::UNIT_WEEKS,
            self::UNIT_MONTHS,
        ];
    }
}
