<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\DayOfWeek;
use App\Models\Availability;
use App\Models\Tenant;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use BackedEnum;

class ManageAvailability extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Horarios de Atención';

    protected static ?string $title = 'Horarios de Atención';

    protected string $view = 'filament.pages.manage-availability';

    public ?array $data = [];

    const DEFAULT_START_TIME = '08:00';
    const DEFAULT_END_TIME = '18:00';
    const AVAILABILITIES_KEY = 'availabilities';

    public function mount(): void
    {
        $tenant = auth()->user()->tenants()->first();

        if ($tenant) {
            $availabilities = Availability::where('tenant_id', $tenant->id)
                ->orderBy('day_of_week')
                ->get();

            $formattedData = [];

            // Ensure all 7 days are present in the UI
            foreach (DayOfWeek::cases() as $day) {
                $availability = $availabilities->firstWhere('day_of_week', $day->value);
                
                $formattedData[] = [
                    'day_of_week' => $day->value,
                    'day_name' => $day->getLabel(),
                    'is_open' => $availability ? $availability->is_open : $day->isOpenByDefault(),
                    'start_time' => $availability ? $availability->start_time?->format('H:i') : self::DEFAULT_START_TIME,
                    'end_time' => $availability ? $availability->end_time?->format('H:i') : self::DEFAULT_END_TIME,
                ];
            }

            $this->form->fill([
                self::AVAILABILITIES_KEY => $formattedData,
                'booking_slot_size' => $tenant->getSetting('booking_slot_size'),
                'default_service_duration' => $tenant->getSetting('default_service_duration'),
                'buffer_time_between_bookings' => $tenant->getSetting('buffer_time_between_bookings'),
            ]);
        }
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Configuración de Agenda')
                    ->description('Define cómo se divide el tiempo en tu calendario y la duración por defecto de tus servicios.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('booking_slot_size')
                                    ->label('Tamaño del Intervalo (min)')
                                    ->helperText('Ej: 15 min para citas rápidas, 60 min para sesiones largas.')
                                    ->numeric()
                                    ->required()
                                    ->suffix('min')
                                    ->minValue(5)
                                    ->maxValue(120),
                                TextInput::make('default_service_duration')
                                    ->label('Duración del Servicio (min)')
                                    ->helperText('Tiempo promedio que dura un servicio.')
                                    ->numeric()
                                    ->required()
                                    ->suffix('min')
                                    ->minValue(5),
                                TextInput::make('buffer_time_between_bookings')
                                    ->label('Tiempo de Buffer (min)')
                                    ->helperText('Tiempo de descanso o preparación entre citas.')
                                    ->numeric()
                                    ->required()
                                    ->suffix('min')
                                    ->minValue(0),
                            ]),
                    ]),

                Section::make('Configuración Semanal')
                    ->description('Define los días y horas en los que tu negocio está disponible para recibir reservas.')
                    ->schema([
                        Repeater::make(self::AVAILABILITIES_KEY)
                            ->label('Días de la semana')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextInput::make('day_name')
                                            ->label('Día')
                                            ->disabled()
                                            ->dehydrated(false),
                                        Toggle::make('is_open')
                                            ->label('Abierto')
                                            ->inline(false)
                                            ->reactive(),
                                        TimePicker::make('start_time')
                                            ->label('Desde')
                                            ->required(fn ($get) => $get('is_open'))
                                            ->visible(fn ($get) => $get('is_open')),
                                        TimePicker::make('end_time')
                                            ->label('Hasta')
                                            ->required(fn ($get) => $get('is_open'))
                                            ->visible(fn ($get) => $get('is_open'))
                                            ->after(fn ($get) => $get('start_time')),
                                    ]),
                            ])
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->columns(1),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $tenant = auth()->user()->tenants()->first();
        $state = $this->form->getState();

        if (!$tenant) {
            return;
        }

        // Guardar Jerarquía de Tiempos
        $tenant->setSetting('booking_slot_size', (int) $state['booking_slot_size']);
        $tenant->setSetting('default_service_duration', (int) $state['default_service_duration']);
        $tenant->setSetting('buffer_time_between_bookings', (int) $state['buffer_time_between_bookings']);

        // Guardar Disponibilidad Semanal
        foreach ($state[self::AVAILABILITIES_KEY] as $dayData) {
            Availability::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'day_of_week' => $dayData['day_of_week'],
                ],
                [
                    'is_open' => $dayData['is_open'],
                    'start_time' => $dayData['is_open'] ? $dayData['start_time'] : null,
                    'end_time' => $dayData['is_open'] ? $dayData['end_time'] : null,
                ]
            );
        }

        Notification::make()
            ->title('Horarios actualizados')
            ->success()
            ->send();
    }
}
