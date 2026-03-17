<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Filament\Pages\ManageSubscription;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = Filament::getTenant();

        // If there is no tenant context, allow the request to proceed.
        if (!$tenant) {
            return $next($request);
        }

        /** @var \App\Models\Tenant $tenant */
        if ($tenant->isExpired()) {
            $subscriptionUrl = ManageSubscription::getUrl(tenant: $tenant);
            
            // Check if already on the subscription page to avoid infinite redirect
            if ($request->url() === $subscriptionUrl) {
                return $next($request);
            }

            return redirect($subscriptionUrl);
        }

        return $next($request);
    }
}
