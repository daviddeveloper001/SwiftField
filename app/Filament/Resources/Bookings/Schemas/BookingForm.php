<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\KeyValue;
use App\Enums\BookingStatus;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->label('Cliente')
                    ->relationship('customer', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('service_id')
                    ->label('Servicio')
                    ->relationship('service', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                DateTimePicker::make('scheduled_at')
                    ->label('Fecha y hora')
                    ->required(),
                Select::make('status')
                    ->label('Estado')
                    ->options(BookingStatus::class)
                    ->required(),
                TextInput::make('lat')
                    ->label('Latitud')
                    ->numeric(),
                TextInput::make('lng')
                    ->label('Longitud')
                    ->numeric(),
                Textarea::make('internal_notes')
                    ->label('Notas Internas'),
                KeyValue::make('custom_values')
                    ->label('Valores Personalizados')
                    ->columnSpanFull(),
            ]);
    }
}
