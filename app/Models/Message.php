<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
   use HasFactory;

   const Pending = 'Pending';
   const Sent = 'Sent';
   const Failed = 'Failed';

   const VIA_EMAIL = 'Email';
   const VIA_WEB = 'Web';
   const VIA_TELEGRAM = 'Telegram';
   const VIA_WHATSAPP = 'Whatsapp';

   const RoleAdmin = 'Admin';
   const RoleCustomer = 'Customer';

   protected $guarded = [];

   public $appends = ['is_sent'];

   public function getIsSentAttribute()
   {
      return $this->status == self::Sent;
   }

   public static function getViaOptions()
   {
      return [
         ['label' => 'Email', 'value' => self::VIA_EMAIL, 'roles' => ['Admin', 'Customer']],
         ['label' => 'Whatsapp', 'value' => self::VIA_WHATSAPP, 'roles' => ['Admin', 'Customer']],
         ['label' => 'Telegram', 'value' => self::VIA_TELEGRAM, 'roles' => 'Admin'],
      ];
   }

   public function pushComplete()
   {
      $this->update([
         'status' => self::Sent,
         'error_log' => null,
         'sent_at' => now()
      ]);
   }

   public function pushFailed($error)
   {
      $this->update([
         'status' => self::Failed,
         'error_log' => $error,
      ]);
   }
}
