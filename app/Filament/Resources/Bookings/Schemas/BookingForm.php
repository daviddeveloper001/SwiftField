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
use App\Models\Customer;

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
                        modifyQueryUsing: fn (Builder $query) => $query->where('tenant_id', auth()->user()->tenants()->first()?->id),
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label(__('branding.forms.customer.name'))
                            ->required(),
                        TextInput::make('phone')
                            ->label(__('branding.forms.customer.phone'))
                            ->tel()
                            ->prefix('+57')
                            ->mask('999 999 9999')
                            ->stripCharacters(' ')
                            ->required()
                            ->rules([
                                fn (\Filament\Schemas\Components\Utilities\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $clean = preg_replace('/[^0-9]/', '', (string)$value);
                                    if (!empty($clean) && !str_starts_with($clean, '57')) {
                                        $clean = '57' . $clean;
                                    }
                                    
                                    $tenantId = auth()->user()->tenants()->first()?->id;
                                    
                                    if (\App\Models\Customer::where('tenant_id', $tenantId)->where('phone', $clean)->exists()) {
                                        $fail(__('validation.unique', ['attribute' => __('branding.forms.customer.phone')]));
                                    }
                                }
                            ]),
                        TextInput::make('email')
                            ->label(__('branding.forms.customer.email'))
                            ->email(),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        $data['tenant_id'] = auth()->user()->tenants()->first()?->id;
                        
                        return Customer::create($data)->id;
                    }),

                

                DateTimePicker::make('scheduled_at')
                    ->label('Fecha y hora')
                    ->required()
                    ->live()
                    ->rules([
                        fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                            $serviceId = $get('service_id');
                            if (! $serviceId || ! $value) {
                                return;
                            }

                            $scheduledAt = \Carbon\Carbon::parse($value);
                            $availabilityService = app(\App\Services\Booking\AvailabilityService::class);
                            
                            // El ID puede ser null en creación
                            $recordId = $get('id');

                            if (! $availabilityService->isRangeAvailable((int) $serviceId, $scheduledAt, $recordId ? (int) $recordId : null)) {
                                $fail('Este horario no está disponible según la configuración del negocio.');
                            }
                        },
                    ]),
                Select::make('status')
                    ->label('Estado')
                    ->options(BookingStatus::class)
                    ->required(),
                TextInput::make('lat')
                    ->hidden()
                    ->dehydrated(),
                TextInput::make('lng')
                    ->hidden()
                    ->dehydrated(),
                
                Select::make('service_id')
                    ->label('Servicio')
                    ->relationship(
                        name: 'service',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('tenant_id', auth()->user()->tenants()->first()?->id),
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($get, $set, $state) {
                        if (! $state) {
                            return;
                        }
                        $service = Service::find($state);
                        if ($service) {
                            if ($service->delivery_mode !== 'hibrido') {
                                $set('custom_values._delivery_mode', $service->delivery_mode);
                            } else {
                                $set('custom_values._delivery_mode', null);
                            }
                        }
                    }),

                Select::make('custom_values._delivery_mode')
                    ->label(__('branding.forms.booking.delivery_mode_label'))
                    ->options([
                        'local' => __('branding.forms.booking.delivery_mode_local'),
                        'domicilio' => __('branding.forms.booking.delivery_mode_domicilio'),
                    ])
                    ->required(fn (Get $get) => self::isHybrid($get))
                    ->visible(fn (Get $get) => self::isHybrid($get))
                    ->live(),

                TextInput::make('custom_values.direccion_escrita')
                    ->label(__('branding.forms.booking.written_address_label'))
                    ->placeholder(__('branding.forms.booking.written_address_placeholder'))
                    ->required(fn (Get $get) => self::isDomicilio($get))
                    ->visible(fn (Get $get) => self::isDomicilio($get))
                    ->live(),

                TextInput::make('custom_values.referencias')
                    ->label(__('branding.forms.booking.references_label'))
                    ->placeholder(__('branding.forms.booking.references_placeholder'))
                    ->visible(fn (Get $get) => self::isDomicilio($get)),

                \Cheesegrits\FilamentGoogleMaps\Fields\Map::make('location')
                    ->label(__('branding.forms.booking.map_label'))
                    ->autocomplete('custom_values.direccion_escrita')
                    ->reverseGeocode([
                        'custom_values.direccion_escrita' => '%n %s, %L',
                    ])
                    ->defaultLocation([4.6097, -74.0817])
                    ->visible(fn (Get $get) => self::isDomicilio($get))
                    ->columnSpanFull(),

                Textarea::make('internal_notes')
                    ->label('Notas Internas')
                    ->columnSpanFull(),
                
                FusedGroup::make()
                    ->label('Campos Personalizados')
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
            ]);
    }

    protected static function isDomicilio(Get $get): bool
    {
        $serviceId = $get('service_id');
        if (! $serviceId) {
            return false;
        }

        $service = Service::find($serviceId);
        if (! $service) {
            return false;
        }

        if ($service->delivery_mode === 'domicilio') {
            return true;
        }

        if ($service->delivery_mode === 'hibrido') {
            return $get('custom_values._delivery_mode') === 'domicilio';
        }

        return false;
    }

    protected static function isHybrid(Get $get): bool
    {
        $serviceId = $get('service_id');
        if (! $serviceId) {
            return false;
        }

        $service = Service::find($serviceId);
        return $service && $service->delivery_mode === 'hibrido';
    }
}
