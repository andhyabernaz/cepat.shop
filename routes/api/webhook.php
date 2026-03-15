<?php

use App\Http\Controllers\DuitkuWebhookController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\TripayWebhookController;
use App\Http\Controllers\XenditWebhookController;

Route::any('tripay/callback', TripayWebhookController::class)->name('tripay.callback');
Route::any('xendit/invoices/callback', XenditWebhookController::class)->name('xendit.callback');
Route::any('midtrans/invoices/callback', MidtransWebhookController::class)->name('midtrans.callback');
Route::any('duitku/callback', DuitkuWebhookController::class)->name('duitku.callback');
