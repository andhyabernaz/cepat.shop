<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
   use HasFactory;

   public $timestamps = false;
   protected $fillable = [
      'filename', 'filepath', 'variable', 'disk', 'visibility', 'sort', 'assetable_type', 'assetable_id'
   ];
   public $appends = ['src', 'url'];

   protected $hidden = [
      'assetable_id',
      'assetable_type'
   ];

   public function getSrcAttribute()
   {
      return url($this->filepath);
   }
   public function getUrlAttribute()
   {
      return url($this->filepath);
   }
}
