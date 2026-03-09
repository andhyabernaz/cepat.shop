<?php

namespace App\Services\Message;

use App\Models\MutasiSaldo;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Withdrawal;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class NotificationService
{
   public function renderMessage($model, $template)
   {
      $pattern = [];
      $shop = $this->getShopPattern();
      $admin = $this->getAdminPattern();

      if ($model instanceof Order) {
         $order = $this->getOrderPattern($model->id);
         if ($model->user_id) {
            $user = $this->getUserPattern($model->user_id);
         } else {
            $user = [
               'user_name' => $order['receiver_name'],
               'user_phone' => $order['receiver_phone'],
               'user_email' => ''
            ];
         }
         $pattern = array_merge($shop, $order, $user, $admin);
      } else if ($model instanceof User) {
         $user = $this->getUserPattern($model->id);
         $pattern = array_merge($shop, $user, $admin);
      } else if ($model instanceof MutasiSaldo) {
         $mutasi = $this->getMutasiSaldoPattern($model);
         $pattern = array_merge($shop, $mutasi, $admin);
      } else if ($model instanceof Withdrawal) {
         $data = $model->toArray();
         $data['amount'] = $this->getPriceFormat($data['amount']);
         $pattern = array_merge($shop, $data, $admin);
      }

      $message = $this->render($template, $pattern);

      return $message;
   }

   protected function render($template, $pattern)
   {
      $m = new \Mustache_Engine(array('entity_flags' => ENT_QUOTES));

      return $m->render($template, $pattern);
   }

   protected function getOrderPattern($orderId)
   {
      $order = (array) DB::table('orders')
         ->select(
            'orders.order_ref as invoice_number',
            'orders.created_at as created_date',
            'orders.expired_at as expired_date',
            'orders.order_qty as order_quantity',
            DB::raw('orders.order_total + orders.payment_fee as order_total'),
            'transactions.payment_name as payment_method',
            'transactions.payment_code as payment_code',
            DB::raw("CONCAT(orders.shipping_courier_name, ' ', orders.shipping_courier_service) as courier"),
            'orders.order_subtotal',
            'orders.shipping_cost',
            'orders.shipping_address',
            'orders.customer_name as receiver_name',
            'orders.customer_whatsapp as receiver_phone',
            'orders.shipping_courier_code as resi_number',
            'orders.payment_fee',
            'orders.shipping_discount',
            'orders.service_fee',
            'orders.voucher_discount',
            'orders.order_unique_code as kode_unik',
         )
         ->where('orders.id', $orderId)
         ->join('transactions', 'transactions.order_id', 'orders.id')
         ->first();

      $order_items = OrderItem::where('order_id', $orderId)->get();
      $items = [];
      $numb = 1;
      foreach ($order_items as $item) {

         $name = $item->name;
         $price = $this->getPriceFormat($item->price);
         $subtotal = $this->getPriceFormat($item->price * $item->quantity);

         $items[] = sprintf("%sx %s\n*Harga*: %s\n*Jumlah:* %s\n", $numb, $name, $price, $subtotal);

         $numb++;
      }

      $order['invoice_link'] = route('invoice', $order['invoice_number']);
      $order['order_items'] = join(" ", $items);
      $order['payment_fee'] = $this->getPriceFormat($order['payment_fee']);
      $order['order_subtotal'] = $this->getPriceFormat($order['order_subtotal']);
      $order['order_total'] = $this->getPriceFormat($order['order_total']);
      $order['shipping_cost'] = $this->getPriceFormat($order['shipping_cost']);
      $order['shipping_address'] = $this->clean_string($order['shipping_address']);
      $order['order_created_date'] = $order['created_date'];
      $order['order_expired_date'] = $order['expired_date'];
      $order['created_date'] = Carbon::parse($order['created_date'])->translatedFormat('D,d M Y h:i');
      $order['expired_date'] = Carbon::parse($order['expired_date'])->translatedFormat('D,d M Y h:i');

      return $order;
   }
   protected function getMutasiSaldoPattern($model)
   {
      $data = $model->toArray();

      $data['referal_amount'] = $this->getPriceFormat($data['amount']);
      $data['referal_last_saldo'] = $this->getPriceFormat($data['last_saldo']);
      $data['referal_description'] = $data['description'];

      return $data;
   }

   protected function getUserPattern($userId)
   {

      $key = 'user_pat_' . $userId;

      $user = Cache::remember(
         $key,
         now()->addHour(),
         function () use ($userId) {
            $data = (array) DB::table('users')
               ->where('id', $userId)->select(
                  'name as user_name',
                  'phone as user_phone',
                  'email as user_email'
               )->first();

            return $data;
         }
      );

      return $user;
   }
   protected function getAdminPattern()
   {
      return Cache::remember('admin_pat', now()->addHour(), function () {
         $data = (array) DB::table('users')->where('role_id', 1)->select(
            'name as admin_name',
            'phone as admin_phone',
            'email as admin_email'
         )->first();
         return $data;
      });
   }
   protected function getShopPattern()
   {

      return Cache::remember('shop_pat', now()->addHour(), function () {
         $shop = (array) DB::table('stores')->select(
            'name as shop_name',
            'phone as shop_phone',
            'address as shop_address'
         )->first();

         $shop['shop_address'] = $this->clean_string($shop['shop_address']);

         return $shop;
      });
   }

   protected function getPriceFormat($number = 0)
   {
      if ($number == 0) {
         return 'Rp ' . $number;
      }
      return 'Rp ' . number_format($number, 0, ',', '.');
   }

   protected function clean_string($string)
   {
      if (is_null($string)) {
         return "";
      }


      $string = preg_replace('/<[^>]*>/', '', $string);
      $string = preg_replace('!\s+!', ' ', $string);
      $string = preg_replace("/&nbsp;/", ' ', $string);
      $string = trim($string);

      return $string;
   }
}
