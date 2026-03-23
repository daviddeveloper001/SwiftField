<?php

namespace App\Filament\SuperAdmin\Resources\ActivityResource\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\KeyValueEntry;
use App\Enums\BookingStatus;

class ActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles del Inquilino')
                    ->schema([
                        TextEntry::make('name')->label('Nombre'),
                        TextEntry::make('email')->label('Correo Electrónico'),
                        TextEntry::make('password')->label('Contraseña'),
                    ])->columns(2),
            ]);
    }
}
