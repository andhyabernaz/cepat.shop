<?php
namespace App\Enums;

enum PaymentServiceEnum: string
{
   case Xendit = 'Xendit';
   case Tripay = 'Tripay';
   case Midtrans = 'Midtrans';
   case Duitku = 'Duitku';

   public static function getDefaultConfig()
   {
      return [
         [
            'vendor' => 'Xendit',
            'fields' => [
               ['name' => 'xendit_api_key', 'value' => NULL],
               ['name' => 'xendit_callback_token', 'value' => NULL],
            ]
         ],
         [
            'vendor' => 'Tripay',
            'fields' => [
               ['name' => 'tripay_api_key', 'value' => NULL],
               ['name' => 'tripay_private_key', 'value' => NULL],
               ['name' => 'tripay_mode', 'value' => 'sandbox'],
               ['name' => 'tripay_merchant_id', 'value' => NULL],
            ]
         ],
         [
            'vendor' => 'Midtrans',
            'fields' => [
               ['name' => 'midtrans_mode', 'value' => 'sandbox'],
               ['name' => 'midtrans_merchant_id', 'value' => NULL],
               ['name' => 'midtrans_server_key', 'value' => NULL],
               ['name' => 'midtrans_client_key', 'value' => NULL],
            ]
         ],
         [
            'vendor' => 'Duitku',
            'fields' => [
               ['name' => 'duitku_mode', 'value' => 'sandbox'],
               ['name' => 'duitku_merchant_id', 'value' => NULL],
               ['name' => 'duitku_api_key', 'value' => NULL],
            ]
         ]
      ];
   }
}
