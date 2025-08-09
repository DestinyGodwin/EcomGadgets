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
        Schema::table('featured_product_plans', function (Blueprint $table) {
        $table->unsignedInteger('featured_limit')->default(0)->after('refresh_interval_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('featured_product_plans', function (Blueprint $table) {
            //
        });
    }
};
