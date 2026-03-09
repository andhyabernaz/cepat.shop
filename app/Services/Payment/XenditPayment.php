<?php

namespace App\Services\Payment;

use Exception;
use Carbon\Carbon;
use App\Models\PaymentConfig;
use App\Enums\PaymentServiceEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class XenditPayment implements PaymentGateway
{
   protected $base_url = 'https://api.xendit.co';
   protected $xendit_api_key;
   protected $xendit_callback_token;
   protected $virtual_accounts = ['BCA', 'BNI', 'BRI', 'BJB', 'BSI', 'BNC', 'CIMB', 'DBS', 'MANDIRI', 'PERMATA'];
   protected $ewalets = ['OVO', 'DANA', 'LINKAJA', 'SHOPEEPAY', 'ASTRAPAY'];

   public function __construct()
   {
      $config = PaymentConfig::getConfigs(PaymentServiceEnum::Xendit->value);
      $this->xendit_api_key =  $config['xendit_api_key'];
      $this->xendit_callback_token = $config['xendit_callback_token'];
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
         $path = 'v2/invoices';

         $redirectUrl = route('invoice', $params['merchant_ref']);
         $payload = [
            'external_id' => $params['merchant_ref'],
            'currency' => 'IDR',
            'locale' => 'id',
            // 'payment_methods' => [$params['method']],
            'amount' => $params['amount'],
            'invoice_duration' => intval($params['order_expired_time']) * 3600,
            'success_redirect_url' => $redirectUrl,
            'failure_redirect_url' => $redirectUrl,
            'items' => $params['order_items']
         ];
         $response = $this->httpPost($path, $payload);
         $result = json_decode($response, true);

         if (isset($result['error_code'])) {
            throw new Exception($result['messages']);
         }

         // Log::info($response);

         $resultData = [
            'pay_code' => $result['account_number'] ?? null,
            'status' => $result['status'],
            'amount' => $result['amount'],
            'checkout_url' => $result['invoice_url'],
            'expired_date' => date_from_utc_to_locale($result['expiry_date']),
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

   public function getVaLists()
   {
      $path = 'available_virtual_account_banks';
      $response = $this->httpGet($path);
      $data = json_decode($response, true);

      $results = [];

      foreach ($data as $item) {
         if ($item['country'] == 'ID') {
            $results[] = [
               'group' => 'Virtual Account',
               'name' => $item['name'],
               'code' => $item['code'],
               'country' => $item['country'],
               'currency' => $item['currency'],
               'active' => $item['is_activated'],
            ];
         }
      }

      return $results;
   }

   protected function createVirtualAccount($params)
   {

      try {
         $path = 'callback_virtual_accounts';

         $expiration_date = Carbon::now()->addHours($params['order_expired_time'])->toIso8601String();
         $payload = [
            'external_id' => $params['merchant_ref'],
            'currency' => 'IDR',
            'bank_code' => $params['method'],
            'name' => $params['customer_name'],
            'is_single_use' => true,
            'is_closed' => true,
            'expected_amount' => $params['amount'],
            'expiration_date' => $expiration_date
         ];
         $response = $this->httpPost($path, $payload);
         $result = json_decode($response, true);

         $amount = 0;

         if (isset($result['capture_amount'])) {
            $amount = $result['capture_amount'];
         } else if (isset($result['charge_amount'])) {
            $amount = $result['charge_amount'];
         } else if (isset($result['expected_amount'])) {
            $amount = $result['expected_amount'];
         }

         return [
            'pay_code' => $result['account_number'],
            'expiration_date' => $result['expiration_date'],
            'status' => $result['status'],
            'amount' => $amount
         ];
      } catch (\Throwable $th) {
         throw $th;
      }
   }

   protected function createEwalet($params)
   {

      try {
         $path = 'ewallets/charges';
         $payload = [
            'reference_id' => $params['merchant_ref'],
            'currency' => 'IDR',
            'amount' => (int) $params['amount'],
            'checkout_method' => 'ONE_TIME_PAYMENT',
            'channel_code' => 'ID_' . $params['method'],
            'channel_properties' => [
               'mobile_number' => $this->formatPhoneNumber($params['customer_phone']),
               'success_redirect_url' => route('invoice', $params['merchant_ref'])
            ]
         ];

         // return $payload;

         $response = $this->httpPost($path, $payload);
         $result = json_decode($response, true);

         // Log::debug('xendit checkout', $result);

         if (isset($result['error_code'])) {
            throw new Exception($result['message']);
         }

         $qrString = '';
         $mobile_checkout_url = '';
         $web_checkout_url = '';

         if (isset($result['actions'])) {
            $actions = $result['actions'];

            $qrString = $actions['qr_checkout_string'] ?? null;

            $mobile_checkout_url = $actions['mobile_deeplink_checkout_url'] ?? null;

            if ($actions['mobile_web_checkout_url']) {
               $web_checkout_url = $actions['mobile_web_checkout_url'];
            } elseif ($actions['desktop_web_checkout_url']) {
               $web_checkout_url = $actions['desktop_web_checkout_url'];
            }
         }

         $amount = 0;

         if (isset($result['capture_amount'])) {
            $amount = $result['capture_amount'];
         } else if (isset($result['charge_amount'])) {
            $amount = $result['charge_amount'];
         } else if (isset($result['expected_amount'])) {
            $amount = $result['expected_amount'];
         }

         return [
            'pay_code' => $result['account_number'] ?? null,
            'expiration_date' => $result['expiration_date'] ?? null,
            'status' => $result['status'] ?? 'PENDING',
            'amount' => $amount,
            'qr_url' => $qrString,
            'mobile_checkout_url' => $mobile_checkout_url,
            'web_checkout_url' => $web_checkout_url,
         ];
      } catch (\Throwable $th) {
         throw $th;
      }
   }

   public function balance()
   {
      $path = 'balance';
      $response = $this->httpGet($path);

      return $response;
   }
   protected function formatPhoneNumber($number)
   {
      $formatted = "$number";

      if ("+62" == substr($formatted, 0, 3)) {
         return $formatted;
      }
      if ("0" == substr($formatted, 0, 1)) {
         $formatted = "+62" . substr($formatted, 1);
      }
      if ("8" == substr($formatted, 0, 1)) {
         $formatted = "+62" . substr($formatted, 1);
      }
      if ("62" == substr($formatted, 0, 2)) {
         $formatted = "+62" . substr($formatted, 2);
      }

      return $formatted;
   }
   protected function buildUrl($path)
   {
      return $this->base_url . '/' . ltrim($path, '/');
   }

   protected function httpPost($path, $payload)
   {
      return Http::withBasicAuth($this->xendit_api_key, '')->post($this->buildUrl($path), $payload);
   }

   protected function httpGet($path, $payload = [])
   {
      return Http::withBasicAuth($this->xendit_api_key, '')->get($this->buildUrl($path), $payload);
   }
}
