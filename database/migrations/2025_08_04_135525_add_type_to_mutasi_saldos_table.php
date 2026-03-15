<?php

use App\Models\MutasiSaldo;
use Illuminate\Support\Facades\DB;
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
        Schema::table('mutasi_saldos', function (Blueprint $table) {
            $table->string('category')->nullable();
        });

        DB::table('mutasi_saldos')->update([
            'category' => MutasiSaldo::CATEGORY_DEFAULT
        ]);

         Schema::table('mutasi_saldos', function (Blueprint $table) {
            $table->string('category')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mutasi_saldos', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
