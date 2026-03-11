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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained();
            $table->foreignId('customer_id')->constrained();
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending')->index();
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->decimal('lat', 10, 8)->nullable();  
            $table->decimal('lng', 11, 8)->nullable();
            $table->json('custom_values')->nullable();
            $table->text('internal_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status', 'scheduled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
