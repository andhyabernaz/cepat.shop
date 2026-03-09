<?php

use App\Models\Order;
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
      Schema::table('orders', function (Blueprint $table) {
         //
      });

      Order::whereNull('shipping_type')->where('shipping_courier_id', '=', 'PICKUP')->update([
         'shipping_type' => Order::SHIPPING_PICKUP
      ]);
      Order::whereNull('shipping_type')->where('shipping_courier_id', '=', 'COD')->update([
         'shipping_type' => Order::SHIPPING_COD
      ]);
      Order::whereNull('shipping_type')->update([
         'shipping_type' => Order::SHIPPING_COURIER
      ]);
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::table('orders', function (Blueprint $table) {
         //
      });
   }
};
