<?php

namespace App\Http\Controllers;

use App\Enums\PaymentServiceEnum;
use App\Events\OrderFailed;
use App\Events\OrderPaid;
use App\Models\NotificationTemplate;
use App\Models\Order;
use App\Models\PaymentConfig;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DuitkuWebhookController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {

        if ($request->method() != 'POST') {
            echo 'Invalid request method';
            exit;
        }

        try {
            Log::debug('callback duitku result', $request->all());

            //   "merchantCode": "DS28166",
            // "amount": "82000",
            // "merchantOrderId": "INV2602000095VB1",
            // "productDetail": "Transaksi INV2602000095VB1",
            // "additionalParam": null,
            // "resultCode": "00",
            // "paymentCode": "BT",
            // "merchantUserId": null,
            // "reference": "DS281662660MZ653CVC546NX",
            // "signature": "028518e9790e65f7c78e0605ff262805",
            // "publisherOrderId": "BT26IZMSIHIG66BJ46I",
            // "settlementDate": "2026-02-17",
            // "vaNumber": "8680015473079624",
            // "sourceAccount": null

            $apiKey = PaymentConfig::getValueByName('duitku_api_key', PaymentServiceEnum::Duitku->value);

            $merchantCode = $request->merchantCode ?? null;
            $amount = $request->amount ?? null;
            $merchantOrderId = $request->merchantOrderId ?? null;
            $paymentMethod = $request->paymentCode ?? null;
            $resultCode = $request->resultCode ?? null;
            $merchantUserId = $request->merchantUserId ?? null;
            $reference = $request->reference ?? null;
            $signature = $request->signature ?? null;
            $spUserHash = $request->spUserHash ?? null;
            $settlementDate = $request->settlementDate ?? null;
            $issuerCode = $request->issuerCode ?? null;

            $params = $merchantCode.$amount.$merchantOrderId.$apiKey;
            $calcSignature = md5($params);

            if (empty($merchantCode) || empty($amount) || empty($merchantOrderId) || empty($signature)) {
                throw new Exception('Bad Parameter');
            }

            if ($signature != $calcSignature) {
                throw new Exception('Bad Signature');
            }

            $order = Order::where('order_ref', $merchantOrderId)
                ->whereIn('order_status', ['PENDING', 'UNPAID'])
                ->first();

            if (! $order) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice current status is not UNPAID',
                ]);
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

            switch ($resultCode) {
                case '00':
                    OrderPaid::dispatch($order);
                    break;

                case '01':
                    OrderFailed::dispatch($order, 'Pembayaran Gagal atau kadalauarsa');
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
        } catch (\Throwable $th) {
            return response()->json([
                'success' => true,
                'message' => $th->getMessage(),
            ]);
        }
    }
}
