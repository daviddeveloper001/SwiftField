<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenant_settings', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->change();
            
            // Re-define unique constraint to allow null tenant_id
            $table->dropUnique(['tenant_id', 'key']);
            $table->unique(['tenant_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_settings', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable(false)->change();
            
            $table->dropUnique(['tenant_id', 'key']);
            $table->unique(['tenant_id', 'key']);
        });
    }
};
