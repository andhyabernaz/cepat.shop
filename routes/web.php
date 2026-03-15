<?php

use App\Http\Middleware\Installed;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\Frontend\FrontController;
use App\Http\Controllers\Install\InstallController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// MOHON UNTUK TIDAK MENGEDIT ROUTE DIBAWAH
Route::get('/install', [InstallController::class, 'index'])->name('install');
Route::any('/auto/cepat', function (Request $request) {
   $target = 'https://shop.cepat.digital/';
   $query = $request->getQueryString();
   if ($query) {
      $target .= '?' . $query;
   }

   return redirect()->away($target, 301);
});
Route::any('/auto/cepat/{any?}', function (Request $request, ?string $any = null) {
   $target = 'https://shop.cepat.digital/' . ltrim((string) $any, '/');
   $query = $request->getQueryString();
   if ($query) {
      $target .= '?' . $query;
   }

   return redirect()->away($target, 301);
})->where('any', '.*');
Route::any('/auto', function (Request $request) {
   $target = 'https://shop.cepat.digital/';
   $query = $request->getQueryString();
   if ($query) {
      $target .= '?' . $query;
   }

   return redirect()->away($target, 301);
});
Route::redirect('/home', '/', 301);
Route::any('/auto/{any}', function (Request $request, string $any) {
   $target = 'https://shop.cepat.digital/' . ltrim($any, '/');
   $query = $request->getQueryString();
   if ($query) {
      $target .= '?' . $query;
   }

   return redirect()->away($target, 301);
})->where('any', '.*');
Route::middleware([Installed::class])->group(
   function () {
      Route::get('/admin', function () {
         return redirect('/admin/dashboard');
      });
      Route::post('/auth/login', function (Request $request) {
         $payload = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
         ], [
            'email.required' => 'Email atau No ponsel wajib diisi.',
            'password.required' => 'Password wajib diisi.',
         ]);

         $user = User::query()
            ->where('email', $payload['email'])
            ->orWhere('phone', $payload['email'])
            ->first();

         if (! $user || ! Hash::check($payload['password'], $user->password)) {
            return redirect()->back()->withErrors(['email' => ['Data kredensial salah.']]);
         }

         Auth::login($user, true);

         return redirect('/admin/dashboard');
      });
      Route::get('/', [FrontController::class, 'homepage']);
      Route::get('/products', [FrontController::class, 'products']);
      Route::get('/products/category/{category}', [FrontController::class, 'productCategory'])->name('product.category');
      Route::get('/products/{id}', [FrontController::class, 'productCategory'])->name('product.bycategory');
      Route::get('/product/{slug}', [FrontController::class, 'productDetail'])->name('product.show');
      Route::get('/posts', [FrontController::class, 'postIndex']);
      Route::get('/post/{slug}', [FrontController::class, 'postDetail'])->name('post.show');
      Route::get('/page/{slug}', [FrontController::class, 'pageDetail'])->name('page.show');
      Route::get('/p/invoice/{id}', [FrontController::class, 'showInvoice'])->name('invoice');
      Route::get('/sitemap.xml', [FrontController::class, 'sitemap']);
      Route::get('/clear-cache', [FrontController::class, 'clearCache']);
      Route::get('/force-update', [UpdateController::class, 'forceUpdate']);
      Route::get('/{any}', [FrontController::class, 'any'])->where('any', '^(?!api|api-public).*$');
   }
);
