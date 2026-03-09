<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use Illuminate\Support\Facades\Log;
use App\Models\NotificationTemplate;
use App\Services\Order\OrderService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessOrderPaid
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
   public function handle(OrderPaid $event): void
   {
      $order = $event->order;
      (new OrderService())->processOrder($order);

      if ($order->is_deposit_type()) {

         $order->pushHistory('Pesanan Selesai');
      } else {

         $order->pushHistory('Pembayaran dikonfirmasi');
      }
      $order->flush_cache();
   }
}
