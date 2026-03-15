<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class BookingsChart extends ChartWidget
{
    protected ?string $heading = 'Tendencia de Reservas (Última Semana)';
    
    protected ?string $maxHeight = '300px';
    
    protected int | string | array $columnSpan = 2;

    protected static bool $isLazy = true;

    protected function getData(): array
    {
        $data = Booking::query()
            ->selectRaw('COUNT(*) as count, DATE(scheduled_at) as date')
            ->where('scheduled_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date');

        $labels = [];
        $values = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('D');
            $values[] = $data->get($date, 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Reservas',
                    'data' => $values,
                    'fill' => 'start',
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
