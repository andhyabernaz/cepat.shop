<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Illuminate\Support\Str;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Store as Shop;
use Illuminate\Support\Facades\DB;
use App\Services\Media\MediaService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class StoreController extends Controller
{

   public function index()
   {
      $shop = Cache::rememberForever('shop', function () {
         return Shop::first();
      });
      $config = Cache::rememberForever('shop_config', function () {
         return Config::first();
      });

      $data['shop'] = $shop;
      $data['config'] = $config;

      return ApiResponse::success($data);
   }

   public function update(Request $request)
   {
      $request->validate([
         'phone' => 'nullable|numeric'
      ]);

      DB::beginTransaction();

      try {
         $shop = Shop::first();

         $shop->name = $request->name;
         $shop->phone = $request->phone;
         if (!$shop->slug) {
            $shop->slug = $request->name ? Str::slug($request->name) : null;
         }
         $shop->address = $request->address;
         $shop->description = $request->description;
         $shop->slogan = $request->slogan;
         $shop->email = $request->email;

         if ($file = $request->file('logo')) {
            if ($shop->logo_path) {
               File::delete($shop->logo_path);
            }

            $filedata = (new MediaService())->toPng()->storeFile($file);
            $shop->logo_path = $filedata['filepath'];
         }

         $shop->save();
         DB::commit();
         Cache::forget('shop');

         return ApiResponse::success($shop);
      } catch (\Throwable $th) {

         DB::rollBack();

         return ApiResponse::failed($th);
      }
   }
   public function updatePwa(Request $request)
   {
      DB::beginTransaction();

      try {
         $shop = Shop::first();

         $shop->app_name = $request->app_name;
         $shop->download_url = $request->download_url;
         $shop->download_desc = $request->download_desc;

         if ($file = $request->file('icon')) {
            if ($shop->icon_path) {
               File::delete($shop->icon_path);
            }

            $filedata = (new MediaService())->toPng()->storeFile($file);
            $shop->icon_path = $filedata['filepath'];
         }
         $shop->save();
         DB::commit();
         Artisan::call('app:generate-manifest');
         Cache::forget('shop');

         return ApiResponse::success($shop);
      } catch (\Throwable $th) {

         DB::rollBack();

         return ApiResponse::failed($th);
      }
   }

   public function generatePwa()
   {
      try {
         Artisan::call('app:generate-manifest');
         return ApiResponse::success();
      } catch (\Throwable $th) {
         return ApiResponse::failed($th);
      }
   }
}
