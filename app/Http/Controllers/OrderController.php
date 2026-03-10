<?php

namespace App\Http\Controllers;

use App\Events\OrderCancelled;
use App\Models\Order;
use App\Models\Product;
use App\Events\OrderPaid;
use App\Events\OrderFailed;
use App\Helpers\ApiResponse;
use App\Jobs\ProcessAffiliateOrderJob;
use Illuminate\Http\Request;
use App\Models\ProductVarian;
use Illuminate\Support\Facades\DB;
use App\Models\NotificationTemplate;
use App\Services\Order\OrderService;
use Illuminate\Support\Facades\Cache;

class OrderController extends Controller
{
   public function __construct(protected OrderService $orderService) {}
   public function index(Request $request)
   {
      try {

         $search = $request->search ?? null;
         $status = $request->status ?? null;

         $instance = Order::with('transaction')->orderByDesc('updated_at');

         if ($search) {
            $instance->where(function ($q) use ($search) {
               $q->where('customer_whatsapp', 'LIKE', '%' . $search . '%')
                  ->orWhere('order_ref', $search)
                  ->orWhere('customer_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('customer_email', 'LIKE', '%' . $search . '%');
            });
         }
         if ($status && $status != 'ALL') {
            $instance->where('order_status', $status);
         }

         $data = $instance->paginate($request->per_page ?? 10)->withQueryString();

         return ApiResponse::success($data);
      } catch (\Throwable $th) {

         return ApiResponse::failed($th);
      }
   }

   public function show($id)
   {
      try {
         $data = Order::with(['items', 'transaction'])->findOrFail($id);
         return ApiResponse::success($data);
      } catch (\Throwable $th) {
         return ApiResponse::failed($th);
      }
   }

   public function destroy($id)
   {

      $order = Order::findOrFail($id);

      $order->delete();

      return ApiResponse::success();
   }
   public function paymentAccepted($id)
   {
      DB::beginTransaction();
      try {
         $order = Order::findOrFail($id);
         OrderPaid::dispatch($order);
         DB::commit();
         if ($order->is_deposit_type()) {
            $messageEvent = NotificationTemplate::ORDER_COMPLETED;
         } else {
            $messageEvent = NotificationTemplate::ORDER_PAYMENT_CONFIRMED;
         }
         return ApiResponse::withEvent($messageEvent)->success();
      } catch (\Throwable $th) {
         DB::rollBack();
         return ApiResponse::failed($th);
      }
   }

   public function statusOptions()
   {
      $data = Order::statusOptions();

      return ApiResponse::success($data);
   }

   public function inputResi(Request $request)
   {
      $request->validate([
         'order_id' => ['required'],
         'resi' => ['required'],
      ]);

      try {
         $order = Order::findOrFail($request->order_id);

         $order->shipping_courier_code = $request->resi;
         $order->updated_at = now();

         $event = null;

         if ($request->boolean('update_to_ship')) {
            $order->shipping_at = now();
            $order->order_status = 'SHIPPING';
            $order->pushHistory('Pesanan dikirim');
            $event = NotificationTemplate::ORDER_SHIPPING;

            $order->dispatchEventMessage($event);
         }

         $order->save();

         return ApiResponse::withEvent($event)->success();
      } catch (\Throwable $th) {
         // $order->dispatchEventMessage($event);

         return ApiResponse::failed($th);
      }
   }

   public function updateStatusOrder(Request $request)
   {
      $request->validate([
         'order_id' => 'required',
         'status' => 'required',
      ]);

      $order = Order::findOrFail($request->order_id);
      $order->order_status = $request->status;
      $order->updated_at = now();

      $order->save();

      $event = null;

      if ($request->status == 'SHIPPING') {
         $order->pushHistory('Pesanan dikirim');
         $event = NotificationTemplate::ORDER_SHIPPING;
      }
      if ($request->status == 'COMPLETE') {
         $event = NotificationTemplate::ORDER_COMPLETED;

         $order->transaction()->update([
            'status' => 'PAID'
         ]);

         $order->pushHistory('Pesanan selesai');
      }

      if ($order->order_status == 'CANCELED') {
         $event = NotificationTemplate::ORDER_FAILED;
         foreach ($order->items as $item) {

            Product::updateStock($item->sku, $item->quantity);
         }
         $order->pushHistory('Pesanan dibatalkan');
      }

      $order->dispatchEventMessage($event);

      return ApiResponse::withEvent($event)->success($order);
   }
   public function completionOrder($id)
   {
      DB::beginTransaction();
      try {
         $event = NotificationTemplate::ORDER_COMPLETED;
         $order = Order::findOrFail($id);
         $this->orderService->completionOrder($order);
         $order->dispatchEventMessage($event);
         DB::commit();
   
         return ApiResponse::withEvent($event)->success($order);
      } catch (\Throwable $th) {
         DB::rollBack();
         return ApiResponse::failed($th);
      }

   }
   public function shipOrder($id)
   {

      $order = Order::findOrFail($id);
      $event = NotificationTemplate::ORDER_SHIPPING;

      $order->update([
         'order_status' => Order::SHIPPING
      ]);

      $order->pushHistory('Pesanan dikirim');

      $order->dispatchEventMessage($event);

      return ApiResponse::withEvent($event)->success($order);
   }
   public function cancelOrder(Request $request, $id)
   {

      try {

         DB::beginTransaction();

         $request->validate([
            'cancellation_reason' => ['required'],
         ]);

         $order = Order::findOrFail($id);

         OrderCancelled::dispatch($order, $request->cancellation_reason);

         DB::commit();

         $event = NotificationTemplate::ORDER_FAILED;
         $order->dispatchEventMessage($event);

         return ApiResponse::withEvent($event)->success();
      } catch (\Exception $e) {
         DB::rollBack();

         return ApiResponse::failed($e);
      }
   }
}
