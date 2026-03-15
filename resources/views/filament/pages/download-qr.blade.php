<x-filament-panels::page>
    <div class="flex flex-col items-center justify-center py-12 bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="mb-8">
            {!! $qrHtml !!}
        </div>

        <div class="text-center max-w-sm">
            <h3 class="text-lg font-semibold text-gray-900">Código QR del Negocio</h3>
            <p class="mt-2 text-sm text-gray-500">
                Este código redirige directamente a tu página de reservas. Imprímelo y muéstralo en tu local para que
                tus clientes agenden rápidamente.
            </p>
        </div>
    </div>
</x-filament-panels::page>
