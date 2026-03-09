<?php

namespace App\Http\Controllers\Frontend;

use Exception;
use App\Models\User;
use App\Models\Order;
use App\Models\Config;
use App\Models\Product;
use App\Models\Voucher;
use App\Events\OrderPaid;
use App\Models\Affiliate;
use App\Models\OrderItem;
use App\Models\MutasiSaldo;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Enums\PaymentTypeEnum;
use App\Enums\ProductTypeEnum;
use Illuminate\Support\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OrderRequest;
use Illuminate\Support\Facades\Log;
use App\Enums\MutasiSaldoStatusEnum;
use App\Models\NotificationTemplate;
use Illuminate\Support\Facades\Cache;
use App\Services\Payment\TripayService;
use App\Services\Payment\PaymentService;

class FrontOrderController extends Controller
{
   public function __construct(
      protected PaymentService $paymentService
   ) {}

   public function getInvoice($invoice)
   {
      try {
         $data =  Order::with(['items', 'transaction'])->where('order_ref', $invoice)->first();

         return ApiResponse::success($data);
      } catch (\Exception $e) {

         return ApiResponse::failed($e);
      }
   }

   public function searchOrder(Request $request)
   {
      $request->validate([
         'key' => ['required', 'string']
      ]);

      try {

         $q = filter_var($request->key, FILTER_SANITIZE_SPECIAL_CHARS);

         $data =  Order::with('transaction')
            ->where('customer_whatsapp', $q)
            ->orWhere('order_ref', $q)
            ->orderByDesc('updated_at')
            ->get();

         return ApiResponse::success($data);
      } catch (\Exception $e) {

         return ApiResponse::failed($e);
      }
   }

   public function getRandomOrder()
   {

      // FIXED Performance Issue
      $max = OrderItem::max('id');
      $latest = $max <= 60 ? 0 : $max - 60;
      $data = Cache::remember('order_items_random',  now()->addMinutes(15), function () use ($latest) {

         return DB::table('order_items')
            ->select('order_items.id', 'order_items.name', 'order_items.created_at', 'orders.customer_name', 'assets.filepath', 'orders.order_total')
            ->join('orders', 'order_items.order_id', 'orders.id')
            ->join('products', 'order_items.product_id', 'products.id')
            ->join('product_asset', 'product_asset.product_id', 'products.id')
            ->join('assets', 'product_asset.asset_id', 'assets.id')
            ->where('order_items.id', '>=', $latest)
            ->where('orders.product_type', Product::PRODUCT_DEFAULT)
            ->inRandomOrder()
            ->groupBy('orders.id')
            ->get()->map(function ($item) {
               $name =  $item->name;
               // $name =  $item->name . ' dengan harga total ' . number_format($item->order_total, 0, ',', '.');
               return [
                  'id' => $item->id,
                  'name' => $name,
                  'customer_name' => $item->customer_name,
                  'created' => $item->created_at >= Carbon::now()->subDays(5) ? Carbon::parse($item->created_at)->diffForHumans() : 'Beberapa waktu lalu',
                  'image' => url($item->filepath)
               ];
            });
      });

      return ApiResponse::success($data);
   }

