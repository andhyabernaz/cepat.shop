<?php

namespace App\Services\Message;

use Exception;
use App\Models\MailConfig;
use App\Mail\MailNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class MailSmtpService
{
   protected $delay;
   protected $mailConfig;

   public function send($recipient, array $payload)
   {

      try {

         $this->mailConfig = MailConfig::first();

         if ($this->mailConfig->is_ready == false) {

            throw new Exception("Mail smtp not configured");
         }

         if (!$recipient) {
            throw new Exception("Recipient email not defined");
         }

         if ($this->mailConfig->smtp_username == $recipient) {
            throw new Exception('Email sender and recipient cannot be the same');
         }

         $cfg = $this->initmailSmtpConfig();

         if ($cfg == true) {
            if ($this->delay) {
               Mail::to($recipient)->later($this->delay, new MailNotification($payload));
            } else {
               Mail::to($recipient)->send(new MailNotification($payload));
            }

            return 'sending mail with smtp successfully';
         } else {
            throw new Exception('error durring init email configuration');
         }
      } catch (\Exception $e) {
         throw $e;
      }
   }
   public function later($recipient, array $payload, $delay = 30)
   {
      $this->delay = $delay;
      return $this->send($recipient, $payload);
   }

   protected function initmailSmtpConfig()
   {

      try {

         $config = array(
            'driver'     => 'smtp',
            'host'       => $this->mailConfig->smtp_host,
            'port'       => $this->mailConfig->smtp_port,
            'from'       => array('address' => $this->mailConfig->from_address, 'name' => $this->mailConfig->from_name),
            'encryption' => $this->mailConfig->smtp_encryption,
            'username'   => $this->mailConfig->smtp_username,
            'password'   => $this->mailConfig->smtp_password,
         );

         Config::set('mail', $config);

         (new \Illuminate\Mail\MailServiceProvider(app()))->register();

         return true;
      } catch (\Throwable $th) {

         return false;
      }
   }
}
