<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Components\FusedGroup;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\BookingStatus;
use Filament\Schemas\Components\Utilities\Get;
use App\Models\Service;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->label('Cliente')
                    ->relationship(
                        name: 'customer',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('tenant_id', Filament::getTenant()->id),
                    )
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('service_id')
                    ->label('Servicio')
                    ->relationship(
                        name: 'service',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('tenant_id', Filament::getTenant()->id),
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live(),
                
                FusedGroup::make()
                    ->schema(function (Get $get) {
                        $serviceId = $get('service_id');
                        if (! $serviceId) {
                            return [];
                        }

                        $service = Service::find($serviceId);
                        if (! $service || ! is_array($service->field_definitions)) {
                            return [];
                        }

                        $fields = [];
                        foreach ($service->field_definitions as $field) {
                            $name = $field['name'] ?? null;
                            if (! $name) continue;

                            $formField = match ($field['type'] ?? 'text') {
                                'select' => Select::make("custom_values.{$name}")
                                    ->options(array_combine($field['options'] ?? [], $field['options'] ?? [])),
                                'number' => TextInput::make("custom_values.{$name}")
                                    ->numeric(),
                                default => TextInput::make("custom_values.{$name}"),
                            };

                            $formField->label($field['label'] ?? ucfirst($name));

                            if ($field['required'] ?? false) {
                                $formField->required();
                            }

                            $fields[] = $formField;
                        }

                        return $fields;
                    })
                    ->columns(2),

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
                    ->label('Notas Internas')
                    ->columnSpanFull(),
            ]);
    }
}
