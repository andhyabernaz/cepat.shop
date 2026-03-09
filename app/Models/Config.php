<?php

namespace App\Models;

use App\Enums\PaymentServiceEnum;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Config extends Model
{
   use HasFactory;

   public $timestamps = false;
   protected $fillable = [
      'theme',
      'theme_color',
      'home_view_mode',
      'product_view_mode',
      'rajaongkir_type',
      'rajaongkir_apikey',
      'rajaongkir_couriers',
      'warehouse_id',
      'warehouse_address',
      'tripay_api_key',
      'tripay_private_key',
      'tripay_mode',
      'tripay_merchant_code',
      'telegram_bot_token',
      'telegram_user_id',
      'is_notifypro',
      'is_payment_gateway',
      'is_guest_checkout',
      'is_whatsapp_checkout',
      'notifypro_interval',
      'notifypro_timeout',
      'cod_list',
      'is_cod_payment',
      'service_fee',
      'service_fee_label',
      'is_service_fee',
      'is_shipping_active',
      'review_auto_approved',
      'order_expired_time',
      'catalog_product_limit',
      'catalog_product_sort',
      'home_product_limit',
      'home_product_sort',
      'accent_color',
      'secondary_color',
      'primary_color',
      'public_review',
      'biteship_apikey',
      'courier_default',
      'biteship_couriers',
      'biteship_warehouse',
      'is_order_pickup',
      'is_local_shipping',
      'is_cash_payment',
      'display_sitename',
      'is_unique_code',
      'display_product_sold',
      'mode_desktop',
      'warehouse_coordinate',
      'local_shipping_costs',
      'local_shipping_label',
      'mapbox_access_token',
      'is_featured_slider',
      'fb_pixel',
      'gtm',
      'custom_css',
      'card_img_ratio',
      'card_img_fit',
      'product_img_fit',
      'product_img_ratio',
      'payment_default',
      'slider_autoplay',
      'slider_interval',
   ];
   public $appends = [
      'is_shippable',
      'can_shipping',
      'is_tripay_ready',
      'is_telegram_ready',
      'is_bank_ready',
      'is_mail_ready',
      'can_cod',
      'tripay_callback',
      'xendit_callback',
      'midtrans_callback',
      'duitku_callback',
      'is_rajaongkir_starter',
      'can_local_shipping',
      'cron_status',
      'is_whatsapp_ready',
      'can_checkout_local',
      'can_checkout_pickup',
      'can_checkout_courier',
      'can_checkout_digital',
      'payment_services',
      'is_pg_ready'
   ];

   const HIDDEN_FIELDS = [
      'rajaongkir_apikey',
      'tripay_api_key',
      'tripay_private_key',
      'tripay_merchant_code',
      'telegram_bot_token',
      'telegram_user_id',
      'biteship_apikey',
   ];

   public $casts = [
      'is_notifypro' => 'boolean',
      'is_payment_gateway' => 'boolean',
      'is_unique_code' => 'boolean',
      'is_cod_payment' => 'boolean',
      'is_whatsapp_checkout' => 'boolean',
      'is_guest_checkout' => 'boolean',
      'is_service_fee' => 'boolean',
      'is_shipping_active' => 'boolean',
      'is_order_pickup' => 'boolean',
      'is_cash_payment' => 'boolean',
      'is_local_shipping' => 'boolean',
      'is_featured_slider' => 'boolean',
      'review_auto_approved' => 'boolean',
      'display_sitename' => 'boolean',
      'display_product_sold' => 'boolean',
      'slider_autoplay' => 'boolean',
      'public_review' => 'boolean',
      'mode_desktop' => 'boolean',
      'cod_list' => 'array',
      'warehouse_address' => 'object',
      'rajaongkir_couriers' => 'array',
      'biteship_warehouse' => 'object',
      'biteship_couriers' => 'array',
      'warehouse_coordinate' => 'array',
      'local_shipping_costs' => 'array'
   ];

   public const RAJAONGKIR_SERVICE = 'Rajaongkir';
   public const BITESHIP_SERVICE = 'Biteship';

   public function getIsShippableAttribute()
   {
      if (!$this->is_shipping_active) {
         return false;
      }
      if ($this->courier_default == self::RAJAONGKIR_SERVICE && $this->rajaongkir_apikey) {
         return true;
      }
      if ($this->courier_default == self::BITESHIP_SERVICE && $this->biteship_apikey) {
         return true;
      }

      return false;
   }

   public function getIsRajaongkirStarterAttribute()
   {
      if ($this->courier_default == self::RAJAONGKIR_SERVICE && $this->rajaongkir_type == 'starter') {
         return true;
      }
      return false;
   }
   public function getCanShippingAttribute()
   {

      if (!$this->is_shipping_active) {
         return false;
      }

      if ($this->courier_default == self::BITESHIP_SERVICE) {
         if (
            $this->biteship_apikey
            && $this->warehouse_address
            && $this->rajaongkir_couriers
         ) {
            return true;
         }
      }
      if ($this->courier_default == self::RAJAONGKIR_SERVICE) {
         if (
            $this->rajaongkir_apikey
            && $this->warehouse_address
            && $this->rajaongkir_couriers
         ) {
            return true;
         }
      }

      return false;
   }
   public function hasPayment()
   {
      $countBankAccount = Cache::remember('bank_account_counts', now()->addHour() ,function () {
         return BankAccount::count();
      });

      if ($countBankAccount > 0 || $this->is_pg_ready == true) {
         return true;
      }
      return false;
   }
   public static function isPgActive(){

       $status = Cache::rememberForever('is_pg_ready', function () {

         $payment_default = static::select('payment_default')->value('payment_default');
         if(!$payment_default) {
            return false;
         }

         $data = PaymentConfig::where('vendor', $payment_default)->get();

         foreach ($data as $item) {

            if ($item->value == NULL || $item->value == '') {
               return false;
               break;
            }
         }

         return true;
      });
      return $status;
   }
   public function isPgReady(): Attribute
   {
      $status = self::isPgActive();
      return Attribute::make(
         get: fn() => $status,
      );
   }
   public function getCanCheckoutPickupAttribute()
   {

      if ($this->is_order_pickup && ($this->is_cash_payment || $this->hasPayment())) {
         return true;
      }
      return false;
   }
   public function getCanCheckoutDigitalAttribute()
   {

      if ($this->hasPayment()){
         return true;
      }
      return false;
   }

   public function getCanCheckoutLocalAttribute()
   {

      if ($this->warehouse_coordinate && $this->local_shipping_costs && $this->is_local_shipping && ($this->is_cod_payment || $this->hasPayment())) {
         return true;
      };


      return false;
   }
   public function getCanCheckoutCourierAttribute()
   {
      $is_courier_ready = false;
      if (!$this->is_shipping_active) {
         return false;
      }
      if ($this->courier_default == self::RAJAONGKIR_SERVICE && $this->rajaongkir_apikey && $this->warehouse_address && $this->rajaongkir_couriers) {
         $is_courier_ready = true;
      }
      if ($this->courier_default == self::BITESHIP_SERVICE && $this->biteship_apikey && $this->biteship_warehouse && $this->biteship_couriers) {
         $is_courier_ready = true;
      }

      if ($is_courier_ready && $this->hasPayment()) {
         return true;
      };

      return false;
   }
   public function getCanCodAttribute()
   {
      return $this->cod_list ? true : false;
   }
   public function getCronStatusAttribute()
   {
      return Cache::has('is_cron');
   }
   public function getCanLocalShippingAttribute()
   {
      return $this->cod_list && $this->is_local_shipping ? true : false;
   }
   public function getIsTripayReadyAttribute()
   {
      return $this->tripay_api_key
         && $this->tripay_private_key
         && $this->tripay_merchant_code
         && $this->is_payment_gateway ? true : false;
   }
   public function getIsTelegramReadyAttribute()
   {
      return $this->telegram_bot_token && $this->telegram_user_id ? true : false;
   }
   public function getIsMailReadyAttribute()
   {
      $mail = config('mail.mailers.smtp');
      if ($mail && $mail['username'] && $mail['host'] && $mail['port'] && $mail['password']) {
         return true;
      }
      return false;
   }
   public function getIsBankReadyAttribute()
   {
      $countBankAccount = Cache::rememberForever('bank_account_count', function () {
         return BankAccount::count();
      });
      return  $countBankAccount > 0 ? true : false;
   }
   public function getTripayCallbackAttribute()
   {
      return route('tripay.callback');
   }
   public function getXenditCallbackAttribute()
   {
      return route('xendit.callback');
   }
   public function getMidtransCallbackAttribute()
   {
      return route('midtrans.callback');
   }
   public function getDuitkuCallbackAttribute()
   {
      return route('duitku.callback');
   }
   public function getIsWhatsappReadyAttribute()
   {
      return Cache::remember('is_whatsapp_ready', now()->addMinute(), function () {
         return Wagateway::where('is_active', 1)->count() > 0;
      });
   }
   public function getPaymentServicesAttribute()
   {
      return PaymentServiceEnum::cases();
   }
}
