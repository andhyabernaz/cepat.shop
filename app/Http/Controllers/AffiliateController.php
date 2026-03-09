<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Affiliate;
use Illuminate\Support\Str;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\AffiliateConfig;
use Illuminate\Support\Facades\DB;
use App\Services\Product\ProductService;

class AffiliateController extends Controller
{

   public function __construct(protected ProductService $service) {}

   public function index(Request $request)
   {

      $instance = DB::table('order_items')
         ->select(
            'users.name as user_name',
            'users.phone as user_phone',
            'users.email as user_email',
            'order_items.name as product_name',
            'order_items.image_url as product_image',
            'order_items.product_url as product_url',
            'orders.order_ref as invoice_ref',
            'orders.created_at',
            DB::raw('order_items.price * order_items.quantity AS subtotal'),
            'orders.order_status',
            'products.aff_is_percentage',
            'products.aff_amount',
         )
         ->join('orders', 'order_items.order_id', 'orders.id')
         ->join('products', 'products.id', 'order_items.product_id')
         ->join('users', 'users.id', 'orders.user_id')
         ->whereNotNull('order_items.affiliate_id')
         ->orderBy('orders.id', 'DESC')
         ->groupBy('order_items.id');


      if ($request->periode) {
         $periode = strtolower($request->periode);

         if ($periode == 'weekly') {
            $from = Carbon::now()->endOfDay()->subDays(7);
            $to = Carbon::now()->endOfday();
            $instance->whereBetween('orders.created_at', [$from, $to]);
         }
         if ($periode == 'monthly') {
            $from = Carbon::now()->endOfDay()->subDays(30);
            $to = Carbon::now()->endOfday();
            $instance->whereBetween('orders.created_at', [$from, $to]);
         }
         if ($periode == 'yearly') {
            $from = Carbon::now()->endOfDay()->subDays(365);
            $to = Carbon::now()->endOfday();
            $instance->whereBetween('orders.created_at', [$from, $to]);
         }
      }

      $paidStatuses = [Order::COMPLETE];
      $unpaidStatuses = [Order::PENDING];
      $processesStatus = [Order::TO_PROCESS, Order::AWAITING_PICKUP, Order::TOSHIP, Order::SHIPPING];
      $failedStatuses = [Order::CANCELED];

      if ($request->status) {
         if ($request->status == 'COMPLETED') {
            $instance->whereIn('orders.order_status', $paidStatuses);
         }
         if ($request->status == 'UNPAID') {
            $instance->whereIn('orders.order_status', $unpaidStatuses);
         }
         if ($request->status == 'PROCESSED') {
            $instance->whereIn('orders.order_status', $processesStatus);
         }
         if ($request->status == 'FAILED') {
            $instance->whereIn('orders.order_status', $failedStatuses);
         }
      }
      $data = $instance->paginate(10)->withQueryString()->toArray();

      for ($i = 0; $i < count($data['data']); $i++) {

         $status = $data['data'][$i]->order_status;

         if (in_array($status, $unpaidStatuses)) {
            $status = "UNPAID";
         } else if (in_array($status, $failedStatuses)) {
            $status = 'FAILED';
         } else if (in_array($status, $processesStatus)) {
            $status = 'PROCESSED';
         } else {
            $status = 'COMPLETED';
         }

         $data['data'][$i]->order_status = $status;
      }

      return ApiResponse::success($data);
   }

   public function store(Request $request)
   {
      $request->validate([
         'code' => 'required|unique:affiliates,code',
      ]);

      $user = $request->user();

      $status = Affiliate::Inactive;

      $cfg = AffiliateConfig::first();

      if ($cfg->is_auto_active) {
         $status = Affiliate::Active;
      }

      $data = Affiliate::create([
         'code' => $request->code,
         'user_id' => $user->id,
         'status' => $status
      ]);

      return ApiResponse::success(($data->fresh()));
   }

   public function show(Request $request)
   {
      $user = $request->user();

      $affiliate = Affiliate::where('user_id', $user->id)->first();

      return ApiResponse::success($affiliate);
   }


   public function update(Request $request, $id)
   {
      $request->validate([
         'code' => 'required'
      ]);

      $affiliate = Affiliate::find($id);
      $affiliate->code = $request->code;
      $affiliate->save();

      return ApiResponse::success($affiliate);
   }
   public function updateStatus(Request $request, $id)
   {
      $request->validate([
         'status' => 'required'
      ]);

      $affiliate = Affiliate::find($id);
      $affiliate->status = $request->status;
      $affiliate->save();

      return ApiResponse::success($affiliate);
   }

   public function checkAffiliateCode($code)
   {
      if ($this->checkAvailable($code)) {
         return response()->json([
            'success' => true,
            'is_exist' => true
         ]);
      }
      return response()->json([
         'success' => true,
         'is_exist' => false
      ]);
   }
   public function checkAffiliateCouponCode($code)
   {
      $is_exist = false;

      if (Affiliate::where('coupon_code', $code)->count() > 0) {
         $is_exist = true;
      }
      return response()->json([
         'success' => true,
         'is_exist' => $is_exist
      ]);
   }

   protected function checkAvailable($code)
   {
      if (Affiliate::where('code', $code)->count() > 0) {
         return true;
      }

      return false;
   }

