<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Withdrawal;
use App\Models\MutasiSaldo;
use App\Helpers\ApiResponse;
use App\Jobs\DispatchNotificationJob;
use Illuminate\Http\Request;
use App\Models\NotificationTemplate;
use App\Models\SaldoConfig;
use Illuminate\Support\Facades\DB;

class SaldoController extends Controller
{
   public function index()
   {
      $data = SaldoConfig::first();
      return ApiResponse::success($data);
   }

   public function store(Request $request)
   {
      $data = SaldoConfig::first();

      $data->update($request->all());

      return ApiResponse::success($data);
   }

  
}
