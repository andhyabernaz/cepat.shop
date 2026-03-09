<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Asset;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Media\MediaService;

class MediaController extends Controller
{
   public function __construct(
      private MediaService $mediaService
   ) {
   }

   public function index(Request $request)
   {
      $data = Asset::latest('id')->whereNull('assetable_id')->paginate($request->per_page ?? 21)->withQueryString();

      return ApiResponse::success($data);
   }

   public function store(Request $request)
   {
      $images = $request->images;

      $assets = [];

      foreach ($images as $image) {
         $filedata = $this->mediaService->storeFile($image);

         $assets[] = Asset::create($filedata);
      }

      return ApiResponse::success($assets);
   }

   public function destroy($id)
   {
      try {
         if (DB::table('product_asset')->where('asset_id', $id)->count() > 0) {
            throw new Exception('Image berelasi dengan produk');
         }
         $asset = Asset::find($id);
         $this->mediaService->deleteAsset($asset);
         return ApiResponse::success();
      } catch (\Throwable $th) {
         return ApiResponse::failed($th->getMessage());
      }
   }
}
