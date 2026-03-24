<?php

namespace App\Filament\SuperAdmin\Resources\TenantResource\Tables;

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
use Filament\Tables\Columns\ImageColumn;
use App\Enums\SubscriptionStatus;
use App\Models\Tenant;
use Filament\Notifications\Notification;

class TenantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre del Negocio')
                    ->searchable(),
                TextColumn::make('subscription_status')
                    ->label('Estado')
                    ->badge(),
                TextColumn::make('subscription_ends_at')
                    ->label('Vence')
                    ->dateTime('d M Y')
                    ->sortable(),
                ImageColumn::make('payment_proof')
                    ->label('Comprobante'),
            ])
            ->filters([
                SelectFilter::make('subscription_status')
                    ->options(collect(SubscriptionStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->getLabel()])->toArray())
                    ->label('Filtrar por Estado'),
            ])
            ->actions([
                Action::make('Aprobar Pago')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Tenant $record) => $record->subscription_status === SubscriptionStatus::PendingPayment)
                    ->action(function (Tenant $record) {
                        $newEndDate = ($record->subscription_ends_at && $record->subscription_ends_at->isFuture()) 
                            ? $record->subscription_ends_at->addDays(30) 
                            : now()->addDays(30);

                        $record->update([
                            'subscription_status' => SubscriptionStatus::Active,
                            'subscription_ends_at' => $newEndDate,
                        ]);

                        Notification::make()
                            ->title('Pago Aprobado')
                            ->body("La suscripción de {$record->name} ha sido activada hasta el " . $newEndDate->format('d M Y'))
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
               BulkActionGroup::make([
                   DeleteBulkAction::make()->label('Eliminar Seleccionados'),
                ])->label('Acciones Masivas'),
            ]);
    }
}
