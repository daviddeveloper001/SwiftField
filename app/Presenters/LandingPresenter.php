<?php

namespace App\Presenters;

use App\Models\Tenant;
use App\DTOs\Landing\LandingConfigDTO;
use App\Support\Result\Result;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Collection;

class LandingPresenter
{
    public function __construct(
        private readonly Tenant $tenant
    ) {}

    /**
     * Prepara la configuración para la Landing del Tenant
     */
    public function getConfig(): Result
    {
        try {
            // El accessor getLandingConfigAttribute ya maneja los fallbacks
            $dto = LandingConfigDTO::fromArray($this->tenant->landing_config);
            
            return Result::success($dto);
        } catch (\Exception $e) {
            return Result::failure("Error procesando configuración de Landing: " . $e->getMessage());
        }
    }

    /**
     * Renderiza las secciones de forma dinámica
     */
    public function renderSections(LandingConfigDTO $config): string
    {
        return collect($config->sections)
            ->map(function (array $section) use ($config) {
                $viewName = "landing.sections.{$section['type']}";

                if (!View::exists($viewName)) {
                    return "<!-- Seccion no encontrada: {$section['type']} -->";
                }

                $viewData = [
                    'tenant' => $this->tenant,
                    'config' => $config,
                    'content' => $section['content'] ?? [],
                    'order' => $section['order'] ?? 0
                ];

                // Inyección dinámica de datos según el tipo de sección
                if ($section['type'] === 'services') {
                    $viewData['services'] = $this->tenant->services()
                        ->where('is_active', true)
                        ->get();
                }

                return View::make($viewName, $viewData)->render();
            })
            ->implode("\n");
    }
}
