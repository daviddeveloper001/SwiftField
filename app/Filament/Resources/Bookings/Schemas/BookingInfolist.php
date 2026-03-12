<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\Section;
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
                    ->schema([
                        KeyValueEntry::make('custom_values')
                            ->label('')
                            ->columnSpanFull(),
                    ])
                    ->description('Datos personalizados llenados por el cliente.')
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
