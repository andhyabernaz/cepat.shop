<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait UseCourier
{
   public static function bootUseCourier()
   {
      $config = getAppConfig();
      $courier = $config->courier_default;

      if(!$config->is_shipping_active) {
         $courier = 'Local';
      }

      static::addGlobalScope('courier_service', function (Builder $builder) use ($courier) {
         return $builder->where('courier_service', $courier);
      });

      static::creating(function ($model) use ($courier) {
         $model->courier_service = $courier;
      });
   }
}
