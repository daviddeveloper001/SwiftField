<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\TenantSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrateTenantSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swiftfield:migrate-settings {--force : Proceder sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra TODAS las configuraciones JSONB de la tabla tenants a la nueva tabla tenant_settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando migración exhaustiva de configuraciones de Tenants...');

        // Leemos directamente de la tabla para saltar los accessors del modelo
        $tenants = DB::table('tenants')->get();

        if ($tenants->isEmpty()) {
            $this->warn('No se encontraron tenants para migrar.');
            return;
        }

        $bar = $this->output->createProgressBar($tenants->count());
        $bar->start();

        $migratedCount = 0;
        $errorsCount = 0;

        foreach ($tenants as $tenantData) {
            try {
                DB::transaction(function () use ($tenantData, &$migratedCount) {
                    $hasChanges = false;
                    $tenant = Tenant::find($tenantData->id);

                    if (!$tenant) return;

                    // Lista de configuraciones legacy a migrar (accedemos al objeto stdClass directamente)
                    $legacyConfigs = [
                        'whatsapp_config',
                        'branding_config',
                        'landing_config'
                    ];

                    foreach ($legacyConfigs as $column) {
                        $value = $tenantData->$column;
                        if (!empty($value)) {
                            // Decodificar el JSON si viene como string desde DB::table
                            $decoded = is_string($value) ? json_decode($value, true) : $value;
                            
                            if ($decoded !== null) {
                                $tenant->setSetting($column, $decoded);
                                $hasChanges = true;
                            }
                        }
                    }

                    // Inicializar Jerarquía de Tiempos
                    $timeKeys = [
                        'booking_slot_size' => 15,
                        'default_service_duration' => 60,
                        'buffer_time_between_bookings' => 0,
                    ];

                    foreach ($timeKeys as $key => $defaultValue) {
                        if (!$tenant->settings()->where('key', $key)->exists()) {
                            $tenant->setSetting($key, $defaultValue);
                            $hasChanges = true;
                        }
                    }

                    if ($hasChanges) {
                        $migratedCount++;
                    }
                });
            } catch (\Exception $e) {
                $errorsCount++;
                Log::error("Error migrando tenant ID {$tenantData->id}: " . $e->getMessage());
                $this->error("\nError en Tenant ID {$tenantData->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Migración completada:");
        $this->line("- Tenants procesados: <info>{$migratedCount}</info>");
        if ($errorsCount > 0) {
            $this->line("- Errores: <error>{$errorsCount}</error>");
        }

        $this->info("\nCampos migrados exitosamente.");
    }
}
