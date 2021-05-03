<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\General\NewsFeedController;
use App\Http\Controllers\Auth\refreshTokenController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\General\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
/*
/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
|
*/





Route::post('/auth/register', [RegisterController::class, 'store']);
Route::post('/auth/login', [LoginController::class, 'store']);
Route::post('/auth/token/refresh', [refreshTokenController::class, 'store']);
Route::post('/auth/recovery', [ForgotPasswordController::class, 'store']);
Route::post('/auth/reset-password/', [ResetPasswordController::class, 'store']);


/*
|--------------------------------------------------------------------------
| Private Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/auth/newsfeed', [NewsFeedController::class, 'index'])->middleware('auth:api');
Route::post('/auth/profile', [ProfileController::class, 'store'])->middleware('auth:api');
Route::post('/auth/logout', [LogoutController::class, 'store'])->middleware(('auth:api'));

// Route::middleware('auth:api')->get('/newsfeed', function (Request $request) {
//   return $request->user();
// });
