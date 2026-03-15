<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promo extends Model
{
   use HasFactory;
   protected $guarded = [];

   public $appends = ['is_active', 'now', 'start', 'end', 'diff_start', 'diff_end', 'status'];

   public function getIsActiveAttribute()
   {
      if (Carbon::parse($this->start_date) <= Carbon::now() && Carbon::parse($this->end_date) > Carbon::now()) {
         return true;
      }
      return false;
   }
   public function getIsLaterAttribute()
   {
      if (Carbon::parse($this->start_date) > Carbon::now() && Carbon::parse($this->end_date) > Carbon::now()) {
         return true;
      }
      return false;
   }

   public function getIsExpiredAttribute()
   {
      if (Carbon::parse($this->end_date) < Carbon::now()) {
         return true;
      }
      return false;
   }
   public function getStatusAttribute()
   {
      if (Carbon::parse($this->end_date) < Carbon::now()) {
         return 'expired';
      }
      if (Carbon::parse($this->start_date) <= Carbon::now() && Carbon::parse($this->end_date) > Carbon::now()) {
         return 'active';
      }
      if (Carbon::parse($this->start_date) > Carbon::now() && Carbon::parse($this->end_date) > Carbon::now()) {
         return 'later';
      }
   }
   public function getNowAttribute()
   {
      return now();
   }
   public function getStartAttribute()
   {
      return Carbon::parse($this->start_date)->format('d F Y H:i');
   }
   public function getEndAttribute()
   {
      return Carbon::parse($this->end_date)->format('d F Y H:i');
   }
   public function getDiffStartAttribute()
   {
      return Carbon::parse($this->start_date)->diffForHumans();
   }
   public function getDiffEndAttribute()
   {
      return Carbon::parse($this->end_date)->diffForHumans();
   }
   public function products()
   {
      return $this->belongsToMany(Product::class, 'product_promos', 'promo_id', 'product_id')->withPivot('discount_type', 'discount_amount');
   }
   public function scopeActive($query)
   {
      $now = new \DateTime();
      return $query->where('start_date', '<=', $now)->where('end_date', '>', $now);
   }
   public function scopeRunning($query)
   {
      $now = new \DateTime();
      return $query->where('start_date', '<=', $now)->where('end_date', '>', $now);
   }
   public function scopelater($query)
   {
      $now = new \DateTime();
      return $query->where('start_date', '>', $now)->where('end_date', '>', $now);
   }
   public function scopeExpired($query)
   {
      $now = new \DateTime();
      return $query->where('end_date', '<', $now);
   }
}
