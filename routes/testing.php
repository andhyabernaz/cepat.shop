<?php

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('testing', function() {

   $date = '2025-07-18T18:06:28.785Z';
   $data = Carbon::parse($date, 'Asia/Jakarta');
   $date2 = date_from_utc_to_locale($date);

   return ApiResponse::success([
      'default' => $date,
      'formatted' => $data,
      'helper' => $date2
   ]);

});
