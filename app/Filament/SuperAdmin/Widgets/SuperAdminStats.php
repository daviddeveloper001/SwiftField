<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Booking;
use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SuperAdminStats extends BaseWidget
{
    protected function getStats(): array
    {
        // Total platform GMV (Gross Merchandise Value)
        $totalEarned = Booking::join('services', 'bookings.service_id', '=', 'services.id')
            ->where('bookings.status', 'confirmed')
            ->sum('services.price');

        // Active Tenants
        $activeTenants = Tenant::where('subscription_status', 'active')->count();

        return [
            Stat::make('Total Impacto (SwiftField)', '$' . number_format((float) $totalEarned, 2))
                ->description('Monto total procesado por todos los clientes')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Clientes Activos', $activeTenants)
                ->description('Suscripciones al día')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
        ];
    }
}
