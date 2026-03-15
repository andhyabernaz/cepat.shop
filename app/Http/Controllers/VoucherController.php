<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\VoucherRequest;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index(Request $request)
   {
      $scope = $request->status ?? 'active';

      $data = Voucher::withCount('orders')->when($scope, function ($q) use ($scope) {
         if ($scope == 'active') {
            $q->active();
         }
         if ($scope == 'later') {
            $q->later();
         }
         if ($scope == 'expired') {
            $q->expired();
         }
      })->get();

      return ApiResponse::success($data);
   }

   /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function store(VoucherRequest $request)
   {
      $validated = $request->validated();

      $data = Voucher::create($validated);

      return ApiResponse::success($data);
   }

   /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function show($id)
   {
      $voucher = Voucher::find($id);
      return ApiResponse::success($voucher);
   }

   /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function update(VoucherRequest $request, $id)
   {
      $validated = $request->validated();
      $voucher = Voucher::find($id);
      $voucher->update($validated);

      return ApiResponse::success($voucher->fresh());
   }

   /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function destroy($id)
   {
      $voucher = Voucher::find($id);
      $voucher->delete();

      return ApiResponse::success();
   }

   public function checkVoucherCode($voucher_code)
   {
      $count = Voucher::where('voucher_code', $voucher_code)->count();

      return ApiResponse::success([
         'voucher_count' => $count
      ]);
   }

   public function generate()
   {
      $code = generateUniqueVoucher();
      return ApiResponse::success([
         'code' => $code
      ]);
   }
}
