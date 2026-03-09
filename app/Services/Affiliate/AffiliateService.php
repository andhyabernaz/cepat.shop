<?php

namespace App\Services\Affiliate;

use App\Models\MutasiSaldo;
use App\Jobs\DispatchEventJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\NotificationTemplate;
use App\Jobs\DispatchNotificationJob;
use App\Models\Affiliate;
use Illuminate\Support\Facades\Cache;

class AffiliateService
{
   public function processOrder($order)
   {
      if ($order->affiliate_processed == 1) {
         return 1;
      }
      $items = $order->items;

      foreach ($items as $item) {

         if ($item->affiliate_id && $item->price > 0) {

            $product = DB::table('products')
               ->where('id', $item->product_id)
               ->where('aff_is_active', 1)
               ->first();

            $userAffiliate = DB::table('users')
               ->select('users.*')
               ->join('affiliates', 'users.id', 'affiliates.user_id')
               ->where('affiliates.status', Affiliate::Active)
               ->where('users.id', $item->affiliate_id)
               ->first();

            if ($product && $userAffiliate) {

               $komisi = 0;

               $totalPrice = intval($item->price) * intval($item->quantity);

               if ($totalPrice > 0) {

                  if ($product->aff_is_percentage == true) {
                     $komisi = intval($totalPrice) * intval($product->aff_amount) / 100;
                  } else {
                     $komisi = intval($product->aff_amount);
                  }

                  $desc = 'Komisi affiliate ' . $product->title . ' Ref #' . $order->order_ref;
                  $payload = [
                     'user_id' => $userAffiliate->id,
                     'type' => MutasiSaldo::TYPE_IN,
                     'category' => MutasiSaldo::CATEGORY_AFFILIATE,
                     'amount' => $komisi,
                     'description' => $desc,
                  ];

                  $event = NotificationTemplate::REFERAL_COMMISSION;

                  $mutasiSaldo = MutasiSaldo::create($payload);

                  DispatchNotificationJob::dispatch($event, $mutasiSaldo);

                  if (!Cache::has('is_cron')) {
                     DispatchEventJob::dispatch($event);
                  }
               }
            }
         }
      }

      $order->aff_processed = 1;
      $order->save();
   }
}
