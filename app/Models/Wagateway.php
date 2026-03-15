<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wagateway extends Model
{
   use HasFactory;

   public $timestamps = false;

   protected $fillable = [
      'provider',
      'endpoint',
      'apikey',
      'is_active',
      'is_default',
      'default_auth',
      'content_type'
   ];

   protected $casts = [
      'is_active' => 'boolean'
   ];

   const PARAM_HEADER = 'HEADER';
   const PARAM_BODY = 'BODY';

   public function params()
   {
      return $this->hasMany(WagatewayParam::class);
   }
   public function headerParams()
   {
      return $this->hasMany(WagatewayParam::class)->where('param_type', self::PARAM_HEADER)->whereNotNull('param_value');
   }
   public function bodyParams()
   {
      return $this->hasMany(WagatewayParam::class)->where('param_type', self::PARAM_BODY)->whereNotNull('param_value');
   }
   public function scopeActive($query)
   {
      return $query->where('is_active', 1);
   }
}
