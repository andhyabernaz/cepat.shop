<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Slider extends Model
{
   use HasFactory;

   protected $fillable = [
      'filepath', 'weight', 'filename', 'post_id'
   ];
   public $timestamps = false;
   public $appends = ['src'];

   public function getSrcAttribute()
   {
      return url($this->filepath);
   }
   public function post()
   {
      return $this->belongsTo(Post::class);
   }
}
