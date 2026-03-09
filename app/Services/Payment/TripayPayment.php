<?php

namespace App\Services\Payment;

use App\Enums\PaymentServiceEnum;
use App\Models\PaymentConfig;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class TripayPayment implements PaymentGateway
{
   protected $base_url;
   protected $tripay_api_key;
   protected $tripay_merchant_id;
   protected $tripay_private_key;
   protected $mode = 'sandbox';

   protected $apiurls = [
      'sanbox' => 'https://tripay.co.id/api-sandbox',
      'sandbox' => 'https://tripay.co.id/api-sandbox',
      'production' => 'https://tripay.co.id/api'
   ];

   public function __construct()
   {
      $tripayConfig = PaymentConfig::getConfigs(PaymentServiceEnum::Tripay->value);
      $this->tripay_api_key = $tripayConfig['tripay_api_key'];
      $this->tripay_private_key =  $tripayConfig['tripay_private_key'];
      $this->tripay_merchant_id = $tripayConfig['tripay_merchant_id'];
      $this->base_url = $this->apiurls[$tripayConfig['tripay_mode']];
   }

   public function paymentChanels($params = []): array
   {
      try {

         $cacheKey = 'tripay_payment_chanels';

         if(Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
         }

         $json = $this->curlGet('/merchant/payment-channel');

         $obj = json_decode($json, true);

         if ($obj['success'] == true) {
            $data = isset($obj['data']) ? $obj['data'] : [];

            if(count($data) > 0) {
               Cache::put($cacheKey, $data, now()->addHours(3));
            }
            return $data;
         }
         throw new Exception($obj['message']);
      } catch (\Throwable $th) {
         throw $th;
      }
   }

   public function createTransaction(array $payload): array
   {

      try {
         $expired_time = Carbon::now()->addHours($payload['order_expired_time'])->timestamp;

         $payload['expired_time'] = $expired_time;
         $payload['callback_url'] = route('tripay.callback');
         $payload['signature'] = $this->generateSignature($payload['merchant_ref'], $payload['amount']);

         $result = $this->curlPost('/transaction/create', $payload);

         $obj = json_decode($result, true);

         if ($obj['success'] == false) {
            throw new Exception($obj['message']);
         }

         $resultData = $obj['data'];
         $resultData['expired_date'] = Carbon::createFromTimestamp($resultData['expired_time']);
         return $resultData;
         
      } catch (\Throwable $th) {
         throw $th;
      }
   }

   public function transactionDetail($reference)
   {
      $payload = [
         'reference' => $reference
      ];

      $result = $this->curlGet('/transaction/detail', $payload);
      return $result;
   }

   public function calculatorFee($code, $amount)
   {
      $payload = [
         'code' => $code,
         'amount' => $amount
      ];

      $result = $this->curlGet('/merchant/fee-calculator', $payload);
      return $result;
   }

   protected function buildUrl($urlpath)
   {
      return rtrim($this->base_url, '/') . '/' . ltrim($urlpath, '/');
   }

   protected function curlGet($urlpath, $payload = [])
   {
      $url = $this->buildUrl($urlpath);

      if (count($payload) > 0) {
         $url = $url . '?' . http_build_query($payload);
      }

      $curl = curl_init();

      $header = [
         'Authorization: Bearer ' . $this->tripay_api_key
      ];

      curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_HEADER, false);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
      curl_setopt($curl, CURLOPT_FAILONERROR, false);

      $response = curl_exec($curl);
      $error = curl_error($curl);
      $errno  = curl_errno($curl);

      curl_close($curl);

      if ($errno) {

         return json_encode([
            'success' => false,
            'message' => $error,
            'connected' => false,
         ]);
      }

      return $response;
   }

   protected function curlPost($urlpath, $data)
   {
      $url = $this->buildUrl($urlpath);

      $header = [
         'Authorization: Bearer ' . $this->tripay_api_key
      ];
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_HEADER, false);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
      curl_setopt($curl, CURLOPT_FAILONERROR, false);
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

      $response = curl_exec($curl);
      $error = curl_error($curl);
      $errno  = curl_errno($curl);
      curl_close($curl);

      // Log::debug($response);

      if ($errno) {

         return json_encode([
            'success' => false,
            'message' => $error,
            'connected' => false,
         ]);
      }

      return $response;
   }

   public function generateSignature($orderRef, $orderTotal)
   {
      return hash_hmac('sha256', $this->tripay_merchant_id . $orderRef . $orderTotal, $this->tripay_private_key);
   }
}
