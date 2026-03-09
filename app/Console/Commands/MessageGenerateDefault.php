<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Models\NotificationTemplate;
use Illuminate\Console\Command;

class MessageGenerateDefault extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'app:message-generate-default';

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
            'event' => NotificationTemplate::USER_REGISTRATION,
            'role' => Message::RoleAdmin,
            'via' => Message::VIA_TELEGRAM,
            'subject' => 'User Registration',
            'label' => 'User Registration',
            'template' => "New user registration\nDetail:\nUser: {{ user_name }}\nEmail: {{ user_email }}\nPhone: {{ user_phone }}"
         ],
         [
            'event' => NotificationTemplate::ORDER_CREATED,
            'role' => Message::RoleAdmin,
            'via' => Message::VIA_TELEGRAM,
            'subject' => 'New order',
            'label' => 'New order',
            'template' => "Halo Admin, ada pesanan baru di  {{ shop_name }}\nDetail:\nInvoice:  {{ invoice_number }}\nMetode Pembayaran:  {{ payment_method }}\nInvoice Link:\n{{ invoice_link }}"
         ],
      ];
   }
}
