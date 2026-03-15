<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Product;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Services\Product\ProductService;

class ProductController extends Controller
{

   public function __construct(protected ProductService $service)
   {
   }

   public function index(Request $request)
   {
      try {
         $data = $this->service->index($request);
         return ApiResponse::success($data);
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }
   public function productVarians($id)
   {
      try {

         $data =  $this->service->productVarianByProductId($id);
         return ApiResponse::success($data);
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }

   public function removeVarian($id)
   {
      try {

         $this->service->removeVarian($id);
         return ApiResponse::success();
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }

    public function getSubscriptionOptions()
   {
       try {
         $data = $this->service->getSubscriptionOptions();
         return ApiResponse::success($data);
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }


   public function searchProducts($key)
   {
      try {

         $data = $this->service->search($key);
         return ApiResponse::success($data);
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }

   public function show($id)
   {

      try {

         $data =  $this->service->show($id);
         return ApiResponse::success($data);
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }
   public function edit($id)
   {

      try {

         $data =  $this->service->edit($id);
         return ApiResponse::success($data);
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }

   public function store(ProductRequest $request)
   {

      try {

         $data =  $this->service->store($request);
         return ApiResponse::success($data);
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }

   public function update(ProductRequest $request, $id)
   {

      try {

         $this->service->update($request, $id);

         return ApiResponse::success();
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }

   public function destroy($id)
   {
      try {
         $this->service->destroy($id);
         return ApiResponse::success();
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }
}
