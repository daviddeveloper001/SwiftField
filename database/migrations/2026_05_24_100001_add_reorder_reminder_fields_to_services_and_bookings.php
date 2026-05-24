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
            $table->boolean('has_reorder_reminder')->default(false)
                ->after('auto_confirm')
                ->comment('Activa el sistema de recordatorios de re-agendamiento');

            $table->unsignedInteger('reorder_value')->nullable()
                ->after('has_reorder_reminder')
                ->comment('Cantidad numérica del periodo de re-agendamiento');

            $table->string('reorder_unit')->nullable()
                ->after('reorder_value')
                ->comment('Unidad de tiempo: days, weeks, months');

            $table->text('reorder_message_template')->nullable()
                ->after('reorder_unit')
                ->comment('Plantilla de texto plano con etiquetas {cliente}, {servicio}, {link_agenda}');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()
                ->after('scheduled_at')
                ->index()
                ->comment('Fecha en que la cita fue marcada como completada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'has_reorder_reminder',
                'reorder_value',
                'reorder_unit',
                'reorder_message_template',
            ]);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('completed_at');
        });
    }
};
