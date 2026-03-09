<?php

namespace App\Services\Shipping;

use Exception;
use App\Models\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CourierService
{
   protected $service;
   protected $config;
   protected $active_couriers = [];
   protected $origin_id;
   protected $package;
   protected $apikey;
   protected $warehouse_coordinate = [];
   protected $is_rajaongkir_service = false;

   public function __construct()
   {
      $this->init();
   }

   public function searchAddress($request)
   {

      try {

         if (!$this->config->is_shipping_active) {
            $this->service = new LocalService();
         }

         $keyword = $request->q;

         $data = $this->service->searchAddress($keyword);
         return $this->formatResponseAddress($data);
      } catch (\Throwable $th) {
         throw $th;
      }
   }
   public function configAddress($request)
   {

      try {

         $keyword = $request->q;

         $data = $this->service->searchAddress($keyword);
         return $this->formatResponseAddress($data);
      } catch (\Throwable $th) {
         throw $th;
      }
   }

   public function getCouriers($request)
   {

      if ($request->courier_default && ($request->rajaongkir_apikey || $request->biteship_apikey)) {

         if ($request->courier_default == Config::RAJAONGKIR_SERVICE) {
            $this->apikey = $request->rajaongkir_apikey;
            $this->package = $request->rajaongkir_type;
            $this->service = new RajaongkirService($this->apikey, $this->package);
            $this->is_rajaongkir_service = true;
         } else if ($request->courier_default == Config::BITESHIP_SERVICE) {
            $this->apikey = $request->biteship_apikey;
            $this->service = new BiteshipService($this->apikey);
            $this->is_rajaongkir_service = false;
         } else {
            return [];
         }
      }
      return $this->service->getCouriers();
   }

   public function getCosts(array $payload)
   {

      try {

         $weightKey = intval($payload['weight']);
         if ($weightKey <= 1400) {
            $weightKey = 1;
         }
         if ($weightKey > 1400 && $weightKey <= 2400) {
            $weightKey = 2;
         }
         if ($weightKey > 2400 && $weightKey <= 3400) {
            $weightKey = 3;
         }

         $cacheKey = $this->origin_id . $payload['destination'] . $weightKey;

         if ($this->is_rajaongkir_service) {
            $this->active_couriers = $this->config->rajaongkir_couriers;
            $this->origin_id = $this->config->warehouse_address->city_id;
         } else {
            $this->active_couriers = $this->config->biteship_couriers;
            $this->origin_id = $this->config->biteship_warehouse->city_id;
         }

         $couriersArr = array_map(function ($item) {
            return $item['value'];
         }, $this->active_couriers);

         if ($this->is_rajaongkir_service) {

            $reqPayload = [
               "origin"        => $this->origin_id,
               "destination"   => $payload['destination'],
               "weight"        => $payload['weight'],
               "courier"      =>  implode(":", $couriersArr)
            ];

            $cacheKey .= implode(":", $couriersArr);
         } else {

            // BITESHIP

            $items = [];

            foreach ($payload['items'] as $item) {
               $item = [
                  'name' => $item['name'],
                  'weight' => $item['weight'],
                  'quantity' => $item['quantity'],
                  'value' => $item['price'],
               ];

               $items[] = $item;
            }

            $reqPayload = [
               'items' => $items,
               'couriers' =>  implode(",", $couriersArr)
            ];

            $couriersTypes = array_map(function ($item) {
               return $item['service_type'];
            }, $this->active_couriers);


            if ((in_array('same_day', $couriersTypes) || in_array('instant', $couriersTypes)) && count($this->warehouse_coordinate) > 0 && isset($payload['destination_latitude']) && isset($payload['destination_longitude'])) {
               $reqPayload['origin_latitude'] = $this->warehouse_coordinate[0];
               $reqPayload['origin_longitude'] =  $this->warehouse_coordinate[1];
               $reqPayload['destination_latitude'] = $payload['destination_latitude'];
               $reqPayload['destination_longitude'] = $payload['destination_longitude'];
            } else {
               $reqPayload['origin_area_id'] = $this->origin_id;
               $reqPayload['destination_area_id'] = $payload['destination'];
            }

            $cacheKey .=  implode(",", $couriersArr);
         }

         if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
         }

         $costs = [];
         $result = $this->service->getCosts($reqPayload);

         // Log::debug('result costs', $result);

         if (count($result) > 0) {
            $costs = $this->formatResponseCosts($result);
            Cache::put($cacheKey, $costs, now()->addHours(5));
         }

         return $costs;
      } catch (Exception $e) {

         throw $e;
      }
   }

   public function trackingWaybill($order)
   {

      try {

         $waybillPayload = [
            'courier' => $order->shipping_courier_id,
            'waybill' => $order->shipping_courier_code
         ];

         if ($this->is_rajaongkir_service) {
            $waybillPayload = [
               'courier' => $order->shipping_courier_id,
               'awb' => $order->shipping_courier_code
            ];

            if ($order->shipping_courier_id == 'jne') {
               $waybillPayload['last_phone_number'] = substr($order->customer_whatsapp, -5);
            }
         }
         $result = $this->service->trackingWaybill($waybillPayload);

         // Log::debug('tracking Results', $result);

         $data = $this->formatResponseWaybill($result);

         return $data;
      } catch (Exception $e) {

         throw $e;
      }
   }

   protected function formatResponseWaybill($payload)
   {

      $data = [
         'histories' => [],
         'delivered' => false,
         'received_at' => null,
         'shipping_status' => 'ON PROCESS'
      ];

      if ($this->is_rajaongkir_service) {

         $manifest = $payload['manifest'] ?? [];

         foreach ($manifest as $item) {

            $mcode = $item['manifest_date'] . $item['manifest_time'];

            $data['histories'][] = [
               'manifest_code' => $mcode,
               'date' => $item['manifest_date'],
               'time' => $item['manifest_time'],
               'city_name' => $item['city_name'],
               'description' =>  $item['manifest_description']
            ];
         }

         if ($payload['delivered'] == true) {

            $receiverName =  $payload['delivery_status']['pod_receiver'];

            $desc = 'Pesanan Terkirim dan telah diterima oleh ' . $receiverName;

            $receivedDate = $payload['delivery_status']['pod_date'] . ' ' .  $payload['delivery_status']['pod_time'];

            $data['histories'][] = [
               'manifest_code' => 'delivered',
               'date' => $payload['delivery_status']['pod_date'],
               'time' => $payload['delivery_status']['pod_time'],
               'description' =>  $desc

            ];

            $data['delivered'] = true;
            $data['received_at'] = $receivedDate;
            $data['shipping_status'] = $payload['summary']['status'];
         }
      } else {
         if (isset($payload['history']) && count($payload['history']) > 0) {

            foreach ($payload['history'] as $item) {

               $data['histories'][] = [
                  'manifest_code' => $item['updated_at'],
                  'date' => Carbon::parse($item['updated_at'])->toDateString(),
                  'time' => Carbon::parse($item['updated_at'])->toTimeString(),
                  'description' =>  $item['note']
               ];
            }

            if (isset($payload['status']) && $payload['status'] == 'delivered') {

               $data['delivered'] = true;
               $data['received_at'] =  Carbon::parse($item['updated_at'])->toDateTimeString();
            }

            if (isset($payload['status'])) {
               $data['shipping_status'] = strtoupper($payload['status']);
            }
         }
      }
      return $data;
   }

   protected function formatResponseCosts($payload)
   {

      $data = [];

      if ($this->is_rajaongkir_service) {
         for ($i = 0; $i < count($payload); $i++) {

            $item['id'] = 'RJ' . $payload[$i]['service'] . $payload[$i]['code'];
            $item['company'] = $payload[$i]['name'];
            $item['courier_name'] = $payload[$i]['name'];
            $item['courier_code'] = $payload[$i]['code'];
            $item['courier_service_name'] = $payload[$i]['description'];
            $item['courier_service_code'] = $payload[$i]['service'];
            $item['price'] = $payload[$i]['cost'];
            $item['duration'] = $payload[$i]['etd'];

            $data[] = $item;
         }
      } else {
         foreach ($payload as $item) {
            $item['id'] = 'BT' . $item['courier_code'] . $item['courier_service_code'];
            $data[] = $item;
         }
      }

      return $data;
   }

   protected function formatResponseAddress($payload)
   {

      $data = [];

      if ($this->service instanceof RajaongkirService) {

         for ($i = 0; $i < count($payload); $i++) {

            $item['vendor_id'] = $payload[$i]['id'];
            $item['id'] = $payload[$i]['id'];
            $item['city_id'] = $payload[$i]['id'];
            $item['name'] = $payload[$i]['label'];
            $item['subdistrict'] = $payload[$i]['subdistrict_name'] . ' ' . $payload[$i]['district_name'];
            $item['city'] = $payload[$i]['city_name'];
            $item['postal_code'] = $payload[$i]['zip_code'] ?? NULL;
            $item['country_name'] = 'Indonesia';

            $data[] = $item;
         }
      } else if($this->service instanceof BiteshipService){

         for ($i = 0; $i < count($payload); $i++) {

            $item['id'] = $payload[$i]['id'];
            $item['vendor_id'] = $payload[$i]['id'];
            $item['name'] = $payload[$i]['name'];
            $item['city_id'] = $payload[$i]['id'];
            $item['subdistrict'] = $payload[$i]['administrative_division_level_3_name'];
            $item['city'] = $payload[$i]['administrative_division_level_2_name'];
            $item['province'] = $payload[$i]['administrative_division_level_1_name'];
            $item['postal_code'] = $payload[$i]['postal_code'] ?? NULL;
            $item['country_name'] = $payload[$i]['country_name'] ?? NULL;
            // $item['orig'] = $payload[$i];

            $data[] = $item;
         }
      }else {

         for ($i = 0; $i < count($payload); $i++) {

            $item['id'] = $payload[$i]['id'];
            $item['vendor_id'] = $payload[$i]['id'];
            $item['name'] = $payload[$i]['name'];
            $item['city_id'] = $payload[$i]['city_id'];
            $item['subdistrict'] = $payload[$i]['subdistrict_name'];
            $item['city'] = $payload[$i]['city'];
            $item['province'] = $payload[$i]['province'];
            $item['postal_code'] = $payload[$i]['postal_code'] ?? NULL;
            $item['country_name'] = $payload[$i]['country_name'] ?? NULL;
            // $item['orig'] = $payload[$i];

            $data[] = $item;
         }

      }


      return $data;
   }

   public function init()
   {
      $this->config = Cache::remember('courier_config', now()->addMinutes(30), function () {
         return Config::select(
            'is_shipping_active',
            'courier_default',
            'rajaongkir_apikey',
            'rajaongkir_type',
            'warehouse_address',
            'rajaongkir_couriers',
            'biteship_apikey',
            'biteship_couriers',
            'biteship_warehouse',
            'warehouse_coordinate'
         )->first();
      });

      if ($this->config->courier_default == Config::BITESHIP_SERVICE) {

         $this->is_rajaongkir_service = false;
         $this->apikey = $this->config->biteship_apikey;
         $this->service = new BiteshipService($this->apikey);

         if ($this->config->warehouse_coordinate && count($this->config->warehouse_coordinate) > 0) {

            $this->warehouse_coordinate = $this->config->warehouse_coordinate;
         }
      } else {
         $this->package = $this->config->rajaongkir_type;
         $this->apikey = $this->config->rajaongkir_apikey;
         if ($this->config->rajaongkir_type == 'pro' && config('rajaongkir.api_pro_token')) {
            $this->apikey = config('rajaongkir.api_pro_token');
         }
         $this->service = new RajaongkirService($this->apikey, $this->package);
         $this->is_rajaongkir_service = true;
      }
   }
}
