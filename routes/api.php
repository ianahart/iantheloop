<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;

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





Route::post('/register', [RegisterController::class, 'store']);




/*
|--------------------------------------------------------------------------
| Private Routes
|--------------------------------------------------------------------------
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
