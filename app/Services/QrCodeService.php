<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class QrCodeService
{
    /**
     * Generate a QR code for a tenant with branding and optional logo.
     */
    public function generateForTenant(Tenant $tenant, string $format = 'svg', int $size = 300): string
    {
        $url = route('tenant.landing', ['slug' => $tenant->slug]);
        $primaryColorHex = $tenant->branding_config['primary_color'] ?? '#000000';
        $logoUrl = $tenant->branding_config['logo_url'] ?? null;

        [$r, $g, $b] = $this->hexToRgb($primaryColorHex);

        $qr = QrCode::format($format)
            ->size($size)
            ->margin(1)
            ->color($r, $g, $b)
            ->errorCorrection('H');

        if ($logoUrl && Storage::disk('public')->exists($logoUrl)) {
            $logoPath = Storage::disk('public')->path($logoUrl);
            return $qr->merge($logoPath, 0.3, true)->generate($url)->toHtml();
        }

        return $qr->generate($url)->toHtml();
    }

    /**
     * Generate raw QR content for download.
     */
    public function getRawForDownload(Tenant $tenant, string $format = 'png', int $size = 500): mixed
    {
        $url = route('tenant.landing', ['slug' => $tenant->slug]);
        $primaryColorHex = $tenant->branding_config['primary_color'] ?? '#000000';
        $logoUrl = $tenant->branding_config['logo_url'] ?? null;

        [$r, $g, $b] = $this->hexToRgb($primaryColorHex);

        $qr = QrCode::format($format)
            ->size($size)
            ->margin(2)
            ->color($r, $g, $b)
            ->errorCorrection('H');

        if ($logoUrl && Storage::disk('public')->exists($logoUrl)) {
            $logoPath = Storage::disk('public')->path($logoUrl);
            return $qr->merge($logoPath, 0.3, true)->generate($url);
        }

        return $qr->generate($url);
    }

    private function hexToRgb(string $hex): array
    {
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        return [$r, $g, $b];
    }
}
