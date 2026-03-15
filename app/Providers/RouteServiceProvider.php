<?php

namespace App\Providers;

use App\Http\Middleware\Installed;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
   /**
    * The path to your application's "home" route.
    *
    * Typically, users are redirected here after authentication.
    *
    * @var string
    */
   public const HOME = '/';

   /**
    * Define your route model bindings, pattern filters, and other route configuration.
    */
   public function boot(): void
   {
      $this->configureRateLimiting();

      $this->routes(function () {
         Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));

         Route::middleware(['api', 'throttle:auth', Installed::class])
            ->prefix('api')
            ->group(base_path('routes/api/auth.php'));

         Route::middleware(['api', Installed::class])
            ->prefix('api-public')
            ->group(base_path('routes/api/public.php'));

         Route::middleware(['api', 'auth:sanctum', 'auth.admin', Installed::class])
            ->prefix('api')
            ->group(base_path('routes/api/admin.php'));

         Route::middleware(['api', 'auth:sanctum', Installed::class])
            ->prefix('api')
            ->group(base_path('routes/api/customer.php'));

         Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api/webhook.php'));

         Route::middleware('api')
            ->prefix('api-testing')
            ->group(base_path('routes/testing.php'));

         Route::prefix('api-public/installer')
            ->middleware('api')
            ->group(base_path('routes/api/install.php'));

         Route::middleware('web')
            ->group(base_path('routes/web.php'));
      });
   }

   /**
    * Configure the rate limiters for the application.
    *
    * @return void
    */
   protected function configureRateLimiting()
   {
      RateLimiter::for('api', function (Request $request) {
         return Limit::perMinute(100)->by(optional($request->user())->id ?: $request->ip());
      });

      RateLimiter::for('auth', function (Request $request) {
         return Limit::perMinute(16)->by(optional($request->user())->id ?: $request->ip());
      });

      RateLimiter::for('global', function (Request $request) {
         return $request->user()
            ? Limit::perMinute(200)->by($request->user()->id)
            : Limit::perMinute(100)->by($request->ip());
      });
   }
}
