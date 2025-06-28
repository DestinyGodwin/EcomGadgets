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
        Schema::create('featured_product_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('featured_product_plans');
            $table->string('reference')->unique();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_product_logs');
    }
};
