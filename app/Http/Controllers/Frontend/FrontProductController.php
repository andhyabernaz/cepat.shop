<?php

namespace App\Http\Controllers\Frontend;

use Exception;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductListCollection;
use App\Services\Product\PublicProductService;

class FrontProductController extends Controller
{
   public function __construct(
      protected PublicProductService $service
      )
   {
   }

   public function getProducts(Request $request)
   {

      try {
         return $this->service->getAll($request);
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }

   public function getProductsFavorites(Request $request)
   {
      $request->validate([
         'pids' => 'required'
      ]);

      try {
         return new ProductListCollection($this->service->getManyInId($request->pids));
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }

   public function productsByCategory(Request $request)
   {

      try {
         $data = $this->service->getProductByCategory($request);
         return ApiResponse::success($data);
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }

   public function searchProduct($key)
   {

      try {
         $key = filter_var($key, FILTER_SANITIZE_SPECIAL_CHARS);
         if (!$key) {
            throw new Exception('Search keyword invalid');
         }
         return new ProductListCollection($this->service->search($key));
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }

   public function productDetail($slug)
   {

      try {

         return new ProductResource($this->service->getSingleProduct($slug));
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }
   public function productRelated($id)
   {

      try {
         return $this->service->productRelated($id);
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }

   public function productLinks($productId)
   {
      try {
         $data = $this->service->productLinks($productId);
         return ApiResponse::success($data);
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }
}
