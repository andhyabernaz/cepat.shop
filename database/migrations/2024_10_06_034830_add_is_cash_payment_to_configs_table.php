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
         $table->boolean('is_cash_payment')->after('is_order_pickup')->default(false);
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::table('configs', function (Blueprint $table) {
         $table->dropColumn('is_cash_payment');
      });
   }
};
