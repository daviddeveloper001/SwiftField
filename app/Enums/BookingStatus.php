<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BookingStatus: string implements HasLabel, HasColor
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case QuotationRequested = 'quotation_requested';
    case Converted = 'converted';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Confirmed => 'Confirmado',
            self::Completed => 'Completado',
            self::Cancelled => 'Cancelado',
            self::QuotationRequested => 'Cotización Solicitada',
            self::Converted => 'Convertido',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Confirmed => 'info',
            self::Completed => 'success',
            self::Cancelled => 'danger',
            self::QuotationRequested => 'warning',
            self::Converted => 'success',
        };
    }
}
