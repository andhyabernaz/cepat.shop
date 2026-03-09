<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Slugable
{
   public static function bootSlugable()
   {
      static::creating(function ($model) {
         $slug = Str::slug($model->title);
         if (static::where('slug', 'like', $slug . '%')->count() > 0) {
            $slug .= '_' . static::max('id');
         }
         $model->slug = $slug;
      });
   }
}
