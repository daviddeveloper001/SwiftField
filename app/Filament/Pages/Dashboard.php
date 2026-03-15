<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('my_qr')
                ->label('Mi Código QR')
                ->icon('heroicon-o-qr-code')
                ->color('amber')
                ->url(fn () => route('filament.admin.pages.download-qr', ['tenant' => auth()->user()->tenants()->first()?->slug]))
                ->button(),
        ];
    }
}
