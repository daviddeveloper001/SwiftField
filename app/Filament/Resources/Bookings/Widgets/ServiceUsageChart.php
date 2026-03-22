<?php

namespace App\Filament\Resources\Bookings\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ServiceUsageChart extends ChartWidget
{
    protected ?string $heading = 'Distribución de Servicios';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Booking::whereNotNull('service_id')
            ->select('service_id', DB::raw('count(*) as total'))
            ->groupBy('service_id')
            ->with('service')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Reservas',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4',
                    ],
                ],
            ],
            'labels' => $data->pluck('service.name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
