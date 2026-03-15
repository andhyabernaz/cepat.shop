<?php

use App\Models\SaldoConfig;
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
        Schema::create('saldo_configs', function (Blueprint $table) {
            $table->id();
            $table->integer('min_deposit_amount')->default(10000);
            $table->integer('min_withdraw_amount')->default(50000);
            $table->integer('max_withdraw_amount')->default(0);
            $table->integer('withdraw_fee')->default(0);
            $table->text('deposit_description')->nullable();
            $table->text('withdraw_description')->nullable();
            $table->text('withdraw_channels')->nullable();
        });

        SaldoConfig::create();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldo_configs');
    }
};
