<?php

namespace App\Filament\Resources\Bookings\Widgets;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;

class BookingStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $tenant = Filament::getTenant();

        if (! $tenant) {
            return [];
        }

        // 0. Ventas Proyectadas (Mes actual vs Pasado)
        $currentMonthSales = Booking::where('bookings.tenant_id', $tenant->id)
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->whereMonth('scheduled_at', now()->month)
            ->whereYear('scheduled_at', now()->year)
            ->sum('services.price');

        $lastMonthSales = Booking::where('bookings.tenant_id', $tenant->id)
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->whereMonth('scheduled_at', now()->subMonth()->month)
            ->whereYear('scheduled_at', now()->subMonth()->year)
            ->sum('services.price');

        $trend = $currentMonthSales - $lastMonthSales;
        $trendIcon = $trend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
        $trendColor = $trend >= 0 ? 'success' : 'danger';
        $trendDescription = $trend >= 0 ? '+$' . number_format($trend, 2) . ' vs mes anterior' : '-$' . number_format(abs($trend), 2) . ' vs mes anterior';

        // 1. Reservas de Hoy
        $todayCount = Booking::where('tenant_id', $tenant->id)
            ->whereDate('scheduled_at', now())
            ->count();

        // 2. Pendientes de Confirmar
        $pendingCount = Booking::where('tenant_id', $tenant->id)
            ->where('status', BookingStatus::Pending)
            ->count();

        // 3. Servicio Popular (últimos 30 días)
        $popularService = Booking::where('tenant_id', $tenant->id)
            ->where('scheduled_at', '>=', now()->subDays(30))
            ->select('service_id', DB::raw('count(*) as total'))
            ->groupBy('service_id')
            ->orderByDesc('total')
            ->first();

        $serviceName = 'N/A';
        if ($popularService) {
            $serviceName = Service::find($popularService->service_id)?->name ?? 'N/A';
        }

        return [
            Stat::make('Ventas Proyectadas', '$' . number_format((float) $currentMonthSales, 2))
                ->description($trendDescription)
                ->descriptionIcon($trendIcon)
                ->color($trendColor),

            Stat::make('Reservas de Hoy', $todayCount)
                ->description('Total agendado para hoy')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),
                
            Stat::make('Pendientes de Confirmar', $pendingCount)
                ->description('Esperando acción')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('Servicio Popular', $serviceName)
                ->description('Más solicitado (30d)')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('info'),
        ];
    }
}
