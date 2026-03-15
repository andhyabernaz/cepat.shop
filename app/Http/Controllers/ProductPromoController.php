<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Promo;
use App\Models\Product;
use App\Models\Category;
use App\Helpers\ApiResponse;
use App\Models\ProductPromo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductPromoController extends Controller
{
   public function store(Request $request)
   {
      $data = $request->validate([
         'promo_id' => 'required',
         'discount_amount' => 'required|numeric',
         'discount_type' => 'required',
         'products' => 'required|array'
      ]);

      $promo = Promo::find($request->promo_id);

      foreach ($data['products'] as $productId) {
         $promo->products()->detach($productId);
         $promo->products()->attach($productId, [
            'discount_amount' => $data['discount_amount'],
            'discount_type' => $data['discount_type']
         ]);
      }

      Product::clearCache();

      return ApiResponse::success($promo->products);
   }
   public function removeProduct(Request $request)
   {
      $request->validate([
         'product_id' => 'required',
         'promo_id' => 'required',
      ]);

      $promo = Promo::find($request->promo_id);

      $promo->products()->detach($request->product_id);

      Cache::flush();

      return ApiResponse::success($promo->products);
   }
   public function findProducts(Request $request)
   {

      $data = Product::doesntHave('promoRelationsRunning')
         ->when($request->category_id, function ($q) use ($request) {
            $ids = Category::where('category_id', $request->category_id)->select('id')->pluck('id')->toArray();
            array_push($ids, $request->category_id);
            $q->whereIn('category_id', $ids);
         })
         ->when($request->search, function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%');
         })
         ->get();
      return ApiResponse::success($data);
   }
   public function productWithoutPromo()
   {

      $data = Product::doesntHave('promoRelationsRunning')->get();
      return ApiResponse::success($data);
   }
   public function getProductPromo($promoId)
   {

      try {

         $promo = Promo::findOrFail($promoId);

         return ApiResponse::success($promo->products);
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }
}
