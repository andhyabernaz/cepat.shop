<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class ConfigController extends Controller
{

   public function show()
   {
      $data =  Config::first();
      return ApiResponse::success($data);
   }
   public function admin()
   {

      $data = Config::first();
      return ApiResponse::success($data);
   }

   public function update(Request $request)
   {
      $config = Config::first();
      $old_theme_color = $config->theme_color;

      $config->update($request->all());

      if ($request->theme_color && $request->theme_color != $old_theme_color) {
         Artisan::all('app:generate-manifest');
      }
      Cache::flush();
      return ApiResponse::success($config->fresh());
   }
}
