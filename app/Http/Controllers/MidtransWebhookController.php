<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Events\OrderPaid;
use App\Events\OrderFailed;
use App\Models\Transaction;
use App\Events\OrderExpired;
use Illuminate\Http\Request;
use App\Models\PaymentConfig;
use App\Enums\PaymentServiceEnum;
use Illuminate\Support\Facades\Log;
use App\Models\NotificationTemplate;

class MidtransWebhookController extends Controller
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

        $serverKey = PaymentConfig::getValueByName('midtrans_server_key', PaymentServiceEnum::Midtrans->value);

        $json = $request->getContent();

        $result = json_decode($json, true);

        $orderRef = $result['order_id'] ?? null;
        $incomingStatus = $result['transaction_status'] ?? null;
        $statusCode = $result['status_code'];
        $incomingSignature =  $result['signature_key'] ?? null;
        $fraudStatus = $result['fraud_status'] ?? null;
        $grossAmount = $result['gross_amount'];

        $successedStatus = ['settlement', 'capture'];
        $failureStatus = ['cancel', 'expire', 'failure'];

        // Log::debug('Midtrans callback', $result);

        $sign = $this->generateSign($orderRef, $statusCode, $grossAmount, $serverKey);

        if ($incomingSignature != $sign) {
            return json_encode(['success' => false, 'message' => "Invalid Credentials"]);
        } 

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

        $transaction = $order->transaction;

        if ($transaction->status == 'PAID') {
            return json_encode(['success' => true, 'message' => "Order ref id {$orderRef} status already paid"]);
        }

        $transaction->paid_at = NULL;

        if (in_array($incomingStatus, $successedStatus)) {

            $transaction->status = Transaction::PAID;
            $transaction->paid_at = now();
            $transaction->save();

            OrderPaid::dispatch($order);
        } else if (in_array($incomingStatus, $failureStatus)) {
            if ($incomingStatus == 'expire') {

                $transaction->status = Transaction::EXPIRED;
                $transaction->save();

                OrderExpired::dispatch($order, 'Pembayaran kadaluarsa');
                $messageEvent = NotificationTemplate::ORDER_FAILED;
            } else {

                $transaction->status = Transaction::FAILED;
                $transaction->save();

                OrderFailed::dispatch($order, 'Pembayaran Gagal');
                $messageEvent = NotificationTemplate::ORDER_FAILED;
            }
        }

        echo json_encode(['success' => true, 'message' => 'Pembayaran sukses']);

        if ($messageEvent) {
            $order->dispatchEventMessage($messageEvent, true);
        }
    }

    protected function generateSign($orderRef, $statusCode, $grossAmount, $serverKey)
    {
        $input = $orderRef . $statusCode . $grossAmount . $serverKey;
        $signature = openssl_digest($input, 'sha512');
        return $signature;
    }
}
