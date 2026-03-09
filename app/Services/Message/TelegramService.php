<?php

namespace App\Services\Message;

use App\Notifications\TelegramNotification;
use Illuminate\Support\Facades\Notification;

class TelegramService
{
   public function send($payload)
   {
      try {
         Notification::route('telegram', config('telegram.user_id'))
            ->notify(new TelegramNotification($payload));
         return true;
      } catch (\Throwable $th) {
         throw $th;
      }
   }
}
