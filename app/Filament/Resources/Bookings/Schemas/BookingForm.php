<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\KeyValue;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('service_id')
                    ->relationship('service', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                DateTimePicker::make('scheduled_at')
                    ->required(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmado',
                        'completed' => 'Completado',
                        'cancelled' => 'Cancelado',
                    ])
                    ->required(),
                TextInput::make('lat')
                    ->numeric(),
                TextInput::make('lng')
                    ->numeric(),
                Textarea::make('internal_notes'),
                KeyValue::make('custom_values')
                    ->columnSpanFull(),
            ]);
    }
}
