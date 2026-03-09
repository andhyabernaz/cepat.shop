<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\Frontend\CustomerController;
use App\Http\Controllers\WithdrawalController;

Route::get('user', [AccountController::class, 'show']);
Route::post('user/update', [AccountController::class, 'update']);
Route::get('customer/orders', [CustomerController::class, 'getOrders']);
Route::get('customer/invoice/{invoice_ref}', [CustomerController::class, 'getInvoice']);
Route::post('customer/submitReviews', [CustomerController::class, 'submitReviews']);
Route::get('customer/getReviews', [CustomerController::class, 'getReviews']);
Route::delete('customer/deleteAccount', [CustomerController::class, 'deleteAccount']);
Route::get('customer/mutasi-saldos', [CustomerController::class, 'mutasiSaldo']);

Route::get('affiliate', [AffiliateController::class, 'show']);
Route::post('affiliate', [AffiliateController::class, 'store']);
Route::put('affiliate/{id}', [AffiliateController::class, 'update']);

Route::get('affiliate/check/{code}', [AffiliateController::class, 'checkAffiliateCode']);
Route::get('affiliate/check-coupon/{code}', [AffiliateController::class, 'checkAffiliateCouponCode']);
Route::post('affiliate/update-coupon/{id}', [AffiliateController::class, 'updateCouponCode']);
Route::get('affiliate/generate', [AffiliateController::class, 'autoGenerate']);
Route::get('affiliate/products', [AffiliateController::class, 'getProductAffiliate']);
Route::get('affiliate/leaderboard', [AffiliateController::class, 'leaderboard']);
Route::get('affiliate/getLeads', [AffiliateController::class, 'leads']);

Route::apiResource('user-address', UserAddressController::class)->except('show');

Route::get('licenses', [LicenseController::class, 'index']);
Route::get('download', [LicenseController::class, 'download']);
Route::get('license/{id}', [LicenseController::class, 'show']);
Route::get('getDownloadUrl/{id}', [LicenseController::class, 'getDownloadUrl']);

 Route::get('leads/visited', [LeadController::class, 'index']);


 Route::post('withdrawal', [WithdrawalController::class, 'store']);
 Route::get('customer/withdrawals', [WithdrawalController::class, 'byUser']);


