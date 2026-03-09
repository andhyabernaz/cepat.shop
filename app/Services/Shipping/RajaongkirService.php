<?php

namespace App\Services\Shipping;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class RajaongkirService
{
   protected $api_key;
   protected $base_url = 'https://rajaongkir.komerce.id/api';

   protected $apiurls = [
      'starter' => 'https://rajaongkir.komerce.id/api',
      'pro' => 'https://rajaongkir.komerce.id/api',
   ];

   protected $couriers = [];

   public function __construct($apikey, $package = 'starter')
   {
      $this->api_key = $apikey;
      $this->base_url = $this->apiurls[$package];

      $couriers = [
         'pro' => config('rajaongkir.courier_pro'),
         'basic' => config('rajaongkir.courier_basic'),
         'starter' => config('rajaongkir.courier_starter')
      ];

      $this->couriers = $couriers[$package];
   }

   public function searchAddress($key)
   {
      $path = 'v1/destination/domestic-destination';
      $params = [
         "search" => $key
      ];

      $data = $this->httpGet($path, $params);

      return $data;
   }

   public function getCouriers()
   {
      return $this->couriers;
   }

   public function province()
   {
      return $this->httpGet('/province');
   }

   public function city($provinceId)
   {

      $payload = ['province' => $provinceId];
      return $this->httpGet('/city?' . http_build_query($payload));
   }
   public function cityAll()
   {
      return $this->httpGet('/city');
   }
   public function subdistrict($cityId)
   {

      $payload = ['city' => $cityId];
      return $this->httpGet('/subdistrict?' . http_build_query($payload));
   }

   public function getCosts(array $payload)
   {
      $path = 'v1/calculate/domestic-cost';
      return $this->httpPost($path, $payload);
   }
   public function trackingWaybill(array $payload)
   {
      $path = 'v1/track/waybill';
      return $this->httpPost($path, $payload);
   }
   protected function httpPost($urlpath, array $payload = [])
   {

      try {
         $url = $this->buildUrl($urlpath);
         $result = Http::asForm()->withHeaders([
            'key' => $this->api_key
         ])->post($url, $payload);
         $json = $result->body();
         // Log::debug($json);
         $resultArr = json_decode($json, true);

         if (isset($resultArr['meta'])) {
            if ($resultArr['meta']['code'] == 200) {
               if (isset($resultArr['data'])) {
                  $data = $resultArr['data'];
               } else {
                  $data = [];
               }
               return $data;
            } else {
               throw new Exception($resultArr['meta']['message']);
            }
         } else {
            throw new Exception('Unknown response data');
         }
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
         $result = Http::withHeaders([
            'key' => $this->api_key
         ])->get($url);
         $json = $result->body();
         // Log::debug($json);
         $resultArr = json_decode($json, true);

         if (isset($resultArr['meta'])) {
            if ($resultArr['meta']['code'] == 200) {
               if (isset($resultArr['data'])) {
                  $data = $resultArr['data'];
               } else {
                  $data = [];
               }
               return $data;
            } else {
               throw new Exception($resultArr['meta']['message']);
            }
         } else {
            throw new Exception('Unknown response data');
         }
      } catch (\Throwable $th) {
         throw $th;
      }
   }

   private function buildUrl($endpoint)
   {
      $url = rtrim($this->base_url, '/') . '/' . ltrim($endpoint, '/');
      return $url;
   }

   public function waybill_testing()
   {
      $sampleData =  [
         "delivered" => true,
         "summary" => [
            "courier_code" => "jne",
            "courier_name" => "Jalur Nugraha Ekakurir (JNE)",
            "waybill_number" => "SOCAG00183235715",
            "service_code" => "OKE",
            "waybill_date" => "2015-03-03",
            "shipper_name" => "IRMA F",
            "receiver_name" => "RISKA VIVI",
            "origin" => "WONOGIRI,KAB.WONOGIRI",
            "destination" => "PALEMBANG",
            "status" => "DELIVERED"
         ],
         "details" => [
            "waybill_number" => "SOCAG00183235715",
            "waybill_date" => "2015-03-03",
            "waybill_time" => "13:23",
            "weight" => "1",
            "origin" => "WONOGIRI,KAB.WONOGIRI",
            "destination" => "PALEMBANG",
            "shippper_name" => "IRMA F",
            "shipper_address1" => "WONOGIRI",
            "shipper_address2" => null,
            "shipper_address3" => null,
            "shipper_city" => "WONOGIRI",
            "receiver_name" => "RISKA VIVI",
            "receiver_address1" => "PERUMAHAN BUKIT SEJAHTERA",
            "receiver_address2" => "AF 05 RT 074\/022",
            "receiver_address3" => "PALEMBANG",
            "receiver_city" => "PALEMBANG"
         ],
         "delivery_status" => [
            "status" => "DELIVERED",
            "pod_receiver" => "RISKA",
            "pod_date" => "2015-03-05",
            "pod_time" => "13:22"
         ],
         "manifest" => [
            [
               "manifest_code" => "1",
               "manifest_description" => "Manifested",
               "manifest_date" => "2015-03-04",
               "manifest_time" => "03:41",
               "city_name" => "SOLO"
            ],
            [
               "manifest_code" => "2",
               "manifest_description" => "On Transit",
               "manifest_date" => "2015-03-04",
               "manifest_time" => "15:44",
               "city_name" => "JAKARTA"
            ],
            [
               "manifest_code" => "3",
               "manifest_description" => "Received On Destination",
               "manifest_date" => "2015-03-05",
               "manifest_time" => "08:57",
               "city_name" => "PALEMBANG"
            ]
         ]
      ];
      return $sampleData;
   }
}
