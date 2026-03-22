<?php

namespace App\Filament\Exports;

use App\Models\Booking;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class BookingExporter extends Exporter
{
    protected static ?string $model = Booking::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('customer.name')->label('Cliente'),
            ExportColumn::make('customer.phone')->label('Teléfono'),
            ExportColumn::make('service.name')->label('Servicio'),
            ExportColumn::make('scheduled_at')->label('Fecha/Hora'),
            ExportColumn::make('status')->label('Estado'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'El reporte de reservas ha sido generado exitosamente.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' filas fallaron al exportar.';
        }

        return $body;
    }
}
