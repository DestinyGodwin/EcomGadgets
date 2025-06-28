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
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->foreignId('state_id');
            $table->foreignId('plan_id')->constrained('advert_plans');
            $table->decimal('amount', 10, 2);
            $table->string('reference')->unique();
            $table->string('transaction_id')->nullable();
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
        Schema::dropIfExists('advert_bookings');
    }
};
