<?php

use App\Http\Middleware\Installed;
use Illuminate\Support\Facades\Route;
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
Route::any('/auto/cepat', function () {
   abort(404);
});
Route::any('/auto/cepat/{any?}', function () {
   abort(404);
})->where('any', '.*');
Route::redirect('/auto', '/', 301);
Route::get('/auto/{any}', function (string $any) {
   return redirect("/{$any}", 301);
})->where('any', '.*');
Route::middleware([Installed::class])->group(
   function () {
      Route::get('/', [FrontController::class, 'homepage']);
      Route::get('/products', [FrontController::class, 'products']);
      Route::get('/products/category/{category}', [FrontController::class, 'productCategory'])->name('product.category');
      Route::get('/products/{id}', [FrontController::class, 'productCategory'])->name('product.bycategory');
      Route::get('/product/{slug}', [FrontController::class, 'productDetail'])->name('product.show');
      Route::get('/posts', [FrontController::class, 'postIndex']);
      Route::get('/post/{slug}', [FrontController::class, 'postDetail'])->name('post.show');
      Route::get('/p/invoice/{id}', [FrontController::class, 'showInvoice'])->name('invoice');
      Route::get('/sitemap.xml', [FrontController::class, 'sitemap']);
      Route::get('/clear-cache', [FrontController::class, 'clearCache']);
      Route::get('/force-update', [UpdateController::class, 'forceUpdate']);
      Route::get('/{any}', [FrontController::class, 'any'])->where('any', '^(?!api|api-public).*$');
   }
);
