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
            $table->boolean('auto_confirm')->default(false)->after('is_active');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('is_auto_confirmed')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('auto_confirm');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('is_auto_confirmed');
        });
    }
};
