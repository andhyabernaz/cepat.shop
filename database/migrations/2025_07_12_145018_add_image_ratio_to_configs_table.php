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
        Schema::table('configs', function (Blueprint $table) {
            $table->string('card_img_ratio')->default('1');
            $table->string('card_img_fit')->default('cover');
            $table->string('product_img_ratio')->default('1');
            $table->string('product_img_fit')->default('cover');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            $table->dropColumn('card_img_ratio');
            $table->dropColumn('card_img_fit');
            $table->dropColumn('product_img_fit');
            $table->dropColumn('product_img_ratio');
        });
    }
};
