<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
   use HasFactory;

   protected $guarded = [];
   public $appends = [
      'image_url',
      'created_locale',
      'teaser'
   ];

   public $casts = [
      'is_listing' => 'boolean',
      'is_promote' => 'boolean'
   ];

   public function scopeListing()
   {
      return $this->where('is_listing', 1);
   }
   public function scopePromote()
   {
      return $this->where('is_promote', 1);
   }

   public function asset()
   {
      return $this->morphOne(Asset::class, 'assetable');
   }

   public function user()
   {
      return $this->belongsTo(User::class);
   }

   public function getImageUrlAttribute()
   {
      return $this->image ? url('/upload/images/' . $this->image) : '';
   }

   public function getCreatedLocaleAttribute()
   {
      return Carbon::parse($this->created_at)->translatedFormat('d F Y');
   }
   public function title(): Attribute
   {
      return Attribute::make(
         get: fn($value) =>  $value ? mb_convert_encoding($value, "UTF-8", "auto") : ''
      );
   }
   public function body(): Attribute
   {
      return Attribute::make(
         get: fn($value) =>  mb_convert_encoding($value, "UTF-8", "auto")
      );
   }
   public function getTeaserAttribute()
   {
      $body = $this->body;

      return createTeaser(mb_convert_encoding($body, "UTF-8", "auto"), 120);
   }
}
