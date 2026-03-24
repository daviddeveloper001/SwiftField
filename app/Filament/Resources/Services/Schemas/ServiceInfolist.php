<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Models\Service;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ServiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label('Negocio'),
                TextEntry::make('name')
                    ->label('Servicio'),
                TextEntry::make('slug'),
                TextEntry::make('price')
                    ->label('Precio')
                    ->money(),
                IconEntry::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                TextEntry::make('description')
                    ->label('Descripción')
                    ->placeholder('-'),
            ]);
    }
}
