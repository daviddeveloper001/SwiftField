<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\ActivityResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Auditoría de Actividad';
    protected static ?string $pluralModelLabel = 'Auditorías de Actividad';
    protected static ?string $modelLabel = 'Auditoría de Actividad';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('log_name')->label('Categoría')->searchable(),
                Tables\Columns\TextColumn::make('description')->label('Descripción')->searchable(),
                Tables\Columns\TextColumn::make('subject_type')->label('Modelo Afectado')->searchable(),
                Tables\Columns\TextColumn::make('subject_id')->label('ID Modelo')->searchable(),
                Tables\Columns\TextColumn::make('causer_type')->label('Tipo Usuario')->searchable(),
                Tables\Columns\TextColumn::make('causer_id')->label('ID Usuario')->searchable(),
                Tables\Columns\TextColumn::make('event')->label('Evento')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Columns\Layout\ViewField::make('properties'),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }
}
