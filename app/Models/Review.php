<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
   use HasFactory;

   protected $fillable = [
      'product_id',
      'comment',
      'rating',
      'name',
      'is_approved',
      'product_name',
      'product_varian',
      'user_id'
   ];

   public $appends = [
      'created'
   ];

   protected $casts = [
      'is_approved' => 'boolean'
   ];

   public function getCreatedAttribute()
   {
      if ($this->created_at > Carbon::now()->subWeek()) {
         return Carbon::parse($this->created_at)->diffForHumans();
      } else {
         return Carbon::parse($this->created_at)->format('d M Y');
      }
   }

   public function product()
   {
      return $this->belongsTo(Product::class);
   }

   public function scopeApproved($query)

   {
      return $query->where('is_approved', 1);
   }
   public function scopeUnapproved($query)
   {
      return $query->where('is_approved', 0);
   }
   public function user()
   {
      return $this->belongsTo(User::class);
   }
   public function reviewImages()
   {
      return $this->morphMany(Asset::class, 'assetable');
   }
}
