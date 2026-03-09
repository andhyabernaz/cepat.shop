<?php

namespace App\Http\Controllers\Frontend;

use Exception;
use App\Models\Order;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use App\Services\Shipping\CourierService;

class ShippingController extends Controller
{

   public function __construct(
      protected CourierService $courierService
   ) {}

   public function getCost(Request $request)
   {
      $request->validate([
         "destination"   => 'required',
         "weight"        => 'nullable|numeric',
         'items'         => 'nullable|array',
         'courier'      => 'nullable'
      ]);

      try {

         $data = $this->courierService->getCosts($request->all());
         return ApiResponse::success($data);
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }
   public function trackingWaybill($order_id)
   {

      $cacheKey = 'tracking_waybill_' . $order_id;
      $order = Order::find($order_id);
      $data = [];
      if (Cache::has($cacheKey)) {
         $data = Cache::get($cacheKey);
         return ApiResponse::success($data);
      }


      try {
         if ($order && $order->canTrackingWaybill()) {

            $responseData = $this->courierService->trackingWaybill($order);

            foreach ($responseData['histories'] as $item) {

               $order->histories()->updateOrCreate([
                  'manifest_code' => $item['manifest_code'],
               ], $item);
            }

            if ($responseData['delivered'] == true) {
               $order->update([
                  'received_at' => $responseData['received_at']
               ]);
            }
            $data['tracking_status'] = "Tracking waybill Success";
         }

         $data['shipping_status'] = $order->received_at ? 'DELIVERED' : 'ON PROCESS';
         $data['tracking_status'] = "Can't tracking waybill";
         $data['histories'] =  $order->histories;
         Cache::put($cacheKey, $data, now()->addHours(2));
      } catch (Exception $e) {
         $data['shipping_status'] = $order->received_at ? 'DELIVERED' : 'ON PROCESS';
         $data['tracking_status'] = $e->getMessage();
         $data['histories'] =  $order->histories;
      }

      // Log::debug($data);
      return ApiResponse::success($data);
   }

   public function searchAddress(Request $request)
   {

      $data = $this->courierService->searchAddress($request);

      return ApiResponse::success($data);
   }
   public function configAddress(Request $request)
   {
      $data = $this->courierService->configAddress($request);

      return ApiResponse::success($data);
   }

   public function getCouriers(Request $request)
   {
      $data = $this->courierService->getCouriers($request);

      return ApiResponse::success($data);
   }
}
