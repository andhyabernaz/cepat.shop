<?php

namespace App\Services\Payment;

use App\Enums\PaymentServiceEnum;
use App\Models\Config;
use Illuminate\Support\Facades\Log;

class PaymentService
{
   protected $service;
   protected $api_key;

   public function __construct()
   {
      $config = Config::select('payment_default')->value('payment_default');

      if ($config == PaymentServiceEnum::Xendit->value) {
         $this->service = new XenditPayment;
      }else if ($config == PaymentServiceEnum::Tripay->value) {
         $this->service = new TripayPayment;
      }else if ($config == PaymentServiceEnum::Midtrans->value) {
         $this->service = new MidtransPayment;
      }else if ($config == PaymentServiceEnum::Duitku->value) {
         $this->service = new DuitkuPayment;
      }
   }

   public function createTransaction($params)
   {
      try {
         $data = $this->service->createTransaction($params);
         return $data;
      } catch (\Throwable $th) {
         throw $th;
      }
   }

   public function paymentChanels($params = [])
   {
      $data = [];

       Log::debug(get_class($this->service));
      if(Config::isPgActive()) {
         $data = $this->service->paymentChanels($params);
      }
      return $data;
   }
}
