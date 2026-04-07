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
        Schema::table('services', function (Blueprint $table) {
            // Cambiar string (varchar(255)) a text para permitir RichEditor
            $table->text('description')->nullable()->change();
            
            // Asegurar que field_definitions sea jsonb para mejor rendimiento en PostgreSQL si se desea
            $table->jsonb('field_definitions')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('description')->nullable()->change();
            $table->json('field_definitions')->nullable()->change();
        });
    }
};