   public function storeOrder(OrderRequest $request)
   {

      $user = auth('sanctum')->user();

      if ($request->voucher_id) {

         $v = Voucher::withCount('orders')->where('id', $request->voucher_id)->first();

         if ($v->usage_quota > 0 && $v->usage_quota <= $v->orders_count) {

            return ApiResponse::failed('Kuota pemakaian voucher habis, silahkan ganti voucher yang lain');
         }
         if ($v->end_date < now()) {
            return ApiResponse::failed('Masa aktif voucher telah kadaluarsa, silahkan ganti voucher yang lain');
         }
      }

      DB::beginTransaction();

      try {

         $config = Config::first();
         $order_expired_time = (int) $config->order_expired_time;

         $referal_code = $request->referal_code ?? null;

         $is_bank_transfer = $request->payment_type == PaymentTypeEnum::PAYMEMT_DIRECT_TRANSFER->value;
         $is_payment_gateway = $request->payment_type == PaymentTypeEnum::PAYMENT_GATEWAY->value;
         $is_saldo_balance = $request->payment_type == PaymentTypeEnum::PAYMEMT_SALDO_BALANCE->value;
         $is_cash_payment = $request->payment_type == PaymentTypeEnum::PAYMEMT_CASH->value;
         $is_cod_payment = $request->payment_type == PaymentTypeEnum::PAYMEMT_COD->value;

         $payment_fee = $request->payment_fee ? intval($request->payment_fee) : 0;
         $service_fee = $request->service_fee ?? 0;

         $grand_total = $request->grand_total;

         $is_free_product = $grand_total == 0;

         $is_product_digital = false;

         $product_type = $request->product_type;

         if (ProductTypeEnum::isDigital($product_type)) {
            $is_product_digital = true;
         }

         $event = null;

         $expired_at = Carbon::now()->addHours($order_expired_time);

         $order_status = Order::PENDING;
         $shipping_type = Order::SHIPPING_COURIER;

         $order_status = Order::PENDING;
         $shipping_type = Order::SHIPPING_COURIER;

         if ($request->shipping_courier_id == Order::SHIPPING_PICKUP) {
            $shipping_type = Order::SHIPPING_PICKUP;
            if ($request->payment_type == Order::PAYMEMT_CASH) {
               $order_status = Order::AWAITING_PICKUP;
            }
         }

         if ($request->shipping_courier_id == Order::SHIPPING_COD) {
            $shipping_type = Order::SHIPPING_COD;
            if ($request->payment_type == Order::PAYMEMT_COD) {
               $order_status = Order::TOSHIP;
            }
         }

         $kode_unik = intval($request->order_unique_code) ?? 0;

         $order = Order::create([
            'user_id' => $user ? $user->id : null,
            'customer_name' => $request->customer_name,
            'customer_whatsapp' => $request->customer_phone,
            'customer_email' => $request->customer_email ?? NULL,
            'order_qty' => $request->order_qty,
            'order_weight' => $request->order_weight,
            'order_unique_code' => $kode_unik,
            'order_subtotal' => $request->order_subtotal,
            'order_total' => $request->grand_total,
            'order_status' =>  $order_status,
            'shipping_type' => $shipping_type,
            'shipping_address' => $request->shipping_address ?? NULL,
            'shipping_courier_id' => $request->shipping_courier_id ?? NULL,
            'shipping_courier_name' => $request->shipping_courier_name ?? NULL,
            'shipping_courier_service' => $request->shipping_courier_service ?? NULL,
            'shipping_cost' => $request->shipping_cost ?? 0,
            'payment_fee' => $payment_fee,
            'service_fee' => $request->service_fee ?? 0,
            'voucher_discount' => $request->voucher_discount ?? 0,
            'shipping_discount' => $request->shipping_discount ?? 0,
            'expired_at' => $expired_at,
            'product_type' => $product_type,
            'note' => $request->customer_note ?? NULL,
            'shipping_coordinate' => $request->shipping_coordinate ?? NULL,
         ]);

         foreach ($request->order_items as $item) {

            if (intval($item['price']) > 0 && $user) {
               $affiliate = null;

               if ($referal_code) {
                  $affiliate = Affiliate::where('user_id', '!=', $user->id)
                     ->where(function ($q) use ($referal_code) {
                        $q->where('code', $referal_code)->orWhere('coupon_code', $referal_code);
                     })
                     ->first();
               } else if (isset($item['affiliate_code']) && $item['affiliate_code']) {
                  $affiliate = Affiliate::where('code', $item['affiliate_code'])
                     ->where('user_id', '!=', $user->id)
                     ->first();
               }

               if ($affiliate && $affiliate->status == Affiliate::Active) {
                  $item['affiliate_id'] = $affiliate->user_id;
               }
            }

            $order->items()->create($item);
         }

         if ($is_payment_gateway) {
            $email = $order->customer_email;
            if (!$email) {
               $admin = User::whereNotNull('role_id')->first();
               $email = $admin->email;
            }

            $items = [];

            foreach ($request->order_items as $row) {
               $item = [
                  'sku' => $row['sku'],
                  'name' => $row['name'],
                  'price' => $row['price'],
                  'quantity' => $row['quantity'],
               ];
               $items[] = $item;
            }

            if ($kode_unik > 0) {
               $items[] = [
                  'sku' => 'kdu',
                  'name' => "kode Unik",
                  'price' => $kode_unik,
                  'quantity' => 1,
               ];
            }
            if ($request->shipping_cost > 0) {
               $items[] = [
                  'sku' => 'onk',
                  'name' => "Ongkos Kirim",
                  'price' => $request->shipping_cost,
                  'quantity' => 1,
               ];
            }

            $payload = [
               'method'                => $request->payment_method,
               'merchant_ref'          => $order->order_ref,
               'amount'                => $order->order_total,
               'customer_name'         => $order->customer_name,
               'customer_email'        => $email,
               'customer_phone'        => $order->customer_whatsapp,
               'order_items'           => $items,
               'order_expired_time'    => $order_expired_time,
            ];

            $result = $this->paymentService->createTransaction($payload);

            // Log::debug('response payment service', $result);

            $transaction = new Transaction();

            $paymentName =  $request->payment_name ?? $config->payment_default;

            if (isset($result['payment_channel'])) {
               $paymentName = $result['payment_channel'];
            }

            $paymentMethod = $request->payment_method ?? NULL;

            if (!$paymentMethod && isset($result['payment_method'])) {
               $paymentMethod = $result['payment_method'];
            }

            $expired_date = $result['expired_date'] ?? $expired_at;

            $transaction->order_id = $order->id;
            $transaction->payment_type = $request->payment_type;
            $transaction->payment_name = $paymentName;
            $transaction->payment_method = $paymentMethod;

            $transaction->snap_token = $result['snap_token'] ?? NULL;
            $transaction->qr_url = $result['qr_url'] ?? NULL;
            $transaction->pay_url = $result['pay_url'] ?? NULL;
            $transaction->checkout_url = $result['checkout_url'] ?? NULL;
            $transaction->payment_code = $result['pay_code'] ?? NULL;
            $transaction->qr_string = $result['qr_string'] ?? NULL;

            $transaction->payment_ref = $result['reference'] ?? NULL;
            $transaction->expired_time = Carbon::parse($expired_date)->timestamp;

            $transaction->amount = $result['amount'];
            $transaction->total_fee = $result['total_fee'] ?? 0;
            $transaction->fee_merchant = $result['fee_merchant'] ?? 0;
            $transaction->fee_customer = $result['fee_customer'] ?? $payment_fee;
            $transaction->instructions = isset($result['instructions']) ? json_encode($result['instructions']) : NULL;

            $transaction->save();

            $order->fresh();

            $order->payment_fee = $result['fee_customer'] ?? 0;
            $order->expired_at = $expired_date;
            $order->save();
         } else {
            $transaction = new Transaction();

            $transaction->order_id = $order->id;
            $transaction->payment_type = $request->payment_type ?? NULL;
            $transaction->payment_method = $request->payment_method ?? NULL;
            $transaction->payment_code = $request->payment_code ?? NULL;
            $transaction->payment_name = $request->payment_name ?? NULL;
            $transaction->amount = $order->order_total;

            $transaction->payment_ref = 'DTR' . Carbon::now()->format('ymd') . rand(10, 99) . Str::upper(Str::random(5));
            $transaction->expired_time = Carbon::now()->addHours($order_expired_time)->timestamp;

            $transaction->save();
         }
         if ($request->voucher_id) {
            $order->vouchers()->attach($request->voucher_id);
         }

         if ($is_saldo_balance) {
            MutasiSaldo::create([
               'amount' => $order->billing_total,
               'category' => MutasiSaldo::CATEGORY_DEFAULT,
               'type' => MutasiSaldo::TYPE_OUT,
               'user_id' => $order->user_id,
               'status' => MutasiSaldoStatusEnum::Success,
               'description' => 'Transaksi #' . $order->order_ref,
            ]);
            OrderPaid::dispatch($order);
         } else if ($is_free_product) {
            OrderPaid::dispatch($order);
         } else {
            $event = NotificationTemplate::ORDER_CREATED;
            $order->dispatchEventMessage($event);
         }

         if (!$is_product_digital) {

            $order->update_stock();
         }

         DB::commit();

         $order->flush_cache();

         return ApiResponse::withEvent($event)->success($order);

         Cache::forget('latest_orders');
         Cache::forget('order_reports');

         $data = $order->load('items', 'transaction');

         return ApiResponse::withEvent($event)->success($data);
      } catch (\Exception $e) {

         DB::rollBack();

         return ApiResponse::failed($e);
      }
   }
}
