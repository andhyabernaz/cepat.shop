<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailConfig extends Model
{
   use HasFactory;

   protected $fillable = [
      'smtp_host',
      'smtp_port',
      'smtp_encryption',
      'smtp_username',
      'smtp_password',
      'from_name',
      'from_address',
      'mail_admin',
      'is_active'
   ];

   protected $casts = [
      'is_active' => 'boolean'
   ];

   public $timestamps = false;

   public $appends = ['is_ready'];

   public function getIsReadyAttribute()
   {
      // if (!$this->is_active) {
      //    return false;
      // }
      return $this->smtp_host
         && $this->smtp_port
         && $this->smtp_username
         && $this->smtp_password
         && $this->smtp_encryption
         && $this->from_address
         && $this->from_name;
   }
}
