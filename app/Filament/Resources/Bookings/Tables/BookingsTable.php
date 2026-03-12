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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;

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
                    ->label('Fecha Agendada'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->label('Estado'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmado',
                        'completed' => 'Completado',
                        'cancelled' => 'Cancelado',
                    ])
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
               ViewAction::make(),
               EditAction::make(),
               Action::make('Ver Ubicacion')
                    ->icon('heroicon-o-map-pin')
                    ->color('info')
                    ->url(fn (\App\Models\Booking $record): string => "https://maps.google.com/?q={$record->lat},{$record->lng}")
                    ->openUrlInNewTab()
                    ->visible(fn (\App\Models\Booking $record): bool => !empty($record->lat) && !empty($record->lng)),
               Action::make('Contactar WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->url(fn (\App\Models\Booking $record) => app(\App\Services\WhatsAppNotificationService::class)->generateBookingUrl($record))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
