<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Constants\ReminderConstants;
use App\Constants\TemplateConstants;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
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

                Repeater::make('field_definitions')
                    ->label('Información Adicional Requerida')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del campo (Interno)')
                            ->required(),
                        TextInput::make('label')
                            ->label('Etiqueta (Lo que ve el cliente)')
                            ->required(),
                        Select::make('type')
                            ->label('Tipo de entrada')
                            ->options([
                                'text' => 'Texto',
                                'number' => 'Número',
                                'date' => 'Fecha',
                                'select' => 'Selección',
                            ])
                            ->required(),
                    ])
                    ->visible(fn ($get) => (bool) $get('requires_quote'))
                    ->columnSpanFull()
                    ->columns(3),

                // ─── Sección: Recordatorio de Re-agendamiento ───
                Fieldset::make('Recordatorio de Re-agendamiento')
                    ->schema([
                        Toggle::make('has_reorder_reminder')
                            ->label('¿Activar recordatorio de re-agendamiento?')
                            ->helperText('Envía un recordatorio al cliente cuando se cumple el periodo configurado desde que completó su última cita.')
                            ->live()
                            ->default(false)
                            ->columnSpanFull(),

                        TextInput::make('reorder_value')
                            ->label('Periodo de retención')
                            ->numeric()
                            ->minValue(1)
                            ->required(fn ($get) => (bool) $get('has_reorder_reminder'))
                            ->visible(fn ($get) => (bool) $get('has_reorder_reminder'))
                            ->helperText('Cantidad numérica del periodo tras el cual se enviará el recordatorio.'),

                        Select::make('reorder_unit')
                            ->label('Unidad de tiempo')
                            ->options(ReminderConstants::unitOptions())
                            ->required(fn ($get) => (bool) $get('has_reorder_reminder'))
                            ->visible(fn ($get) => (bool) $get('has_reorder_reminder')),

                        Textarea::make('reorder_message_template')
                            ->label('Plantilla del mensaje')
                            ->rows(4)
                            ->required(fn ($get) => (bool) $get('has_reorder_reminder'))
                            ->visible(fn ($get) => (bool) $get('has_reorder_reminder'))
                            ->helperText(TemplateConstants::helperText())
                            ->placeholder('Hola {cliente}, ha pasado un tiempo desde tu última sesión de {servicio}. ¡Agenda tu próxima cita aquí! {link_agenda}')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

