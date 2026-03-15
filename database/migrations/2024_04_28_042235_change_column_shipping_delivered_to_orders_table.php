<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnShippingDeliveredToOrdersTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::table('orders', function (Blueprint $table) {
         $table->renameColumn('shipping_delivered', 'shipping_at');
         $table->renameColumn('shipping_received', 'received_at');
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
         $table->renameColumn('shipping_at', 'shipping_delivered');
         $table->renameColumn('received_at', 'shipping_received');
      });
   }
}
