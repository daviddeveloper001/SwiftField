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
}
