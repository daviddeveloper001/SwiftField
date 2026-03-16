<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label('Descripción')
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->label('Precio')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                Toggle::make('is_active')
                    ->label('Activo')
                    ->required()
                    ->default(true),
                Repeater::make('field_definitions')
                    ->label('Definición de Campos')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del campo (key)')
                            ->required(),
                        TextInput::make('label')
                            ->label('Etiqueta (label)')
                            ->required(),
                        Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'text' => 'Texto',
                                'number' => 'Número',
                                'date' => 'Fecha',
                                'select' => 'Selección',
                            ])
                            ->required(),
                        // Aquí podríamos agregar lógica para opciones si el tipo es select
                    ])
                    ->columnSpanFull()
                    ->columns(3),
            ]);
    }
}
