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
        Schema::create('notification_channels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('driver')->comment('Fully qualified class name del driver');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // Insertar el canal de WhatsApp como canal por defecto
        \Illuminate\Support\Facades\DB::table('notification_channels')->insert([
            'name'       => 'WhatsApp',
            'driver'     => \App\Services\Messaging\Drivers\WhatsAppNotificationDriver::class,
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_channels');
    }
};
