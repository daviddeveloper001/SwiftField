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
        Schema::create('system_exceptions', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->string('file');
            $table->integer('line');
            $table->longText('stack_trace');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('url')->nullable();
            $table->string('method')->nullable();
            $table->string('status')->default('open')->index();
            $table->softDeletes();
            $table->timestamps();

            // Optimal indexing
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_exceptions');
    }
};
