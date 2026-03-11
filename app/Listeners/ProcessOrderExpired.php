<?php

namespace App\Listeners;

use App\Models\Order;
use App\Models\Transaction;
use App\Events\OrderExpired;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessOrderExpired
{
   /**
    * Create the event listener.
    */
   public function __construct()
   {
      //
   }

   /**
    * Handle the event.
    */
   public function handle(OrderExpired $event): void
   {
      $order = $event->order;
      $order->update([
         'order_status' => Order::CANCELED,
         'cancellation_reason' => $event->reason
      ]);

      $order->transaction()->update([
         'status' => Transaction::EXPIRED,
         'note' =>  $event->reason
      ]);

      $msg = "Pesanan dibatalkan, {$event->reason}";
      $order->pushHistory($msg);
      $order->flush_cache();
   }
}
