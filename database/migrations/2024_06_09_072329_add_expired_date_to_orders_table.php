<?php

use App\Models\Order;
use Illuminate\Support\Carbon;
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
         $table->timestamp('expired_at')->after('updated_at')->nullable();
         $table->text('cancellation_reason')->nullable();
      });

      $orders = Order::with('transaction')->whereNull('expired_at')->get();

      foreach ($orders as $order) {
         $expired_at = Carbon::createFromTimestamp($order->transaction->expired_time);
         $order->expired_at = $expired_at;
         $order->save();
      }
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::table('orders', function (Blueprint $table) {
         $table->dropColumn('expired_at');
         $table->dropColumn('cancellation_reason');
      });
   }
};