   protected function autoGenerate($number = 8)
   {
      $code = Str::random($number);

      if ($this->checkAvailable($code)) {

         $this->autoGenerate($number + 1);
      } else {

         return ApiResponse::success($code);
      }
   }
   public function getProductAffiliate(Request $request)
   {
      $data = $this->service->index($request);

      return ApiResponse::success($data);
   }
   public function leaderboard(Request $request)
   {
      $instance = DB::table('order_items')
         ->select(
            'users.id as user_id',
            'users.name as user_name',
            'users.phone as user_phone',
            'users.email as user_email',
            'order_items.product_id',
            'order_items.name as product_name',
            DB::raw("COUNT(*) AS item_count"),
            DB::raw("SUM(order_items.price * order_items.quantity) AS total_transaction"),
            DB::raw("MAX(order_items.image_url) as product_image"),
            DB::raw("MAX(order_items.product_url) as product_url")
         )
         ->join('users', 'order_items.affiliate_id', 'users.id')
         ->join('orders', 'orders.id', 'order_items.order_id')
         ->whereNotNull('order_items.affiliate_id')
         ->where('orders.order_status', Order::COMPLETE)
         ->groupBy('users.id', 'order_items.product_id')
         ->orderBy('item_count', 'DESC');


      if ($request->periode) {
         $periode = strtolower($request->periode);

         if ($periode == 'weekly') {
            $from = Carbon::now()->endOfDay()->subDays(7);
            $to = Carbon::now()->endOfDay();
            $instance->whereBetween('order_items.created_at', [$from, $to]);
         }
         if ($periode == 'monthly') {
            $from = Carbon::now()->endOfDay()->subDays(30);
            $to = Carbon::now()->endOfDay();
            $instance->whereBetween('order_items.created_at', [$from, $to]);
         }
         if ($periode == 'yearly') {
            $from = Carbon::now()->endOfDay()->subDays(365);
            $to = Carbon::now()->endOfDay();
            $instance->whereBetween('order_items.created_at', [$from, $to]);
         }
      }
      $data = $instance->paginate(10)->withQueryString();

      return ApiResponse::success($data);
   }
   public function leads(Request $request)
   {

      $user = $request->user();

      $instance = DB::table('order_items')
         ->select(
            'users.name as user_name',
            'users.phone as user_phone',
            'users.email as user_email',
            'order_items.name as product_name',
            'order_items.image_url as product_image',
            'order_items.product_url as product_url',
            'orders.order_ref as invoice_ref',
            DB::raw('(order_items.price * order_items.quantity) AS subtotal'),
            'orders.order_status',
            'products.aff_is_percentage',
            'products.aff_amount'
         )
         ->join('orders', 'order_items.order_id', 'orders.id')
         ->join('products', 'products.id', 'order_items.product_id')
         ->join('users', 'users.id', 'orders.user_id')
         ->where('order_items.affiliate_id', $user->id)
         ->orderBy('orders.id', 'DESC');

      $paidStatuses = [Order::COMPLETE];
      $unpaidStatuses = [Order::PENDING];
      $processesStatus = [Order::TO_PROCESS, Order::AWAITING_PICKUP, Order::TOSHIP, Order::SHIPPING];
      $failedStatuses = [Order::CANCELED];

      if ($request->status) {
         if ($request->status == 'COMPLETED') {
            $instance->whereIn('orders.order_status', $paidStatuses);
         }
         if ($request->status == 'UNPAID') {
            $instance->whereIn('orders.order_status', $unpaidStatuses);
         }
         if ($request->status == 'PROCESSED') {
            $instance->whereIn('orders.order_status', $processesStatus);
         }
         if ($request->status == 'FAILED') {
            $instance->whereIn('orders.order_status', $failedStatuses);
         }
      }


      if ($request->periode) {
         $periode = strtolower($request->periode);

         if ($periode == 'today') {
            $instance->whereDate('orders.created_at', now());
         }
         if ($periode == 'weekly') {
            $from = Carbon::now()->endOfDay()->subDays(7);
            $to = Carbon::now()->endOfDay();
            $instance->whereBetween('orders.created_at', [$from, $to]);
         }
         if ($periode == 'monthly') {
            $from = Carbon::now()->endOfDay()->subDays(30);
            $to = Carbon::now()->endOfDay();
            $instance->whereBetween('orders.created_at', [$from, $to]);
         }
         if ($periode == 'yearly') {
            $from = Carbon::now()->endOfDay()->subDays(365);
            $to = Carbon::now()->endOfDay();
            $instance->whereBetween('orders.created_at', [$from, $to]);
         }
      }
      $data = $instance->paginate(10)->withQueryString()->toArray();

      for ($i = 0; $i < count($data['data']); $i++) {

         $status = $data['data'][$i]->order_status;

         if (in_array($status, $unpaidStatuses)) {
            $status = "UNPAID";
         } else if (in_array($status, $failedStatuses)) {
            $status = 'FAILED';
         } else if (in_array($status, $processesStatus)) {
            $status = 'PROCESSED';
         } else {
            $status = 'COMPLETED';
         }

         $data['data'][$i]->order_status = $status;
      }

      return ApiResponse::success($data);
   }

   public function checkReferalCode(Request $request)
   {
      $result = [
         'success' => false,
         'results' => null
      ];
      $ref = User::whereHas('affiliate', function ($query) use ($request) {
         $query->where('code', $request->referal_code);
      })->select('id', 'name')->first();

      if ($ref) {
         $result['results'] = $ref;
         $result['success'] = true;
      }

      return response()->json($result);
   }

   public function users(Request $request)
   {
      $data = Affiliate::select(
         'users.name as user_name',
         'affiliates.*',
      )
         ->when($request->status && $request->status != 'ALL', function ($q) use ($request) {
            $q->where('status', intval($request->status));
         })
         ->join('users', 'users.id', 'affiliates.user_id')
         ->paginate($request->per_page ?? 10)->withQueryString();

      return ApiResponse::success($data);
   }
}
