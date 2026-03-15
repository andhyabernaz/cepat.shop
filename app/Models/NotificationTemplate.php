<?php

namespace App\Models;

use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
   use HasFactory, Sortable;

   public $timestamps = false;

   protected $fillable = [
      'event',
      'label',
      'role',
      'subject',
      'template',
      'via',
      'sort'
   ];

   const USER_REGISTRATION = 'user_registration';
   const ORDER_CREATED = 'order_created';
   const ORDER_PAYMENT_SUBMITED = 'payment_submited';
   const ORDER_PAYMENT_CONFIRMED = 'payment_confirmed';
   const ORDER_COMPLETED = 'order_completed';
   const ORDER_FAILED = 'order_failed';
   const REFERAL_COMMISSION = 'referal_commission';
   const WITHDRAW_CREATED = 'withdraw_created';
   const WITHDRAW_PROCESSED = 'withdraw_processed';
   const WITHDRAW_ABORTED = 'withdraw_aborted';

   public static function eventOptions()
   {
      $data = [
         ['label' => 'USER REGISTRATION', 'value' => self::USER_REGISTRATION],
         ['label' => 'ORDER CREATED', 'value' => self::ORDER_CREATED],
         ['label' => 'ORDER COMPLETED', 'value' => self::ORDER_COMPLETED],
         ['label' => 'ORDER FAILED', 'value' => self::ORDER_FAILED],
         ['label' => 'PAYMENT SUBMITED', 'value' => self::ORDER_PAYMENT_SUBMITED],
         ['label' => 'PAYMENT CONFIRMED', 'value' => self::ORDER_PAYMENT_CONFIRMED],
         ['label' => 'WITHDRAW CREATED', 'value' => self::WITHDRAW_CREATED],
         ['label' => 'WITHDRAW PROCESSED', 'value' => self::WITHDRAW_PROCESSED],
         ['label' => 'WITHDRAW ABORTED', 'value' => self::WITHDRAW_ABORTED],
         ['label' => 'KOMISI REFERAL', 'value' => self::REFERAL_COMMISSION],
      ];

      return $data;
   }

   public static function getParams()
   {
      return [
         [
            'label' => 'User Params',
            'items' => [
               'user_name',
               'user_email',
               'user_phone',
            ]
         ],
         [
            'label' => 'Order Params',
            'items' => [
               'invoice_number',
               'invoice_link',
               'created_date',
               'expired_date',
               'order_quantity',
               'order_subtotal',
               'order_total',
               'service_fee',
               'kode_unik',
               'voucher_discount',
               'payment_fee',
               'payment_method',
               'payment_code',
               'receiver_name',
               'receiver_phone',
               'order_items',
            ]
         ],
         [
            'label' => 'Shop & Admin Params',
            'items' => [
               'shop_name',
               'shop_phone',
               'shop_address',
               'admin_name',
               'admin_email',
               'admin_phone',
            ]
         ],
         [
            'label' => 'Referal Params',
            'items' => [
               'referal_amount',
               'referal_last_saldo',
               'referal_description',
            ]
         ],
         [
            'label' => 'Withdraw Params',
            'items' => [
               'amount',
               'target_account',
               'target_number',
               'note',
               'status',
               'reason',
            ]
         ],
      ];
   }
}
