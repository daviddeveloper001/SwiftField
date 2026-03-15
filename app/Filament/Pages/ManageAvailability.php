<?php

declare(strict_types=1);

namespace App\Filament\Pages;

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

    protected static array $days = [
        0 => 'Domingo',
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
    ];

    public function mount(): void
    {
        $tenant = auth()->user()->tenants()->first();

        if ($tenant) {
            $availabilities = Availability::where('tenant_id', $tenant->id)
                ->orderBy('day_of_week')
                ->get();

            $formattedData = [];

            // Ensure all 7 days are present in the UI
            foreach (static::$days as $dayIndex => $dayName) {
                $availability = $availabilities->firstWhere('day_of_week', $dayIndex);
                
                $formattedData[] = [
                    'day_of_week' => $dayIndex,
                    'day_name' => $dayName,
                    'is_open' => $availability ? $availability->is_open : ($dayIndex != 0), // Default open except Sunday
                    'start_time' => $availability ? $availability->start_time?->format('H:i') : '08:00',
                    'end_time' => $availability ? $availability->end_time?->format('H:i') : '18:00',
                ];
            }

            $this->form->fill(['availabilities' => $formattedData]);
        }
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Configuración Semanal')
                    ->description('Define los días y horas en los que tu negocio está disponible para recibir reservas.')
                    ->schema([
                        Repeater::make('availabilities')
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

        foreach ($state['availabilities'] as $dayData) {
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
