<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
   use HasFactory;
   protected $guarded = [];
   public $appends = ['logo', 'icon', 'app_url'];

   public function getLogoAttribute()
   {
      return $this->logo_path ? url($this->logo_path) : null;
   }
   public function getIconAttribute()
   {
      return $this->icon_path ? url($this->icon_path) : null;
   }
   public function getAppUrlAttribute()
   {
      return config('app.url');
   }
}
