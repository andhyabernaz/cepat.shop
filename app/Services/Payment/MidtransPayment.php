<?php

namespace App\Services\Payment;

use Exception;
use Carbon\Carbon;
use App\Models\PaymentConfig;
use App\Enums\PaymentServiceEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class MidtransPayment implements PaymentGateway
{
   protected $base_url;
   protected $midtrans_merchant_id;
   protected $midtrans_server_key;
   protected $midtrans_client_key;
   protected $virtual_accounts = ['BCA', 'BNI', 'BRI', 'BJB', 'BSI', 'BNC', 'CIMB', 'DBS', 'MANDIRI', 'PERMATA'];
   protected $ewalets = ['OVO', 'DANA', 'LINKAJA', 'SHOPEEPAY', 'ASTRAPAY'];

   public function __construct()
   {
      $apiurls = config('midtrans.base_urls');
      $cfg = PaymentConfig::getConfigs(PaymentServiceEnum::Midtrans->value);
      $this->midtrans_merchant_id = $cfg['midtrans_merchant_id'];
      $this->midtrans_server_key =  $cfg['midtrans_server_key'];
      $this->midtrans_client_key = $cfg['midtrans_client_key'];
      $this->base_url = $apiurls[$cfg['midtrans_mode']];
   }

   public function paymentChanels($params = []): array
   {
      $payments = [];
      foreach ($this->virtual_accounts as $item) {
         $payments[] = [
            'active' => true,
            'group' => 'Virtual Account',
            'name' => $item . ' Virtual Account',
            'code' => $item
         ];
      }
      foreach ($this->ewalets as $item) {
         $payments[] = [
            'active' => true,
            'group' => 'E-Wallet',
            'name' => $item,
            'code' => $item
         ];
      }
      return $payments;
   }

   public function createTransaction($params): array
   {
      try {
         $path = 'snap/v1/transactions';

         $headers = [
            'X-Override-Notification' => route('midtrans.callback')
         ];

         $redirectUrl = route('invoice', $params['merchant_ref']);

         $payload = [
            'transaction_details' => [
               'order_id' => $params['merchant_ref'],
               'gross_amount' => $params['amount']
            ],
            'customer_details' => [
               "name" => $params['customer_name'],
               "email" => $params['customer_email'],
            ],
            "expiry" =>  [
               "start_time" => Carbon::now()->format('Y-m-d H:i:s P'),
               "unit" => "hour",
               "duration" => intval($params['order_expired_time'])
            ],
            "callbacks" => [
               "finish" => $redirectUrl,
               "error" => $redirectUrl
            ],
            // 'item_details' => $params['order_items']
         ];
         $response = $this->httpPost($path, $payload, $headers);
         $result = json_decode($response, true);

         if (isset($result['error_messages'])) {
            throw new Exception($result['error_messages'][0]);
         }

         // Log::info($response);

         $resultData = [
            'snap_token' => $result['token'] ?? null,
            'checkout_url' => $result['redirect_url'],
            'amount' => $params['amount'],
         ];

         if (isset($result['payment_channel'])) {
            $resultData['payment_channel'] = $result['payment_channel'];
         }
         if (isset($result['payment_method'])) {
            $resultData['payment_method'] = $result['payment_method'];
         }

         return $resultData;
      } catch (\Throwable $th) {
         Log::error($th);
         throw $th;
      }
   }
   protected function buildUrl($path)
   {
      return $this->base_url . '/' . ltrim($path, '/');
   }

   protected function httpPost($path, $payload, $headers = [])
   {
      return Http::asJson()
         ->withBasicAuth($this->midtrans_server_key, '')
         ->withHeaders($headers)
         ->post($this->buildUrl($path), $payload);
   }

   protected function httpGet($path, $payload = [])
   {
      return Http::adJson()->withBasicAuth($this->midtrans_server_key, '')->get($this->buildUrl($path), $payload);
   }
}
