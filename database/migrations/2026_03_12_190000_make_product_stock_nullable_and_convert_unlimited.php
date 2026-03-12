<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('products')
            ->where('stock', -1)
            ->update(['stock' => null]);

        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('products')
            ->whereNull('stock')
            ->update(['stock' => -1]);

        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock')->nullable(false)->change();
        });
    }
};
