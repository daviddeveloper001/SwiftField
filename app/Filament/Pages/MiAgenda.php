<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\BookingCalendarWidget;
use BackedEnum;

class MiAgenda extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-calendar';
    protected string $view = 'filament.pages.mi-agenda';
    protected static ?string $navigationLabel = 'Mi Agenda';
    protected static ?string $title = 'Mi Agenda';
    protected static ?int $navigationSort = 1;

    protected function getHeaderWidgets(): array
    {
        return [
            BookingCalendarWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('blockTime')
                ->label('Bloquear Horario')
                ->icon('heroicon-o-lock-closed')
                ->color('gray')
                ->form([
                    \Filament\Forms\Components\DateTimePicker::make('start_time')
                        ->label('Desde')
                        ->required(),
                    \Filament\Forms\Components\DateTimePicker::make('end_time')
                        ->label('Hasta')
                        ->required()
                        ->after('start_time'),
                    \Filament\Forms\Components\TextInput::make('reason')
                        ->label('Razón (opcional)')
                        ->maxLength(255),
                ])
                ->action(function (array $data) {
                    $tenant = \Filament\Facades\Filament::getTenant();
                    if ($tenant) {
                        \App\Models\AvailabilityBlock::create([
                            'tenant_id' => $tenant->id,
                            'start_time' => $data['start_time'],
                            'end_time' => $data['end_time'],
                            'reason' => $data['reason'],
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Horario bloqueado')
                            ->success()
                            ->send();

                        // Refresh page to show new block
                        redirect(request()->header('Referer'));
                    }
                })
        ];
    }
}
