<?php

namespace App\Services\Shipping;

use App\Models\Subdistrict;

class LocalService
{
   public function searchAddress($keyword)
   {
      $key = "%{$keyword}";
      $data = Subdistrict::where('subdistrict_name', 'like', $key)
         ->orWhere('city', 'like', $key)
         ->orWhere('province', 'like', $key)
         ->get()
         ->toArray();
      return $data;
   }
}
