<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\View;

class TenantLandingController extends Controller
{
    public function show(string $slug): View
    {
        $tenant = Tenant::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $landing = $tenant->landing_config ?? [];

        $viewData = [
            'tenant' => $tenant,
            'headline' => $landing['headline'] ?? 'Agenda tu servicio profesional en segundos',
            'subheadline' => $landing['subheadline'] ?? 'Bienvenido a ' . $tenant->name,
            'description' => $landing['description']
                ?? $tenant->branding_config['description']
                ?? 'Estamos listos para atenderte con la calidad de siempre.',
            'features' => $landing['features'] ?? [],
        ];

        // Obtenemos el template HTML de la base de datos o un fallback genérico
        $htmlTemplate = $landing['html_template'] ?? $this->getDefaultHtmlTemplate();

        // Renderizamos el HTML dinámicamente usando Blade
        $renderedContent = Blade::render($htmlTemplate, $viewData);

        return view('tenant-landing', [
            'tenant' => $tenant,
            'content' => $renderedContent
        ]);
    }

    /**
     * Template por defecto si el tenant no tiene uno configurado.
     */
    private function getDefaultHtmlTemplate(): string
    {
        return <<<'BLADE'
            <div class="py-12 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="lg:text-center mb-12">
                        <h2 class="text-base text-primary font-semibold tracking-wide uppercase">{{ $subheadline }}</h2>
                        <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                            {{ $headline }}
                        </p>
                        <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                            {{ $description }}
                        </p>
                    </div>

                    <div class="mt-10">
                        <livewire:booking-form :tenantId="$tenant->id" />
                    </div>
                </div>
            </div>
        BLADE;
    }
}
