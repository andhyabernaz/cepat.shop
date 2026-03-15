<?php

namespace App\Services\Message;

use App\Models\Message;
use Exception;

class MessageService
{
   public function sendMessage(Message $message)
   {

      try {

         $payload = [
            'subject' => $message->subject,
            'body' => $message->body
         ];
         if ($message->via == Message::VIA_EMAIL) {
            (new MailSmtpService())->send($message->recipient, $payload);
            return true;
         }
         if ($message->via == Message::VIA_TELEGRAM) {
            (new TelegramService())->send($payload);
            return true;
         }
         if ($message->via == Message::VIA_WHATSAPP) {
            (new WhatsappService())->send($message->recipient, $payload['body']);
            return true;
         }
         throw new Exception('Uncategorized via method');
      } catch (\Throwable $th) {
         throw $th;
      }
   }

   public function saveMessage(array $payload)
   {
      $message = Message::create($payload);

      return $message;
   }
}
