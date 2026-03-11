<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BankController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\SaldoController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\NotifyController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WagatewayController;
use App\Http\Controllers\MailConfigController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\MutasiSaldoController;
use App\Http\Controllers\ProductPromoController;
use App\Http\Controllers\PaymentConfigController;
use App\Http\Controllers\AffiliateConfigController;
use App\Http\Controllers\DigitalDownloadController;
use App\Http\Controllers\Frontend\ReviewController;
use App\Http\Controllers\NotificationTemplateController;

Route::get('config', [ConfigController::class, 'admin']);
Route::post('config', [ConfigController::class, 'update']);

Route::get('order/status-options', [OrderController::class, 'statusOptions']);
Route::get('orders', [OrderController::class, 'index']);
Route::get('orders/{id}', [OrderController::class, 'show']);
Route::put('orders/{id}', [OrderController::class, 'update']);
Route::delete('orders/{id}', [OrderController::class, 'destroy']);
Route::post('orders/{id}/complete', [OrderController::class, 'completionOrder']);
Route::post('orders/{id}/cancel', [OrderController::class, 'cancelOrder']);
Route::post('orders/{id}/accept-payment', [OrderController::class, 'paymentAccepted']);
Route::post('orders/{id}/update-status', [OrderController::class, 'updateStatusOrder']);

Route::get('userList', [UserController::class, 'userList']);
Route::apiResource('users', UserController::class);

Route::post('slider/setPostLink', [SliderController::class, 'setPostLink']);
Route::post('slider/update-weight', [SliderController::class, 'updateWeight']);
Route::apiResource('sliders', SliderController::class);

Route::post('blocks/setPostLink', [BlockController::class, 'setPostLink']);
Route::apiResource('blocks', BlockController::class);

Route::apiResource('banks', BankController::class);

Route::get('products/{id}/varians', [ProductController::class, 'productVarians']);
Route::delete('products/{id}/removeVarian', [ProductController::class, 'removeVarian']);
Route::apiResource('products', ProductController::class);

Route::get('shop', [StoreController::class, 'index']);
Route::post('shop', [StoreController::class, 'update']);
Route::post('update-pwa', [StoreController::class, 'updatePwa']);

Route::apiResource('categories', CategoryController::class);

Route::apiResource('posts', PostController::class);
Route::get('post-tags', [PostController::class, 'postTags']);

Route::get('update', [UpdateController::class, 'overview']);
Route::post('update', [UpdateController::class, 'update']);
Route::post('clearCache', [UpdateController::class, 'clearCache']);

Route::get('product-promo/products/{promoId}', [ProductPromoController::class, 'getProductPromo']);
Route::get('product-promo/find-product', [ProductPromoController::class, 'findProducts']);
Route::post('product-promo/remove', [ProductPromoController::class, 'removeProduct']);
Route::post('product-promo', [ProductPromoController::class, 'store']);
Route::get('product-promo/product-without-promo', [ProductPromoController::class, 'productWithoutPromo']);


Route::get('promo/detail/{id}', [PromoController::class, 'promoDetail']);
Route::apiResource('promos', PromoController::class);

Route::get('telegram-test', [NotifyController::class, 'testingTelegram']);
Route::get('email-test', [NotifyController::class, 'testingEmail']);

Route::get('config-email', [MailConfigController::class, 'show']);
Route::post('config-email', [MailConfigController::class, 'update']);

Route::get('reviews', [ReviewController::class, 'index']);
Route::post('reviews', [ReviewController::class, 'publish']);
Route::delete('reviews/{id}', [ReviewController::class, 'destroy']);

Route::get('export/orders', [ExportController::class, 'orders']);

Route::get('generate-pwa', [StoreController::class, 'generatePwa']);

Route::get('vouchers/checkCode/{voucher_code}', [VoucherController::class, 'checkVoucherCode']);
Route::get('vouchers/generate', [VoucherController::class, 'generate']);
Route::apiResource('vouchers', VoucherController::class);

Route::apiResource('assets', MediaController::class);

Route::post('notification-template/sort', [NotificationTemplateController::class, 'sort']);
Route::post('notification-template/bulkUpdate', [NotificationTemplateController::class, 'bulkUpdate']);
Route::get('notification-template/order-status-options', [NotificationTemplateController::class, 'getOrderStatusOptions']);
Route::get('notification-template/order-event-options', [NotificationTemplateController::class, 'getOrderEventOptions']);
Route::get('notification-template/order-options', [NotificationTemplateController::class, 'getOrderOptions']);
Route::apiResource('notification-template', NotificationTemplateController::class);

Route::get('messages', [MessageController::class, 'index']);
Route::post('messages/{id}', [MessageController::class, 'send']);

Route::get('payment-configs', [PaymentConfigController::class, 'index']);
Route::post('payment-configs', [PaymentConfigController::class, 'store']);

Route::get('adminReports', [DashboardController::class, 'index']);

Route::post('wagateway-config/setAsDefault/{id}', [WagatewayController::class, 'setAsDefault']);
Route::post('wagateway-config/testing/{id}', [WagatewayController::class, 'testing']);
Route::apiResource('wagateway-config', WagatewayController::class);

Route::get('mutasi-saldos', [MutasiSaldoController::class, 'index']);
Route::post('marketplaces/{id}/setStatus', [MarketplaceController::class, 'setStatus']);
Route::apiResource('marketplaces', MarketplaceController::class);

Route::get('product-subscriptions-options', [ProductController::class, 'getSubscriptionOptions']);

Route::post('digital-download/upload', [DigitalDownloadController::class, 'upload']);
Route::post('digital-download/delete/{id}', [DigitalDownloadController::class, 'destroy']);

Route::get('payment-configs', [PaymentConfigController::class, 'index']);
Route::post('payment-configs', [PaymentConfigController::class, 'store']);

Route::post('affiliate-config', [AffiliateConfigController::class, 'store']);
Route::get('affiliate-config', [AffiliateConfigController::class, 'index']);

Route::get('saldo-config', [SaldoController::class, 'index']);
Route::post('saldo-config', [SaldoController::class, 'store']);

Route::get('withdrawal', [WithdrawalController::class, 'index']);
Route::post('withdraw-process/{id}', [WithdrawalController::class, 'process']);
Route::post('withdrawal-abort/{id}', [WithdrawalController::class, 'abort']);

Route::put('affiliates/{id}/updateStatus', [AffiliateController::class, 'updateStatus']);
Route::get('affiliates', [AffiliateController::class, 'index']);
Route::get('affiliate-users', [AffiliateController::class, 'users']);

Route::post('roles/assign', [RoleController::class, 'assign']);
Route::apiResource('roles', RoleController::class);

Route::post('permissions/toggle', [PermissionController::class, 'toggle']);
Route::get('roles-permissions', [PermissionController::class, 'rolePermissions']);
Route::get('getUserPermissions', [PermissionController::class, 'getUserPermissions']);


