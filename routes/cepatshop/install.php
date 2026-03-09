<?php

use App\Http\Controllers\Install\InstallController;
use Illuminate\Support\Facades\Route;

Route::get('server', [InstallController::class, 'serverRequirements']);
Route::get('database', [InstallController::class, 'databaseIndex']);
Route::post('database', [InstallController::class, 'databaseStore']);
Route::post('install', [InstallController::class, 'install']);
Route::get('status', [InstallController::class, 'status']);
