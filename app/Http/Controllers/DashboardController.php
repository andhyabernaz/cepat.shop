<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
   public function index(Request $request)
   {
      $latest_orders =
         Cache::remember(
            'latest_orders',
            now()->addMinutes(30),
            function () {
               return Order::latest()->limit(5)->get();
            }
         );

      $order_reports = Cache::remember('order_reports', now()->addMinutes(30), function () {
         return DB::table('orders')->select(
            DB::raw("COUNT(*) as order_total"),
            DB::raw(
               "SUM(CASE
                  WHEN orders.order_status = 'PENDING'
                  THEN 1 ELSE 0 END) AS 'order_pending'"
            ),
            DB::raw(
               "SUM(CASE
                  WHEN orders.order_status = 'TO_PROCESS'
                  THEN 1 ELSE 0 END) AS 'order_to_process'"
            ),
            DB::raw(
               "SUM(CASE
                  WHEN orders.order_status = 'COMPLETE'
                  THEN 1 ELSE 0 END) AS 'order_complete'"
            ),
            DB::raw(
               "SUM(CASE
                  WHEN orders.order_status = 'CANCELED'
                  THEN 1 ELSE 0 END) AS 'order_cancelled'"
            ),
         )->get()->map(function ($report) {

            return [
               [
                  'label' => 'Total Order',
                  'total' => $report->order_total,
                  'color' => 'purple',
                  'icon' => 'receipt'
               ],
               [
                  'label' => 'Order Pending',
                  'total' => $report->order_pending ?? 0,
                  'color' => 'grey',
                  'icon' => 'receipt'
               ],
               [
                  'label' => 'Perlu Diproses',
                  'total' => $report->order_to_process ?? 0,
                  'color' => 'orange',
                  'icon' => 'receipt'
               ],
               [
                  'label' => 'Order Selesai',
                  'total' => $report->order_complete ?? 0,
                  'color' => 'green',
                  'icon' => 'receipt'
               ],
               [
                  'label' => 'Order Dibatalkan',
                  'total' => $report->order_cancelled ?? 0,
                  'color' => 'red',
                  'icon' => 'receipt'
               ]
            ];
         });
      });

      return ApiResponse::success([
         'latest_orders' => $latest_orders,
         'order_reports' => $order_reports[0],
         'transaction_reports' => $this->_transactionReports($request),
         'landing_stats' => $this->landingStats(),
         'testimonials' => $this->testimonials(),
      ]);
   }

   protected function landingStats()
   {
      return [
         [
            'label' => 'Pelanggan',
            'value' => User::query()->whereNull('role_id')->count(),
            'icon' => 'groups',
            'accent' => 'primary',
            'description' => 'Akun customer yang siap ditargetkan lewat landing page.',
         ],
         [
            'label' => 'Produk Aktif',
            'value' => Product::query()->where('status', 1)->count(),
            'icon' => 'inventory_2',
            'accent' => 'teal',
            'description' => 'Katalog aktif yang bisa dipromosikan dari hero sampai CTA.',
         ],
         [
            'label' => 'Ulasan Tayang',
            'value' => Review::query()->approved()->count(),
            'icon' => 'reviews',
            'accent' => 'amber-8',
            'description' => 'Social proof yang bisa dipakai kembali untuk blok testimoni.',
         ],
         [
            'label' => 'Order Selesai',
            'value' => Order::query()->where('order_status', Order::COMPLETE)->count(),
            'icon' => 'task_alt',
            'accent' => 'positive',
            'description' => 'Pesanan yang sudah selesai dan siap jadi bukti performa.',
         ],
      ];
   }

   protected function testimonials()
   {
      return Review::query()
         ->approved()
         ->with('product:id,title,slug')
         ->latest()
         ->limit(6)
         ->get()
         ->map(function ($review) {
            $comment = trim(strip_tags((string) $review->comment));

            return [
               'id' => $review->id,
               'name' => $review->name ?: 'Pelanggan',
               'comment' => $comment,
               'rating' => intval($review->rating ?? 5),
               'product_name' => $review->product_name ?: optional($review->product)->title ?: 'Produk',
               'product_slug' => optional($review->product)->slug,
               'created' => $review->created,
            ];
         })
         ->values();
   }

   protected function _transactionReports($request)
   {

      $cacheKey = 'transaction_reports' . http_build_query($request->all());
      Cache::forget($cacheKey);

      $transactions = Cache::remember(
         $cacheKey,
         now()->addMinutes(15),
         function () use ($request) {
            return DB::table('orders')->select(
               DB::raw("SUM(orders.order_total + orders.payment_fee) AS 'total'"),
               DB::raw("SUM(transactions.fee_merchant) AS 'expenses'"),
            )
               ->join('transactions', 'transactions.order_id', 'orders.id')
               ->where('transactions.status', 'PAID')
               ->when($request->period, function ($q) use ($request) {
                  switch ($request->period) {
                     case 'today':
                        $q->whereDate('orders.created_at', Carbon::now());
                        break;
                     case 'monthly':
                        $q->whereMonth('orders.created_at', Carbon::now())->whereYear('orders.created_at', Carbon::now());
                        break;
                     case 'yearly':
                        $q->whereYear('orders.created_at', Carbon::now());
                        break;

                     default:
                        break;
                  }
               })
               ->get()->map(function ($report) {

                  $total = (int) $report->total ?? 0;
                  $expenses = (int) $report->expenses ?? 0;
                  $net = $total - $expenses;

                  return [
                     [
                        'label' => 'Total Omset',
                        'total' => $total,
                        'color' => 'purple',
                        'icon' => 'monetization_on'
                     ],
                     [
                        'label' => 'Total Beban Biaya',
                        'total' => $expenses,
                        'color' => 'red',
                        'icon' => 'monetization_on',
                        'desc' => 'Biaya layanan payment gateway'
                     ],
                     [
                        'label' => 'Total Penghasilan',
                        'total' => $net,
                        'icon' => 'monetization_on',
                        'color' => 'green',
                     ],
                  ];
               });
         }
      );

      return $transactions[0];
   }
}
