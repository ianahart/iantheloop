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
use App\Http\Controllers\general\StatsController;
use App\Http\Controllers\General\NetworkController;
use App\Http\Controllers\General\UserController;
use App\Http\Controllers\General\PostController;
use App\Http\Controllers\General\PostLikeController;
use App\Http\Controllers\General\FlaggedPostController;


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
Route::get('/auth/profile/{profileId}/edit', [ProfileController::class, 'edit'])->middleware('auth:api');
Route::get('/auth/profile/{profileId}/about', [ProfileController::class, 'showAbout'])->middleware('auth:api');
Route::patch('/auth/profile/{profileId}/update', [ProfileController::class, 'update'])->middleware('auth:api');
Route::get('/auth/profile/{id}', [ProfileController::class, 'show'])->middleware('auth:api');
Route::post('/auth/profile', [ProfileController::class, 'store'])->middleware('auth:api');
Route::post('/auth/logout', [LogoutController::class, 'store'])->middleware('auth:api');
Route::patch('/auth/stats/follow/{userId}/update', [StatsController::class, 'updateFollowStats'])->middleware('auth:api');
Route::patch('/auth/stats/unfollow/{userId}/update', [StatsController::class, 'updateUnfollowStats'])->middleware('auth:api');
Route::get('/auth/network/following/show/{userId}', [NetworkController::class, 'showFollowing'])->middleware('auth:api');
Route::get('/auth/network/followers/show/{userId}', [NetworkController::class, 'showFollowers'])->middleware('auth:api');
Route::patch('/auth/user/status/{userId}/update', [UserController::class, 'updateUserStatus'])->middleware('auth:api');
Route::post('/auth/posts/store', [PostController::class, 'store'])->middleware('auth:api');
Route::get('/auth/posts', [PostController::class, 'index'])->middleware('auth:api');
Route::delete('/auth/posts/{id}/delete', [PostController::class, 'delete'])->middleware('auth:api');
Route::post('/auth/post-likes/store', [PostLikeController::class, 'store'])->middleware('auth:api');
Route::delete('/auth/post-likes/{id}/delete', [PostLikeController::class, 'delete'])->middleware('auth:api');
Route::post('/auth/flagged-posts/store', [FlaggedPostController::class, 'store'])->middleware('auth:api');
