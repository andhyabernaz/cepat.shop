<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class TelegramNotification extends Notification implements ShouldQueue
{
   use Queueable;

   public $body = '';

   /**
    * Create a new notification instance.
    *
    * @return void
    */
   public function __construct($payload)
   {
      if (is_array($payload)) {
         $this->body =  $payload['subject'] . "\n" . $payload['body'];
      } else if (is_string($payload)) {

         $this->body = $payload;
      }
   }

   /**
    * Get the notification's delivery channels.
    *
    * @param  mixed  $notifiable
    * @return array
    */
   public function via($notifiable)
   {
      return [TelegramChannel::class];
   }

   /**
    * Get the mail representation of the notification.
    *
    * @param  mixed  $notifiable
    * @return \Illuminate\Notifications\Messages\MailMessage
    */
   public function toTelegram($notifiable)
   {
      return TelegramMessage::create()
         ->content($this->body);
   }

   /**
    * Get the array representation of the notification.
    *
    * @param  mixed  $notifiable
    * @return array
    */
   public function toArray($notifiable)
   {
      return [
         //
      ];
   }
}
