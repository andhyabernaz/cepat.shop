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
            $table->string('mapbox_access_token')->nullable();
            $table->text('local_shipping_costs')->nullable();
            $table->string('warehouse_coordinate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            $table->dropColumn('mapbox_access_token');
            $table->dropColumn('warehouse_coordinate');
            $table->dropColumn('local_shipping_costs');
        });
    }
};
