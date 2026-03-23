<?php

namespace App\Filament\SuperAdmin\Resources\SystemExceptions;

use App\Filament\SuperAdmin\Resources\SystemExceptions\Pages\ManageSystemExceptions;
use App\Models\SystemException;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\SuperAdmin\Resources\SystemExceptions\Schemas\SystemExceptionInfolist;
use App\Filament\SuperAdmin\Resources\SystemExceptions\Tables\SystemExceptionsTable;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;

class SystemExceptionResource extends Resource
{
    protected static ?string $model = SystemException::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;


    protected static \UnitEnum|string|null $navigationGroup = 'Logs de Sistema';

    protected static ?string $modelLabel = 'Error de Sistema';
    protected static ?string $pluralModelLabel = 'Errores de Sistema';

    protected static ?string $recordTitleAttribute = 'message';

    public static function infolist(Schema $schema): Schema
    {
       return SystemExceptionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SystemExceptionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSystemExceptions::route('/'),
        ];
    }
}
