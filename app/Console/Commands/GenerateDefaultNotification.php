<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Models\NotificationTemplate;
use Illuminate\Console\Command;

class GenerateDefaultNotification extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'app:generate-default-notification';

   /**
    * The console command description.
    *
    * @var string
    */
   protected $description = 'Command description';

   /**
    * Execute the console command.
    */
   public function handle()
   {
      $data = [
         [
            "event" => NotificationTemplate::USER_REGISTRATION,
            "role" => "Admin",
            "label" => "User Registration",
            "subject" => "User Registration",
            "template" => "Pengguna baru telah mendaftar di {{ shop_name }}\nNama:  {{ user_name }} \nEmail:  {{ user_email }} \nPhone:  {{ user_phone }}",
            "via" => Message::VIA_EMAIL,
            "sort" => 1
         ],
         [
            "event" => NotificationTemplate::USER_REGISTRATION,
            "role" => "Customer",
            "label" => "User Registration",
            "subject" => "User Registration",
            "template" => "Terima kasih telah bergabung dengan kami\nBerikut detail akun anda\nNama:  {{ user_name }} \nEmail:  {{ user_email }} \nPhone:  {{ user_phone }}",
            "via" => Message::VIA_EMAIL,
            "sort" => 1
         ],
         [
            "event" => NotificationTemplate::ORDER_CREATED,
            "role" => "Customer",
            "label" => "Pesanan Baru",
            "subject" => "Pesanan Baru",
            "template" => "Halo kak {{ user_name }},\nTerima kasih telah berbelanja di toko kami\n\nBerikut detil pesanan anda\nNo pesanan:  {{ invoice_number }} \nTotal:  {{ order_total }} \nMetode Pembayaran:  {{ payment_method }} \nBatas Pembayaran:   {{ expired_date }} \n\nInvoice Link:\n{{ invoice_link }}\n\nRegards,\n{{ shop_name }}",
            "via" => Message::VIA_EMAIL,
            "sort" => 2
         ],
         [
            "event" => NotificationTemplate::ORDER_CREATED,
            "role" => "Admin",
            "label" => "Pesanan Baru",
            "subject" => "Pesanan Baru",
            "template" => "Halo Admin, ada pesanan baru di  {{ shop_name }} \nDetail Pesanan:\n\nInvoice:  {{ invoice_number }} \nMetode Pembayaran:  {{ payment_method }} \nInvoice Link:\n {{ invoice_link }}",
            "via" => Message::VIA_EMAIL,
            "sort" => 2
         ],
         [
            "event" => NotificationTemplate::ORDER_PAYMENT_CONFIRMED,
            "role" => "Customer",
            "label" => "Pembayaran diterima",
            "subject" => "Pembayaran diterima",
            "template" => "Halo kak {{ user_name }},\nPembayaran atas pesanan  {{ invoice_number }} telah kami terima\npesanan akan segera kami proses\n\nregards,\n {{ shop_name }}",
            "via" => Message::VIA_EMAIL,
            "sort" => 2
         ],
         [
            "event" => NotificationTemplate::ORDER_SHIPPING,
            "role" => "Customer",
            "label" => "Pesanan dikirim",
            "subject" => "Pesanan dikirim",
            "template" => "Halo kak {{ user_name }},\nPesanan  {{ invoice_number }}  sedang dalam pengiriman\nInvoice Link:\n {{ invoice_link }}\n\nRegards,\n{{ shop_name }}",
            "via" => Message::VIA_EMAIL,
            "sort" => 3
         ],
         [
            "event" => NotificationTemplate::ORDER_PAYMENT_SUBMITED,
            "role" => "Admin",
            "label" => "Payment Submited",
            "subject" => "Payment Submited",
            "template" => "Customer telah melakukan pembayaran atas pesanan  {{ invoice_number }}",
            "via" => Message::VIA_EMAIL,
            "sort" => 3
         ],
         [
            "event" =>  NotificationTemplate::ORDER_SHIPPING,
            "role" => "Admin",
            "label" => "Pesanan Dikirim",
            "subject" => "Pesanan Dikirim",
            "template" => "Pesanan sedang kirim\n\nInvoice:   {{ invoice_number }} \nNo Resi:  {{ resi_number }} \n\nLink:  {{ invoice_link }}",
            "via" => Message::VIA_EMAIL,
            "sort" => 4
         ],
         [
            "event" =>  NotificationTemplate::ORDER_COMPLETED,
            "role" => "Admin",
            "label" => "Pesanan Selesai",
            "subject" => "Pesanan Selesai",
            "template" => "Halo Admin,\n\nPesanan  {{ invoice_number }} telah selesai",
            "via" => Message::VIA_EMAIL,
            "sort" => 5
         ]
      ];

      foreach ($data as $item) {
         if (NotificationTemplate::where('role', $item['role'])->where('event', $item['event'])->count() == 0) {
            NotificationTemplate::create($item);
         }
      }
   }
}
