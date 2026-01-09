<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     Schema::create('featured_product_subscriptions', function (Blueprint $table) {
    //         $table->uuid('id')->primary();
    //         $table->foreignUuid('store_id')->constrained()->onDelete('cascade');
    //         $table->foreignUuid('plan_id')->constrained('featured_product_plans')->onDelete('cascade');
    //         $table->string('reference')->unique();
    //         $table->timestamp('starts_at');
    //         $table->timestamp('ends_at')->nullable();
    //         $table->boolean('is_active')->default(true);
    //         $table->timestamp('last_refreshed_at')->nullable();
    //         $table->softDeletes();
    //         $table->timestamps();
    //         $table->index(['store_id', 'is_active']);
    //         $table->index(['plan_id', 'is_active']);
    //     });
    // }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_product_subscriptions');
    }
};
