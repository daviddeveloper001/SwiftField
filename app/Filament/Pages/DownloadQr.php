<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Tenant;
use App\Services\QrCodeService;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Response;
use BackedEnum;

class DownloadQr extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $navigationLabel = 'Mi Código QR';

    protected static ?string $title = 'Descargar Código QR';

    protected string $view = 'filament.pages.download-qr';

    public string $qrHtml = '';

    public function mount(QrCodeService $qrService): void
    {
        $tenant = auth()->user()->tenants()->first();

        if ($tenant) {
            $this->qrHtml = $qrService->generateForTenant($tenant, 'svg', 400);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_svg')
                ->label('Descargar SVG')
                ->icon('heroicon-o-arrow-down-tray')
                ->action('downloadSvg'),
            
            Action::make('download_png')
                ->label('Descargar PNG')
                ->icon('heroicon-o-photo')
                ->color('success')
                ->action('downloadPng'),
        ];
    }

    public function downloadSvg(QrCodeService $qrService)
    {
        $tenant = auth()->user()->tenants()->first();
        if (!$tenant) return;

        $content = $qrService->getRawForDownload($tenant, 'svg', 1000);
        
        return Response::streamDownload(function () use ($content) {
            echo $content;
        }, "qr-{$tenant->slug}.svg");
    }

    public function downloadPng(QrCodeService $qrService)
    {
        $tenant = auth()->user()->tenants()->first();
        if (!$tenant) return;

        $content = $qrService->getRawForDownload($tenant, 'png', 1000);
        
        return Response::streamDownload(function () use ($content) {
            echo $content;
        }, "qr-{$tenant->slug}.png");
    }
}
