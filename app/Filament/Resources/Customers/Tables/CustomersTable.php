<?php

namespace App\Filament\Resources\Customers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('branding.sections.visual_identity.name') ?? 'Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label(__('branding.sections.communication.phone') ?? 'Teléfono')
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => 
                        str_starts_with($state, '57') ? substr($state, 2) : $state
                    ),

                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable(),

                TextColumn::make('bookings_count')
                    ->label('Citas')
                    ->counts('bookings')
                    ->badge()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
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
