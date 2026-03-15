<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Product;
use App\Models\Affiliate;
use App\Helpers\ApiResponse;
use App\Models\AffiliateConfig;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
   public function index(Request $request)
   {
      $user = $request->user();

      $data = Product::select(
         'products.id',
         'products.title',
         'products.slug',
         'products.price',
         'products.stock',
         'products.product_type',
         'products.category_id',
         'products.aff_is_active',
         'products.aff_is_percentage',
         'products.aff_amount',
         DB::raw("MAX(product_varians.price) as maxPrice"),
      )
         ->leftJoin('product_varians', 'products.id', 'product_varians.product_id')
         ->join('leads', 'leads.product_id', 'products.id')

         ->when($request->periode, function ($instance) use ($request) {
            $periode = strtolower($request->periode);

            if ($periode == 'today') {
               $from = Carbon::now()->startOfDay();
               $to = Carbon::now()->endOfDay();
               $instance->whereBetween('leads.created_at', [$from, $to]);
            } else if ($periode == 'weekly') {
               $from = Carbon::now()->endOfDay()->subDays(7);
               $to = Carbon::now();
               $instance->whereBetween('leads.created_at', [$from, $to]);
            } else if ($periode == 'monthly') {
               $from = Carbon::now()->endOfDay()->subDays(30);
               $to = Carbon::now();
               $instance->whereBetween('leads.created_at', [$from, $to]);
            } else if ($periode == 'yearly') {
               $from = Carbon::now()->endOfDay()->subDays(365);
               $to = Carbon::now();
               $instance->whereBetween('leads.created_at', [$from, $to]);
            }
         })
         ->where('leads.user_id', $user->id)
         ->withCount('leads')
         ->with('assets')
         ->orderByDesc('products.id')
         ->groupBy('products.id')
         ->paginate(10);

      return ApiResponse::success($data);
   }
   public function store(Request $request)
   {
      try {
         $request->validate([
            'product_id' => 'required',
            'affiliate_code' => 'required',
         ]);

         $affiliateConfig = Cache::remember('aff_config', now()->addHour(), function () {
            return  AffiliateConfig::first();
         });

         if (!$affiliateConfig->is_active) {
            throw new Exception("Afilliate module not active");
         }

         $affiliate = Affiliate::where('code', $request->affiliate_code)->first();

         if (!$affiliate) {
            throw new Exception("Afilliate not found");
         }


         if ($affiliate->status != Affiliate::Active) {
            throw new Exception("Afilliate not active");
         }

         $user = auth('sanctum')->user();

         if ($user && $user->id == $affiliate->user_id) {
             throw new Exception("Is own product affiliate");
         }

         Lead::create([
            'product_id' => $request->product_id,
            'user_id' => $affiliate->user_id
         ]);

         return response()->json(['success' => true, 'message' => "Success"]);
      } catch (\Exception $e) {
         return response()->json(['success' => false, 'message' => $e->getMessage()]);
      }
   }
}
