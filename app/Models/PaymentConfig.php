<?php

namespace App\Models;

use App\Enums\PaymentServiceEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentConfig extends Model
{
   use HasFactory;

   protected $fillable = [
      'vendor',
      'name',
      'value'
   ];

   public $timestamps = false;

   public static function getValueByName($name, $vendor = null)
   {
      $val = PaymentConfig::when($vendor, function ($q) use ($vendor) {
         $q->where('vendor', $vendor);
      })->where('name', $name)->value('value');
      return $val;
   }
   public static function getConfigs($vendor = null)
   {
      $configs = PaymentConfig::when($vendor, function ($q) use ($vendor) {
         $q->where('vendor', $vendor);
      })->get();
      $data = [];
      foreach ($configs as $config) {
         $data[$config->name] = $config->value;
      }

      return $data;
   }

   public static function createDefault()
   {
      $paymentServices = PaymentServiceEnum::getDefaultConfig();

        foreach ($paymentServices as $payment) {
            foreach ($payment['fields'] as $item) {
                if (PaymentConfig::where('vendor', $payment['vendor'])->where('name', $item['name'])->count() == 0) {
                    $item['vendor'] = $payment['vendor'];
                    PaymentConfig::create($item);
                }
            }
        }
   }

}
