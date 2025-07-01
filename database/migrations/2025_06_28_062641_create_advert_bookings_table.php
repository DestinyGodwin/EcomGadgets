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
        Schema::create('advert_bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('store_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2)->default(0);
            $table->foreignUuid('state_id')->constrained();
            $table->foreignUuid('plan_id')->nullable()->constrained('advert_plans')->nullOnDelete();
            $table->string('reference')->unique()->nullable();
            $table->string('title');
            $table->string('link')->nullable();
            $table->string('image')->nullable();
            $table->string('transaction_id')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_dummy')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advert_bookings');
    }
};
