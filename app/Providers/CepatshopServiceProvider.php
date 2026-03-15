<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Store;
use App\Models\Slider;
use App\Models\BankAccount;
use App\Models\PaymentConfig;
use App\Observers\PostObserver;
use App\Enums\PaymentServiceEnum;
use App\Observers\SliderObserver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\Config as ModelsConfig;
use App\Observers\BankAccountObserver;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Relations\Relation;

class CepatshopServiceProvider extends ServiceProvider
{
   /**
    * Register services.
    *
    * @return void
    */
   public function register()
   {
      try {
         if (Schema::hasTable('configs')) {

            $setting = DB::table('configs')->first();

            if ($setting) //checking if table is not empty
            {

               if ($setting->telegram_bot_token && $setting->telegram_user_id) {
                  Config::set('services.telegram-bot-api', ['token' => $setting->telegram_bot_token]);
                  Config::set('telegram', ['user_id' => $setting->telegram_user_id]);
               }
            }
         }
      } catch (\Throwable $th) {
         //throw $th;
      }
   }

   /**
    * Bootstrap services.
    *
    * @return void
    */
   public function boot()
   {
      Schema::defaultStringLength(191);

      BankAccount::observe(BankAccountObserver::class);
      Post::observe(PostObserver::class);
      Slider::observe(SliderObserver::class);

      $shop = [
         'name' => 'Cepatshop',
         'description' => 'Next Generation online shop Apps'
      ];

      $head = [
         'fb_pixel' => null,
         'gtm' => null,
         'custom_css' => null,

      ];

      try {

         if(DB::connection()->getPdo()) {

            $shop = Cache::rememberForever('shop', function () {
               return Store::first();
            });

            $config = Cache::rememberForever('configs', function () {
               return ModelsConfig::first();
            });
   
            $head['fb_pixel'] = $config->fb_pixel;
            $head['gtm'] = $config->gtm;
            $head['custom_css'] = $config->custom_css;

            if($config->payment_default == PaymentServiceEnum::Midtrans->value) {

               $midtransConfig = PaymentConfig::getConfigs(PaymentServiceEnum::Midtrans->value);
               $urls = config('midtrans.snapjs_urls');
               $head['midtrans_script'] = $urls[$midtransConfig['midtrans_mode']];
               $head['midtrans_client_key'] = $midtransConfig['midtrans_client_key'];
            }

         }

      } catch (\Throwable $th) {
         //throw $th;
      }

      View::macro('vue', function ($page = []) use ($shop, $head) {
         return view('app', [
            'jsapp' => [
               'page' => $page,
               'shop' => $shop,
               'head' => $head
            ]
         ]);
      });

      Relation::enforceMorphMap([
         'Post' => 'App\Models\Post',
         'Product' => 'App\Models\Product',
         'User' => 'App\Models\User',
         'Withdrawal' => 'App\Models\Withdrawal',
         'Review' => 'App\Models\Review',
      ]);

      $validations = ['required', 'min', 'max'];

      foreach ($validations as $v) {

         Validator::replacer($v, function ($message, $attribute, $rule, $parameters) {
            if (str_contains($message, ':nth') && preg_match("/\.(\d+)\./", $attribute, $match)) {
               return str_replace(":nth", $match[1] + 1, $message);
            }

            return $message;
         });
      }
   }
}
