<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SubscriptionStatus: string implements HasLabel, HasColor
{
    case Trial = 'trial';
    case Active = 'active';
    case Expired = 'expired';
    case PendingPayment = 'pending_payment';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Trial => 'Prueba (Trial)',
            self::Active => 'Activo',
            self::Expired => 'Expirado',
            self::PendingPayment => 'Pago Pendiente',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Trial => 'info',
            self::Active => 'success',
            self::Expired => 'danger',
            self::PendingPayment => 'warning',
        };
    }
}
