<?php

namespace App\Http\Controllers\Frontend;

use Exception;
use App\Models\Order;
use App\Models\Config;
use App\Models\Review;
use App\Models\Product;
use App\Models\MutasiSaldo;
use App\Models\UserAddress;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use Illuminate\Support\Facades\File;
use App\Http\Requests\CustomerReviewRequest;
use App\Services\Media\MediaService;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Laravel\Facades\Image;

class CustomerController extends Controller
{
   public function __construct(
      protected MediaService $mediaService
   ) {}
   public function getOrders(Request $request)
   {

      try {

         $data = Order::when($request->status, function ($q) use ($request) {
            if ($request->status != 'ALL') {
               $statusArr =  [Order::TO_PROCESS, Order::TOSHIP];
               if (in_array($request->status, $statusArr)) {
                  $q->whereIn('order_status', $statusArr);
               } else {
                  $q->where('order_status', $request->status);
               }
            }
         })->where('user_id', $request->user()->id)
            ->latest('updated_at')
            ->paginate($request->per_page ?? 8)
            ->withQueryString();

         return ApiResponse::success($data);
      } catch (\Throwable $th) {

         return ApiResponse::failed($th);
      }
   }
   public function getReviews(Request $request)
   {

      try {

         $data = Review::where('user_id', $request->user()->id)
            ->with('reviewImages')
            ->latest('created_at')
            ->paginate($request->per_page ?? 10)
            ->withQueryString();

         return ApiResponse::success($data);
      } catch (\Throwable $th) {

         return ApiResponse::failed($th);
      }
   }
   public function getInvoice(Request $request, $invoice_ref)
   {

      try {

         $order = Order::where('user_id', $request->user()->id)
            ->where('order_ref', $invoice_ref)
            ->with('items.product')
            ->first();

         // if (!$order) {
         //    throw new Exception('Forbidden');
         // }

         return ApiResponse::success($order);
      } catch (\Throwable $th) {

         return ApiResponse::failed($th);
      }
   }

   public function submitReviews(CustomerReviewRequest $request)
   {
      $user = $request->user();
      $config = Config::first();
      $order = Order::find($request->order_id);

      try {

         foreach ($request->items as $item) {

            $product = Product::findOrFail($item['product_id']);

            $approvCfg = false;

            if ($config->review_auto_approved) {
               $approvCfg = true;
            }

            $review = $product->reviews()->create([
               'comment' => strip_tags($item['comment'], '<b><br>'),
               'rating' => $item['rating'],
               'name' => $user->name,
               'is_approved' => $approvCfg,
               'user_id' => $user->id,
               'product_name' => $item['product_name'],
               'product_varian' => $item['product_varian'] ?? NULL,
            ]);

            $data = $review;
            $msg = $approvCfg ? 'Berhasil mengulas produk' : 'Ulasan anda menunggu di publish';

            if ($item['review_images']) {
               foreach ($item['review_images'] as $img) {
                  $filedata = $this->mediaService->storeDataUrl($img);
                  $review->reviewImages()->create($filedata);
               }
            }

            Product::clearCache($product->id);
         }

         $order->update([
            'is_reviewed' => 1
         ]);

         return ApiResponse::success($data, $msg);
      } catch (Exception $e) {

         return ApiResponse::failed($e);
      }
   }

   public function deleteAccount(Request $request)
   {

      $user = $request->user();

      UserAddress::where('user_id', $user->id)->delete();

      if (env('FORCE_USER_DELETE') == true) {
         $user->forceDelete();
      } else {
         $user->delete();
      }


      return ApiResponse::success();
   }

   public function uploadPaymentProof(Request $request, $order_id)
   {
      $request->validate([
         'image' => ['required'],
      ]);

      DB::beginTransaction();

      try {


         $iconDir = public_path('upload/payments');

         if (!File::isDirectory($iconDir)) {
            File::makeDirectory($iconDir, 0775, true, true);
         }

         $order = Order::findOrFail($order_id);

         $filename = "upload/payments/" . uniqid('payment_') . $order->order_ref . '.webp';

         Image::read($request->file('image'))->scale(400)->toWebp()->save($filename);

         $transaction = $order->transaction;

         $transaction->update([
            'payment_proof' => $filename,
         ]);

         $order->update([
            'order_status' => Order::TO_PROCESS
         ]);

         DB::commit();

         $desc = 'Pembeli telah mengirim bukti transfer';

         $order->pushHistory($desc);

         $event = NotificationTemplate::ORDER_PAYMENT_SUBMITED;
         $order->dispatchEventMessage($event);


         return ApiResponse::withEvent($event)->success($order->load('transaction'));
      } catch (\Throwable $th) {
         DB::rollBack();
         return ApiResponse::failed($th->getMessage());
      }
   }
   public function mutasiSaldo(Request $request)
   {
      $data = MutasiSaldo::where('user_id', $request->user()->id)
         ->when($request->category, function ($q) use ($request) {
            $q->where('category', $request->category);
         })
         ->latest('id')->paginate($request->per_page ?? 6)->withQueryString();

      return ApiResponse::success($data);
   }
}
