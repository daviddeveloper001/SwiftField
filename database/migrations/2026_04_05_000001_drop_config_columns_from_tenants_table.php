<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * ADVERTENCIA: Ejecutar solo DESPUÉS de correr el comando 'swiftfield:migrate-settings'.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Eliminamos las columnas de configuración que ahora residen en tenant_settings
            $table->dropColumn([
                'branding_config',
                'landing_config',
                'whatsapp_config'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->json('branding_config')->nullable();
            $table->json('landing_config')->nullable();
            $table->json('whatsapp_config')->nullable();
        });
    }
};
