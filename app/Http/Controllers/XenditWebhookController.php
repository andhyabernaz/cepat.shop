<?php

namespace App\Http\Controllers;

use App\Enums\PaymentServiceEnum;
use App\Models\Order;
use App\Events\OrderPaid;
use App\Events\OrderFailed;
use App\Models\Transaction;
use App\Events\OrderExpired;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\NotificationTemplate;
use App\Models\PaymentConfig;

class XenditWebhookController extends Controller
{
   /**
    * Handle the incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function __invoke(Request $request)
   {
      if ($request->method() != 'POST') {
         echo 'Invalid request method';
         die;
      }
      $callbackToken = $request->server('HTTP_X_CALLBACK_TOKEN') ?? '';

      $xendit_callback_token = PaymentConfig::getValueByName('xendit_callback_token', PaymentServiceEnum::Xendit->value);

      if ($xendit_callback_token != $callbackToken) {

         return json_encode(['success' => false, 'message' => 'Token not valid']);
      }

      $json = $request->getContent();

      $result = json_decode($json, true);

      $event = null;

      $data = $result;

      if (isset($result['event'])) {
         $event = $result['event'];
      }

      if (isset($result['data'])) {
         $data = $result['data'];
      }

      $orderRef = null;

      if (isset($data['external_id'])) {
         $orderRef = $data['external_id'];
      } elseif (isset($data['reference_id'])) {
         $orderRef = $data['reference_id'];
      }

      if (isset($data['status'])) {
         $status = $data['status'];
      }

      // Log::debug('Xendit callback', $result);

      if (!$orderRef) {
         return json_encode(['success' => false, 'message' => "No order ref in payload"]);
      }

      $order = Order::with('transaction')->where('order_ref', $orderRef)->first();

      $messageEvent = null;

      if (!$order) {
         return json_encode(['success' => true, 'message' => "Order ref id {$orderRef} not found"]);
      }

      if ($order->is_deposit_type()) {
         $messageEvent = NotificationTemplate::ORDER_COMPLETED;
      } else {
         $messageEvent = NotificationTemplate::ORDER_PAYMENT_CONFIRMED;
      }

      if ($event && $event == 'ewallet.capture') {

         $transaction = $order->transaction;

         if ($transaction->status == 'PAID') {
            return json_encode(['success' => false, 'message' => 'Order status was paid']);
         }

         OrderPaid::dispatch($order);
      } else {

         $transaction = $order->transaction;
         $status = $data['status'];

         if ($transaction->status == 'PAID') {
            return json_encode(['success' => false, 'message' => 'Order status was paid']);
         }

         $transaction->payment_method = $data['payment_method'] ?? NULL;
         $transaction->payment_name = $data['payment_channel'] ?? NULL;
         $transaction->paid_at = NULL;

         if ($status == 'PAID') {

            $transaction->status = Transaction::PAID;
            $transaction->paid_at = now();
            $transaction->save();

            OrderPaid::dispatch($order);
         } else if ($status == 'EXPIRED') {

            $transaction->status = Transaction::EXPIRED;
            $transaction->save();

            OrderExpired::dispatch($order, 'Pembayaran Kadaluarsa');
            $messageEvent = NotificationTemplate::ORDER_FAILED;
         } else if ($status == 'FAILED') {

            $transaction->status = Transaction::FAILED;
            $transaction->save();

            OrderFailed::dispatch($order, 'Pembayaran Gagal');
            $messageEvent = NotificationTemplate::ORDER_FAILED;
         }
      }

      echo json_encode(['success' => true]);

      if ($messageEvent) {
         $order->dispatchEventMessage($messageEvent, true);
      }
   }
}
