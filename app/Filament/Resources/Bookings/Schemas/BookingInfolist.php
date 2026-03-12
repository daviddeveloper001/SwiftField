<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\KeyValueEntry;
use App\Enums\BookingStatus;

class BookingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles de Reserva')
                    ->schema([
                        TextEntry::make('customer.name')->label('Cliente'),
                        TextEntry::make('customer.phone')->label('Teléfono'),
                        TextEntry::make('service.name')->label('Servicio'),
                        TextEntry::make('scheduled_at')->dateTime('d M Y - h:i A')->label('Agendamiento'),
                        TextEntry::make('status')
                            ->badge()
                            ->label('Estado'),
                    ])->columns(2),
                
                Section::make('Valores Dinámicos')
                    ->schema(function ($record) {
                        $service = $record->service;
                        if (! $service || ! is_array($service->field_definitions) || empty($service->field_definitions)) {
                            return [
                                KeyValueEntry::make('custom_values')
                                    ->label('')
                                    ->columnSpanFull(),
                            ];
                        }

                        $entries = [];
                        foreach ($service->field_definitions as $field) {
                            $name = $field['name'] ?? null;
                            if (! $name) continue;

                            $entries[] = TextEntry::make("custom_values.{$name}")
                                ->label($field['label'] ?? ucfirst($name))
                                ->placeholder('N/A');
                        }

                        return $entries;
                    })
                    ->columns(2)
                    ->description('Datos personalizados llenados por el cliente.')
                    ->visible(fn ($record) => !empty($record->custom_values))
                    ->collapsible(),

                Section::make('Notas Internas')
                    ->schema([
                        TextEntry::make('internal_notes')->label('')->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(fn ($record) => empty($record->internal_notes)),
            ]);
    }
}
