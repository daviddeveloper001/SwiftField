<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\SuperAdminPanelProvider;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\LogContext::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->context(function () {
            try {
                if (class_exists(\Filament\Facades\Filament::class) && \Filament\Facades\Filament::isServing() && \Filament\Facades\Filament::hasTenancy()) {
                    if ($tenant = \Filament\Facades\Filament::getTenant()) {
                        return ['tenant_id' => $tenant->id];
                    }
                }
            } catch (\Throwable $e) {}

            return [];
        });

        $exceptions->report(function (\Throwable $e) {
            // Ignore 404s
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                return;
            }

            try {
                $tenantId = null;
                if (class_exists(\Filament\Facades\Filament::class) && \Filament\Facades\Filament::isServing() && \Filament\Facades\Filament::hasTenancy()) {
                    $tenantId = \Filament\Facades\Filament::getTenant()?->id;
                }

                \App\Models\SystemException::create([
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'stack_trace' => $e->getTraceAsString(),
                    'user_id' => auth()->id() ?? null,
                    'tenant_id' => $tenantId,
                    'url' => request()->fullUrl(),
                    'method' => request()->method(),
                    'status' => 'open',
                ]);
            } catch (\Throwable $th) {
                // Silently fail if logging fails to prevent loops
            }
        });
    })
    ->withProviders([
        AdminPanelProvider::class,
        SuperAdminPanelProvider::class,
    ])
    ->create();
