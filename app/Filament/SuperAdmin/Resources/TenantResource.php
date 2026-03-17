<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Enums\SubscriptionStatus;
use App\Models\Tenant;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Gestión de Clientes';
    protected static ?string $modelLabel = 'Cliente';

    public static function table(Table $table): Table
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
                Tables\Filters\SelectFilter::make('subscription_status')
                    ->options(SubscriptionStatus::class)
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenants::route('/'),
        ];
    }
}

namespace App\Filament\SuperAdmin\Resources\TenantResource\Pages;

use App\Filament\SuperAdmin\Resources\TenantResource;
use Filament\Resources\Pages\ListRecords;

class ListTenants extends ListRecords
{
    protected static string $resource = TenantResource::class;
}
