<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;

Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/logout', [AuthController::class, 'logout']);
Route::get('auth/validationToken', [AuthController::class, 'validationToken']);

Route::post('auth/password-token', [PasswordResetController::class, 'requestPasswordToken']);
Route::get('auth/validate-token/{token}', [PasswordResetController::class, 'validateToken']);
Route::post('auth/password-reset', [PasswordResetController::class, 'resetPassword']);
