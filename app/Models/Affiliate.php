<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Affiliate extends Model
{
   use HasFactory;
   
   protected $fillable = [
      'user_id',
      'status',
      'code',
      'coupon_code'
   ];

   const Active = 1;
   const Inactive = 2;
   const Suspended = 3;

   public $appends = [
      'status_color',
      'status_label',
      'is_active',
      'is_inactive',
      'is_suspended'
   ];

   public function getStatusLabelAttribute()
   {
      switch ($this->status) {
         case 1:
            return 'Active';
         case 2:
            return 'Inactive';
         case 3:
            return 'Suspended';
      }
   }
   public function getStatusColorAttribute()
   {
      switch ($this->status) {
         case 1:
            return 'green';
         case 2:
            return 'grey-8';
         case 3:
            return 'red';
      }
   }
   public function getIsACtiveAttribute()
   {
      return $this->status == self::Active;
   }
   public function getIsInactiveAttribute()
   {
      return $this->status == self::Inactive;
   }
   public function getIsSuspendedAttribute()
   {
      return $this->status == self::Suspended;
   }
   public function scopeActive($query)
   {
      return $query->where('status', self::Active);
   }
   public function user()
   {
      return $this->belongsTo(User::class);
   }
   public function mutasiSaldo()
   {
      return $this->morphMany(MutasiSaldo::class, 'entitiable');
   }
}
