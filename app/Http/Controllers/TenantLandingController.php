<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\View\View;

class TenantLandingController extends Controller
{
    public function show(string $slug): View
    {
        $tenant = Tenant::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $landing = $tenant->landing_config ?? [];

        return view('tenant-landing', [
            'tenant' => $tenant,
            'headline' => $landing['headline'] ?? 'Agenda tu servicio profesional en segundos',
            'subheadline' => $landing['subheadline'] ?? 'Bienvenido a ' . $tenant->name,
            'description' => $landing['description']
                ?? $tenant->branding_config['description']
                ?? 'Estamos listos para atenderte con la calidad de siempre.',
            'features' => $landing['features'] ?? [],
        ]);
    }
}
