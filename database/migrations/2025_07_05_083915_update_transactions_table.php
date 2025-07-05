<?php

use App\Enums\V1\TransactionType;
use Illuminate\Support\Facades\DB;
use App\Enums\V1\TransactionStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('type')->change();
            $table->string('status')->change();
        });

        DB::statement("ALTER TABLE transactions ADD CONSTRAINT chk_transaction_type CHECK (type IN ('" . implode("','", TransactionType::values()) . "'))");
        DB::statement("ALTER TABLE transactions ADD CONSTRAINT chk_transaction_status CHECK (status IN ('" . implode("','", TransactionStatus::values()) . "'))");
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            DB::statement("ALTER TABLE transactions DROP CONSTRAINT IF EXISTS chk_transaction_type");
            DB::statement("ALTER TABLE transactions DROP CONSTRAINT IF EXISTS chk_transaction_status");
        });
    }
};