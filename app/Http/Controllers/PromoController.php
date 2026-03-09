<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use App\Models\Product;
use App\Helpers\ApiResponse;
use App\Models\ProductPromo;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class PromoController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index(Request $request)
   {
      $data = Promo::withCount('products')
         ->when($request->status, function ($q) use ($request) {
            $status = $request->status;
            if ($status == 'active') {
               $q->active();
            }
            if ($status == 'later') {
               $q->later();
            }
            if ($status == 'expired') {
               $q->expired();
            }
         })
         ->latest()->get();
      if (!Cache::has('promo_is_updated')) {
         ProductPromo::whereHas('promoExpired')->delete();

         Cache::put('promo_is_updated', 1, now()->addHours(2));
      }
      return ApiResponse::success($data);
   }

   /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function store(Request $request)
   {
      $request->validate([
         'label' => 'required',
         'start_date' => 'required',
         'end_date' => 'required|date',
      ]);

      $promo = Promo::create([
         'label' => $request->label,
         'start_date' => Carbon::parse($request->start_date),
         'end_date' => Carbon::parse($request->end_date),
      ]);

      Cache::flush();

      return ApiResponse::success($promo);
   }

   /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

   public function show($id)
   {
      $data = Promo::find($id);
      return ApiResponse::success($data);
   }

   public function promoDetail($id)
   {
      $promo = Promo::findOrFail($id);

      return ApiResponse::success(
         [
            'promo' => $promo,
            'products' => $promo->products
         ]
      );
   }

   /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, $id)
   {
      $request->validate([
         'label' => 'required',
         'start_date' => 'required|date',
         'end_date' => 'required|date',
      ]);

      $promo = Promo::findOrFail($id);

      $promo->update([
         'label' => $request->label,
         'start_date' => Carbon::parse($request->start_date),
         'end_date' => Carbon::parse($request->end_date),
      ]);

      Cache::flush();

      return ApiResponse::success($promo->fresh());
   }

   /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function destroy($id)
   {
      $promo = Promo::findOrFail($id);
      ProductPromo::where('promo_id', $promo->id)->delete();
      $promo->delete();

      Cache::flush();

      return response(['success' => true], 200);
   }
}
