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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('aff_is_active')->default(false);
            $table->boolean('aff_is_percentage')->default(false);
            $table->integer('aff_amount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('aff_is_active');
            $table->dropColumn('aff_is_percentage');
            $table->dropColumn('aff_amount');
        });
    }
};
