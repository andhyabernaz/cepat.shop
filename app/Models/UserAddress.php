<?php

namespace App\Models;

use App\Traits\UseCourier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
   use HasFactory, UseCourier;

   protected $fillable = [
      'user_id',
      'is_primary',
      'title',
      'address_street',
      'subdistrict_id',
      'city_id',
      'receiver_name',
      'receiver_phone',
      'receiver_email',
      'subdistrict',
      'city',
      'province',
      'postal_code',
      'courier_service',
      'vendor_id',
      'coordinate'
   ];

   protected $casts = [
      'is_primary' => 'boolean',
      'coordinate' => 'array',
   ];

   public $timestamps = false;

   public $appends = ['is_complete', 'destination_id', 'full_address', 'name'];

   public function address()
   {
      return $this->belongsTo(Subdistrict::class, 'subdistrict_id', 'id');
   }
   public function user()
   {
      return $this->belongsTo(User::class);
   }
   public function getIsCompleteAttribute()
   {
      if ($this->courier_service == Config::RAJAONGKIR_SERVICE && $this->city_id == null) {
         return false;
      }
      if(!$this->coordinate) {
         return false;
      }
      return true;
   }
   public function getDestinationIdAttribute()
   {
      return $this->city_id;
   }
   public function getFullAddressAttribute()
   {
      $fullAddress = $this->address_street . ' ' . $this->subdistrict . ' ' . $this->city;
      if($this->province) {
         $fullAddress .= " {$this->province}";
      }
      $fullAddress .= " {$this->postal_code}";
      return trim($fullAddress);
   }
   public function getNameAttribute()
   {
      $fullAddress = $this->subdistrict . ' ' . $this->city . ' ' . $this->province;
      return trim($fullAddress);
   }
}
