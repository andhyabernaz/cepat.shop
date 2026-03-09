<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotifyController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\ReviewController;
use App\Http\Controllers\Frontend\CustomerController;
use App\Http\Controllers\Frontend\FrontApiController;
use App\Http\Controllers\Frontend\ShippingController;
use App\Http\Controllers\Frontend\FrontOrderController;
use App\Http\Controllers\Frontend\FrontProductController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\NotificationDispathEventController;
use App\Http\Controllers\PaymentServiceController;
use App\Http\Controllers\SaldoController;
use App\Http\Controllers\SseController;

Route::get('getInitialData', [FrontApiController::class, 'getInitialData']);
Route::get('clear-cache', [FrontApiController::class, 'clearCache']);
Route::get('getPost/{slug}', [FrontApiController::class, 'getPostDetail']);
Route::get('getPromotePosts', [FrontApiController::class, 'getPromotePosts']);
Route::get('getCategories', [FrontApiController::class, 'getCategories']);
Route::get('getPosts', [FrontApiController::class, 'getPosts']);
Route::get('getRelatedPost/{id}', [FrontApiController::class, 'getRelatedPost']);
Route::get('post-tags', [FrontApiController::class, 'postTags']);
Route::get('getSliders', [FrontApiController::class, 'getSliders']);
Route::get('getProducts', [FrontProductController::class, 'getProducts']);
Route::get('productLinks/{productId}', [FrontProductController::class, 'productLinks']);
Route::get('product-item/{slug}', [FrontProductController::class, 'productDetail']);
Route::get('product-related/{id}', [FrontProductController::class, 'productRelated']);
Route::post('product-favorites', [FrontProductController::class, 'getProductsFavorites']);
Route::get('product-category', [FrontProductController::class, 'productsByCategory']);
Route::get('product-search/{key}', [FrontProductController::class, 'searchProduct']);
Route::post('product-review', [ReviewController::class, 'store']);
Route::get('product-review/{id}', [ReviewController::class, 'show']);
Route::post('storeorder', [FrontOrderController::class, 'storeOrder']);
Route::get('invoice/{invoice}', [FrontOrderController::class, 'getInvoice']);
Route::post('order-search', [FrontOrderController::class, 'searchOrder']);
Route::get('order-random', [FrontOrderController::class, 'getRandomOrder']);
Route::get('getBanks', [FrontApiController::class, 'getBanks']);
Route::post('notify-order/sendWhatsapp', [NotifyController::class, 'sendWhatsapp']);
Route::post('carts/clear', [CartController::class, 'clear']);
Route::apiResource('carts', CartController::class);
Route::get('shipping/tracking/{order_id}', [ShippingController::class, 'trackingWaybill']);
Route::get('shipping/searchAddress', [ShippingController::class, 'searchAddress']);
Route::post('shipping/costs', [ShippingController::class, 'getCost']);
Route::get('shipping/getCouriers', [ShippingController::class, 'getCouriers']);
Route::get('shop', [FrontApiController::class, 'getShop']);
Route::get('config', [FrontApiController::class, 'getConfig']);
Route::get('getVoucherActive', [FrontApiController::class, 'getVoucherActive']);
Route::get('getVoucherByCode/{voucher_code}', [FrontApiController::class, 'getVoucherByCode']);

Route::get('payment-chanels', [PaymentServiceController::class, 'paymentChanels']);

Route::post('dispatchEvent', NotificationDispathEventController::class);

Route::post('uploadPaymentProof/{order_id}', [CustomerController::class, 'uploadPaymentProof']);

Route::post('runCommand', [FrontApiController::class, 'runCommand']);
Route::get('marketplaces', [FrontApiController::class, 'marketplaces']);
Route::get('getAffiliateConfig', [FrontApiController::class, 'getAffiliateConfig']);

Route::post('pushLeads', [LeadController::class, 'store']);
Route::get('getSaldoConfig', [SaldoController::class, 'index']);

Route::get('/sse/payment-status/{trxId}', [SseController::class, 'streamPayments']);
Route::get('/poll/payment-status/{trxId}', [SseController::class, 'polling']);
