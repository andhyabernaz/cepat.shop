<?php

namespace App\Listeners;

use App\Models\Order;
use App\Events\OrderFailed;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessOrderFailed
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
   public function handle(OrderFailed $event): void
   {
      $order = $event->order;
      // Log::debug('ProcessingOrderFailed ' . $order->order_ref);
      $order->update([
         'order_status' => Order::CANCELED,
         'cancellation_reason' => $event->reason
      ]);

      $order->transaction()->update([
         'status' => Transaction::FAILED,
         'note' =>  $event->reason
      ]);

      $msg = "Pesanan dibatalkan, {$event->reason}";
      $order->pushHistory($msg);
      $order->flush_cache();
   }
}
