<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 1;

    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $ventasProyectadas = Booking::query()
            ->whereBetween('scheduled_at', [$startOfMonth, $endOfMonth])
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->sum('services.price');

        return [
            Stat::make('Ventas Proyectadas', '$' . number_format((float) $ventasProyectadas, 2))
                ->description('Métrica del mes actual')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
