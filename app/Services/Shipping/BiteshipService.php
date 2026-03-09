<?php

namespace App\Services\Shipping;

use App\Models\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class BiteshipService
{
   protected $api_key;
   protected $base_url = 'https://api.biteship.com';

   public function __construct($apikey)
   {
      $this->api_key = $apikey;
   }

   // public static function init(array $config)
   // {
   //   self::api_key = $config['api_key'];
   //   self::base_url = $config['base_url'];
   //    return new static(self::class);
   // }

   public function searchAddress($keyword)
   {

      try {
         $data = [];
         $endpoint = '/v1/maps/areas';
         $params = [
            'countries' => 'ID',
            'input' => $keyword,
            'type' => 'single'
         ];
         $cacheKey = 'biteship_search_' . $keyword;
         if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
         }
         $result = $this->httpGet($endpoint, $params);
         // Log::debug($result);
         $obj = json_decode($result, true);
         if ($obj['success'] == true) {
            $data = $obj['areas'];
            if (count($data) > 0) {
               Cache::put($cacheKey, $data);
            }
         }
         return $data;
      } catch (\Throwable $th) {
         throw $th;
      }
   }
   public function getCouriers()
   {

      try {
         $data = [];
         $cacheKey = 'biteship_couriers';
         if (Cache::has($cacheKey)) {
            $data = Cache::get($cacheKey);
         } else {

            $endpoint = '/v1/couriers';
            $result = $this->httpGet($endpoint);
            $obj = json_decode($result, true);

            if ($obj['success'] == true) {
               $data = $obj['couriers'];

               Cache::put($cacheKey, $data);
            }
         }

         $collection = collect($data);
         // $collection = $collection->where('shipment_duration_unit', '!=', 'hours');
         $unique = $collection->unique('courier_code')->map(function ($el) {

            $el['label'] =  $el['courier_name'];
            $el['value'] = $el['courier_code'];
            $el['vendor'] =  Config::BITESHIP_SERVICE;
            return $el;
         });

         return array_values($unique->toArray());

         return $unique;
      } catch (\Throwable $th) {
         throw $th;
      }
   }

   public function getCosts(array $payload)
   {

      try {
         $data = [];
         $endpoint = '/v1/rates/couriers';
         $result = $this->httpPost($endpoint, $payload);
         $obj = json_decode($result, true);
         if ($obj['success'] == true && isset($obj['pricing'])) {
            $data = $obj['pricing'];
         } else {
            Log::debug($result);
         }
         return $data;
      } catch (\Throwable $th) {
         throw $th;
      }
   }
   public function trackingWaybill(array $payload)
   {
      $data = [];
      $waybill = $payload['waybill'];
      $courier = $payload['courier'];

      $endpoint = "/v1/trackings/{$waybill}/couriers/{$courier}";

      $result = $this->httpGet($endpoint);
      $obj = json_decode($result, true);

      if ($obj['success'] == true) {
         $data = $obj;
      }
      return $data;
   }

   protected function httpPost($urlpath, array $payload = [])
   {

      try {
         $url = $this->buildUrl($urlpath);
         $result = Http::withToken($this->api_key)->post($url, $payload);
         return $result->body();
      } catch (\Throwable $th) {
         throw $th;
      }
   }
   protected function httpGet($urlpath, array $payload = [])
   {

      try {
         $url = $this->buildUrl($urlpath);
         if (count($payload) > 0) {
            $url = $url . '?' . http_build_query($payload);
         }
         $result = Http::withToken($this->api_key)->get($url);
         return $result->body();
      } catch (\Throwable $th) {
         throw $th;
      }
   }
   protected function buildUrl($urlpath)
   {
      return rtrim($this->base_url, '/') . '/' . ltrim($urlpath, '/');
   }
}
