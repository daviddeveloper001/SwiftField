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
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state ?? '')))
                    ->columnSpanFull(),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(
                        table: 'services',
                        column: 'slug',
                        ignoreRecord: true,
                        modifyRuleUsing: fn (\Illuminate\Validation\Rules\Unique $rule) => $rule->where('tenant_id', auth()->user()->tenant_id)
                    )
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
                Toggle::make('requires_quote')
                    ->label('Requiere Cotización')
                    ->helperText('Si se activa, el cliente pedirá un presupuesto en lugar de reservar una cita con fecha fija.')
                    ->live(),
                TextInput::make('quote_label')
                    ->label('Etiqueta para detalles')
                    ->placeholder('Ej: Describe tu proyecto o ideas...')
                    ->visible(fn ($get) => $get('requires_quote'))
                    ->required(fn ($get) => $get('requires_quote')),
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
