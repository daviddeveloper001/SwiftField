<?php

namespace App\Filament\SuperAdmin\Resources\ActivityResource;

use App\Filament\SuperAdmin\Resources\ActivityResource\Pages;
use App\Filament\SuperAdmin\Resources\ActivityResource\Tables\ActivityTable;
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
        return ActivityTable::make($table);
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
