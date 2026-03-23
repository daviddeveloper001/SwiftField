<?php

namespace App\Filament\SuperAdmin\Resources\SystemExceptions\Tables;

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
use App\Models\SystemException;
use Filament\Notifications\Notification;

class SystemExceptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('message')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'danger',
                        'fixed' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('method')
                    ->label('Método')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('message')
                    ->label('Mensaje')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('resolve')
                    ->label('Marcar Resuelto')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (SystemException $record): bool => $record->status === 'open')
                    ->action(fn (SystemException $record) => $record->update(['status' => 'fixed']))
                    ->requiresConfirmation(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
