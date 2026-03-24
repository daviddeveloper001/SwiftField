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
        // For PostgreSQL, the cleanest way in Laravel to update an enum is to change it to string 
        // which allows any value, or recreate the constraint. 
        // Let's use string for better future-proofing in this dynamic scenario.
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending')->change();
        });
    }
};
