<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Events\OrderPaid;
use App\Events\OrderFailed;
use App\Events\OrderExpired;
use Illuminate\Http\Request;
use App\Models\PaymentConfig;
use App\Enums\PaymentServiceEnum;
use Illuminate\Support\Facades\Log;
use App\Models\NotificationTemplate;

class TripayWebhookController extends Controller
{
   /**
    * Handle the incoming request.
    */
   public function __invoke(Request $request)
   {

      if ($request->method() != 'POST') {
         echo 'Invalid request method';
         die;
      }

      $callbackSignature = $request->server('HTTP_X_CALLBACK_SIGNATURE') ?? '';

      $json = $request->getContent();

      $data = json_decode($json);

      $tripay_private_key = PaymentConfig::getValueByName('tripay_private_key', PaymentServiceEnum::Tripay->value);

      $signature = hash_hmac('sha256', $json, $tripay_private_key);

      if ($signature !== (string) $callbackSignature) {
         return 'Invalid signature';
      }

      if ('payment_status' !== (string) $request->server('HTTP_X_CALLBACK_EVENT')) {
         return 'Invalid callback event, no action was taken';
      }

      $status = (string) strtoupper($data->status);

      // Log::debug('tripay callback', [
      //    'status' => $status,
      //    'response' => $json
      // ]);

      $merchantRef = $data->merchant_ref;

      $order = Order::where('order_ref', $merchantRef)
         ->where('order_status', 'PENDING')
         ->orWhere('order_status', 'UNPAID')
         ->first();

      if (!$order) {
         // return "Invoice not found or current status is not UNPAID";
         return response()->json([
            'success' => true,
            'message' => "Invoice current status is not UNPAID"
         ]);
      }

      if ((int) $data->total_amount !== (int) $order->order_total + (int) $order->payment_fee) {
         return 'Invalid amount, Expected: ' . $order->order_total . ' - Received: ' . $data->total_amount;
      }

      $messageEvent = null;

      if ($order->is_deposit_type()) {
         $messageEvent = NotificationTemplate::ORDER_COMPLETED;
      } else {
         $messageEvent = NotificationTemplate::ORDER_PAYMENT_CONFIRMED;
      }

      $responseData = [
         'success' => true,
      ];

      switch ($status) {
         case 'PAID':
            OrderPaid::dispatch($order);
            break;

         case 'EXPIRED':
            OrderExpired::dispatch($order, $data->note);
            $messageEvent = NotificationTemplate::ORDER_FAILED;
            break;

         case 'FAILED':
            OrderFailed::dispatch($order, $data->note);
            $messageEvent = NotificationTemplate::ORDER_FAILED;
            break;

         default:
            $responseData['success'] = false;
            $responseData['error'] = 'Unrecognized payment status';
            break;
      }

      echo json_encode($responseData);

      if ($messageEvent) {
         $order->dispatchEventMessage($messageEvent, true);
      }
   }
}
