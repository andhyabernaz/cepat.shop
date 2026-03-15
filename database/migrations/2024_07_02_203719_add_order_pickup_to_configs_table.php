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
         $table->boolean('is_order_pickup')->default(0);
         $table->boolean('is_local_shipping')->default(0);
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::table('configs', function (Blueprint $table) {
         $table->dropColumn('is_order_pickup');
         $table->dropColumn('is_local_shipping');
      });
   }
};
