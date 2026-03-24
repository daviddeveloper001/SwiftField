<?php

namespace App\Filament\Resources\Bookings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TextInput;
use App\Models\Booking;
use App\Services\WhatsAppNotificationService;
use App\Enums\BookingStatus;
use Filament\Actions\ExportAction;
use App\Filament\Exports\BookingExporter;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable()
                    ->label('Cliente'),
                TextColumn::make('service.name')
                    ->searchable()
                    ->sortable()
                    ->label('Servicio'),    
                TextColumn::make('scheduled_at')
                    ->dateTime('d M Y - h:i A')
                    ->sortable()
                    ->label('Fecha Agendada')
                    ->placeholder('Por definir (Cotización)')
                    ->color(fn (Booking $record): ?string => $record->scheduled_at?->isToday() ? 'primary' : 'gray'),
                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->label('Estado'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(BookingStatus::class)
                    ->label('Estado'),
                Filter::make('scheduled_at')
                    ->form([
                        DatePicker::make('created_from')->label('Desde'),
                        DatePicker::make('created_until')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_at', '<=', $date),
                            );
                    })
                    ->label('Rango de Fechas'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()->label('Ver'),
                EditAction::make()->label('Editar'),
                Action::make('Convertir a Cita')
                    ->label('Convertir a Cita')
                    ->modalHeading('Convertir Cotización a Cita')
                    ->modalSubmitActionLabel('Confirmar Conversión')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (Booking $record): bool => $record->status === BookingStatus::QuotationRequested)
                    ->form([
                        DatePicker::make('date')
                            ->label('Fecha')
                            ->required(),
                        TimePicker::make('time')
                            ->label('Hora')
                            ->required(),
                        TextInput::make('price')
                            ->label('Precio Final')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                    ])
                    ->action(function (Booking $record, array $data): void {
                        $scheduledAt = $data['date'] . ' ' . $data['time'];
                        
                        $record->update([
                            'status' => BookingStatus::Confirmed,
                            'scheduled_at' => $scheduledAt,
                        ]);

                        // Enviar notificación si es necesario
                    }),
                Action::make('Confirmar Reserva')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (Booking $record, $livewire) {
                        $record->update(['status' => BookingStatus::Confirmed]);
                        $url = app(WhatsAppNotificationService::class)->getConfirmationUrl($record);
                        $livewire->js("window.open('{$url}', '_blank')");
                    })
                    ->visible(fn (Booking $record): bool => $record->status === BookingStatus::Pending),
                Action::make('Enviar Recordatorio')
                    ->icon('heroicon-o-bell-alert')
                    ->color('warning')
                    ->url(fn (Booking $record) => app(WhatsAppNotificationService::class)->getReminderUrl($record))
                    ->openUrlInNewTab()
                    ->visible(fn (Booking $record): bool => 
                        $record->status === BookingStatus::Confirmed && 
                        $record->scheduled_at->isToday()
                    ),
               Action::make('Ver Ubicacion')
                    ->icon('heroicon-o-map-pin')
                    ->color('info')
                    ->url(fn (Booking $record): string => "https://www.google.com/maps?q={$record->lat},{$record->lng}")
                    ->openUrlInNewTab()
                    ->visible(fn (Booking $record): bool => !empty($record->lat) && !empty($record->lng)),
               Action::make('Contactar WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->url(fn (Booking $record) => app(WhatsAppNotificationService::class)->getInboundUrl($record))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Eliminar Seleccionados'),
                    ForceDeleteBulkAction::make()->label('Eliminar Permanentemente'),
                    RestoreBulkAction::make()->label('Restaurar Seleccionados'),
                ])->label('Acciones Masivas'),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(BookingExporter::class)
                    ->label('Exportar Reporte')
                    ->icon('heroicon-o-document-arrow-down'),
            ]);
    }
}
