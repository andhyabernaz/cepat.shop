<?php

namespace App\Http\Controllers\Frontend;

use Exception;
use App\Models\Config;
use App\Models\Review;
use App\Models\Product;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class ReviewController extends Controller
{

   public function index(Request $request)
   {
      try {

         $is_approved = $request->is_approved ? 1 : 0;

         $data = Review::latest()->where('is_approved', $is_approved)->paginate($request->per_page ?? 8)->withQueryString();

         return ApiResponse::success($data);
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }
   public function publish(Request $request)
   {

      $request->validate([
         'id' => 'required',
         'product_id' => 'required',
      ]);

      try {

         $review = Review::find($request->id);

         $review->update([
            'is_approved' => 1
         ]);

         Product::clearCache($review->product_id);

         return ApiResponse::success();
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }
   public function destroy($id)
   {

      try {

         $review = Review::find($id);
         Product::clearCache($review->product_id);
         $review->delete();

         return ApiResponse::success();
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }

   public function store(Request $request)
   {
      $request->validate([
         'product_id' => ['required'],
         'name' => ['required'],
         'product_name' => ['required'],
         'comment' => ['required', 'string', 'max:300'],
         'rating' => ['required', 'numeric', 'min:1', 'max:5'],
      ]);

      try {

         $product = Product::findOrFail($request->product_id);

         $approvCfg = false;

         $config = Config::first();

         if ($config->review_auto_approved) {
            $approvCfg = true;
         }

         $review = $product->reviews()->create([
            'comment' => strip_tags($request->comment, '<b><strong><br>'),
            'product_name' => $request->product_name,
            'rating' => $request->rating,
            'name' => strip_tags($request->name),
            'is_approved' => $approvCfg
         ]);

         $data = $review;
         $msg = $approvCfg ? 'Berhasil mengulas produk' : 'Ulasan anda menunggu di publish';

         Product::clearCache($product->id);

         return ApiResponse::success($data, $msg);
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }
   public function show(Request $request, $id)
   {

      try {

         $data  = Review::with('reviewImages')->where('product_id', $id)->approved()->latest()->skip($request->skip ?? 0)->take(6)->get();

         return ApiResponse::success($data);
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }
}
