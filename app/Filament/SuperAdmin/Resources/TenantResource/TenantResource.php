<?php

namespace App\Filament\SuperAdmin\Resources\TenantResource;

use App\Enums\SubscriptionStatus;
use App\Models\Tenant;
use App\Filament\SuperAdmin\Resources\TenantResource\Pages\ListTenants;
use App\Filament\SuperAdmin\Resources\TenantResource\Tables\TenantsTable;
use BackedEnum;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Gestión de Clientes';
    protected static ?string $modelLabel = 'Cliente';


    public static function table(Table $table): Table
    {
        return TenantsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenants::route('/'),
            'create' => \App\Filament\SuperAdmin\Resources\TenantResource\Pages\CreateTenant::route('/create'),
            'edit' => \App\Filament\SuperAdmin\Resources\TenantResource\Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
