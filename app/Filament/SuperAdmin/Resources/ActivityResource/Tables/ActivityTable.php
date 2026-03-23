<?php

namespace App\Filament\SuperAdmin\Resources\ActivityResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ForceDeleteBulkAction; 
use Filament\Tables\Actions\RestoreBulkAction;
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

class ActivityTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('log_name')->label('Categoría')->searchable(),
                TextColumn::make('description')->label('Descripción')->searchable(),
                TextColumn::make('subject_type')->label('Modelo Afectado')->searchable(),
                TextColumn::make('subject_id')->label('ID Modelo')->searchable(),
                TextColumn::make('causer_type')->label('Tipo Usuario')->searchable(),
                TextColumn::make('causer_id')->label('ID Usuario')->searchable(),
                TextColumn::make('event')->label('Evento')->searchable(),
                TextColumn::make('created_at')->label('Fecha')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('subscription_status')
                    ->options(collect(SubscriptionStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->getLabel()])->toArray())
                    ->label('Filtrar por Estado'),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
               BulkActionGroup::make([
                   DeleteBulkAction::make(),
                ]),
            ])
             ->defaultSort('created_at', 'desc');
    }
}
