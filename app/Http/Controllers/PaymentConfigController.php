<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\PaymentConfig;
use Illuminate\Support\Facades\Cache;

class PaymentConfigController extends Controller
{
   public function index(Request $request)
   {
      $vendor = $request->vendor ?? null;
      $data = PaymentConfig::getConfigs($vendor);

      return ApiResponse::success($data);
   }

   public function store(Request $request)
   {

      foreach ($request->all() as $key => $val) {
         PaymentConfig::where('name', $key)->update([
            'value' => $val ?? NULL,
         ]);
      }

      Cache::forget('is_pg_ready');

      return ApiResponse::success();
   }
}
