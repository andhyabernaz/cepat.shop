<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DigitalDownload extends Model
{
   use HasFactory, SoftDeletes;

   protected $guarded = [];

   const CLASSIFIED = ['filepath', 'src'];

   public function scopeActive($query)
   {
      return $query->where('is_active', 1);
   }
   public function scopeNotActive($query)
   {
      return $query->where('is_active', 0);
   }
}
