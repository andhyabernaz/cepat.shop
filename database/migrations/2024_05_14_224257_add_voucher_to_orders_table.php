<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVoucherToOrdersTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::table('orders', function (Blueprint $table) {
         $table->integer('voucher_discount')->default(0);
         $table->integer('shipping_discount')->default(0);
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::table('orders', function (Blueprint $table) {
         $table->dropColumn('voucher_discount');
         $table->dropColumn('shipping_discount');
      });
   }
}
