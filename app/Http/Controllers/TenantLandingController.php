<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Presenters\LandingPresenter;
use Illuminate\View\View;

class TenantLandingController extends Controller
{
    /**
     * Muestra la landing page dinámica del Tenant.
     */
    public function show(string $slug): View
    {
        $tenant = Tenant::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Instanciamos el Presenter con el Tenant
        $presenter = new LandingPresenter($tenant);
        
        // Obtenemos la configuración (con fallbacks automáticos)
        $result = $presenter->getConfig();

        // Renderizamos usando la nueva vista maestra dinámica
        return view('landing.main', [
            'tenant' => $tenant,
            'config' => $result->data,
            'presenter' => $presenter
        ]);
    }
}
