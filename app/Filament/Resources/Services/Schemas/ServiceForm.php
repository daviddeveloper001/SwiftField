<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
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
                RichEditor::make('description')
                    ->label('Descripción')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'bulletList',
                        'undo',
                        'redo',
                    ])
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->label('Precio')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('duration_minutes')
                    ->label('Duración (minutos)')
                    ->required()
                    ->numeric()
                    ->default(60)
                    ->suffix('min')
                    ->helperText('Duración estimada para el agendamiento.')
                    ->visible(fn ($get) => !$get('requires_quote')),
                Toggle::make('is_active')
                    ->label('Activo')
                    ->required()
                    ->default(true)
                    ->inline(false),
                Toggle::make('auto_confirm')
                    ->label('¿Confirmar automáticamente?')
                    ->helperText('Si se activa, el sistema agendará la cita automáticamente sin intervención del administrador.')
                    ->default(false)
                    ->inline(false),
                Select::make('delivery_mode')
                    ->label('Modalidad de Prestación')
                    ->options([
                        'local' => 'Local (En el establecimiento)',
                        'domicilio' => 'A Domicilio',
                        'hibrido' => 'Híbrido (Cliente elige)',
                    ])
                    ->default('local')
                    ->required()
                    ->live(),
                TextInput::make('shipping_fee')
                    ->label('Costo de Domicilio')
                    ->numeric()
                    ->prefix('$')
                    ->default(0)
                    ->visible(fn ($get) => in_array($get('delivery_mode'), ['domicilio', 'hibrido']))
                    ->required(fn ($get) => in_array($get('delivery_mode'), ['domicilio', 'hibrido'])),

                Toggle::make('requires_quote')
                    ->label('Requiere Cotización')
                    ->helperText('Si se activa, el cliente pedirá un presupuesto en lugar de reservar una cita con fecha fija.')
                    ->live()
                    ->columnSpanFull(),
                TextInput::make('quote_label')
                    ->label('Etiqueta para detalles')
                    ->placeholder('Ej: Describe tu proyecto o ideas...')
                    ->visible(fn ($get) => $get('requires_quote'))
                    ->required(fn ($get) => $get('requires_quote'))
                    ->columnSpanFull(),
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
                    ])
                    ->columnSpanFull()
                    ->columns(3),
            ]);
    }
}
