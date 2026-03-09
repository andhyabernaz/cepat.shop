<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Config;
use App\Models\MailConfig;
use App\Helpers\ApiResponse;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Services\Message\MailSmtpService;
use App\Services\Message\WhatsappService;
use App\Notifications\TelegramNotification;
use Exception;
use Illuminate\Support\Facades\Notification;

class NotifyController extends Controller
{
   protected $config;
   public function __construct()
   {
      $this->config =  Config::first();
   }

   public function testingTelegram()
   {
      $res = [
         'success' => true,
         'data' => [
            'type' => 'positive',
            'message' => 'Berhasil mengirim telegram'
         ]
      ];

      if ($this->config->is_telegram_ready) {

         try {

            $message = "Halo admin!\nTesting notifikasi telegram berhasil";

            Notification::route('telegram', config('telegram.user_id'))
               ->notify(new TelegramNotification($message));
         } catch (\Throwable $th) {

            $res['success'] = false;
            $res['data']['type'] = 'negative';
            $res['data']['message'] = $th->getMessage();
         }
      } else {
         $res['success'] = false;
         $res['data']['type'] = 'negative';
         $res['data']['message'] = 'Pengaturan telegram belum sesuai';
      }

      return response()->json($res);
   }
   public function testingEmail()
   {
      $res = [
         'success' => true,
         'data' => [
            'type' => 'positive',
            'message' => 'Berhasil mengirim email'
         ]
      ];

      try {
         $mailConfig = MailConfig::first();
         $shop = Store::first();

         if (!$mailConfig->is_ready) {
            throw new Exception('Pengaturan smtp email belum benar');
         }
         if (!$shop->email) {
            throw new Exception('Alamat email di pengaturan toko belum diisi');
         }

         $payload = [
            'subject' => "Testing email notifikasi",
            'body' => "Halo admin!\nTesting notifikasi smtp berhasil",
         ];
         (new MailSmtpService($mailConfig))->send($shop->email, $payload);
      } catch (\Throwable $th) {

         $res['success'] = false;
         $res['data']['type'] = 'negative';
         $res['data']['message'] = $th->getMessage();
      }

      return response()->json($res);
   }

   public function sendWhatsapp(Request $request)
   {

      try {
         $message = $request->message;
         $recepient = $request->recipient;

         if ($request->user_id) {
            $user = User::find($request->user_id);
            if ($user->phone) {
               $recepient = $user->phone;
            }
         }

         (new WhatsappService())->send($recepient, $message);

         return ApiResponse::success();
      } catch (\Throwable $th) {
         return ApiResponse::failed($th);
      }
   }
}
