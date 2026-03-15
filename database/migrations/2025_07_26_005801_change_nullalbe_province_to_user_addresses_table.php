<?php

use App\Models\Config;
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
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->string('province')->nullable()->change();
        });

        DB::table('user_addresses')->where('courier_service', Config::RAJAONGKIR_SERVICE)->delete();
        DB::table('configs')->update(['warehouse_address' => NULL]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
             $table->string('province')->nullable()->change();
        });
    }
};
