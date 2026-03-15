<?php

use App\Models\User;
use App\Models\Store;
use App\Models\Voucher;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Config as AppConfig;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

if (!function_exists('getSessionUser')) {

   function getSessionUser()
   {

      $sessionUser = request()->server('HTTP_SESSION_USER');

      return $sessionUser;
   }
}

function getUserAdmin()
{
   $user = Cache::rememberForever('user-admin', function () {
      return User::whereHas('role', function ($q) {
         $q->where('id', 1);
      })->first();
   });

   return $user;
}


function date_from_utc_to_locale($date)
{
   if (is_numeric($date)) {
      $date = Carbon::createFromTimestampMs($date)->timezone('UTC')->toDateTimeString();
   }
   $dt = new \DateTime($date, new \DateTimeZone('UTC'));
   $dt->setTimezone(new \DateTimeZone(config('app.timezone')));

   return $dt->format('Y-m-d H:i:s');
}

function setCurrentMailConfig($mailConfig)
{

   if ($mailConfig->is_ready) {

      $cfg = [
         'transport' => 'smtp',
         'host' => $mailConfig->smtp_host,
         'port' => $mailConfig->smtp_port,
         'username' => $mailConfig->smtp_username,
         'password' => $mailConfig->smtp_password,
         'encryption' => Str::lower($mailConfig->smtp_encryption),
         'timeout' => null,
         'auth_mode' => null,
      ];

      Config::set('mail.mailers.smtp', $cfg);

      $form  = [
         'address' => $mailConfig->from_address,
         'name' => $mailConfig->from_name,
      ];

      Config::set('mail.from', $form);
   }
}

function shortNumberPlus($num)
{
   if ($num == 0) {
      return $num;
   }
   $units = ['', 'Rb+', 'Jt+', 'M+', 'T+'];
   for ($i = 0; $num >= 1000; $i++) {
      $num /= 1000;
   }
   return round($num, 1, PHP_ROUND_HALF_DOWN) . $units[$i];
}

function generateUniqueVoucher()
{
   DB::table('vouchers')->where('end_date', '<', now())->update(['voucher_code' => null]);
   $v = Str::upper(Str::random(14));
   if (Voucher::where('voucher_code', $v)->count() > 0) {
      generateUniqueVoucher();
   }

   return $v;
}

function getCurrentSanctumUser($column = null)
{
   $user =  auth('sanctum')->user();

   return $user ? ($column ? $user[$column] : $user) : null;
}

function money_format_idr($numb) 
{
   if(!$numb) {
      return "Rp 0";
   }
   return "Rp" .number_format($numb, 0, ',', '.');
}

function generateUserEmailOrderCreated($order)
{
   $shop = getShop();
   $appName = $shop->name;

   $total = 'Rp. ' . number_format($order->order_total, 0, ",", ".");
   $tgl = Carbon::parse($order->created_at)->format('d/m/Y  H:i');
   $expiredDate = Carbon::createFromTimestamp($order->transaction->expired_time)->format('d/m/Y H:i');

   $body = "Halo, Yth $order->customer_name \nTerima kasih sudah berbelanja di $appName\nBerikut detil pesanan anda:\nNama : $order->customer_name \nInvoice : $order->order_ref\nDibuat : $tgl\nTotal :  $total\n\nSegera lakukan pembayaran sebelum $expiredDate agar pesanan anda dapat diproses, Terimakasih.";

   return [
      'customer_email' => $order->customer_email,
      'subject' => 'Notifikasi pesanan baru',
      'body' => $body,
      'action' => [
         'action_url' => route('invoice', $order->order_ref),
         'action_label' => 'View Order'
      ]
   ];
}
function generateAdminEmailOrderCreated($order)
{
   $methodArr = explode('_', $order->transaction->payment_method);
   $paymentMethod = join(' ', $methodArr);
   $appName = config('app.name');
   $invoiceId = $order->order_ref;
   $name = $order->customer_name;
   $email = $order->customer_email;
   $phone = $order->customer_whatsapp;
   $total = 'Rp. ' . number_format($order->order_total, 0, ",", ".");

   $body = "Halo Admin $appName,\nSelamat ada pesanan baru!\nDetil Pesanan:\nInvoice : $invoiceId\nNama   : $name\nEmail: $email\nPhone: $phone\nOrder Total : $total\nMetode Pembayaran : $paymentMethod";

   return [
      'customer_email' => $order->customer_email,
      'admin_email' => $order->customer_email,
      'subject' => 'Notifikasi pesanan baru',
      'body' => $body,
      'action' => [
         'action_url' => route('invoice', $order->order_ref),
         'action_label' => 'View Order'
      ]
   ];
}

function getAppConfig()
{
   return Cache::rememberForever('config', function () {
      return AppConfig::first();
   });
}
function getShop()
{
   return Cache::rememberForever('shop', function () {
      return Store::first();
   });
}

function createTeaser($html, $length = 200)
{
   if (!$html) {
      return '';
   }
   $str = strip_tags($html);

   return substr($str, 0, $length) . '..';
}

function shortNumber($num)
{
   $units = ['', 'Rb', 'Jt', 'M', 'T'];
   for ($i = 0; $num >= 1000; $i++) {
      $num /= 1000;
   }
   return round($num, 1) . $units[$i];
}

function setEnv(array $payload) {}

function formatPhoneWithPrefix($number)
{

   if (!is_string($number)) {
      $number = "$number";
   }

   $number = trim($number);
   $number = stripslashes($number);
   $number = htmlspecialchars($number);

   return preg_replace('/^(?:\+?62|0)?/', '62', $number);
}

function is_cron_running()
{
   return Cache::has('is_cron');
}
