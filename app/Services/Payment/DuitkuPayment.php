<?php

namespace App\Services\Payment;

use App\Enums\PaymentServiceEnum;
use App\Models\PaymentConfig;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DuitkuPayment implements PaymentGateway
{
   protected $base_url;
   protected $duitku_api_key;
   protected $duitku_merchant_id;
   public function __construct()
   {
      $apiurls = config('duitku.base_urls');
      $cfg = PaymentConfig::getConfigs(PaymentServiceEnum::Duitku->value);
      $this->duitku_merchant_id = $cfg['duitku_merchant_id'];
      $this->duitku_api_key =  $cfg['duitku_api_key'];
      $this->base_url = $apiurls[$cfg['duitku_mode']];
   }
   public function paymentChanels($params = []): array
   {

      try {
         $cacheKey = 'duitku_payment_chanels_' . http_build_query($params);
         if (Cache::has($cacheKey))
            return Cache::get($cacheKey);
         $path = 'merchant/paymentmethod/getpaymentmethod';

         $datetime = date('Y-m-d H:i:s');
         $signature = hash('sha256', $this->duitku_merchant_id . $params['amount'] . $datetime . $this->duitku_api_key);

         $payload = array(
            'merchantcode' => $this->duitku_merchant_id,
            'amount' => $params['amount'],
            'datetime' => $datetime,
            'signature' => $signature
         );

         $response = $this->curlPost($this->buildUrl($path), $payload);

         $result = json_decode($response, true);
         if ($result['responseCode'] == '00' && isset($result['paymentFee'])) {
            Cache::put($cacheKey, $result['paymentFee'], now()->addHour());
            return $result['paymentFee'];
         }

         throw new Exception($result['responseMessage']);
      } catch (\Throwable $th) {
         throw $th;
      }
   }
   public function createTransaction(array $params): array
   {
      try {
         $path = 'merchant/v2/inquiry';

         $expMinutes = $params['order_expired_time'] * 60;
         if (in_array($params['method'], ['JP'])) {
            $expMinutes = 10;
         } else if (in_array($params['method'], ['SP', 'LQ', 'NQ', 'GQ'])) {
            $expMinutes = 60;
         } else {
            $expMinutes = $expMinutes <= 1440 ? $expMinutes : 1440;
         }
         $returnUrl = route('invoice', $params['merchant_ref']);
         $callbackUrl = route('duitku.callback');
         $signature = MD5($this->duitku_merchant_id . $params['merchant_ref'] . $params['amount'] . $this->duitku_api_key);
         $payload = [
            'merchantCode' => $this->duitku_merchant_id,
            'paymentAmount' => $params['amount'],
            'merchantOrderId' => $params['merchant_ref'],
            'productDetails' => 'Transaksi ' . $params['merchant_ref'],
            'email' => $params['customer_email'],
            'paymentMethod' => $params['method'],
            'customerVaName' => $params['customer_name'],
            'phoneNumber' => $params['customer_phone'],
            'returnUrl' => $returnUrl,
            'callbackUrl' => $callbackUrl,
            'signature' => $signature,
            'expiryPeriod' => $expMinutes
         ];
         $response = $this->curlPost($this->buildUrl($path), $payload);

         Log::debug($response);

         $result = json_decode($response, true);

         if ($result['statusCode'] == '00') {
            return [
               'reference' => $result['reference'],
               'pay_code' => $result['vaNumber'] ?? null,
               'amount' => $result['amount'],
               'qr_string' => $result['qrString'] ?? null,
               'pay_url' => $result['paymentUrl'],
               'expired_date' => Carbon::now()->addMinutes($expMinutes)->toDateTimeString()
            ];
         }

         throw new Exception($result['statusMessage']);
      } catch (\Throwable $th) {
         throw $th;
      }
   }
   protected function buildUrl($path)
   {
      return $this->base_url . '/' . ltrim($path, '/');
   }
   protected function curlPost($url, $params)
   {
      try {
         $params_string = json_encode($params);

         $ch = curl_init();

         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
         curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
               'Content-Type: application/json',
               'Content-Length: ' . strlen($params_string)
            )
         );
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

         $request = curl_exec($ch);
         $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

         if ($httpCode == 200) {
            return $request;
         }
         $request = json_decode($request);
         $error_message = "Server Error " . $httpCode . " " . $request->Message;
         throw new Exception($error_message);
      } catch (\Throwable $th) {
         throw $th;
      }
   }
}
